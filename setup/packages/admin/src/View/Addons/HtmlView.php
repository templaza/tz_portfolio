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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\View\Addons;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
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

    protected $items;

    /**
     * The pagination object
     *
     * @var  \Joomla\CMS\Pagination\Pagination
     */
    protected $pagination;

    /**
     * The model state
     *
     * @var   \Joomla\Registry\Registry
     */
    protected $state;

    /**
     * Form object for search filters
     *
     * @var  \Joomla\CMS\Form\Form
     */
    public $filterForm;

    /**
     * The active search filters
     *
     * @var  array
     */
    public $activeFilters;

    private $isEmptyState = false;

    /**
     * Display the view.
     */
    public function display($tpl = null) {
        $model = $this->getModel();
        $this -> state          = $model->getState();
        $this -> items          = $model->getItems();
        $this -> pagination     = $model->getPagination();
        $this -> filterForm     = $model->getFilterForm();
        $this -> activeFilters  = $model->getActiveFilters();
        $this -> isEmptyState   = $model->getIsEmptyState();

        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal' && $this->getLayout() !== 'upload') {
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

        // Get the results for each action.
        $canDo  = TZ_PortfolioHelper::getActions( 'addon');

        ToolbarHelper::title(Text::_('COM_TZ_PORTFOLIO_ADDONS_MANAGER'), 'puzzle');

        if ($canDo->get('core.create')) {
            ToolbarHelper::addNew('addon.upload', 'COM_TZ_PORTFOLIO_INSTALL_UPDATE');
        }

        if(!$this -> isEmptyState){
            if ($canDo->get('core.edit' )) {
                ToolbarHelper::editList('addon.edit');
            }

            if ($canDo->get('core.delete')){
                ToolbarHelper::deleteList(Text::_('COM_TZ_PORTFOLIO_QUESTION_DELETE'),'addon.uninstall',
                    'JTOOLBAR_UNINSTALL');
            }

            if ($canDo->get('core.edit.state')) {
                ToolbarHelper::publish($this -> getName().'.publish','JENABLED', true);
                ToolbarHelper::unpublish($this -> getName().'.unpublish','JDISABLED', true);
            }

            if($user->authorise('core.admin', 'com_tz_portfolio')
                || $user->authorise('core.options', 'com_tz_portfolio')){
                ToolbarHelper::preferences('com_tz_portfolio');
            }
        }

        ToolbarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,
            'https://tzportfolio.com/document.html?tmpl=component');

        TZ_PortfolioToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_VIDEO_TUTORIALS', 'youtube', 'youtube');

        ToolbarHelper::link('javascript:', Text::_('COM_TZ_PORTFOLIO_INTRO_GUIDE'), 'support');

    }
}