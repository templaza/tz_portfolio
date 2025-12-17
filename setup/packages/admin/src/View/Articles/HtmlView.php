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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\View\Articles;

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
use TemPlaza\Component\TZ_Portfolio\Administrator\Extension\TZ_PortfolioComponent;
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
        $this->items		    = $this->get('Items');
        $this->pagination	    = $this->get('Pagination');
        $this->state		    = $this->get('State');
        $this->authors		    = $this->get('Authors');
        $this -> filterForm     = $this -> get('FilterForm');
        $this -> activeFilters  = $this -> get('ActiveFilters');
        $this -> isEmptyState   = $this -> get('IsEmptyState');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
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

        $this-> f_levels    = $options;

        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal')
        {
            $this->addToolbar();
        }
        else
        {
            // In article associations modal we need to remove language filter if forcing a language.
            // We also need to change the category filter to show show categories with All or the forced language.
            if ($forcedLanguage = Factory::getApplication()->input->get('forcedLanguage', '', 'CMD'))
            {
                // If the language is forced we can't allow to select the language, so transform the language selector filter into a hidden field.
                $languageXml = new \SimpleXMLElement('<field name="language" type="hidden" default="' . $forcedLanguage . '" />');
                $this->filterForm->setField($languageXml, 'filter', true);

                // Also, unset the active language filter so the search tools is not open by default with this filter.
                unset($this->activeFilters['language']);

                // One last changes needed is to change the category filter to just show categories with All language or with the forced language.
                $this->filterForm->setFieldAttribute('category_id', 'language', '*,' . $forcedLanguage, 'filter');
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
//        $user   = TZ_PortfolioUser::getUser();
        $user       = $this -> getCurrentUser();
        $toolbar    = Toolbar::getInstance();

        // Get the results for each action.
        $canDo      = TZ_PortfolioHelper::getActions( 'category', $this -> state -> get('filter.category_id'));

        ToolbarHelper::title(Text::_('COM_TZ_PORTFOLIO_ARTICLES_TITLE'), 'stack article');

        if ($canDo->get('core.create')) {
            $toolbar->addNew('article.add');
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
                $childBar->publish('articles.publish')->listCheck(true);

                $childBar->unpublish('articles.unpublish')->listCheck(true);

                $childBar->standardButton('featured', 'JFEATURE', 'articles.featured')
                    ->listCheck(true);

                $childBar->standardButton('unfeatured', 'JUNFEATURE', 'articles.unfeatured')
                    ->listCheck(true);

                $childBar->checkin('articles.checkin')->listCheck(true);

                if ($this->state->get('filter.published') != TZ_PortfolioComponent::CONDITION_TRASHED) {
                    $childBar->trash('articles.trash')->listCheck(true);
                }
            }
        }


        if (!$this->isEmptyState && ($canDo->get('core.delete')|| $canDo->get('core.delete.own'))) {
            $toolbar->delete('groups.delete', 'JTOOLBAR_DELETE')
                ->message('JGLOBAL_CONFIRM_DELETE')
                ->listCheck(true);
        }

        if ($user->authorise('core.admin', 'com_tz_portfolio')
            || $user->authorise('core.options', 'com_tz_portfolio')) {
            $toolbar -> preferences('com_tz_portfolio');
        }

        $toolbar -> help('JHELP_CONTENT_ARTICLE_MANAGER');

        TZ_PortfolioToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_VIDEO_TUTORIALS', 'youtube', 'youtube');

    }
}