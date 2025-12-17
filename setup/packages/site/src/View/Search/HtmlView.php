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

namespace TemPlaza\Component\TZ_Portfolio\Site\View\Search;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Menu\SiteMenu;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Button\DropdownButton;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\WebAsset\WebAssetManager;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Extension\TZ_PortfolioComponent;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\ToolbarHelper as TZ_PortfolioToolbarHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\CategoriesHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\ExtraFieldsFrontHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\RouteHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\TagHelper;

/**
 * HTML Search View class for the TZ Portfolio component.
 */
class HtmlView extends BaseHtmlView
{
    protected $state            = null;
    protected $item             = null;
    protected $items            = null;
    protected $media            = null;
    protected $lang_sef         = '';
    protected $itemTags         = null;
    protected $itemCategories   = null;
    protected $params           = null;
    protected $pagination       = null;
    protected $Itemid           = null;
    protected $char             = null;
    protected $availLetter      = null;
    protected $form             = null;
    protected $results          = null;
    protected $error            = null;
    protected $catOptions       = null;
    protected $total            = 0;

    function __construct($config = array()){
        $this -> item           = new \stdClass();
        parent::__construct($config);
    }

    function display($tpl=null){

        $error  = null;
        $params = null;
        $app    = Factory::getApplication();
        $doc    = Factory::getDocument();

        $state          = $this -> get('State');
        $params         = $state -> get('params');
        $this -> state  = $state;
        $items          = $this -> get('Items');

        $this -> catOptions = $this -> get('CategoriesOptions');

        $searchword = $state->get('filter.searchword');

        // Limit searchword
        $lang        = Factory::getLanguage();
        $upper_limit = $lang->getUpperLimitSearchWord();
        $lower_limit = $lang->getLowerLimitSearchWord();

        if($items){

            $user	        = Factory::getUser();
            $userId	        = $user->get('id');
            $guest	        = $user->get('guest');

//            JLoader::import('category',COM_TZ_PORTFOLIO_PLUS_PATH_SITE.DIRECTORY_SEPARATOR.'helpers');

            $_params        = null;

            $threadLink     = null;
            $comments       = null;

            if(count($items)>0){
                $content_ids        = ArrayHelper::getColumn($items, 'id');
                $mainCategories     = CategoriesHelper::getCategoriesByArticleId($content_ids,
                    array('main' => true));
                $second_categories  = CategoriesHelper::getCategoriesByArticleId($content_ids,
                    array('main' => false));

                $tags   = null;
                if(count($content_ids) && $params -> get('show_tags',1)) {
                    $tags = TagHelper::getTagsByArticleId($content_ids, array(
                            'orderby' => 'm.contentid',
                            'menuActive' => $params -> get('menu_active', 'auto'),
                            'reverse_contentid' => true
                        )
                    );
                }

                PluginHelper::importPlugin('content');
                AddonHelper::importPlugin('content');
                AddonHelper::importPlugin('mediatype');

                $app -> triggerEvent('onAlwaysLoadDocument', array('com_tz_portfolio.portfolio'));
                $app -> triggerEvent('onLoadData', array('com_tz_portfolio.portfolio', $items, $params));

                foreach($items as $i => &$item){

                    $_params        = clone($params);

                    $item->params   = clone($_params);

                    $app -> triggerEvent('onTPContentBeforePrepare', array('com_tz_portfolio.portfolio',
                        &$item, &$item -> params));

                    $articleParams = new Registry();
                    $articleParams->loadString($item->attribs);

                    if($mainCategories && isset($mainCategories[$item -> id])){
                        $mainCategory   = $mainCategories[$item -> id];
                        if($mainCategory){
                            $item -> catid          = $mainCategory -> id;
                            $item -> category_title = $mainCategory -> title;
                            $item -> catslug        = $mainCategory -> id.':'.$mainCategory -> alias;
                            $item -> category_link  = $mainCategory -> link;

                            // Merge main category's params to article
                            $catParams  = new Registry($mainCategory ->  params);
                            if($inheritFrom = $catParams -> get('inheritFrom', 0)){
                                if($inheritCategory    = CategoriesHelper::getCategoriesById($inheritFrom)) {
                                    $inheritCatParams   = new Registry($inheritCategory->params);
                                    $catParams          = clone($inheritCatParams);
                                }
                            }
                            $item -> params -> merge($catParams);
                        }
                    }else {
                        // Create main category's link
                        $item -> category_link      = Route::_(RouteHelper::getCategoryRoute($item -> catid));

                        // Merge main category's params to article
                        if($mainCategory = CategoriesHelper::getCategoriesById($item -> catid)) {
                            $catParams = new Registry($mainCategory->params);
                            if ($inheritFrom = $catParams->get('inheritFrom', 0)) {
                                if ($inheritCategory = CategoriesHelper::getCategoriesById($inheritFrom)) {
                                    $inheritCatParams = new Registry($inheritCategory->params);
                                    $catParams = clone($inheritCatParams);
                                }
                            }
                            $item->params->merge($catParams);
                        }
                    }

                    // Merge with article params
                    $item -> params -> merge($articleParams);

                    // Get all second categories
                    $item -> second_categories  = null;
                    if(isset($second_categories[$item -> id])) {
                        $item->second_categories = $second_categories[$item -> id];
                    }

                    // Get article's tags
                    $item -> tags   = null;
                    if($tags && count($tags) && isset($tags[$item -> id])){
                        $item -> tags   = $tags[$item -> id];
                    }

                    /*** Start New Source ***/
                    $tmpl   = null;
                    if($item->params -> get('tz_use_lightbox',0)){
                        $tmpl   = '&tmpl=component';
                    }

                    $config = Factory::getConfig();
                    $ssl    = 2;
                    if($config -> get('force_ssl')){
                        $ssl    = $config -> get('force_ssl');
                    }
                    $uri    = Uri::getInstance();
                    if($uri -> isSsl()){
                        $ssl    = 1;
                    }

                    // Create Article Link
                    $item ->link        = Route::_(RouteHelper::getArticleRoute($item -> slug, $item -> catid).$tmpl);
                    $item -> fullLink   = Route::_(RouteHelper::getArticleRoute($item -> slug, $item -> catid), true, $ssl);

                    // Create author Link
                    $item -> author_link    = Route::_(RouteHelper::getUserRoute($item -> created_by,
                        $params -> get('user_menu_active','auto')));

                    // Compute the asset access permissions.
                    // Technically guest could edit an article, but lets not check that to improve performance a little.
                    if (!$guest) {
                        $asset	= 'com_tz_portfolio.article.'.$item->id;

                        // Check general edit permission first.
                        if ($user->authorise('core.edit', $asset)) {
                            $item->params->set('access-edit', true);
                        }
                        // Now check if edit.own is available.
                        elseif (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
                            // Check for a valid user and that they are the owner.
                            if ($userId == $item->created_by) {
                                $item->params->set('access-edit', true);
                            }
                        }
                    }

                    $media      = $item -> media;
                    if($item -> media && !empty($item -> media)) {
                        $registry   = new Registry($item -> media);
                        $obj        = $registry->toObject();
                        $item->media = clone($obj);
                    }

                    $item -> mediatypes = array();

                    // Add feed links
                    if (Factory::getApplication() -> input -> getCmd('format',null) != 'feed') {

                        // Old plugins: Ensure that text property is available
                        if (!isset($item->text))
                        {
                            $item->text = $item->introtext;
                        }

                        //
                        // Process the content plugins.
                        //

                        $app -> triggerEvent('onContentPrepare', array ('com_tz_portfolio.portfolio', &$item, &$item -> params, $state -> get('list.start')));
                        $item->introtext = $item->text;

                        $item->event = new \stdClass();
                        $results = $app -> triggerEvent('onContentAfterTitle', array('com_tz_portfolio.portfolio', &$item, &$item -> params, $state -> get('list.start')));
                        $item->event->afterDisplayTitle = trim(implode("\n", $results));

                        $results = $app -> triggerEvent('onContentBeforeDisplay', array('com_tz_portfolio.portfolio', &$item, &$item -> params, $state -> get('list.start')));
                        $item->event->beforeDisplayContent = trim(implode("\n", $results));

                        $results = $app -> triggerEvent('onContentAfterDisplay', array('com_tz_portfolio.portfolio', &$item, &$item -> params, $state -> get('list.start')));
                        $item->event->afterDisplayContent = trim(implode("\n", $results));

                        // Process the tz portfolio's content plugins.
                        $results    = $app -> triggerEvent('onContentDisplayVote',array('com_tz_portfolio.portfolio',
                            &$item, &$item -> params, $state -> get('list.start')));
                        $item -> event -> contentDisplayVote   = trim(implode("\n", $results));

                        $results    = $app -> triggerEvent('onBeforeDisplayAdditionInfo',array('com_tz_portfolio.portfolio',
                            &$item, &$item -> params, $state -> get('list.start')));
                        $item -> event -> beforeDisplayAdditionInfo   = trim(implode("\n", $results));

                        $results    = $app -> triggerEvent('onAfterDisplayAdditionInfo',array('com_tz_portfolio.portfolio',
                            &$item, &$item -> params, $state -> get('list.start')));
                        $item -> event -> afterDisplayAdditionInfo   = trim(implode("\n", $results));

                        $results = $app -> triggerEvent('onContentDisplayListView', array('com_tz_portfolio.portfolio',
                            &$item, &$item -> params, $state -> get('list.start')));
                        $item->event->contentDisplayListView = trim(implode("\n", $results));

                        // Process the tz portfolio's mediatype plugins.
                        $results    = $app -> triggerEvent('onContentDisplayMediaType',array('com_tz_portfolio.portfolio',
                            &$item, &$item -> params, $state -> get('list.start')));
                        if($item){
                            $item -> event -> onContentDisplayMediaType    = trim(implode("\n", $results));

                            if($results    = $app -> triggerEvent('onAddMediaType')){
                                $mediatypes = array();
                                foreach($results as $result){
                                    if(isset($result -> special) && $result -> special) {
                                        $mediatypes[] = $result -> value;
                                    }
                                }
                                $item -> mediatypes = $mediatypes;
                            }
                        }else{
                            unset($items[$i]);
                        }

                        $app -> triggerEvent('onContentAfterPrepare', array('com_tz_portfolio.portfolio',
                            &$item, &$item -> params, $state -> get('list.start')));
                    }

                    if($item && strlen(trim($item -> introtext)) && $introLimit = $params -> get('tz_article_intro_limit')){
                        $item -> introtext   = '<p>'.HTMLHelper::_('string.truncate', $item->introtext, $introLimit, true, false).'</p>';
                    }

                    // Get article's extrafields
                    $extraFields    = ExtraFieldsFrontHelper::getExtraFields($item, $item -> params,
                        false, array('filter.list_view' => true, 'filter.group' => $params -> get('order_fieldgroup', 'rdate')));
                    $item -> extrafields    = $extraFields;

                    $app -> triggerEvent('onTPContentAfterPrepare', array('com_tz_portfolio.portfolio',
                        &$item, &$item -> params, $state -> get('list.start')));

                }
            }
        }

        $total   = 0;
        if($_total  = $this -> get('Total')){
            $total  = $_total;
        }

        $this -> params         = $params;
        $this -> items          = $items;
        $this -> total          = $total;
        $this -> pagination     = $this -> get('Pagination');
        $this -> char           = $state -> get('filter.char');
        $this -> availLetter    = $this -> get('AvailableLetter');

        //Escape strings for HTML output
        $this->pageclass_sfx    = htmlspecialchars($params->get('pageclass_sfx', ''));

        $advFilterFields  = $this -> get('AdvFilterFields');
        $this -> advFilterFields  = $advFilterFields;

        $this -> _prepareDocument();

        parent::display($tpl);
    }

    protected function _prepareDocument()
    {
        $app    = Factory::getApplication();
        $title  = $this->params->get('page_title', '');

        if (empty($title)) {
            $title = $app->get('sitename');
        }
        elseif ($app->get('sitename_pagetitles', 0) == 1) {
            $title = Text::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        }
        elseif ($app->get('sitename_pagetitles', 0) == 2) {
            $title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
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
