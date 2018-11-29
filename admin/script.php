<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use TZ_Portfolio_Plus\Installer\TZ_Portfolio_PlusInstaller;

jimport('joomla.filesytem.file');
jimport('joomla.filesytem.folder');
jimport('joomla.installer.installer');

class com_tz_portfolio_plusInstallerScript{

    protected $install_new  = false;

    function postflight($type, $parent){

        $asset  = null;
        $db     = JFactory::getDbo();
        $query  = $db -> getQuery(true);

        $query->select('*');
        $query->from('#__assets');
        $query->where('name = ' . $db->quote('com_tz_portfolio_plus.category'));
        $db->setQuery($query);

        if(!$asset = $db->loadObject()){
            $query -> clear();
            $query->select('*');
            $query->from('#__assets');
            $query->where('name = ' . $db->quote('com_tz_portfolio_plus'));
            $db->setQuery($query);
            $asset = $db->loadObject();
        }

        $assetTbl   = JTable::getInstance('Asset');
        $assetTblLoaded = $assetTbl -> loadByName('com_tz_portfolio_plus.category.2');

        if($this -> install_new) {
            $user = JFactory::getUser();

            $query = $db->getQuery(true);
            $query->update('#__tz_portfolio_plus_categories');
            $query->set('created_user_id = ' . $user->id);
            $query->set('params = ' .$db -> quote(''));
            $db->setQuery($query);
            $db->execute();

            if($asset && !$assetTblLoaded) {
                $query->clear();

                $query->insert('#__assets');
                $query->columns('parent_id, lft, rgt, level, name, title, rules');
                $query->values($asset->id . ',' . ($asset->lft + 1) . ',' . ($asset->rgt + 1)
                    . ',2,' . $db->quote('com_tz_portfolio_plus.category.2') . ',' . $db->quote('Uncategorised')
                    . ',' . $db->quote('{}'));
                $db->setQuery($query);
                $db->execute();

                $new_asset_id = $db->insertid();
            }else{
                if($assetTblLoaded){
                    $new_asset_id   = $assetTbl -> id;
                }
            }

            if($new_asset_id) {
                $query->clear();
                $query->update('#__tz_portfolio_plus_categories');
                $query->set('asset_id = ' . $new_asset_id);
                $query->where('id = 2');
                $db->setQuery($query);
                $db->execute();
            }
        }

        if($assetTblLoaded && $assetTbl -> parent_id != $asset -> id){
            if($asset){
                $query -> clear();
                $query -> update('#__assets');
                $query -> set('parent_id='.(int) $asset -> id);
                $query -> where('id = '.(int) $assetTbl -> id);
                $db -> setQuery($query);
                $db -> execute();
            }
        }

        JFactory::getLanguage() -> load('com_tz_portfolio_plus');

        if($this -> install_new) {
            //Create folder
            $mediaFolder    = 'tz_portfolio_plus';
            $mediaFolderPath    = JPATH_SITE.'/media/'.$mediaFolder;
            $article    = 'article';
            $cache      = 'cache';
            $src        = 'src';
            $html   = htmlspecialchars_decode('<!DOCTYPE html><title></title>');

            if(!\JFolder::exists($mediaFolderPath)){
                Folder::create($mediaFolderPath);
            }
            if(!\JFile::exists($mediaFolderPath.'/index.html')){
                File::write($mediaFolderPath.'/index.html',$html);
            }
            if(!\JFolder::exists($mediaFolderPath.'/'.$article)){
                Folder::create($mediaFolderPath.'/'.$article);
            }
            if(!\JFile::exists($mediaFolderPath.'/'.$article.'/'.'index.html')){
                File::write($mediaFolderPath.'/'.$article.'/'.'index.html',$html);
            }
            if(!\JFolder::exists($mediaFolderPath.'/'.$article.'/'.$cache)){
                Folder::create($mediaFolderPath.'/'.$article.'/'.$cache);
            }
            if(!\JFile::exists($mediaFolderPath.'/'.$article.'/'.$cache.'/'.'index.html')){
                File::write($mediaFolderPath.'/'.$article.'/'.$cache.'/'.'index.html',$html);
            }
            if(!\JFolder::exists($mediaFolderPath.'/'.$article.'/'.$src)){
                Folder::create($mediaFolderPath.'/'.$article.'/'.$src);
            }
            if(!\JFile::exists($mediaFolderPath.'/'.$article.'/'.$src.'/'.'index.html')){
                File::write($mediaFolderPath.'/'.$article.'/'.$src.'/'.'index.html',$html);
            }
        }

        $status             = new stdClass;
        $status -> modules  = array();
        $src = $parent->getParent()->getPath('source');

        if(version_compare( JVERSION, '1.6.0', 'ge' )) {
            // Install modules packages
            $modules = $parent->getParent()->manifest->xpath('modules/module');

            foreach($modules as $module){

                $result = null;
                $mname  = $module -> attributes() -> module;
                $mname  = (string)$mname;
                $client = $module -> attributes() -> client;

                if(is_null($client)) $client = 'site';

                ($client=='administrator')? $path=$src.'/'.'administrator'.'/'.'modules'.'/'.$mname:
                    $path = $src.'/'.'modules'.'/'.$mname;

                $installer  = new JInstaller();
                $result     = $installer -> install($path);

                $status -> modules[]    = array('name'=>$mname,'client'=>$client, 'result'=>$result);
            }

            // Install modules packages
            $installer  = new JInstaller();
            $plugins    = $parent->getParent()->manifest->xpath('plugins/plugin');
            foreach($plugins as $plugin){

                $result = null;
                $folder = null;
                $pname  = $plugin -> attributes() -> plugin;
                $pname  = (string) $pname;
                $group  = $plugin -> attributes() -> group;
                $folder = $plugin -> attributes() -> folder;

                if(isset($folder)){
                    $folder = $plugin -> attributes() -> folder;
                }

                $path       = $src.'/'.'plugins'.'/'.$group.'/'.$folder;
                $result     = $installer -> install($path);

                $query = 'UPDATE #__extensions SET `enabled`=1, `params` = "" WHERE `type`="plugin" AND `element`="'
                    . $pname . '" AND `folder`="' . $group . '"';
                $db->setQuery($query);
                $db->execute();

                $status->plugins[] = array('name'=>$pname,'group'=>$group, 'result'=>$result);
            }


            if(!$this -> install_new) {
                // Insert default template
                $template_sql   = 'SELECT COUNT(*) FROM #__tz_portfolio_plus_templates';
                $db -> setQuery($template_sql);
                if(!$db -> loadResult()){
                    $def_file   = JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/views/template_style/tmpl/default.json';
                    if(\JFile::exists($def_file)){
                        $def_value      = file_get_contents($def_file);
                        $template_sql2  = 'INSERT INTO `#__tz_portfolio_plus_templates`(`id`, `title`, `home`, `params`) VALUES(1, \'system - Default\', \'1\',\''.$def_value.'\')';
                        $db -> setQuery($template_sql2);
                        $db -> query();
                    }
                }
            }
        }

        // Create default permission
        $this -> createSectionPermissions($src);

        // Install templates to update version
        $this -> installTemplates($src, $parent, $status);

        // Install AddOns to update version
        $this -> installAddOns($src, $parent, $status);

        // Display information when installed
        $this -> installationResult($status);

    }

