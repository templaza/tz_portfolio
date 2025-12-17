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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\Registry\Registry;
use Joomla\CMS\Helper\ModuleHelper AS JoomlaModuleHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioTemplate;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioUri;

//jimport('joomla.filesytem.file');
//jimport('joomla.application.module.helper');

//JLoader::import('com_tz_portfolio_plus.includes.framework',JPATH_ADMINISTRATOR.'/components');

class ModuleHelper extends JoomlaModuleHelper{

    public static function getLayoutPath($module, $layout = 'default')
    {
        return self::getTZLayoutPath($module, $layout);
    }

    public static function getTZLayoutPath($module, $layout = 'default')
    {
        $_template      = Factory::getApplication()->getTemplate(true);
        $template       = $_template -> template;
        $defaultLayout  = $layout;
        $moduleName     = '';

        if (strpos($layout, ':') !== false)
        {
            // Get the template and file name from the string
            $temp = explode(':', $layout);
            $template = $temp[0] === '_' ? $template : $temp[0];
            $layout = $temp[1];
            $defaultLayout = $temp[1] ?: 'default';
        }

        $modParams  = new Registry();
        if(is_string($module)){
            $moduleName = $module;
            if($objModule  = ModuleHelper::getModule($module)){
                if(is_string($objModule -> params)) {
                    $modParams->loadString($objModule->params);
                }else{
                    $modParams  = $objModule -> params;
                }
            }
        }else{
            $moduleName = $module -> module;
            if(isset($module -> params) && $module -> params){
                if(is_string($module -> params)){
                    $modParams -> loadString($module -> params);
                }else{
                    $modParams  = $module -> params;
                }
            }
        }

        if($tplId = (int) $modParams -> def('template_id', 0)) {
            $tpTemplate = TZ_PortfolioTemplate::getTemplateById($tplId);
        }
        else{
            $tpTemplate = TZ_PortfolioTemplate::getTemplate(true);
        }

        if($tpTemplate){
            $tplParams  = $tpTemplate->params;

            var_dump($tpTemplate);
            die(__FILE__);

            $tpdefPath  = null;
            $tpPath     = null;

            if(isset($tpTemplate -> home_path) && $tpTemplate -> home_path){
                $tpdefPath    = $tpTemplate -> home_path.'/' . $moduleName . '/' . $layout . '.php';
            }
            if(isset($tpTemplate -> base_path) && $tpTemplate -> base_path){
                $tpPath    = $tpTemplate -> base_path.'/' . $moduleName . '/' . $layout . '.php';
            }

            // Add template.css file if it has have in template
            if(is_dir(COM_TZ_PORTFOLIO_STYLE_PATH.DIRECTORY_SEPARATOR.$tpTemplate -> template)) {
//            if (File::exists(COM_TZ_PORTFOLIO_STYLE_PATH . '/' . $tpTemplate -> template
//                . '/css/template.css')) {

                $docOptions = array();
                $docOptions['template']     = $tpTemplate->template;
                $docOptions['file']         = 'template.php';
                $docOptions['params']       = $tplParams;
                $docOptions['directory']    = COM_TZ_PORTFOLIO_STYLE_PATH;

                $doc    = Factory::getApplication() -> getDocument();

                $docClone   = clone($doc);
                // Add template.css file if it has have in template
                $legacyPath = COM_TZ_PORTFOLIO_STYLE_PATH . DIRECTORY_SEPARATOR . $tpTemplate -> template
                    . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'template.css';
                if((TZ_PortfolioTemplate::getSassDirByStyle($tpTemplate -> template)
                        || (!TZ_PortfolioTemplate::getSassDirByStyle($tpTemplate -> template) && TZ_PortfolioTemplate::getSassDirCore()))
                    && !File::exists($legacyPath) &&
                    $cssRelativePath = TZ_PortfolioTemplate::getCssStyleName($tpTemplate -> template,
                        $modParams, $docOptions['params'] -> get('colors', array()), $docClone)){
                    $docClone->addStyleSheet(TZ_PortfolioUri::base(true)
                        . '/css/'.$cssRelativePath, array('version' => 'auto'));
                }else
                    if (File::exists($legacyPath)) {
                        $docClone->addStyleSheet(TZ_PortfolioUri::base(true) . '/templates/'
                            . $tpTemplate -> template . '/css/template.css', array('version' => 'auto'));
                    }
//                $docClone -> addStyleSheet(TZ_PortfolioUri::base(true) . '/templates/'
//                    . $tpTemplate -> template . '/css/template.css', array('version' => 'auto'));

//                var_dump($cssRelativePath);
//                unset($docClone -> _styleSheets[TZ_PortfolioUri::base(true)
//                    . '/css/style/elegant/style-fc36-f8ec2f40b743846bc9e1dd5edaf9e0ca.css']);
                $docClone -> parse($docOptions);
                $doc -> setHeadData($docClone -> getHeadData());

            }
        }

        // Build the template and base path for the layout
        $tPath = JPATH_THEMES . '/' . $template . '/html/' . $moduleName . '/' . $layout . '.php';
        $bPath = JPATH_BASE . '/modules/' . $moduleName . '/tmpl/' . $defaultLayout . '.php';
        $dPath = JPATH_BASE . '/modules/' . $moduleName . '/tmpl/default.php';

        // If the template has a layout override use it
        if ($tplParams->get('override_html_template_site', 0)) {

            if(file_exists($tpPath)){
                return $tpPath;
            }

            if(file_exists($tpdefPath)){
                return $tpdefPath;
            }

            if (file_exists($tPath))
            {
                return $tPath;
            }
        }else{
            if (file_exists($tPath))
            {
                return $tPath;
            }

            if(file_exists($tpPath)){
                return $tpPath;
            }

            if(file_exists($tpdefPath)){
                return $tpdefPath;
            }
        }

        if (file_exists($bPath))
        {
            return $bPath;
        }

        return $dPath;
    }

