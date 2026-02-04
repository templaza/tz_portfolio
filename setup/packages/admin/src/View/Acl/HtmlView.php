<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    Sonny

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

namespace TemPlaza\Component\TZ_Portfolio\Administrator\View\Acl;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\ToolbarHelper as TZ_PortfolioToolbarHelper;

class HtmlView extends BaseHtmlView {

    /**
     * The model state
     *
     * @var   \Joomla\Registry\Registry
     */
    protected $state;

    protected $item     = null;
    protected $form     = null;

    private $isEmptyState = false;

    /**
     * Display the view.
     */
    public function display($tpl = null) {
        $this -> state = $this -> get('State');
        $this -> item  = $this -> get('Item');
        $this -> form  = $this -> get('Form');

        $this -> addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since	1.6
     */
    protected function addToolbar()
    {
        
        Factory::getApplication()->input->set('hidemainmenu', true);
        
        $toolbar    = Toolbar::getInstance();

        // Get the results for each action.
        $canDo      = TZ_PortfolioHelper::getActions('com_tz_portfolio_plus');

        $section    = $this -> state -> get('acl.section');

        switch ($section){
            default:
                $text   = Text::_('COM_TZ_PORTFOLIO_'.strtoupper($section).'S');
                break;
            case 'category':
                $text   = Text::_('COM_TZ_PORTFOLIO_CATEGORIES');
                break;
            case 'group':
                $text   = Text::_('COM_TZ_PORTFOLIO_FIELD_GROUPS');
                break;
            case 'style':
                $text   = Text::_('COM_TZ_PORTFOLIO_TEMPLATE_STYLES');
                break;

        }

        ToolbarHelper::title(Text::sprintf('COM_TZ_PORTFOLIO_ACL_MANAGER_TASK', Text::_($text)), 'lock');

        if ($canDo->get('core.edit' ) || $canDo -> get('core.edit.own')) {
            $toolbar->apply('acl.apply');
            $toolbar->save('acl.save');
            $toolbar->cancel('acl.cancel', 'JTOOLBAR_CLOSE');
        }

        $toolbar -> help('JHELP_CONTENT_ARTICLE_MANAGER',false,
            'https://www.tzportfolio.com/document.html?tmpl=component');

        TZ_PortfolioToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_VIDEO_TUTORIALS', 'youtube', 'youtube');

    }
}