    protected function createSectionPermissions($sourcePath){
        if(!defined('COM_TZ_PORTFOLIO_PLUS_ACL_SECTIONS')){
            JLoader::import('includes.defines',$sourcePath.'/admin');
        }
        if($sections   = COM_TZ_PORTFOLIO_PLUS_ACL_SECTIONS){
            if(is_string($sections)) {
                $sections = json_decode($sections);
            }
            if(count($sections)){
                // Get the parent asset id so we have a correct tree.
                $parentAsset = JTable::getInstance('Asset');

                if($parentAsset->loadByName('com_tz_portfolio_plus')){

                    $parentAssetId = $parentAsset->id;

                    // Create permissions for acl
                    $asset  = JTable::getInstance('Asset');

                    foreach($sections as $section){
                        $name  = 'com_tz_portfolio_plus.'.$section;
                        $asset -> reset();
                        if($asset->loadByName($name) !== false){
                            continue;
                        }
                        $asset -> id		= 0;
                        $asset -> parent_id = $parentAssetId;
                        $asset -> name  	= $name;
                        switch ($section){
                            default:
                                $asset -> title  = JText::_('COM_TZ_PORTFOLIO_PLUS_'.strtoupper($section).'S');
                                break;
                            case 'category':
                                $asset -> title  = JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES');
                                break;
                            case 'group':
                                $asset -> title  = JText::_('COM_TZ_PORTFOLIO_PLUS_FIELD_GROUPS');
                                break;
                            case 'style':
                                $asset -> title  = JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATE_STYLES');
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
    }

    protected function installTemplates($sourcePath, $parent, &$status = null){
        // Install for add-ons to update version
        JLoader::import('com_tz_portfolio_plus.libraries.installer',JPATH_ADMINISTRATOR
            .DIRECTORY_SEPARATOR.'components');

        $tzInstaller    = TZ_Portfolio_PlusInstaller::getInstance();
        $templates      = $parent->getParent()->manifest->xpath('templates/template');

        foreach($templates as $template) {
            $name   = (string) ($template -> attributes() -> template);
            $path   = $sourcePath . '/site/templates/'.$name;
            $result = $tzInstaller -> install($path);

            $status -> templates[] = array('name' => $name, 'result' => $result);
        }
    }

    protected function installAddOns($sourcePath, $parent, &$status = null){
        // Install for add-ons to update version
        JLoader::import('com_tz_portfolio_plus.libraries.installer',JPATH_ADMINISTRATOR
            .DIRECTORY_SEPARATOR.'components');

        $tzInstaller    = TZ_Portfolio_PlusInstaller::getInstance();
        $addons         = $parent->getParent()->manifest->xpath('addons/addon');

        foreach($addons as $addon) {

            $group  = $addon -> attributes() -> group;
            $name   = (string) ($addon -> attributes() -> addon);
            $path   = $sourcePath . '/site/addons/'.$group.'/'.$name;
            $result = $tzInstaller -> install($path);

            $status -> addons[] = array('name' => $name,'group' => $group, 'result' => $result);
        }
        // End reinstall for add-ons to update version
    }

    public function install($parent)
    {
        $this -> install_new    = true;
    }
    function uninstall($parent){
        $mediaFolder    = 'tz_portfolio_plus';
        $mediaFolderPath    = JPATH_SITE.'/'.'media'.'/'.$mediaFolder;
        if(\JFolder::exists($mediaFolderPath)){
            Folder::delete($mediaFolderPath);
        }
        $imageFolderPath    = JPATH_SITE.'/'.'images'.'/'.$mediaFolder;
        if(\JFolder::exists($imageFolderPath)){
            Folder::delete($imageFolderPath);
        }

        $status = new stdClass();
        $status->modules = array ();
        $status->plugins = array ();

        $_parent    = $parent -> getParent();
        $modules = $_parent -> manifest -> xpath('modules/module');
        $plugins = $_parent -> manifest -> xpath('plugins/plugin');

        $db = JFactory::getDBO();
        $result = null;
        if($modules){
            foreach($modules as $module){
                $mname = (string)$module->attributes() -> module;
                $client = (string)$module->attributes() -> client;

                $query = "SELECT `extension_id` FROM #__extensions WHERE `type`='module' AND `element` = ".$db->Quote($mname)."";
                $db->setQuery($query);
                $IDs = $db->loadColumn();
                if (count($IDs)) {
                    foreach ($IDs as $id) {
                        $installer = new JInstaller;
                        $result = $installer->uninstall('module', $id);
                    }
                }
                $status->modules[] = array ('name'=>$mname, 'client'=>$client, 'result'=>$result);
            }
        }

        if($plugins){
            foreach ($plugins as $plugin) {

                $pname = (string)$plugin->attributes() -> plugin;
                $pgroup = (string)$plugin->attributes() -> group;

                $query = "SELECT `extension_id` FROM #__extensions WHERE `type`='plugin' AND `element` = "
                    .$db->Quote($pname)." AND `folder` = ".$db->Quote($pgroup);
                $db->setQuery($query);
                $IDs = $db->loadColumn();
                if (count($IDs)) {
                    foreach ($IDs as $id) {
                        $installer = new JInstaller;
                        $result = $installer->uninstall('plugin', $id);
                    }
                }
                $status->plugins[] = array ('name'=>$pname, 'group'=>$pgroup, 'result'=>$result);
            }
        }

        $query = $db -> getQuery(true);
        $query -> delete('#__assets');
        $query -> where('name LIKE '.$db -> quote('com_tz_portfolio_plus').'%');
        $db -> setQuery($query);
        $db -> execute();

        $this -> uninstallationResult($status);
    }

    function update($adapter){
        $db     = JFactory::getDbo();
        $listTable  = array(
            $db -> replacePrefix('#__tz_portfolio_plus_addon_data'),
            $db -> replacePrefix('#__tz_portfolio_plus_addon_meta'),
            $db -> replacePrefix('#__tz_portfolio_plus_categories'),
            $db -> replacePrefix('#__tz_portfolio_plus_content'),
            $db -> replacePrefix('#__tz_portfolio_plus_content_category_map'),
            $db -> replacePrefix('#__tz_portfolio_plus_content_featured_map'),
            $db -> replacePrefix('#__tz_portfolio_plus_content_rating'),
            $db -> replacePrefix('#__tz_portfolio_plus_content_rejected'),
            $db -> replacePrefix('#__tz_portfolio_plus_extensions'),
            $db -> replacePrefix('#__tz_portfolio_plus_fieldgroups'),
            $db -> replacePrefix('#__tz_portfolio_plus_fields'),
            $db -> replacePrefix('#__tz_portfolio_plus_field_content_map'),
            $db -> replacePrefix('#__tz_portfolio_plus_field_fieldgroup_map'),
            $db -> replacePrefix('#__tz_portfolio_plus_tags'),
            $db -> replacePrefix('#__tz_portfolio_plus_tag_content_map'),
            $db -> replacePrefix('#__tz_portfolio_plus_templates')
        );
        $disableTables  = array_diff($listTable,$db -> getTableList());

        if(count($disableTables)){
            $installer  = JInstaller::getInstance();
            $sql        = $adapter -> getParent() -> manifest;
            $installer ->parseSQLFiles($sql -> install->sql);
        }

        // Alter collection of all tables
        //$db -> alterTableCharacterSet('#__tz_portfolio_plus_addon_data');
        //$db -> alterTableCharacterSet('#__tz_portfolio_plus_content');

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


        // Remove view tag and user
        $viewPath   = JPATH_SITE.'/components/com_tz_portfolio_plus/views';
        $tagsPath   = JPath::clean($viewPath.'/tags');
        $usersPath  = JPath::clean($viewPath.'/users');
        if(\JFolder::exists($tagsPath)){
            Folder::delete($tagsPath);
        }
        if(\JFolder::exists($usersPath)){
            Folder::delete($usersPath);
        }

        // Remove helpers/icon.php file (it used to old version)
        $iconFile = JPATH_SITE.'/components/com_tz_portfolio_plus/helpers/icon.php';
        if(\JFile::exists($iconFile)){
            File::delete($iconFile);
        }
    }

    public function installationResult($status){
        $lang   = JFactory::getLanguage();
        $lang -> load('com_tz_portfolio_plus');
        $rows   = 0;
        ?>
        <h2 style="margin-top: 20px;"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS'); ?></h2>
        <span style="font-weight: normal"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_DESCRIPTION');?></span>
        <h3 style="margin-top: 20px;"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_INSTALL_STATUS'); ?></h3>
        <table class="table table-striped">
            <thead>
            <tr>
                <th class="title" colspan="2"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_EXTENSION'); ?></th>
                <th width="30%"><?php echo JText::_('JSTATUS'); ?></th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="3"></td>
            </tr>
            </tfoot>
            <tbody>
            <tr class="row0">
                <td class="key" colspan="2"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS').' '.JText::_('COM_TZ_PORTFOLIO_PLUS_COMPONENT'); ?></td>
                <td><span style="color: green; font-weight: bold;"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_INSTALLED'); ?></span></td>
            </tr>
            <?php if (count($status->modules)): ?>
                <tr>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_MODULE'); ?></th>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_CLIENT'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->modules as $module): ?>
                    <?php
                    if(!$lang -> exists((string) $module['name'],JPATH_SITE)):
                        $lang -> load((string)$module['name'],JPATH_SITE);
                    endif;
                    ?>
                    <tr class="row<?php echo (++ $rows % 2); ?>">
                        <td class="key"><?php echo JText::_($module['name']); ?></td>
                        <td class="key"><?php echo ucfirst($module['client']); ?></td>
                        <td><span style="color: green; font-weight: bold;"><?php echo ($module['result'])?JText::_('COM_TZ_PORTFOLIO_PLUS_INSTALLED'):JText::_('COM_TZ_PORTFOLIO_PLUS_NOT_INSTALLED'); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($status && isset($status -> plugins) && count($status->plugins)): ?>
                <tr>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_PLUGIN'); ?></th>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_GROUP'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->plugins as $plugin): ?>
                    <?php
                    if(!$lang -> exists('plg_'.$plugin['group'].'_'.(string)$plugin['name'],JPATH_ADMINISTRATOR, null, true)):
                        $lang -> load('plg_'.$plugin['group'].'_'.(string)$plugin['name'],JPATH_ADMINISTRATOR, null, true);
                    endif;
                    ?>
                    <tr class="row<?php echo (++ $rows % 2); ?>">
                        <td class="key"><?php echo JText::_(strtoupper('plg_'.$plugin['group'].'_'.$plugin['name'])); ?></td>
                        <td class="key"><?php echo ucfirst($plugin['group']); ?></td>
                        <td><span style="color: green; font-weight: bold;"><?php echo ($plugin['result'])?JText::_('COM_TZ_PORTFOLIO_PLUS_INSTALLED'):JText::_('COM_TZ_PORTFOLIO_PLUS_NOT_INSTALLED'); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($status && isset($status -> templates) && count($status->templates)): ?>
                <tr>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATE_OF_COMPONENT'); ?></th>
                    <th></th>
                    <th></th>
                </tr>
                <?php foreach ($status->templates as $template): ?>
                    <?php
                    $tmplPath   = JPATH_SITE.'/components/com_tz_portfolio_plus/templates/'.$template['name'];
                    if(!$lang -> exists('tpl_'.(string)$template['name'], $tmplPath, null, true)):
                        $lang -> load('tpl_'.(string)$template['name'], $tmplPath, null, true);
                    endif;
                    ?>
                    <tr class="row<?php echo (++ $rows % 2); ?>">
                        <td class="key"><?php echo JText::_(strtoupper('tz_portfolio_plus_tpl_'.$template['name'])); ?></td>
                        <td></td>
                        <td>
                            <span style="color: green; font-weight: bold;"><?php echo ($template['result'])?
                                    JText::_('COM_TZ_PORTFOLIO_PLUS_INSTALLED'):
                                    JText::_('COM_TZ_PORTFOLIO_PLUS_NOT_INSTALLED'); ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($status && isset($status -> addons) && count($status->addons)): ?>
                <tr>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ADDON'); ?></th>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_GROUP'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->addons as $addon): ?>
                    <?php
                    $addonPath  = JPATH_SITE.'/components/com_tz_portfolio_plus/addons/'
                        .$addon['group'].'/'.$addon['name'];
                    if(!$lang -> exists('plg_'.$addon['group'].'_'.(string)$addon['name'], $addonPath, null, true)):
                        $lang -> load('plg_'.$addon['group'].'_'.(string)$addon['name'], $addonPath, null, true);
                    endif;
                    ?>
                    <tr class="row<?php echo (++ $rows % 2); ?>">
                        <td class="key"><?php echo JText::_(strtoupper('plg_'.$addon['group'].'_'.$addon['name'])); ?></td>
                        <td class="key"><?php echo ucfirst($addon['group']); ?></td>
                        <td>
                            <span style="color: green; font-weight: bold;"><?php echo ($addon['result'])?
                                    JText::_('COM_TZ_PORTFOLIO_PLUS_INSTALLED'):
                                    JText::_('COM_TZ_PORTFOLIO_PLUS_NOT_INSTALLED'); ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (isset($status -> languages) AND count($status->languages)): ?>
                <tr>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LANGUAGES'); ?></th>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_COUNTRY'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->languages as $language): ?>
                    <tr class="row<?php echo (++ $rows % 2); ?>">
                        <td class="key"><?php echo ucfirst($language['language']); ?></td>
                        <td class="key"><?php echo ucfirst($language['country']); ?></td>
                        <td><span style="color: green; font-weight: bold;"><?php echo ($language['result'])?JText::_('COM_TZ_PORTFOLIO_PLUS_INSTALLED'):JText::_('COM_TZ_PORTFOLIO_PLUS_NOT_INSTALLED'); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            </tbody>
        </table>
    <?php
    }
    function uninstallationResult($status){
        $lang   = JFactory::getLanguage();
        $lang -> load('com_tz_portfolio_plus');
        $rows   = 0;
        ?>
        <h2 style="margin-top: 20px;"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS'); ?></h2>
        <span style="font-weight: normal"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_DESCRIPTION');?></span>
        <h3 style="margin-top: 20px;"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_REMOVE_STATUS'); ?></h3>
        <table class="table table-striped table-condensed">
            <thead>
            <tr>
                <th class="title" colspan="2"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_EXTENSION'); ?></th>
                <th width="30%"><?php echo JText::_('JSTATUS'); ?></th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="3"></td>
            </tr>
            </tfoot>
            <tbody>
            <tr class="row0">
                <td class="key" colspan="2"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS').' '.JText::_('COM_TZ_PORTFOLIO_PLUS_COMPONENT'); ?></td>
                <td><span style="color: green; font-weight: bold;"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_REMOVED'); ?></span></td>
            </tr>
            <?php if (count($status->modules)): ?>
                <tr>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_MODULE'); ?></th>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_CLIENT'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->modules as $module): ?>
                    <?php
                    if($lang -> exists($module['name'])):
                        $lang -> load($module['name']);
                    endif;
                    ?>
                    <tr class="row<?php echo (++ $rows % 2); ?>">
                        <td class="key"><?php echo JText::_($module['name']); ?></td>
                        <td class="key"><?php echo ucfirst($module['client']); ?></td>
                        <td><span style="color: green; font-weight: bold;"><?php echo ($module['result'])?JText::_('COM_TZ_PORTFOLIO_PLUS_REMOVED'):JText::_('COM_TZ_PORTFOLIO_PLUS_NOT_REMOVED'); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (count($status->plugins)): ?>
                <tr>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_PLUGIN'); ?></th>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_GROUP'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->plugins as $plugin): ?>
                    <?php
                    if($lang -> exists('plg_'.$plugin['group'].'_'.$plugin['name'])):
                        $lang -> load('plg_'.$plugin['group'].'_'.$plugin['name']);
                    endif;
                    ?>
                    <tr class="row<?php echo (++ $rows % 2); ?>">
                        <td class="key"><?php echo JText::_(strtoupper('plg_'.$plugin['group'].'_'.$plugin['name'])); ?></td>
                        <td class="key"><?php echo ucfirst($plugin['group']); ?></td>
                        <td><span style="color: green; font-weight: bold;"><?php echo ($plugin['result'])?JText::_('COM_TZ_PORTFOLIO_PLUS_REMOVED'):JText::_('COM_TZ_PORTFOLIO_PLUS_NOT_REMOVED'); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (isset($status -> languages) AND count($status->languages)): ?>
                <tr>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LANGUAGES'); ?></th>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_COUNTRY'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->languages as $language): ?>
                    <tr class="row<?php echo (++ $rows % 2); ?>">
                        <td class="key"><?php echo ucfirst($language['language']); ?></td>
                        <td class="key"><?php echo ucfirst($language['country']); ?></td>
                        <td><span style="color: green; font-weight: bold;"><?php echo ($language['result'])?JText::_('COM_TZ_PORTFOLIO_PLUS_REMOVED'):JText::_('COM_TZ_PORTFOLIO_PLUS_NOT_REMOVED'); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    <?php
    }
}
?>