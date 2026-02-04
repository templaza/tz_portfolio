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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\View\Layouts;

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
        $model = $this->getModel();
        $this->items		    = $model->getItems();
        $this->state		    = $this->get('State');
        $this->pagination	    = $this->get('pagination');
        $this -> filterForm     = $this -> get('FilterForm');
        $this -> activeFilters  = $this -> get('ActiveFilters');

        Factory::getApplication() -> getLanguage() -> load('com_templates');

        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal') {
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

        ToolBarHelper::title(Text::_('COM_TZ_PORTFOLIO_TEMPLATE_STYLES_MANAGER'), 'palette');

        if ($canDo->get('core.edit.state'))
        {
            $toolbar -> makeDefault($this -> getName().'.setDefault', 'COM_TEMPLATES_TOOLBAR_SET_HOME');
        }

        if($canDo -> get('core.edit')) {
            $toolbar -> edit('style.edit') -> listCheck(true);
        }

        if ($canDo->get('core.create'))
        {
            $toolbar -> customButton('styles.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
        }

        if ($canDo->get('core.delete')){
            $toolbar ->delete(Text::_('COM_TZ_PORTFOLIO_QUESTION_DELETE'),'styles.delete')
                -> listCheck(true);
        }

        if($user->authorise('core.admin', 'com_tz_portfolio')
            || $user->authorise('core.options', 'com_tz_portfolio')){
            $toolbar -> preferences('com_tz_portfolio');
        }

        $toolbar -> help('JHELP_CONTENT_ARTICLE_MANAGER',false,
            'https://www.tzportfolio.com/document/administration/32-how-to-use-template-styles-in-tz-portfolio-plus.html?tmpl=component');

        TZ_PortfolioToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_VIDEO_TUTORIALS', 'youtube', 'youtube');
	}
}
