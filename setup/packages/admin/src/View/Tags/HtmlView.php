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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\View\Tags;

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

        $this -> items          = $this -> get('Items');
        $this -> state          = $this -> get('State');
        $this -> pagination     = $this -> get('Pagination');
        $this -> filterForm     = $this -> get('FilterForm');
        $this -> activeFilters  = $this -> get('ActiveFilters');
        $this -> isEmptyState   = $this -> get('IsEmptyState');
//        if (!\count($this->items) && $this -> isEmptyState = $this->get('IsEmptyState')) {
//            $this -> setLayout('emptystate');
//        }
        // Check for errors.
        if (\count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

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

        $user       = Factory::getApplication()->getIdentity();
        $toolbar    = Toolbar::getInstance();

        // Get the results for each action.
        $canDo = TZ_PortfolioHelper::getActions('group');

        ToolBarHelper::title(Text::_('COM_TZ_PORTFOLIO_TAGS_MANAGER'), 'tags');

        if($canDo -> get('core.create') || (count($user->getAuthorisedFieldGroups('core.create'))) > 0 ) {
            $toolbar -> addNew('tag.add');
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
                $childBar->publish('tags.publish')->listCheck(true);

                $childBar->unpublish('tags.unpublish')->listCheck(true);
            }
        }


        if (!$this->isEmptyState && ($canDo->get('core.delete')|| $canDo->get('core.delete.own'))) {
            $toolbar->delete('tags.delete', 'JTOOLBAR_DELETE')
                ->message('JGLOBAL_CONFIRM_DELETE')
                ->listCheck(true);
        }

        if ($user->authorise('core.admin', 'com_tz_portfolio')
            || $user->authorise('core.options', 'com_tz_portfolio')) {
            $toolbar -> preferences('com_tz_portfolio');
        }

        $toolbar -> help('JHELP_CONTENT_ARTICLE_MANAGER',false,
            'https://www.tzportfolio.com/document/administration/54-how-to-create-tags-in-tz-portfolio-plus.html?tmpl=component');

        TZ_PortfolioToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_VIDEO_TUTORIALS', 'youtube', 'youtube');
	}
}
