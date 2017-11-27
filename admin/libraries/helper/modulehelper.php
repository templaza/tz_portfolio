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

jimport('joomla.application.module.helper');

JLoader::import('com_tz_portfolio_plus.includes.framework',JPATH_ADMINISTRATOR.'/components');

abstract class TZ_Portfolio_PlusModuleHelper extends JModuleHelper{
    public static function getLayoutPath($module, $layout = 'default')
    {
        $_template  = \JFactory::getApplication()->getTemplate(true);
        $template   = $_template -> template;
        $defaultLayout = $layout;

        if (strpos($layout, ':') !== false)
        {
            // Get the template and file name from the string
            $temp = explode(':', $layout);
            $template = $temp[0] === '_' ? $template : $temp[0];
            $layout = $temp[1];
            $defaultLayout = $temp[1] ?: 'default';
        }


        $tpTemplate = TZ_Portfolio_PlusTemplate::getTemplate(true);
        $tplParams  = $tpTemplate->params;

        $tpdefPath  = null;
        $tpPath     = null;

        if(isset($tpTemplate -> home_path) && $tpTemplate -> home_path){
            $tpdefPath    = $tpTemplate -> home_path.'/' . $module . '/' . $layout . '.php';
        }
        if(isset($tpTemplate -> base_path) && $tpTemplate -> base_path){
            $tpPath    = $tpTemplate -> base_path.'/' . $module . '/' . $layout . '.php';
        }

        // Add template.css file if it has have in template
        if (JFile::exists(COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH . '/' . $tpTemplate -> template
            . '/css/template.css')
        ) {

            $docOptions = array();
            $docOptions['template']     = $tpTemplate->template;
            $docOptions['file']         = 'template.php';
            $docOptions['params']       = $tplParams;
            $docOptions['directory']    = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH;

            $doc    = JFactory::getDocument();

            $docClone   = clone($doc);
            $docClone -> addStyleSheet(TZ_Portfolio_PlusUri::base(true) . '/templates/'
                . $tpTemplate -> template . '/css/template.css');

            $docClone -> parse($docOptions);
            $doc -> setHeadData($docClone -> getHeadData());

        }

        // Build the template and base path for the layout
        $tPath = JPATH_THEMES . '/' . $template . '/html/' . $module . '/' . $layout . '.php';
        $bPath = JPATH_BASE . '/modules/' . $module . '/tmpl/' . $defaultLayout . '.php';
        $dPath = JPATH_BASE . '/modules/' . $module . '/tmpl/default.php';

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
}