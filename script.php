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

jimport('joomla.installer.installer');

class com_tz_portfolio_plusInstallerScript{

    protected $install_new  = false;

    function postflight($type, $parent){

        $db     = JFactory::getDbo();

        if($this -> install_new) {
            $user = JFactory::getUser();

            $query = $db->getQuery(true);
            $query->update('#__tz_portfolio_plus_categories');
            $query->set('created_user_id = ' . $user->id);
            $db->setQuery($query);
            $db->execute();

            $query->clear();
            $query->select('*');
            $query->from('#__assets');
            $query->where('name = ' . $db->quote('com_tz_portfolio_plus'));
            $db->setQuery($query);
            $asset = $db->loadObject();

            $query->clear();

            $query->insert('#__assets');
            $query->columns('parent_id, lft, rgt, level, name, title, rules');
            $query->values($asset->id . ',' . ($asset->lft + 1) . ',' . ($asset->rgt + 1)
                . ',2,' . $db->quote('com_tz_portfolio_plus.category.2') . ',' . $db->quote('Uncategorised')
                . ',' . $db->quote('{"core.create":{"6":1,"3":1},"core.delete":{"6":1},"core.edit":{"6":1,"4":1},'
                    . '"core.edit.state":{"6":1,"5":1},"core.edit.own":{"6":1,"3":1}}'));
            $db->setQuery($query);
            $db->execute();

            $new_asset_id = $db->insertid();

            $query->clear();
            $query->update('#__tz_portfolio_plus_categories');
            $query->set('asset_id = ' . $new_asset_id);
            $query->where('id = 2');
            $db->setQuery($query);
            $db->execute();
        }

        JFactory::getLanguage() -> load('com_tz_portfolio_plus');


        //Create folder
        $mediaFolder    = 'tz_portfolio_plus';
        $mediaFolderPath    = JPATH_SITE.'/media/'.$mediaFolder;
        $article    = 'article';
        $cache      = 'cache';
        $src        = 'src';
        $html   = htmlspecialchars_decode('<!DOCTYPE html><title></title>');

        if(!JFolder::exists($mediaFolderPath)){
            JFolder::create($mediaFolderPath);
        }
        if(!JFile::exists($mediaFolderPath.'/index.html')){
            JFile::write($mediaFolderPath.'/index.html',$html);
        }
        if(!JFolder::exists($mediaFolderPath.'/'.$article)){
            JFolder::create($mediaFolderPath.'/'.$article);
        }
        if(!JFile::exists($mediaFolderPath.'/'.$article.'/'.'index.html')){
            JFile::write($mediaFolderPath.'/'.$article.'/'.'index.html',$html);
        }
        if(!JFolder::exists($mediaFolderPath.'/'.$article.'/'.$cache)){
            JFolder::create($mediaFolderPath.'/'.$article.'/'.$cache);
        }
        if(!JFile::exists($mediaFolderPath.'/'.$article.'/'.$cache.'/'.'index.html')){
            JFile::write($mediaFolderPath.'/'.$article.'/'.$cache.'/'.'index.html',$html);
        }
        if(!JFolder::exists($mediaFolderPath.'/'.$article.'/'.$src)){
            JFolder::create($mediaFolderPath.'/'.$article.'/'.$src);
        }
        if(!JFile::exists($mediaFolderPath.'/'.$article.'/'.$src.'/'.'index.html')){
            JFile::write($mediaFolderPath.'/'.$article.'/'.$src.'/'.'index.html',$html);
        }
        //Install plugins
        $status = new stdClass;
        $status->modules = array();
        $src = $parent->getParent()->getPath('source');

        if(version_compare( JVERSION, '1.6.0', 'ge' )) {
            $modules = $parent->getParent()->manifest->xpath('modules/module');

            foreach($modules as $module){
                $result = null;
                $mname = $module->attributes() -> module;
                $mname = (string)$mname;
                $client = $module->attributes() -> client;
                if(is_null($client)) $client = 'site';
                ($client=='administrator')? $path=$src.'/'.'administrator'.'/'.'modules'.'/'.$mname: $path = $src.'/'.'modules'.'/'.$mname;
                $installer = new JInstaller();
                $result = $installer->install($path);
                $status->modules[] = array('name'=>$mname,'client'=>$client, 'result'=>$result);
            }

            $plugins = $parent->getParent()->manifest->xpath('plugins/plugin');
            foreach($plugins as $plugin){
                $result = null;
                $folder = null;
                $pname  = $plugin->attributes() -> plugin;
                $pname  = (string) $pname;
                $group  = $plugin->attributes() -> group;
                $folder = $plugin -> attributes() -> folder;
                if(isset($folder)){
                    $folder = $plugin -> attributes() -> folder;
                }
                $path   = $src.'/'.'plugins'.'/'.$group.'/'.$folder;

                $installer = new JInstaller();
                $result = $installer->install($path);


                $query  = 'UPDATE #__extensions SET `enabled`=1 WHERE `type`="plugin" AND `element`="'
                    .$pname.'" AND `folder`="'.$group.'"';
                $db -> setQuery($query);
                $db -> execute();

                $status->plugins[] = array('name'=>$pname,'group'=>$group, 'result'=>$result);
            }

            // Insert default template
            $template_sql   = 'SELECT COUNT(*) FROM #__tz_portfolio_plus_templates';
            $db -> setQuery($template_sql);
            if(!$db -> loadResult()){
                $def_file   = JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/views/template_style/tmpl/default.json';
                if(JFile::exists($def_file)){
                    $def_value      = JFile::read($def_file);
                    $template_sql2  = 'INSERT INTO `#__tz_portfolio_plus_templates`(`id`, `title`, `home`, `params`) VALUES(1, \'Default\', \'1\',\''.$def_value.'\')';
                    $db -> setQuery($template_sql2);
                    $db -> query();
                }
            }
        }


        // Reinstall for add-ons to update version
        JLoader::import('com_tz_portfolio_plus.libraries.installer',JPATH_ADMINISTRATOR
            .DIRECTORY_SEPARATOR.'components');
        $tzInstaller    = TZ_Portfolio_PlusInstaller::getInstance();
        $addon_cores    = array('content' => 'vote',
            'extrafields' => array('checkboxes','dropdownlist','multipleselect', 'radio', 'text', 'textarea'),
            'mediatype' => 'image',
            'user' => 'profile');
        foreach($addon_cores as $type => $addon) {
            $addon_path = $src . '/site/addons/'.$type;
            if(is_array($addon)){
                foreach($addon as $value) {
                    $tzInstaller->install($addon_path.'/' . $value);
                }
            }else{
                $tzInstaller -> install($addon_path.'/'.$addon);
            }
        }
        // End reinstall for add-ons to update version

        $this -> installationResult($status);

    }

