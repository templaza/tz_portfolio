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

namespace TemPlaza\Component\TZ_Portfolio\Site\Controller;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Menu\SiteMenu;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Response\JsonResponse;
use TemPlaza\Component\TZ_Portfolio\Site\Model\PortfolioModel;

// no direct access
defined('_JEXEC') or die;

class PortfolioController extends BaseController
{
//    public function getModel($name = 'Portfolio', $prefix = 'TZ_Portfolio_PlusModel', $config = array('ignore_request' => true))
//    {
//        $model = parent::getModel($name, $prefix, $config);
//
//        return $model;
//    }

    public function ajax(){

        $app        = Factory::getApplication();
        $document   = Factory::getDocument();
        $viewType   = $document->getType();
        $vName      = $this->input->get('view', $this->default_view);
        $viewLayout = $this->input->get('layout', 'default', 'string');
        $sublayout  = 'item';

        $data['Itemid']         = $this->input -> getInt('Itemid');
        $data['page']           = $this->input -> getInt('page');
        $data['layout']         = $this->input -> getString('layout');
        $data['char']           = $this->input -> getString('char');
        $data['id']             = $this->input -> getInt('id');
        $data['uid']            = $this->input -> getInt('uid');
        $data['tid']            = $this->input -> getInt('tid');
        $data['tagAlias']       = $this->input -> getString('tagAlias');
        $data['shownIds']       = $this->input -> get('shownIds', array(), 'array');
        $data['shownIds']       = array_unique($data['shownIds']);
        $data['shownIds']       = array_filter($data['shownIds']);
        $data['fields']         = $this->input -> get('fields', array(), 'array');
        $data['searchword']     = $this->input -> getString('searchword');
        $data['igCatId']        = $this->input -> get('igCatId');
        $data['igTagId']        = $this->input -> get('igTagId');

        $input		= $app -> input;
        $Itemid     = $input -> getInt('Itemid');

        $params = ComponentHelper::getParams('com_tz_portfolio');
//        $menu       = SiteMenu::getInstance('site');
        $menu       = $app -> getMenu();
        $menuParams = $menu -> getParams($Itemid);

        $params -> merge($menuParams);

        if(strpos($viewLayout,':')) {
            list($layout, $sublayout) = explode(':',$viewLayout);
        }

        if($view = $this->getView($vName, $viewType, '', array('layout' => $layout))) {

            /**
             * Get/Create the model
             * @var PortfolioModel $model
             */
            if ($model = $this->getModel($vName, '',  array('ignore_request' => true))) {
                if (!$model->ajax($data)) {
                    echo new JsonResponse(null, $model -> getError(), true);
                    $app -> close();
                }

                // Push the model into the view (as default)
                $view->setModel($model, true);
            }

            $view->document = $document;

            $html   = new \stdClass();
            // Display the view
            ob_start();
            $view->display($sublayout);
            $content    = ob_get_contents();
            ob_end_clean();

            $content    = str_replace('</script>','<\\/script>',$content);

            if($params -> get('tz_show_filter', 1)) {
                $filter = null;
                if($params -> get('tz_filter_type', 'categories') == 'tags'
                    && !(int) $params -> get('show_all_filter', 0)){
                    $filter = $view -> loadTemplate('filter_tags');
                }
                if(($params -> get('tz_filter_type', 'categories') == 'categories')
                    && !(int) $params -> get('show_all_filter', 0)){
                    $filter = $view -> loadTemplate('filter_categories');
                }
                if($filter) {
                    $filter         = trim($filter);
                    $html -> filter = $filter;
                }
            }

            $content    = preg_replace('/[\s]{2}/', ' ',$content);
            $content    = preg_replace('/[ ]+/', ' ',$content);
            $content    = trim($content);
            $html -> articles   = $content;

            $app -> setHeader('Content-Type', 'application/json; charset=' . $app->charSet, true);
            $app -> sendHeaders();

            echo new JsonResponse($html);
        }
        $app -> close();
    }
}