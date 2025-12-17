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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\View\Extension;

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
 * Extension view.
 *
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

        $this->state                = $this->get('State');

        if($this -> getLayout() == 'upload_list_item') {
            $this -> itemsServer    = $this -> get('ItemsFromServer');
            $paginationServer       = $this -> get('PaginationFromServer');
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