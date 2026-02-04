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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\View\Styles;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Button\DropdownButton;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use TemPlaza\Component\TZ_Portfolio\Administrator\Extension\TZ_PortfolioComponent;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\ToolbarHelper as TZ_PortfolioToolbarHelper;

/**
 * Categories view class for the Category package.
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The model state
     *
     * @var   \Joomla\Registry\Registry
     */
    protected $state;
    protected $items;
    protected $templates;
    protected $form;
    protected $sidebar;
    protected $pagination;

    /**
     * Is this view an Empty State
     *
     * @var  boolean
     */
    private $isEmptyState = false;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
        if($this -> getLayout() == 'upload') {
            $this->form = $this->get('Form');
        }
        $this->state            = $this->get('State');
        $this->items            = $this->get('Items');
        $this -> templates      = $this -> get('Templates');
        $this->pagination       = $this->get('pagination');
        $this -> filterForm     = $this -> get('FilterForm');
        $this -> activeFilters  = $this -> get('ActiveFilters');

        Factory::getApplication() -> getLanguage() -> load('com_templates');
        Factory::getApplication() -> getLanguage() -> load('com_installer');

        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal' && $this->getLayout() !== 'upload') {
            $this -> addToolbar();
        }

        parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 */
	protected function addToolbar()
	{

        $user       = Factory::getUser();
        $toolbar    = Toolbar::getInstance();

        // Get the results for each action.
        $canDo = TZ_PortfolioHelper::getActions('style');


        ToolBarHelper::title(Text::_('COM_TZ_PORTFOLIO_TEMPLATES_MANAGER'),'eye');

        if($canDo -> get('core.create')) {
            $toolbar -> addNew('style.upload', 'COM_TZ_PORTFOLIO_INSTALL_UPDATE');
        }

        if ($canDo->get('core.delete')){
            $toolbar -> delete('style.uninstall','JTOOLBAR_UNINSTALL')
                -> message(Text::_('COM_TZ_PORTFOLIO_QUESTION_DELETE'))
                -> listCheck(true);
        }

        if ($canDo->get('core.edit.state')) {
            $toolbar -> publish($this -> getName().'.publish','JENABLED')
                -> listCheck(true);
            $toolbar -> unpublish($this -> getName().'.unpublish','JDISABLED')
                -> listCheck(true);
        }

        if($user->authorise('core.admin', 'com_tz_portfolio')
            || $user->authorise('core.options', 'com_tz_portfolio')){
            $toolbar -> preferences('com_tz_portfolio');
        }

        $toolbar -> help('JHELP_CONTENT_ARTICLE_MANAGER',false,
            'https://www.tzportfolio.com/document/administration/35-how-to-use-templates-in-tz-portfolio-plus.html?tmpl=component');

        TZ_PortfolioToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_VIDEO_TUTORIALS', 'youtube', 'youtube');

        ToolbarHelper::link('javascript:', Text::_('COM_TZ_PORTFOLIO_INTRO_GUIDE'), 'support');
	}
}
