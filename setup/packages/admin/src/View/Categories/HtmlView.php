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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\View\Categories;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\Toolbar;
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
class HtmlView extends BaseHtmlView
{
    /**
     * The model state
     *
     * @var   \Joomla\Registry\Registry
     */
    protected $state;
    /**
     * An array of items
     *
     * @var  array
     */
	protected $items;
    protected $f_levels;
	protected $pagination;
	protected $listsGroup;

    /**
     * Is this view an Empty State
     *
     * @var  boolean
     * @since 4.0.0
     */
    private $isEmptyState = false;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
        Factory::getApplication() -> getLanguage()->load('com_categories');

		$this -> state		    = $this->get('State');
		$this -> items		    = $this->get('Items');
		$this -> pagination	    = $this->get('Pagination');
        $this -> filterForm     = $this -> get('FilterForm');
        $this -> activeFilters  = $this -> get('ActiveFilters');
        $this -> isEmptyState   = $this->get('IsEmptyState');

		// Check for errors.
		if (($errors = $this->get('Errors')) && count($errors)) {
            throw new GenericDataException(implode("\n", $errors), 500);
		}

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item) {
			$this->ordering[$item->parent_id][] = $item->id;
		}

		// Levels filter.
		$options	= array();
		$options[]	= HTMLHelper::_('select.option', '1', Text::_('J1'));
		$options[]	= HTMLHelper::_('select.option', '2', Text::_('J2'));
		$options[]	= HTMLHelper::_('select.option', '3', Text::_('J3'));
		$options[]	= HTMLHelper::_('select.option', '4', Text::_('J4'));
		$options[]	= HTMLHelper::_('select.option', '5', Text::_('J5'));
		$options[]	= HTMLHelper::_('select.option', '6', Text::_('J6'));
		$options[]	= HTMLHelper::_('select.option', '7', Text::_('J7'));
		$options[]	= HTMLHelper::_('select.option', '8', Text::_('J8'));
		$options[]	= HTMLHelper::_('select.option', '9', Text::_('J9'));
		$options[]	= HTMLHelper::_('select.option', '10', Text::_('J10'));

		$this -> f_levels   = $options;
        $this -> listsGroup = $this -> get('Groups');

        if($this->getLayout() !== 'modal') {
            $this->addToolbar();
        }else{
            // In article associations modal we need to remove language filter if forcing a language.
            if ($forcedLanguage = Factory::getApplication()->input->get('forcedLanguage', '', 'CMD'))
            {
                // If the language is forced we can't allow to select the language, so transform the language selector filter into a hidden field.
                $languageXml = new \SimpleXMLElement('<field name="language" type="hidden" default="' . $forcedLanguage . '" />');
                $this->filterForm->setField($languageXml, 'filter', true);

                // Also, unset the active language filter so the search tools is not open by default with this filter.
                unset($this->activeFilters['language']);
            }
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
		$categoryId	= $this->state->get('filter.category_id');
		$component	= $this->state->get('filter.component');
		$section	= $this->state->get('filter.section');
		$canDo		= null;
		$user		= Factory::getApplication()->getIdentity();
        $toolbar    = Toolbar::getInstance();

		// Need to load the menu language file as mod_menu hasn't been loaded yet.
		$lang = Factory::getApplication() -> getLanguage();

		// Get the results for each action.
		$canDo = TZ_PortfolioHelper::getActions( 'category', $categoryId);

		// If a component categories title string is present, let's use it.
		if ($lang->hasKey($component_section_key = strtoupper($component.($section?"_$section":'')))) {
			$title = Text::sprintf( 'COM_TZ_PORTFOLIO_CATEGORIES_TITLE', $this->escape(Text::_($component_section_key)));
		}
		// Else use the base title
		else {
			$title = Text::_('COM_TZ_PORTFOLIO_CATEGORIES_BASE_TITLE');
		}

		// Load specific css component
		HTMLHelper::_('stylesheet', $component.'/administrator/categories.css', array(), true);

		// Prepare the toolbar.
		ToolbarHelper::title($title, 'folder categories '.substr($component, 4).($section?"-$section":'').'-categories');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories($component, 'core.create'))) > 0 ) {
            $toolbar -> addNew('category.add');
		}

		if ($canDo->get('core.edit' ) || $canDo->get('core.edit.own')) {
            $toolbar -> edit('category.edit') -> listCheck(true);
		}

		if (!$this->isEmptyState && ($canDo->get('core.edit.state') || $canDo -> get('core.edit.state.own')
                || $user->authorise('core.admin'))) {

            /** @var  DropdownButton $dropdown */
            $dropdown = $toolbar->dropdownButton('status-group', 'JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('icon-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();

            if ($canDo->get('core.edit.state')) {
                $childBar->publish('categories.publish')->listCheck(true);

                $childBar->unpublish('categories.unpublish')->listCheck(true);

                $childBar->archive('categories.archive')->listCheck(true);
            }

            if ($user->authorise('core.admin')) {
                $childBar->checkin('categories.checkin');
            }

            if ($canDo->get('core.edit.state') && $this->state->get('filter.published') != -2) {
                $childBar->trash('categories.trash')->listCheck(true);
            }

            // Add a batch button
            if (
                $canDo->get('core.create')
                && $canDo->get('core.edit')
                && $canDo->get('core.edit.state')
            ) {
                $childBar->popupButton('batch', 'JTOOLBAR_BATCH')
                    ->popupType('inline')
                    ->textHeader(Text::_('COM_TZ_PORTFOLIO_CATEGORIES_BATCH_OPTIONS'))
                    ->url('#joomla-dialog-batch')
                    ->modalWidth('800px')
                    ->modalHeight('fit-content')
                    ->listCheck(true);
            }
		}

		if ($canDo->get('core.admin')) {
            $toolbar -> standardButton('refresh', 'JTOOLBAR_REBUILD')
                -> task ('categories.rebuild');
		}

        if (!$this->isEmptyState && $this->state->get('filter.published') == -2 && $canDo->get('core.delete', $component)) {
            $toolbar -> delete('categories.delete', 'JTOOLBAR_EMPTY_TRASH')
                ->message('JGLOBAL_CONFIRM_DELETE')
                ->listCheck(true);
        }

		if ($canDo->get('core.admin') || $canDo->get('core.options')) {
			$toolbar -> preferences('com_tz_portfolio');
		}

		// Compute the ref_key if it does exist in the component
		if (!$lang->hasKey($ref_key = strtoupper($component.($section?"_$section":'')).'_CATEGORIES_HELP_KEY')) {
			$ref_key = 'JHELP_COMPONENTS_'.strtoupper(substr($component, 4).($section?"_$section":'')).'_CATEGORIES';
		}

		// Get help for the categories view for the component by
		// -remotely searching in a language defined dedicated URL: *component*_HELP_URL
		// -locally  searching in a component help file if helpURL param exists in the component and is set to ''
		// -remotely searching in a component URL if helpURL param exists in the component and is NOT set to ''
		if ($lang->hasKey($lang_help_url = strtoupper($component).'_HELP_URL')) {
			$debug = $lang->setDebug(false);
			$url = Text::_($lang_help_url);
			$lang->setDebug($debug);
		}
		else {
			$url = null;
		}

		ToolbarHelper::help($ref_key, false,
            'https://www.tzportfolio.com/document/administration/48-how-to-create-a-category-in-tz-portfolio-plus.html?tmpl=component'
            , 'com_tz_portfolio');

        TZ_PortfolioToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_VIDEO_TUTORIALS', 'youtube', 'youtube');
	}
}
