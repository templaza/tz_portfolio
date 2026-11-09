<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    Sonny

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://tzportfolio.com

# Technical Support:  Forum - https://github.com/templaza/tz_portfolio/issues/

# Family website: http://templaza.com

# Family Support: Forum - https://templaza.com/Forums.html

-------------------------------------------------------------------------*/

namespace TemPlaza\Component\TZ_Portfolio\Administrator\View\Addon;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\View\JsonView as BaseJsonView;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * Dashboard view.
 *
 * @package		Joomla.Administrator
 * @subpakage	TZ.Portfolio
 */
class AjaxView extends BaseHtmlView {

    protected $state;
    protected $itemsServer;

    /**
     * Display the view.
     */
    public function display($tpl = null) {

        $app    = Factory::getApplication();
        $json   = new JsonResponse();
        $data   = new \stdClass();
        $model  = $this->getModel();
        $this->state                = $model->getState();

        if($this -> getLayout() == 'upload_list_item') {
            $this -> itemsServer    = $model -> getItemsFromServer();
            $paginationServer       = $model -> getPaginationFromServer();
            $data -> html           = $this -> loadTemplate($tpl);

            $pagHtml    = $paginationServer -> getListFooter();
            $data -> pagination = $pagHtml;

            $json -> data   = $data;
        }

        $app -> setHeader('Content-Type', 'application/json; charset=' . $app->charSet, true);
        $app -> sendHeaders();

        echo json_encode($json);
        $app -> close();

    }
}