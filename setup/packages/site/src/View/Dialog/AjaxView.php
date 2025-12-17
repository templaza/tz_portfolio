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

namespace TemPlaza\Component\TZ_Portfolio\Site\View\Dialog;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * Ajax Dialog View class for the TZ Portfolio component.
 */
class AjaxView extends BaseHtmlView {
    protected $state;
    protected $formReject;

    public function display($tpl = null)
    {
        $this -> state      = $this -> get('State');
        $this -> formReject = $this -> get('FormReject');

        ob_start();
        parent::display($tpl);
        $html   = ob_get_contents();
        ob_end_clean();
        $json   = new JsonResponse();
        $json ->data    = $html;

        echo json_encode($json);

        Factory::getApplication()->close();
    }
}