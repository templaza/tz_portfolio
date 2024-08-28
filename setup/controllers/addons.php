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

use Joomla\Filesystem\Folder;

class TZ_PortfolioSetupControllerAddons extends TZ_PortfolioSetupControllerLegacy
{
    public function lists()
    {

        $result = new stdClass();

        // Get a list of folders in the module and plugins.
        $path = $this->input->get('path', '', 'default');



        $modulesExtractPath = $path . '/modules';
        $pluginsExtractPath = $path . '/plugins';
        $addonsExtractPath  = $path . '/add-ons';
        $stylesExtractPath  = $path . '/styles';

        $install    = new stdClass();
        $install -> moduleDisabled  = false;
        $install -> pluginDisabled  = false;
        $install -> addonDisabled   = false;
        $install -> styleDisabled   = false;

        // Get the modules list
        $modules = $this->getModulesList($path, $modulesExtractPath,$install -> moduleDisabled );

        // Get the plugins list
        $plugins = $this->getPluginsList($path, $pluginsExtractPath,$install -> pluginDisabled);

        // Get the add-ons list
        $addons = $this->getAddOnsList($path, $addonsExtractPath,$install -> addonDisabled);

        // Get the styles list
        $styles = $this->getStylesList($path, $stylesExtractPath,$install -> styleDisabled);


        $data = new stdClass();

        $data->modules  = $modules;
        $data->plugins  = $plugins;
        $data->styles   = $styles;
        $data->addons   = $addons;

        ob_start();
        include(dirname(__DIR__) . '/view/steps/addons_list.php');
        $contents = ob_get_contents();
        ob_end_clean();

        $result->html       = $contents;
        $result->modulePath = $modulesExtractPath;
        $result->pluginPath = $pluginsExtractPath;
        $result->addonPath  = $addonsExtractPath;
        $result->stylePath  = $stylesExtractPath;

        $result->scripts = array();

        return $this->output($result);
    }

    private function getModulesList($path, $tmp, &$disabled = false)
    {
        $zip = $path . '/modules.zip';

        if(file_exists($zip)){
            $state = $this->tppExtract($zip, $tmp);
    
            // @TODO: Return errors
            if (!$state) {
                return false;
            }
        }

        if(!is_dir($tmp)){
            return false;
        }

        // Get a list of modules
        $items = Folder::folders($tmp, '.', false, true);

        $modules = array();

        $modulesProtect = array(
            'mod_tz_portfolio',
            'mod_tz_portfolio_tags',
            'mod_tz_portfolio_filter',
            'mod_tz_portfolio_categories',
            'mod_tz_portfolio_articles_archive'
        );

        foreach ($items as $item) {
            $element = basename($item);
            $manifest = $item . '/' . $element . '.xml';

            // Read the xml file
            $parser = simplexml_load_file($manifest);

            $module = new stdClass();
            $module->title = (string) $parser->name;
            $module->version = (string) $parser->version;
            $module->description = (string) $parser->description;
            $module->description = trim($module->description);
            $module->element = $element;
            $module->disabled = false;
            $module->checked = true;

            // Check if the module of core, put a flag
            // Disable this only if the module is checked.
            if (in_array($module->title, $modulesProtect)) {
                $module->disabled = true;
            }

            if($module -> disabled) {
                $disabled = true;
            }

            $modules[] = $module;
        }

        return $modules;
    }

    private function getPluginsList($path, $tmp, &$disabled = false)
    {
        $zip = $path . '/plugins.zip';

        if(file_exists($zip)){
            $state = $this->tppExtract($zip, $tmp);
    
            // @TODO: Return errors
            if (!$state) {
                return false;
            }
        }
        
        if(!is_dir($tmp)){
            return false;
        }

        // Get a list of plugin groups
        $groups = Folder::folders($tmp, '.', false, true);

        $plugins = array();

        $pluginsProtect = array(
            'plg_system_tz_portfolio',
            'plg_quickicon_tz_portfolio_plus'
        );

        foreach ($groups as $group) {
            $groupTitle = basename($group);

            // Get a list of items in each groups
            $items = Folder::folders($group, '.', false, true);

            foreach ($items as $item) {
                $element = basename($item);
                $manifest = $item . '/' . $element . '.xml';

                // Read the xml file
                $parser = simplexml_load_file($manifest);

                if (!$parser) {
                    continue;
                }
                $plugin = new stdClass();
                $plugin->element = $element;
                $plugin->group = $groupTitle;
                $plugin->title = (string) $parser->name;
                $plugin->version = (string) $parser->version;
                $plugin->description = (string) $parser->description;
                $plugin->description = trim($plugin->description);
                $plugin->disabled = false;

                if (in_array($plugin->title, $pluginsProtect)) {
                    $plugin->disabled = true;
                }

                if($plugin -> disabled) {
                    $disabled = true;
                }
                $plugins[] = $plugin;
            }
        }

        return $plugins;
    }

