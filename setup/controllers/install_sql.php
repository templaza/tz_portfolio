<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\Folder;
use Joomla\Database\DatabaseInterface;

class TZ_PortfolioSetupControllerInstall_Sql extends TZ_PortfolioSetupControllerLegacy
{

    /**
     * Perform installation of SQL queries
     *
     * @since	2.2.7
     * @access	public
     */
    public function initialize()
    {
        // Get the temporary path from the server.
        $tmpPath = $this->input->get('path', '', 'default');

        // There should be a queries.zip archive in the archive.
        $tmpQueriesPath = $tmpPath . '/sql.zip';

        // Extract the queries
        $path = $tmpPath . '/sql';

        if(file_exists($tmpQueriesPath)){
            // Check if this folder exists.
            if (is_dir($path)) {
                Folder::delete($path);
            }

            // Extract the archive now
            $state = $this->tppExtract($tmpQueriesPath, $path);

            if (!$state) {
                $this->setInfo('COM_TZ_PORTFOLIO_SETUP_ERROR_UNABLE_EXTRACT_QUERIES', false);
                return $this->output();
            }
        }

        // Get the list of files in the folder.
        $queryFiles = Folder::files($path, '[^demo].+sql', true, true);
        if($this->input -> getInt('sample_data', 0)){
            $queryFiles[]  = $path.'/demo.sql';
        }

        // When there are no queries file, we should just display a proper warning instead of exit
        if (!$queryFiles) {
            $this->setInfo('COM_TZ_PORTFOLIO_SETUP_ERROR_EMPTY_QUERIES_FOLDER', false);
            return $this->output();
        }

        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $isMySQL = $this->isMySQL();
        $total = 0;

        foreach ($queryFiles as $file) {
            // Get the contents of the file
            $contents = file_get_contents($file);
            $queries = $this->splitSql($contents);

            foreach ($queries as $query) {
                $query = trim($query);

                if ($isMySQL && !$this->hasUTF8mb4Support()) {
                    $query = $this->convertUtf8mb4QueryToUtf8($query);
                }

                if (!empty($query)) {
                    $db->setQuery($query);
                    $state = $db->execute();
                }
            }
            $total += 1;
        }

        // Alter tables
        $this -> alterTable();

        $this -> createSectionPermissions();

        if(file_exists(JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/tz_portfolio_plus.xml')){
            $this -> migratePermissions();
        }

        // lets fix the created_by id
        $this->fixArticlesAuthorId();

        $this -> addDefaultStylePreset();

        $this->setInfo(Text::sprintf('COM_TZ_PORTFOLIO_INSTALLATION_SQL_EXECUTED_SUCCESS', $total), true);
        return $this->output();
    }

    public function fixArticlesAuthorId()
    {
        // assuming the user that logged into backed installer will be a superadmin as well.
        $my = Factory::getUser();

        $db =  Factory::getContainer()->get(DatabaseInterface::class);

        /* Fix author id of articles */
        $query = "update `#__tz_portfolio_plus_content` set `created_by` = " . $my->id;
        $query .= " where `created_by` = 0";

        $db->setQuery($query);
        $db->execute();

        /* Before fix author id of categories add asset id for each category with created_user_id is 0 */
        $this -> fixAssetCategory();

        /* Fix author id of categories */
        $query = "update `#__tz_portfolio_plus_categories` set `created_user_id` = " . $my->id;
        $query .= " where `created_user_id` = 0";

        $db->setQuery($query);
        $db->execute();

        return true;
    }

    public function fixAssetCategory()
    {
        $asset  = null;
        $db     = Factory::getContainer()->get(DatabaseInterface::class);
        $query  = $db -> getQuery(true);

        $query->select('*');
        $query->from('#__assets');
        $query->where('name = ' . $db->quote('com_tz_portfolio.category'));
        $db->setQuery($query);

        if(!$asset = $db->loadObject()){
            $query -> clear();
            $query->select('*');
            $query->from('#__assets');
            $query->where('name = ' . $db->quote('com_tz_portfolio'));
            $db->setQuery($query);
            $asset = $db->loadObject();
        }

        $assetTbl       = Table::getInstance('Asset');

        /* Get all categories with created_user_id is 0 */
        $query -> clear();
        $query -> select('id, title');
        $query -> from('#__tz_portfolio_plus_categories');
        $query -> where('created_user_id = 0 AND id <> 1');
        $db -> setQuery($query);
        if($categories = $db -> loadObjectList()){
            foreach($categories as $item){
                $assetTblLoaded = $assetTbl -> loadByName('com_tz_portfolio.category.'.$item -> id);
                if($asset && !$assetTblLoaded) {
                    $query->clear();

                    $query->insert('#__assets');
                    $query->columns('parent_id, lft, rgt, level, name, title, rules');
                    $query->values($asset->id . ',' . ($asset->lft + 1) . ',' . ($asset->rgt + 1)
                        . ',2,' . $db->quote('com_tz_portfolio.category.'.$item -> id) . ',' . $db->quote($item -> title)
                        . ',' . $db->quote('{}'));
                    $db->setQuery($query);
                    $db->execute();

                    $new_asset_id = $db->insertid();
                }else{
                    if($assetTblLoaded){
                        $new_asset_id   = $assetTbl -> id;
                    }
                }

                if(isset($new_asset_id) && !empty($new_asset_id)) {
                    $query->clear();
                    $query->update('#__tz_portfolio_plus_categories');
                    $query->set('asset_id = ' . $new_asset_id);
                    $query->where('id = '.$item -> id);
                    $db->setQuery($query);
                    $db->execute();
                }
            }
        }

        return true;
    }

    public function addDefaultStylePreset(){
        $db = Factory::getDbo();

        // Insert default template
        $template_sql   = 'SELECT COUNT(*) FROM #__tz_portfolio_plus_templates';
        $db -> setQuery($template_sql);

        if(!$db -> loadResult()){
            $def_file   = JPATH_ROOT.'/media/com_tz_portfolio/style/default.json';
            if(file_exists($def_file)){
                $def_value      = file_get_contents($def_file);
                $template_sql2  = 'INSERT IGNORE INTO `#__tz_portfolio_plus_templates`(`id`, `title`, `home`, `params`) VALUES(1, \'system - Default\', \'1\',\''.$def_value.'\')';
                $db -> setQuery($template_sql2);
                $db -> execute();
            }
        }
    }

    public function alterTable()
    {
        $db = Factory::getDbo();

        // Add fields for table tz_portfolio_plus_content;
        $fields = $db -> getTableColumns('#__tz_portfolio_plus_content');
        $arr    = array();
        if(!array_key_exists('priority',$fields)){
            $arr[]  = 'ADD `priority` INT NOT NULL DEFAULT \'0\'';
        }
        if(!array_key_exists('status',$fields)){
            $arr[]  = 'ADD `status` TINYINT(4) NOT NULL DEFAULT \'0\' COMMENT \'Store old state to restore state\' AFTER `state`;';
        }

        if($arr && count($arr)>0){
            $arr    = implode(',',$arr);
            if($arr){
                $query  = 'ALTER TABLE `#__tz_portfolio_plus_content` '.$arr;
                $db -> setQuery($query);
                $db -> execute();
            }
        }

        // Add fields for table tz_portfolio_plus_extensions;
        $fields = $db -> getTableColumns('#__tz_portfolio_plus_extensions');
        $arr    = array();
        if(!array_key_exists('asset_id',$fields)){
            $arr[]  = 'ADD `asset_id` int(10) unsigned NOT NULL DEFAULT \'0\'';
        }

        if($arr && count($arr)>0){
            $arr    = implode(',',$arr);
            if($arr){
                $query  = 'ALTER TABLE `#__tz_portfolio_plus_extensions` '.$arr;
                $db -> setQuery($query);
                $db -> execute();
            }
        }

        // Add fields for table tz_portfolio_plus_templates;
        $fields = $db -> getTableColumns('#__tz_portfolio_plus_templates');
        $arr    = array();
        if(!array_key_exists('preset',$fields)){
            $arr[]  = 'ADD `preset` VARCHAR( 255 ) NOT NULL';
        }

        if($arr && count($arr)>0){
            $arr    = implode(',',$arr);
            if($arr){
                $query  = 'ALTER TABLE `#__tz_portfolio_plus_templates` '.$arr;
                $db -> setQuery($query);
                $db -> execute();
            }
        }

        // Add fields for table tz_portfolio_plus_fields;
        $fields = $db -> getTableColumns('#__tz_portfolio_plus_fields');
        $arr    = array();
        if(!array_key_exists('advanced_search',$fields)){
            $arr[]  = 'ADD `advanced_search` tinyint(4) NOT NULL DEFAULT \'0\'';
        }
        if(!array_key_exists('list_view',$fields)){
            $arr[]  = 'ADD `list_view` tinyint(4) NOT NULL DEFAULT \'0\'';
        }
        if(!array_key_exists('detail_view',$fields)){
            $arr[]  = 'ADD `detail_view` tinyint(4) NOT NULL DEFAULT \'1\'';
        }
        if(!array_key_exists('asset_id',$fields)){
            $arr[]  = 'ADD `asset_id` INT UNSIGNED NOT NULL DEFAULT \'0\'';
        }
        if(!array_key_exists('created',$fields)){
            $arr[]  = 'ADD `created` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'';
        }
        if(!array_key_exists('created_by',$fields)){
            $arr[]  = 'ADD `created_by` INT UNSIGNED NOT NULL DEFAULT \'0\'';
        }
        if(!array_key_exists('modified',$fields)){
            $arr[]  = 'ADD `modified` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'';
        }
        if(!array_key_exists('modified_by',$fields)){
            $arr[]  = 'ADD `modified_by` INT UNSIGNED NOT NULL DEFAULT \'0\'';
        }
        if(!array_key_exists('access',$fields)){
            $arr[]  = 'ADD `access` INT(10) UNSIGNED NOT NULL DEFAULT \'1\'';
        }
        if(!array_key_exists('checked_out',$fields)){
            $arr[]  = 'ADD `checked_out` int(10) unsigned NOT NULL DEFAULT \'0\'';
        }
        if(!array_key_exists('checked_out_time',$fields)){
            $arr[]  = 'ADD `checked_out_time` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'';
        }

        if($arr && count($arr)>0){
            $arr    = implode(',',$arr);
            if($arr){
                $query  = 'ALTER TABLE `#__tz_portfolio_plus_fields` '.$arr;
                $db -> setQuery($query);
                $db -> execute();
            }
        }

        // Add fields for table tz_portfolio_plus_field_content_map
        $fields = $db -> getTableColumns('#__tz_portfolio_plus_field_fieldgroup_map');
        $arr    = array();
        if(!array_key_exists('ordering',$fields)){
            $arr[]  = 'ADD `ordering` int(11) NOT NULL';
        }

        if($arr && count($arr)>0){
            $arr    = implode(',',$arr);
            if($arr){
                $query  = 'ALTER TABLE `#__tz_portfolio_plus_field_fieldgroup_map` '.$arr;
                $db -> setQuery($query);
                $db -> execute();
            }
        }

        // Add fields for table tz_portfolio_plus_fieldgroups
        $fields = $db -> getTableColumns('#__tz_portfolio_plus_fieldgroups');
        $arr    = array();
        if(!array_key_exists('field_ordering_type',$fields)){
            $arr[]  = 'ADD `field_ordering_type` tinyint(4) NOT NULL DEFAULT \'0\'';
        }
        if(!array_key_exists('ordering',$fields)){
            $arr[]  = 'ADD `ordering` int(11) NOT NULL DEFAULT \'0\'';
        }
        if(!array_key_exists('asset_id',$fields)){
            $arr[]  = 'ADD `asset_id` int(10) unsigned NOT NULL DEFAULT \'0\' COMMENT \'FK to the #__assets table.\'';
        }
        if(!array_key_exists('created',$fields)){
            $arr[]  = 'ADD `created` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'';
        }
        if(!array_key_exists('created_by',$fields)){
            $arr[]  = 'ADD `created_by` INT UNSIGNED NOT NULL DEFAULT \'0\'';
        }
        if(!array_key_exists('modified',$fields)){
            $arr[]  = 'ADD `modified` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'';
        }
        if(!array_key_exists('modified_by',$fields)){
            $arr[]  = 'ADD `modified_by` INT UNSIGNED NOT NULL';
        }
        if(!array_key_exists('access',$fields)){
            $arr[]  = 'ADD `access` INT(10) UNSIGNED NOT NULL DEFAULT \'1\'';
        }
        if(!array_key_exists('checked_out',$fields)){
            $arr[]  = 'ADD `checked_out` int(10) unsigned NOT NULL DEFAULT \'0\'';
        }
        if(!array_key_exists('checked_out_time',$fields)){
            $arr[]  = 'ADD `checked_out_time` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'';
        }

        if($arr && count($arr)>0){
            $arr    = implode(',',$arr);
            if($arr){
                $query  = 'ALTER TABLE `#__tz_portfolio_plus_fieldgroups` '.$arr;
                $db -> setQuery($query);
                $db -> execute();
            }
        }

        // Add fields for table tz_portfolio_plus_addon_data
        $fields = $db -> getTableColumns('#__tz_portfolio_plus_addon_data');
        $arr    = array();
        if(!array_key_exists('asset_id',$fields)){
            $arr[]  = 'ADD `asset_id` INT UNSIGNED NOT NULL DEFAULT \'0\'';
        }
        if(!array_key_exists('created',$fields)){
            $arr[]  = 'ADD `created` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'';
        }
        if(!array_key_exists('created_by',$fields)){
            $arr[]  = 'ADD `created_by` INT UNSIGNED NOT NULL';
        }
        if(!array_key_exists('modified',$fields)){
            $arr[]  = 'ADD `modified` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'';
        }
        if(!array_key_exists('modified_by',$fields)){
            $arr[]  = 'ADD `modified_by` INT UNSIGNED NOT NULL';
        }
        if(!array_key_exists('checked_out',$fields)){
            $arr[]  = 'ADD `checked_out` INT NOT NULL DEFAULT \'0\'';
        }
        if(!array_key_exists('checked_out_time',$fields)){
            $arr[]  = 'ADD `checked_out_time` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'';
        }
        if(!array_key_exists('publish_up',$fields)){
            $arr[]  = 'ADD `publish_up` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'';
        }
        if(!array_key_exists('publish_down',$fields)){
            $arr[]  = 'ADD `publish_down` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\'';
        }
        if(!array_key_exists('access',$fields)){
            $arr[]  = 'ADD `access` INT(10) UNSIGNED NOT NULL DEFAULT \'0\'';
        }

        if($arr && count($arr)>0){
            $arr    = implode(',',$arr);
            if($arr){
                $query  = 'ALTER TABLE `#__tz_portfolio_plus_addon_data` '.$arr;
                $db -> setQuery($query);
                $db -> execute();
            }
        }

        // Add fields for table tz_portfolio_plus_addon_data
        $arr        = array();
        $tblName    = '#__tz_portfolio_plus_addon_meta';
        $fields     = $db -> getTableColumns($tblName, false);

        if(!array_key_exists('addon_id',$fields)){
            $arr[]  = 'ADD `addon_id` INT UNSIGNED NOT NULL AFTER `id`';
        }

        if($arr && count($arr)>0){
            $arr    = implode(',',$arr);
            if($arr){
                $query  = 'ALTER TABLE `'.$tblName.'` '.$arr;
                $db -> setQuery($query);
                $db -> execute();
            }
        }

        if($fields && count($fields) && isset($fields['id']) && $fields['id']){
            if(empty($fields['id'] -> Extra)){
                $query  = 'ALTER TABLE `'.$tblName.'` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT';
                $db -> setQuery($query);
                $db -> execute();
            }
        }
        return true;
    }


    protected function createSectionPermissions(){
        $sections   = array('category', 'group', 'tag', 'addon', 'template', 'style');
        if(count($sections)){
            // Get the parent asset id so we have a correct tree.
            $parentAsset = Table::getInstance('Asset');

            if($parentAsset->loadByName('com_tz_portfolio')){

                $parentAssetId = $parentAsset->id;

                // Create permissions for acl
                $asset  = Table::getInstance('Asset');

                foreach($sections as $section){
                    $name  = 'com_tz_portfolio.'.$section;
                    $asset -> reset();
                    if($asset->loadByName($name) !== false){
                        continue;
                    }
                    $asset -> id		= 0;
                    $asset -> parent_id = $parentAssetId;
                    $asset -> name  	= $name;
                    switch ($section){
                        default:
                            $asset -> title  = Text::_('COM_TZ_PORTFOLIO_'.strtoupper($section).'S');
                            break;
                        case 'category':
                            $asset -> title  = Text::_('COM_TZ_PORTFOLIO_CATEGORIES');
                            break;
                        case 'group':
                            $asset -> title  = Text::_('COM_TZ_PORTFOLIO_FIELD_GROUPS');
                            break;
                        case 'style':
                            $asset -> title  = Text::_('COM_TZ_PORTFOLIO_TEMPLATE_STYLES');
                            break;

                    }
                    $asset->setLocation($parentAssetId, 'last-child');
                    if ($asset->check())
                    {
                        $asset->store();
                    }
                }
            }
        }
    }

    protected function migratePermissions(){
        $db     = Factory::getContainer()->get(DatabaseInterface::class);
        $query  = $db -> getQuery(true);

        // Get the parent asset id so we have a correct tree.
        $tblAsset       = Table::getInstance('Asset');
        $tblParentAsset = Table::getInstance('Asset');

        $query -> select('a.*');
        $query -> from($tblAsset -> getTableName().' AS a');
        $query -> where('a.name LIKE '.$db -> quote('com_tz_portfolio_plus%'));
        $query -> order('a.parent_id ASC');

        $db -> setQuery($query);

        if($assets = $db -> loadObjectList()){
            foreach($assets as $asset){
                $tblAsset -> reset();
                $tblAsset -> set('id', 0);

                $tblParentAsset -> reset();
                $tblParentAsset -> set('id', 0);

                $newAssetName   = preg_replace('/^com_tz_portfolio_plus/',
                    'com_tz_portfolio', $asset -> name);

                if(!$tblAsset -> loadByName($newAssetName)){
                    /* Get parent asset */
                    $parentId   = $asset -> parent_id;

                    if($tblParentAsset -> load($asset -> parent_id)){
                        $parentId   = $tblParentAsset -> parent_id;

                        if(preg_match('/^com_tz_portfolio_plus/', $tblParentAsset -> name)){
                            $parentName   = preg_replace('/^com_tz_portfolio_plus/',
                                'com_tz_portfolio', $tblParentAsset -> name);
                            $tblParentAsset -> reset();
                            $tblParentAsset -> id   = 0;
                            if($tblParentAsset -> loadByName($parentName)){
                                $parentId   = $tblParentAsset -> id;
                            }
                        }
                    }
                    /* Create asset */
                    $tblAsset->setLocation($parentId, 'last-child');

                    $tblAsset -> parent_id  = $parentId;
                    $tblAsset -> name       = $newAssetName;
                    $tblAsset -> rules      = $asset -> rules;
                    $tblAsset -> title      = $asset -> title;

                    $tblAsset -> store();
                }
            }
        }
    }
}
