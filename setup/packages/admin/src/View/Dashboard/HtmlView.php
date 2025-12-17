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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\View\Dashboard;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\ToolbarHelper as TZ_PortfolioToolbarHelper;

/**
 * Dashboard view.
 *
 * @package		Joomla.Administrator
 * @subpakage	TZ.Portfolio
 */
class HtmlView extends BaseHtmlView {
//    protected $xml;

    /* @since 2.2.7 */
    protected $license;

    protected $info;

    /**
     * Display the view.
     */
    public function display($tpl = null) {

        $this -> info   = $this -> get('Information');

        $this -> license    = $this -> get('License');

        // We don't need toolbar in the modal window.
        if ($this -> getLayout() !== 'modal') {
            $this -> addToolbar();
        }

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since	1.6
     */
    protected function addToolbar()
    {
        $user   = $this -> getCurrentUser();
        $canDo	= TZ_PortfolioHelper::getActions();

        ToolbarHelper::title(Text::_('COM_TZ_PORTFOLIO_DASHBOARD'), 'home-2');

        if ($user->authorise('core.admin', 'com_tz_portfolio')
            || $user->authorise('core.options', 'com_tz_portfolio')) {
            ToolbarHelper::preferences('com_tz_portfolio');
        }

        ToolbarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,
            'https://www.tzportfolio.com/document.html?tmpl=component');

        TZ_PortfolioToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_VIDEO_TUTORIALS', 'youtube', 'youtube');

        ToolbarHelper::link('javascript:', Text::_('COM_TZ_PORTFOLIO_INTRO_GUIDE'), 'support');

        Sidebar::setAction('index.php?option=com_tz_portfolio&view=dashboard');
//        JHtmlSidebar::setAction('index.php?option=com_tz_portfolio&view=dashboard');

    }

    /**
     * Display quick icon button.
     *
     * @param	string	$link
     * @param	string	$image
     * @param	string	$text
     */
    protected function _quickIcon($link, $image, $text) {
        $button	= array(
            'link'	=> Route::_($link),
//            'image'	=> 'administrator/components/com_tz_portfolio/assets/' . $image,
            'image'	=> $image,
            'text'	=> Text::_($text)
        );

        $this->button	= $button;
        echo $this->loadTemplate('button');
    }
}