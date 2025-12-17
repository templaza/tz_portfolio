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

namespace TemPlaza\Component\TZ_Portfolio\Site\View\Form;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Categories\Categories;
use Joomla\CMS\WebAsset\WebAssetManager;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioUri;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioUser;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\TagHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\RouteHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\CategoriesHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\ExtraFieldsFrontHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\UIkitHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioTemplate;

/**
 * HTML Form View class for the TZ Portfolio component.
 */
class HtmlView extends BaseHtmlView
{

    protected $form;

    protected $item;

    protected $return_page;

    protected $state;
    protected $params;
    protected $listTags;
    protected $tagsSuggest;
    protected $plgTabs      = array();
    protected $extraFields  = array();

    public function display($tpl = null)
    {
        // Initialise variables.
        $app		= Factory::getApplication();
        $user		= TZ_PortfolioUser::getUser();

        // Get model data.
        $this->state        = $this->get('State');
        $this->item         = $this->get('Item');
        $this->form         = $this->get('Form');
        $this->return_page  = $this->get('ReturnPage');

        if (empty($this->item->id))
        {
            $authorised = $user->authorise('core.create', 'com_tz_portfolio_plus')
                || count($user->getAuthorisedCategories('com_tz_portfolio_plus', 'core.create'));
        }
        else
        {
            $authorised = $this->item->params->get('access-edit');
        }

        if ($authorised !== true)
        {
            $app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
            $app->setHeader('status', 403, true);

            return false;
        }

        $this -> extraFields	= $this -> get('ExtraFields');

        // Create a shortcut to the parameters.
        $params = &$this->state->params;

        // Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx', ''));

        $this->params = $params;

        // Load Tabs's title from plugin group tz_portfolio_plus_mediatype

        AddonHelper::importAllAddOns();

        $this -> advancedDesc       = $app -> triggerEvent('onAddFormToArticleDescription', array($this -> item));
        $this -> beforeDescription  = $app -> triggerEvent('onAddFormBeforeArticleDescription', array($this -> item));
        $this -> afterDescription   = $app -> triggerEvent('onAddFormAfterArticleDescription', array($this -> item));

//        TZ_Portfolio_PlusPluginHelper::importPlugin('mediatype');
        if($mediaType  = $app -> triggerEvent('onAddMediaType')){
            $mediaType  = array_filter($mediaType);

            $mediaForm	= $app -> triggerEvent('onMediaTypeDisplayArticleForm',array($this -> item));
            $mediaForm  = array_filter($mediaForm);

            if(count($mediaType)){
                $plugin	= array();
                foreach($mediaType as $i => $type){
                    $plugin[$i]			= new \stdClass();
                    $plugin[$i] -> type	= $type;
                    $plugin[$i] -> html	= '';
                    if($mediaForm && count($mediaForm) && isset($mediaForm[$i])) {
                        $plugin[$i]->html = $mediaForm[$i];
                    }
                    $this -> plgTabs[$i]	= $plugin[$i];
                }
            }
        }

        $this->_prepareDocument();
        parent::display($tpl);
    }

    /**
     * Prepares the document
     */
    protected function _prepareDocument()
    {
        $app   = Factory::getApplication();
        $menus = $app->getMenu();
        $title = null;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();

        if ($menu)
        {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        }
        else
        {
            $this->params->def('page_heading', Text::_('COM_CONTENT_FORM_EDIT_ARTICLE'));
        }

        $title = $this->params->def('page_title', Text::_('COM_CONTENT_FORM_EDIT_ARTICLE'));

        if ($app->get('sitename_pagetitles', 0) == 1)
        {
            $title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        }
        elseif ($app->get('sitename_pagetitles', 0) == 2)
        {
            $title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }

        $this->document->setTitle($title);

        $pathway = $app->getPathWay();
        $pathway->addItem($title, '');

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
