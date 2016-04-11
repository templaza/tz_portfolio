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

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.application.component.controller');

class TZ_Portfolio_PlusControllerLegacy  extends JControllerLegacy{

    public function display($cachable = false, $urlparams = array())
    {

        $document = JFactory::getDocument();
        $viewType = $document->getType();
        $viewName = $this->input->get('view', $this->default_view);
        $viewLayout = $this->input->get('layout', 'default', 'string');

        $view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath, 'layout' => $viewLayout));

        parent::display($cachable, $urlparams);

        $this -> parseDocument($view);

        return $this;
    }

    public function getView($name = '', $type = '', $prefix = 'TZ_Portfolio_PlusView', $config = array())
    {
        $view   = parent::getView($name,$type,$prefix,$config);
        if($view) {
            $view -> document   = JFactory::getDocument();
            if($template   = TZ_Portfolio_PlusTemplate::getTemplate(true)){
                if($template -> id){
                    $tplparams  = $template -> params;
                    $path       = $view -> get('_path');

                    $bool_tpl   = false;
                    if(JFolder::exists(COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH.DIRECTORY_SEPARATOR.$template -> template)) {
                        $bool_tpl   = true;
                    }
                    if($bool_tpl) {
                        $name   = strtolower($name);
                        // Load template language
                        $lang   = JFactory::getLanguage();
                        $lang -> load('tpl_'.$template -> template, COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH.DIRECTORY_SEPARATOR.$template -> template);

                        if(!$tplparams -> get('override_html_template_site',0)) {
                            $last_path  = array_pop($path['template']);
                            if(isset($template -> base_path) && $template -> base_path) {
                                $path['template'][] = $template->base_path . DIRECTORY_SEPARATOR . $name;
                            }
                            if(isset($template -> home_path) && $template -> home_path) {
                                $path['template'][] = $template->home_path . DIRECTORY_SEPARATOR . $name;
                            }
                            $path['template'][] = $last_path;
                            $view -> set('_path',$path);
                        }else{
                            if(isset($template -> home_path) && $template -> home_path) {
                                $view->addTemplatePath($template->home_path . DIRECTORY_SEPARATOR . $name);
                            }
                            $view ->addTemplatePath($template -> base_path . DIRECTORY_SEPARATOR . $name);
                        }
                    }
                }
            }

        }
        return $view;
    }

    public function getModel($name = '', $prefix = 'TZ_Portfolio_PlusModel', $config = array())
    {
        return parent::getModel($name,$prefix,$config);
    }

    public function parseDocument(&$view = null){
        if($view){
            if(isset($view -> document)){
                if($template   = TZ_Portfolio_PlusTemplate::getTemplate(true)) {
                    if(JFolder::exists(COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH.DIRECTORY_SEPARATOR.$template -> template)) {
                        $docOptions['template']     = $template->template;
                        $docOptions['file']         = 'template.php';
                        $docOptions['params']       = $template->params;
                        $docOptions['directory']    = COM_TZ_PORTFOLIO_PLUS_PATH_SITE . DIRECTORY_SEPARATOR . 'templates';

                        // Add template.css file if it has have in template
                        if (JFile::exists(COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH . DIRECTORY_SEPARATOR . $template -> template
                            . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'template.css')
                        ) {
                            $view->document->addStyleSheet(TZ_Portfolio_PlusUri::base(true) . '/templates/'
                                . $template -> template . '/css/template.css');
                        }

                        // Parse document of view to require template.php(in tz portfolio template) file.
                        $view->document->parse($docOptions);
                    }

                    return true;
                }
            }
            return false;
        }
        return false;
    }

}