    public static function getAddOnModuleLayout($group, $name, $module, $layout='default', $folder = 'modules', Registry $params = null){

        $template   = Factory::getApplication()->getTemplate();

        if(!$layout){
            $layout = 'default';
        }

        $cfglayout  = $layout;

        if (strpos($layout, ':') !== false)
        {
            // Get the template and file name from the string
            $temp = explode(':', $layout);
            $template = ($temp[0] == '_') ? $template : $temp[0];
            $layout = $temp[1];
            $cfglayout = ($temp[1]) ? $temp[1] : 'default';
        }

        $modParams  = new Registry();
        if(is_string($module) && !$params){
            if($objModule  = ModuleHelper::getModule($module)){
                if(is_string($objModule -> params)) {
                    $modParams->loadString($objModule->params);
                }else{
                    $modParams  = $objModule -> params;
                }
            }
        }
        if($params){
            $modParams  = $params;
        }

        // Get template
        $tpTemplate = null;
        if($tplId = (int) $modParams -> def('template_id', 0)) {
            $tpTemplate = TZ_PortfolioTemplate::getTemplateById($tplId);
        }

        if(!$tpTemplate){
            $tpTemplate = TZ_PortfolioTemplate::getTemplate(true);
        }

        // Path from AddOn with module's layout config
        $bPath = COM_TZ_PORTFOLIO_ADDON_PATH . '/' . $group . '/' . $name . '/'.$folder.'/'.$module
            .'/'. $cfglayout . '.php';

        $mpaths = PathHelper::getModuleTmplPathOfAddOn($module, $group, $name, true);

        if(!empty($mpaths)){
            if(is_array($mpaths)){
                foreach ($mpaths as $key => $_mpath){
                    if($key != 'mbasePath') {
                        $mpath = $_mpath . '/ado_' . $group . '_' . $name . '/' . $cfglayout . '.php';
                    }else{
                        $mpath = $_mpath . '/' . $cfglayout . '.php';
                    }
                    if(file_exists($mpath)){
                        return $mpath;
                    }
                }
            }
        }


        $tplParams  = $tpTemplate->params;

        // Build the template and base path for the layout
        $tpdefPath  = null;
        $tpPath     = null;


        // Path from template of TZ Portfolio Plus assigned
        if(isset($tpTemplate -> home_path) && $tpTemplate -> home_path){
            $tpdefPath    = $tpTemplate -> home_path.'/' . $module .'/plg_' . $group. '_' . $name
                . '/' . $layout . '.php';
        }
        // Path from default template of TZ Portfolio Plus
        if(isset($tpTemplate -> base_path) && $tpTemplate -> base_path){
            $tpPath    = $tpTemplate -> base_path.'/' . $module .'/plg_' . $group. '_' . $name
                . '/' . $layout . '.php';
        }

        // Path from Joomla Template
        $tPath = JPATH_THEMES . '/' . $template . '/html/'.$module.'/plg_' . $group . '_' . $name . '/' . $layout . '.php';

        // Path from module with module's layout config
        $mPath  = JPATH_SITE.'/modules/'.$module.'/tmpl/plg_'.$group.'_'.$name.'/'.$cfglayout.'.php';

        // Path from AddOn with module's layout config
        $bPath = COM_TZ_PORTFOLIO_ADDON_PATH . '/' . $group . '/' . $name . '/'.$folder.'/'.$module
            .'/'. $cfglayout . '.php';

//        var_dump('$bPath');
//        var_dump($bPath);
//        var_dump('$tPath');
//        var_dump($tPath);
//        var_dump('$mPath');
//        var_dump($mPath);
//        var_dump('$tpPath');
//        var_dump($tpPath);
//////        $bPath  = COM_TZ_PORTFOLIO_ADDON_PATH.'/'.$group.'/'.$name.
////        var_dump(PathHelper::getModuleTmplPathOfAddOn($module, $group, $name, true));
////        var_dump($folder);
////        var_dump($module);
////        var_dump($tpTemplate);
//        die(__FILE__);

//        if($files = Folder::files(COM_TZ_PORTFOLIO_SITE_HELPERS_PATH,'.php')){
//            foreach ($files as $file){
//                JLoader::import('com_tz_portfolio_plus.helpers.'.File::stripExt($file), JPATH_SITE.'/components');
//            }
//        }
//
//        // If the template has a layout override use it
//        if ($tplParams->get('override_html_template_site', 0)) {
//
//            // Return path from TZ Portfolio Plus's Template assigned
//            if(file_exists($tpPath)){
//                return $tpPath;
//            }
//
//            // Return path from TZ Portfolio Plus's template which set default
//            if(file_exists($tpdefPath)){
//                return $tpdefPath;
//            }
//
//            // Return path from Joomla Template
//            if (file_exists($tPath))
//            {
//                return $tPath;
//            }
//
//        }else{
//            // If the template has a layout override use it
//
//            // Return path from TZ Portfolio Plus's Template assigned
//            if (file_exists($tPath))
//            {
//                return $tPath;
//            }
//
//            // Return path from Joomla Template
//            if(file_exists($tpPath)){
//                return $tpPath;
//            }
//
//            // Return path from TZ Portfolio Plus's template with default layout
//            if(file_exists($tpdefPath)){
//                return $tpdefPath;
//            }
//        }
//
//        // Return path from module (If the module support the addon)
//        if(file_exists($mPath)){
//            return $mPath;
//        }
//
//        // Return path from TZ Porfolio Plus's addon
//        if (file_exists($bPath))
//        {
//            return $bPath;
//        }


        return false;
    }
}