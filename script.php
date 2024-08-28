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

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\LanguageHelper;

class com_tz_portfolioInstallerScript{

    protected $install_new  = false;

    public function postflight($type, $parent){
        if($type != 'uninstall') {
            // Display information when installed
            $this->installationResult();
        }
    }

    public function uninstall($parent){
        $mediaFolder    = 'com_tz_portfolio';
        $mediaFolderPath    = JPATH_SITE.'/'.'media'.'/'.$mediaFolder;
        if(is_dir($mediaFolderPath)){
            Folder::delete($mediaFolderPath);
        }
        $imageFolderPath    = JPATH_SITE.'/'.'images'.'/'.$mediaFolder;
        if(is_dir($imageFolderPath)){
            Folder::delete($imageFolderPath);
        }

        $status = new stdClass();
        $status->modules = array ();
        $status->plugins = array ();

        $_parent    = $parent -> getParent();
        $modules = $_parent -> manifest -> xpath('modules/module');
        $plugins = $_parent -> manifest -> xpath('plugins/plugin');

        $db = Factory::getDbo();
        $result = null;
        if($modules){
            foreach($modules as $module){
                $mname = (string)$module->attributes() -> module;
                $client = (string)$module->attributes() -> client;

                $query = "SELECT `extension_id` FROM #__extensions WHERE `type`='module' AND `element` = "
                    .$db->quote($mname);
                $db->setQuery($query);
                $IDs = $db->loadColumn();
                if (count($IDs)) {
                    foreach ($IDs as $id) {
                        $installer = new Installer;
                        $result = $installer->uninstall('module', $id);
                    }
                    $status->modules[] = array ('name'=>$mname, 'client'=>$client, 'result'=>$result);
                }
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
                        $installer = new Installer;
                        $result = $installer->uninstall('plugin', $id);
                    }
                    $status->plugins[] = array ('name'=>$pname, 'group'=>$pgroup, 'result'=>$result);
                }
            }
        }

        $this -> uninstallationResult($status);
    }

    public function installationResult(){
        ?>
        <style>
            .tpp-installation-status{
                padding-bottom: 30px;
            }
            .tpp-installation-status .box-head{
                display: flex;
                align-items: center;
                margin-bottom: 10px;
            }
            .tpp-installation-status .box-head .logo{
                max-height: none;
                margin-right: 10px;
            }
            .tpp-installation-status .box-head .title small{
                display: block;
                font-size: 14px;
                margin-top: 7px;
            }
            .tpp-installation-status .box-content{
                padding: 20px 30px;
                background: #f9fafc;
                border: 1px solid #efefef;
            }
            .tpp-installation-status .box-content h3{
                margin-bottom: 30px;
            }
            .tpp-installation-status .btn-success{
                font-size: 12px;
                margin-top: 10px;
                font-weight: bold;
                padding: 12px 24px;
                border-color: #66bb6a;
                background-color: #66bb6a;
                text-transform: uppercase;
            }
            .tpp-installation-status .btn-success:hover,
            .tpp-installation-status .btn-success:focus{
                background: #7fc682;
                border-color: #7fc682;
            }
        </style>
        <?php
        $lang = Factory::getApplication() -> getLanguage();
        $lang -> load('com_tz_portfolio', JPATH_ADMINISTRATOR);
        ?>
        <div class="tpp-installation-status">
            <div class="box-head">
                <div class="logo">
                    <img src="<?php echo Uri::root().'/administrator/components/com_tz_portfolio/setup/assets/images/logo.png';?>" alt="<?php echo Text::_('COM_TZ_PORTFOLIO'); ?>"/>
                </div>
                <h2 class="title"><?php echo Text::_('COM_TZ_PORTFOLIO'); ?><small><?php echo Text::_('COM_TZ_PORTFOLIO_DESCRIPTION'); ?></small></h2>
            </div>
            <div class="box-content">
                <h3><?php echo Text::_('COM_TZ_PORTFOLIO_THANKS_YOU_FOR_USING'); ?></h3>
                <p><?php echo Text::_('COM_TZ_PORTFOLIO_THANKS_YOU_FOR_USING_DESC'); ?></p>
                <a href="<?php echo Uri::root();?>administrator/index.php?option=com_tz_portfolio" class="btn btn-success text-white"><?php echo Text::_('COM_TZ_PORTFOLIO_CONTINUE_WITH_INSTALLATION'); ?></a>
            </div>
        </div>
    <?php
    }
    function uninstallationResult($status){
        $lang   = Factory::getApplication() -> getLanguage();
        $lang -> load('com_tz_portfolio', JPATH_ADMINISTRATOR);
        $rows   = 0;
        ?>
        <h2 style="margin-top: 20px;"><?php echo Text::_('COM_TZ_PORTFOLIO'); ?></h2>
        <span style="font-weight: normal"><?php echo Text::_('COM_TZ_PORTFOLIO_DESCRIPTION');?></span>
        <h3 style="margin-top: 20px;"><?php echo Text::_('COM_TZ_PORTFOLIO_REMOVE_STATUS'); ?></h3>
        <table class="table table-striped table-condensed">
            <thead>
            <tr>
                <th class="title" colspan="2"><?php echo Text::_('COM_TZ_PORTFOLIO_EXTENSION'); ?></th>
                <th width="30%"><?php echo Text::_('JSTATUS'); ?></th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="3"></td>
            </tr>
            </tfoot>
            <tbody>
            <tr class="row0">
                <td class="key" colspan="2"><?php echo Text::_('COM_TZ_PORTFOLIO').' '.Text::_('COM_TZ_PORTFOLIO_COMPONENT'); ?></td>
                <td><span style="color: green; font-weight: bold;"><?php echo Text::_('COM_TZ_PORTFOLIO_REMOVED'); ?></span></td>
            </tr>
            <?php if (count($status->modules)): ?>
                <tr>
                    <th><?php echo Text::_('COM_TZ_PORTFOLIO_MODULE'); ?></th>
                    <th><?php echo Text::_('COM_TZ_PORTFOLIO_CLIENT'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->modules as $module): ?>
                    <?php
                    if(LanguageHelper::exists($module['name'])):
                        $lang -> load($module['name']);
                    endif;
                    ?>
                    <tr class="row<?php echo (++ $rows % 2); ?>">
                        <td class="key"><?php echo Text::_($module['name']); ?></td>
                        <td class="key"><?php echo ucfirst($module['client']); ?></td>
                        <td><span style="color: green; font-weight: bold;"><?php echo ($module['result'])?Text::_('COM_TZ_PORTFOLIO_REMOVED'):Text::_('COM_TZ_PORTFOLIO_NOT_REMOVED'); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (count($status->plugins)): ?>
                <tr>
                    <th><?php echo Text::_('COM_TZ_PORTFOLIO_PLUGIN'); ?></th>
                    <th><?php echo Text::_('COM_TZ_PORTFOLIO_GROUP'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->plugins as $plugin): ?>
                    <?php
                    if(LanguageHelper::exists('plg_'.$plugin['group'].'_'.$plugin['name'])):
                        $lang -> load('plg_'.$plugin['group'].'_'.$plugin['name']);
                    endif;
                    ?>
                    <tr class="row<?php echo (++ $rows % 2); ?>">
                        <td class="key"><?php echo Text::_(strtoupper('plg_'.$plugin['group'].'_'.$plugin['name'])); ?></td>
                        <td class="key"><?php echo ucfirst($plugin['group']); ?></td>
                        <td><span style="color: green; font-weight: bold;"><?php echo ($plugin['result'])?Text::_('COM_TZ_PORTFOLIO_REMOVED'):Text::_('COM_TZ_PORTFOLIO_NOT_REMOVED'); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (isset($status -> languages) AND count($status->languages)): ?>
                <tr>
                    <th><?php echo Text::_('COM_TZ_PORTFOLIO_LANGUAGES'); ?></th>
                    <th><?php echo Text::_('COM_TZ_PORTFOLIO_COUNTRY'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->languages as $language): ?>
                    <tr class="row<?php echo (++ $rows % 2); ?>">
                        <td class="key"><?php echo ucfirst($language['language']); ?></td>
                        <td class="key"><?php echo ucfirst($language['country']); ?></td>
                        <td><span style="color: green; font-weight: bold;"><?php echo ($language['result'])?Text::_('COM_TZ_PORTFOLIO_REMOVED'):Text::_('COM_TZ_PORTFOLIO_NOT_REMOVED'); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    <?php
    }
}
?>