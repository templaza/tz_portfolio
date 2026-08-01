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

namespace TemPlaza\Component\TZ_Portfolio\Site\View\Myarticles;

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
 * HTML My Article View class for the TZ Portfolio component.
 */
class HtmlView extends BaseHtmlView
{
    protected $char             = null;
    protected $item             = null;
    protected $items            = null;
    protected $media            = null;
    protected $state            = null;
    protected $params           = null;
    protected $Itemid           = null;
    protected $lang_sef         = '';
    protected $tagAbout         = null;
    protected $ajaxLink         = null;
    protected $itemTags         = null;
    protected $pagination       = null;
    protected $authorAbout      = null;
    protected $availLetter      = null;
    protected $itemCategories   = null;

    function __construct($config = array()){
        $this -> item           = new \stdClass();
        parent::__construct($config);
    }

    public function display($tpl=null){

        $this->items		    = $this->get('Items');
        $this->pagination	    = $this->get('Pagination');
        $this->state		    = $this->get('State');
        $this->authors		    = $this->get('Authors');
        $this -> filterForm     = $this -> get('FilterForm');
        $this -> activeFilters  = $this -> get('ActiveFilters');

        $params = $this -> state -> get('params');


//        $user   = JFactory::getApplication()->getIdentity();
//        $app    = JFactory::getApplication();
//
//        $filterPublished    = $this -> state -> get('filter.published');

//        if(!is_array($filterPublished) && $filterPublished == 3
//            && !$user -> authorise('core.approve', 'com_tz_portfolio_plus')){
//            $app->enqueueMessage(JText::_('COM_TZ_PORTFOLIO_PLUS_NO_PERMISSION_TO_MODERATE_ARTICLE'), 'error');
//            $app->setHeader('status', 500, true);
//            return false;
//        }

        $this -> params = $params;

        //Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx', ''));


        parent::display($tpl);
    }

    protected function _prepareDocument()
    {
        $app    = Factory::getApplication();
        $title  = $this->params->get('page_title', '');

        if (empty($title)) {
            $title = $app->getCfg('sitename');
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = Text::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = Text::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }

        $this->document->setTitle($title);

        if ($this->params->get('menu-meta_description'))
        {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords'))
        {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots'))
        {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }
    }
}