    private function getAddOnsList($path, $tmp, &$disabled = false)
    {
        $zip = $path . '/addons.zip';

        if(file_exists($zip)){
            $state = $this->tppExtract($zip, $tmp);

            // @TODO: Return errors
            if (!$state) {
                return false;
            }
        }

        if(!is_dir($tmp)){
            return false;
        }

        // Get a list of plugin groups
        $groups = Folder::folders($tmp, '.', false, true);

        $addons = array();

        $addonsProtect = array(
            'plg_user_profile',
            'plg_content_vote',
            'plg_mediatype_image',
            'plg_extrafields_text',
            'plg_extrafields_radio',
            'plg_extrafields_textarea',
            'plg_extrafields_checkboxes',
            'plg_extrafields_dropdownlist',
            'plg_extrafields_multipleselect'
        );

        foreach ($groups as $group) {
            $groupTitle = basename($group);

            // Get a list of items in each groups
            $items = Folder::folders($group, '.', false, true);

            foreach ($items as $item) {
                $element = basename($item);
                $manifest = $item . '/' . $element . '.xml';

                // Read the xml file
                $parser = simplexml_load_file($manifest);

                if (!$parser) {
                    continue;
                }

                $attribs    = $parser -> attributes();
                $type       = (string) $attribs -> type;

                if(!$type || ($type && $type != 'tz_portfolio_plus-plugin' && $type != 'tz_portfolio-addon')){
                    continue;
                }

                $addon = new stdClass();
                $addon->element = $element;
                $addon->group = $groupTitle;
                $addon->title = (string) $parser->name;
                $addon->version = (string) $parser->version;
                $addon->description = (string) $parser->description;
                $addon->description = trim($addon->description);
                $addon->disabled = false;

                if (in_array($addon->title, $addonsProtect)) {
                    $addon->disabled = true;
                }

                if($addon -> disabled) {
                    $disabled = true;
                }

                $addons[] = $addon;
            }
        }

        return $addons;
    }

    private function getStylesList($path, $tmp, &$disabled = false)
    {

        $zip = $path . '/styles.zip';

        if(file_exists($zip)){
            $state = $this->tppExtract($zip, $tmp);

            // @TODO: Return errors
            if (!$state) {
                return false;
            }
        }

        if(!is_dir($tmp)){
            return false;
        }

        // Get a list of modules
        $items = Folder::folders($tmp, '.', false, true);

        $styles = array();

        $stylesProtect = array(
            'system',
            'elegant'
        );

        foreach ($items as $item) {

            $element = basename($item);
            $manifest = $item . '/template.xml';

            // Read the xml file
            $parser = simplexml_load_file($manifest);

            if (!$parser) {
                continue;
            }

            $attribs    = $parser -> attributes();
            $type       = (string) $attribs -> type;

            if(!$type || ($type && $type != 'tz_portfolio_plus-template' & $type != 'tz_portfolio-style')){
                continue;
            }

            $style = new stdClass();
            $style->title = (string) $parser->name;
            $style->version = (string) $parser->version;
            $style->description = (string) $parser->description;
            $style->description = trim($style->description);
            $style->element = $element;
            $style->disabled = false;
            $style->checked = true;

            if (in_array($style->title, $stylesProtect)) {
                $style->disabled = true;
            }

            if($style -> disabled) {
                $disabled = true;
            }

            $styles[] = $style;
        }

        return $styles;
    }
}