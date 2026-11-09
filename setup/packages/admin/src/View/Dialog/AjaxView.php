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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\View\Dialog;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Button\DropdownButton;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\ToolbarHelper as TZ_PortfolioToolbarHelper;

/**
 * Categories view class for the Category package.
 */
class AjaxView extends BaseHtmlView
{
    protected $state;
    protected $formReject;

    public function display($tpl = null)
    {
        $model = $this->getModel();
        $this -> state      = $model->getState();
        $this -> formReject = $model->getFormReject();

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
