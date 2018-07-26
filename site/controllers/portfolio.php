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

// no direct access
defined('_JEXEC') or die;

//jimport('joomla.application.component.controller');

class TZ_Portfolio_PlusControllerPortfolio extends TZ_Portfolio_PlusControllerLegacy
{
    public function getModel($name = 'Portfolio', $prefix = 'TZ_Portfolio_PlusModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    function ajax(){

        $document   = JFactory::getDocument();
        $viewType   = $document->getType();
        $vName      = $this->input->get('view', $this->default_view);
        $viewLayout = $this->input->get('layout', 'default', 'string');
        $sublayout  = 'item';

        $input		= JFactory::getApplication() -> input;
        $Itemid     = $input -> getInt('Itemid');

        $params = JComponentHelper::getParams('com_tz_portfolio_plus');
        $menu       = JMenu::getInstance('site');
        $menuParams = $menu -> getParams($Itemid);

        $params -> merge($menuParams);

        if(strpos($viewLayout,':')) {
            list($layout, $sublayout) = explode(':',$viewLayout);
        }

        if($view = $this->getView($vName, $viewType, '', array('layout' => $layout))) {

            // Get/Create the model
            if ($model = $this->getModel($vName)) {
                if (!$model->ajax()) {
                    var_dump($model -> getError());
                    die();
                }

                // Push the model into the view (as default)
                $view->setModel($model, true);
            }

            $view->document = $document;

            JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

            $html   = new stdClass();
            // Display the view
            ob_start();
            $view->display($sublayout);
            $content    = ob_get_contents();
            ob_end_clean();

            $content    = str_replace('</script>','<\\/script>',$content);

            if($params -> get('tz_show_filter', 1)) {
                $filter = null;
                if($params -> get('tz_filter_type', 'categories') == 'tags'){
                    $filter = $view -> loadTemplate('filter_tags');
                }
                if($params -> get('tz_filter_type', 'categories') == 'categories'){
                    $filter = $view -> loadTemplate('filter_categories');
                }
                if($filter) {
                    $filter         = trim($filter);
                    $html -> filter = $filter;
                }
            }

            $html -> articles   = $content;
            echo json_encode($html);
        }
        die();
    }

//    public function ajaxComments(){
//        $model  = $this -> getModel();
//        echo $model -> ajaxComments();
//        die();
//    }
}