    function install($parent)
    {
        $this -> install_new    = true;
    }
    function uninstall($parent){
        $mediaFolder    = 'tz_portfolio_plus';
        $mediaFolderPath    = JPATH_SITE.'/'.'media'.'/'.$mediaFolder;
        if(JFolder::exists($mediaFolderPath)){
            JFolder::delete($mediaFolderPath);
        }
        $imageFolderPath    = JPATH_SITE.'/'.'images'.'/'.$mediaFolder;
        if(JFolder::exists($imageFolderPath)){
            JFolder::delete($imageFolderPath);
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
            $db -> replacePrefix('#__tz_portfolio_plus_categories'),
            $db -> replacePrefix('#__tz_portfolio_plus_content'),
            $db -> replacePrefix('#__tz_portfolio_plus_content_category_map'),
            $db -> replacePrefix('#__tz_portfolio_plus_content_featured_map'),
            $db -> replacePrefix('#__tz_portfolio_plus_content_rating'),
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

        if($arr && count($arr)>0){
            $arr    = implode(',',$arr);
            if($arr){
                $query  = 'ALTER TABLE `#__tz_portfolio_plus_fieldgroups` '.$arr;
                $db -> setQuery($query);
                $db -> execute();
            }
        }
    }

    public function installationResult($status){
        $lang   = JFactory::getLanguage();
        $lang -> load('com_tz_portfolio_plus');
        $rows   = 0;
        ?>
        <h2><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS'); ?></h2>
        <span style="font-weight: normal"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_DESCRIPTION');?></span>
        <h3 style="margin-top: 20px;"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_INSTALL_STATUS'); ?></h3>
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
        <h2><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS'); ?></h2>
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