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

namespace TemPlaza\Component\TZ_Portfolio\Site\View\Article;

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
use TemPlaza\Component\TZ_Portfolio\Site\Helper\TagHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\RouteHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\CategoriesHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\ExtraFieldsFrontHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\UIkitHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioTemplate;

/**
 * HTML Article View class for the TZ Portfolio component.
 */
class HtmlView extends BaseHtmlView
{
    protected $item;
    protected $params;
    protected $print;
    protected $state;
    protected $user;
    protected $generateLayout;
    protected $listMedia;
    protected $authorParams;
    protected $itemsRelated;
    protected $listTags;

    function display($tpl = null)
    {
        // Initialise variables.
        $app		= Factory::getApplication();

        $tmpl   = $app -> input -> getString('tmpl');
//        if($tmpl){
////            HTMLHelper::_('bootstrap.framework');
////            HTMLHelper::_('jquery.framework');
//
////            $wa -> useScript('bootstrap');
//        }

//        /* @var WebAssetManager $wa */
//        $wa = $this -> document -> getWebAssetManager();
//        $wa -> registerAndUseStyle('com_tz_portfolio.addon.content.vote2', TZ_PortfolioUri::root()
//            . '/add-ons/content/vote/css/vote.css'/*, array('version' => 'auto')*/);

        $user		= Factory::getUser();
        $dispatcher	= Factory::getApplication()->getDispatcher();

        $this->state	= $this->get('State');
        $params	        = $this->state->get('params');
        $this->item		= $this->get('Item');
        $offset         = $this->state->get('list.offset');
        $related        = $this -> get('ItemRelated');

        // Merge article params. If this is single-article view, menu params override article params
        // Otherwise, article params override menu item params
        $this->params	= $this->state->get('params');

        $active	= $app->getMenu()->getActive();
        $temp	= clone ($this->params);
        $tempR	= clone ($this->params);

        PluginHelper::importPlugin('content');
        AddonHelper::importPlugin('mediatype');
        AddonHelper::importPlugin('content');
        AddonHelper::importPlugin('user');

        if($this -> item -> id && $params -> get('show_tags',1)) {
            $this -> item -> listTags = TagHelper::getTagsByArticleId($this -> item -> id, array(
                    'orderby' => 'm.contentid',
                    'menuActive' => $params -> get('menu_active', 'auto')
                )
            );
        }


        $mediatypes = array();
        if($results    = $app -> triggerEvent('onAddMediaType')){
            foreach($results as $result){
                if(isset($result -> special) && $result -> special) {
                    $mediatypes[] = $result -> value;
                }
            }
        }

        if($tmpl){
            $tmpl   = '&amp;tmpl='.$tmpl;
        }

        if($params -> get('tz_use_lightbox', 0) && !$tmpl){
            $tmpl   = '&amp;tmpl=component';
        }

        $this->print = $app->input->getBool('print');
        $this->user		= $user;

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        // Create a shortcut for $item.
        $item = &$this->item;

        $app -> triggerEvent('onTPContentBeforePrepare', array('com_tz_portfolio.article',
            &$item, &$this->params, $offset));

        // Get second categories
        $second_categories  = CategoriesHelper::getCategoriesByArticleId($item -> id,
            array('main' => false, 'reverse_contentid' => false));
        $item -> second_categories  = $second_categories;

        // Add router helpers.
        $item->slug			= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
        $item->catslug		= $item->category_alias ? ($item->catid.':'.$item->category_alias) : $item->catid;
        $item->parent_slug	= $item->category_alias ? ($item->parent_id.':'.$item->parent_alias) : $item->parent_id;

        // TODO: Change based on shownoauth
        $item->readmore_link = null;

        // Check to see which parameters should take priority
        if ($active) {
            $currentLink = $active->link;
            // If the current view is the active item and an article view for this article, then the menu item params take priority
            if (strpos($currentLink, 'view=article') && (strpos($currentLink, '&id='.(string) $item->id))) {
                // $item->params are the article params, $temp are the menu item params
                // Merge so that the menu item params take priority
                $item->params->merge($temp);
                // Load layout from active query (in case it is an alternative menu item)
                if (isset($active->query['layout'])) {
                    $this->setLayout($active->query['layout']);
                }
            }
            else {
                // Current view is not a single article, so the article params take priority here
                // Merge the menu item params with the article params so that the article params take priority
                $temp->merge($item->params);
                $item->params = $temp;

                // Check for alternative layouts (since we are not in a single-article menu item)
                // Single-article menu item layout takes priority over alt layout for an article
                if ($layout = $item->params->get('article_layout')) {
                    $this->setLayout($layout);
                }
            }
        }
        else {
            // Merge so that article params take priority
            $temp->merge($item->params);
            $item->params = $temp;
            // Check for alternative layouts (since we are not in a single-article menu item)
            // Single-article menu item layout takes priority over alt layout for an article
            if ($layout = $item->params->get('article_layout')) {
                $this->setLayout($layout);
            }
        }

        $item -> params -> set('show_cat_icons', $item -> params -> get('show_icons'));

//        // Disable email icon with joomla 4.x
//        if(COM_TZ_PORTFOLIO_JVERSION_4_COMPARE) {
//            $item->params->set('show_email_icon', 0);
//        }

        // Create "link" and "fullLink" for article object
        $tmpl   = null;
        if($item -> params -> get('tz_use_lightbox',0)){
            $tmpl   = '&amp;tmpl=component';
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

        $item ->link        = Route::_(RouteHelper::getArticleRoute($item -> slug,$item -> catid).$tmpl);
        $item -> fullLink   = Route::_(RouteHelper::getArticleRoute($item -> slug,$item -> catid), true, $ssl);

        $item->parent_link = Route::_(RouteHelper::getCategoryRoute($item->parent_slug));
        $item -> category_link  = Route::_(RouteHelper::getCategoryRoute($item->catslug));

        $item -> author_link    = Route::_(RouteHelper::getUserRoute($item -> created_by,
            $params -> get('user_menu_active','auto')));

        // Check the view access to the article (the model has already computed the values).
        if ($item->params->get('access-view') != true && (($item->params->get('show_noauth') != true &&  $user->get('guest') ))) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        //
        // Process the content plugins.
        //

        $app -> triggerEvent('onAlwaysLoadDocument', array('com_tz_portfolio.article'));
        $app -> triggerEvent('onLoadData', array('com_tz_portfolio.article', $this -> item, $params));

        if ($item->params->get('show_intro', 1)) {
            $item->text = $item->introtext.' '.$item->fulltext;
        }
        elseif ($item->fulltext) {
            $item->text = $item->fulltext;
        }
        else  {
            $item->text = $item->introtext;
        }

        if ($item->params->get('show_intro', 1)) {
            $text = $item->introtext.' '.$item->fulltext;
        }
        elseif ($item->fulltext) {
            $text = $item->fulltext;
        }
        else  {
            $text = $item->introtext;
        }

        if($item -> introtext && !empty($item -> introtext)) {
            $item->text = $item->introtext;
            $results = $app -> triggerEvent('onContentPrepare', array('com_tz_portfolio.article', &$item, &$item->params, $offset));
            $results = $app -> triggerEvent('onContentAfterTitle', array('com_tz_portfolio.article', &$item, &$item->params, $offset));
            $results = $app -> triggerEvent('onContentBeforeDisplay', array('com_tz_portfolio.article', &$item, &$item->params, $offset));
            $results = $app -> triggerEvent('onContentAfterDisplay', array('com_tz_portfolio.article', &$item, &$item->params, $offset));

            $item->introtext = $item->text;
        }
        if($item -> fulltext && !empty($item -> fulltext)) {
            $item->text = $item->fulltext;
            $results = $app -> triggerEvent('onContentPrepare', array('com_tz_portfolio.article', &$item, &$item -> params, $offset));
            $results = $app -> triggerEvent('onContentAfterTitle', array('com_tz_portfolio.article', &$item, &$item -> params, $offset));
            $results = $app -> triggerEvent('onContentBeforeDisplay', array('com_tz_portfolio.article', &$item, &$item -> params, $offset));
            $results = $app -> triggerEvent('onContentAfterDisplay', array('com_tz_portfolio.article', &$item, &$item -> params, $offset));

            $item->fulltext = $item->text;
        }

        $item -> text   = $text;

        $app -> triggerEvent('onTPContentPrepare', array ('com_tz_portfolio.article', &$item, &$item -> params, $offset));

        $results = $app -> triggerEvent('onContentPrepare', array ('com_tz_portfolio.article', &$item, &$item -> params, $offset));

        $item->event = new \stdClass();
        $results = $app -> triggerEvent('onContentDisplayAuthorAbout',
            array('com_tz_portfolio.article', $item -> author_id, &$item->params, &$item, $offset));
        $item->event->authorAbout = trim(implode("\n", $results));

        $results = $app -> triggerEvent('onContentAfterTitle',
            array('com_tz_portfolio.article', &$item, &$item -> params, $offset));
        $item->event->afterDisplayTitle = trim(implode("\n", $results));

        $results = $app -> triggerEvent('onContentBeforeDisplay',
            array('com_tz_portfolio.article', &$item, &$item -> params, $offset));
        $item->event->beforeDisplayContent = trim(implode("\n", $results));

        $results = $app -> triggerEvent('onContentAfterDisplay',
            array('com_tz_portfolio.article', &$item, &$item -> params, $offset));
        $item->event->afterDisplayContent = trim(implode("\n", $results));

        // Trigger portfolio's plugin
        $results = $app -> triggerEvent('onContentDisplayCommentCount',
            array('com_tz_portfolio.article',&$item,&$item -> params,$offset));
        $item -> event -> contentDisplayCommentCountCount  = trim(implode("\n",$results));

        $results = $app -> triggerEvent('onContentDisplayVote',
            array('com_tz_portfolio.article', &$item, &$item -> params, $offset));
        $item->event->contentDisplayVote = trim(implode("\n", $results));

        $results    = $app -> triggerEvent('onBeforeDisplayAdditionInfo',
            array('com_tz_portfolio.article',
                &$item, &$item -> params, $offset));
        $item -> event -> beforeDisplayAdditionInfo   = trim(implode("\n", $results));

        $results    = $app -> triggerEvent('onAfterDisplayAdditionInfo',
            array('com_tz_portfolio.article',
                &$item, &$item -> params, $offset));
        $item -> event -> afterDisplayAdditionInfo   = trim(implode("\n", $results));

        $results    = $app -> triggerEvent('onContentDisplayMediaType',
            array('com_tz_portfolio.article',
                &$item, &$item -> params, $offset));
        $item -> event -> onContentDisplayMediaType    = trim(implode("\n", $results));

        $item->event->contentDisplayArticleView = null;

        if($template   = TZ_PortfolioTemplate::getTemplate(true)){
            $tplparams  = $template -> params;
            if(!$tplparams -> get('use_single_layout_builder',1)){
                $results = $app -> triggerEvent('onContentDisplayArticleView',
                    array('com_tz_portfolio.article',
                        &$item, &$item->params, $offset));
                $item->event->contentDisplayArticleView = trim(implode("\n", $results));
            }
        }

        // Increment the hit counter of the article.
        if (!$this->params->get('intro_only') && $offset == 0) {
            $model = $this->getModel();
            $model->hit();
        }

        foreach($related as $i => &$itemR){

            $app -> triggerEvent('onTPContentBeforePrepare', array('com_tz_portfolio.article',
                &$itemR, &$item -> params, $offset, 'related'));

            $itemR -> link   = Route::_(RouteHelper::getArticleRoute($itemR -> slug, $itemR -> catid).$tmpl);

            $media      = $itemR -> media;
            $registry   = new Registry();
            $registry -> loadString($media);

            $media              = $registry -> toObject();
            $itemR -> media     = $media;

            $itemR -> event = new \stdClass();
            $results    = $app -> triggerEvent('onContentDisplayMediaType',array('com_tz_portfolio.article',
                &$itemR, &$item -> params, $offset, 'related'));

            if($itemR) {
                $itemR->event->onContentDisplayMediaType = trim(implode("\n", $results));

                $itemR->mediatypes = $mediatypes;
            }else{

                unset($related[$i]);
            }

            $app -> triggerEvent('onTPContentAfterPrepare', array('com_tz_portfolio.article',
                &$itemR, &$item -> params, $offset, 'related'));
        }

        $this -> itemsRelated   = $related;

        // Get article's extrafields
//        JLoader::import('extrafields', COM_TZ_PORTFOLIO_SITE_HELPERS_PATH);
        $extraFields    = ExtraFieldsFrontHelper::getExtraFields($this -> item, $params,
            false, array('filter.detail_view' => true));
        $this -> item -> extrafields    = $extraFields;

        $app -> triggerEvent('onTPContentAfterPrepare', array('com_tz_portfolio.article',
            &$item, &$item -> params, $offset));

        if(isset($item -> listTags)){
            $this -> listTags   = $item -> listTags;
        }

        //Escape strings for HTML output
        $this->pageclass_sfx = $this->item->params->get('pageclass_sfx') ? htmlspecialchars($this->item->params->get('pageclass_sfx', '')) : '';

        $this->_prepareDocument();

        $this -> generateLayout($item,$params,$dispatcher);

        parent::display($tpl);

    }

    /**
     * Prepares the document
     */
    protected function _prepareDocument()
    {
        $app	= Factory::getApplication();
        $menus	= $app->getMenu();
        $pathway = $app->getPathway();
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
            $this->params->def('page_heading', Text::_('JGLOBAL_ARTICLES'));
        }

        $title = $this->params->get('page_title', '');

        $id = null;
        if($menu && isset($menu -> query) && isset($menu -> query['id'])) {
            $id = (int)@$menu->query['id'];
        }

        // if the menu item does not concern this article
        if ($menu && ($menu->query['option'] != 'com_tz_portfolio' || $menu->query['view'] != 'article' || $id != $this->item->id))
        {
            // If this is not a single article menu item, set the page title to the article title
            if ($this->item->title) {
                $title = $this->item->title;
            }
            $path = array(array('title' => $this->item->title, 'link' => ''));

            $catIds     = $this -> params -> get('catid');
            $catAllow   = true;

            if($catIds && is_array($catIds)){
                $catIds = array_filter($catIds);
                $catIds = array_values($catIds);
                if(!count($catIds) || in_array($this -> item -> catid, $catIds)) {
                    $catAllow = false;
                }
            }

            if($catAllow){
                $category = Categories::getInstance('TZ_Portfolio')->get($this->item->catid);
                while ($category && ($menu->query['option'] != 'com_tz_portfolio' || $menu->query['view'] == 'article' || $id != $category->id) && $category->id > 1)
                {
                    $path[] = array('title' => $category->title, 'link' => RouteHelper::getCategoryRoute($category->id));
                    $category = $category->getParent();
                }
            }
            $path = array_reverse($path);
            foreach($path as $item)
            {
                $pathway->addItem($item['title'], $item['link']);
            }
        }

        // Check for empty title and add site name if param is set
        if (empty($title)) {
            $title = $app->getCfg('sitename');
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = Text::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = Text::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }
        if (empty($title)) {
            $title = $this->item->title;
        }
        $this->document->setTitle($title);

        $metadata   = $this -> item -> metadata;
        if($metadata -> get('page_title')){
            $this -> document -> setTitle($metadata -> get('page_title'));
        }

        $description    = null;
        if ($this->item->metadesc){
            $description    = $this -> item -> metadesc;
        }elseif(!empty($this -> item -> introtext)){
            $description    = strip_tags($this -> item -> introtext);
            $description    = explode(' ',$description);
            $description    = array_splice($description,0,25);
            $description    = trim(implode(' ',$description));
            if(!strpos($description,'...'))
                $description    .= '...';
        }elseif (!$this->item->metadesc && $this->params->get('menu-meta_description'))
        {
            $description    = $this -> params -> get('menu-meta_description');
        }

        if($description){
            $this -> document -> setDescription($description);
        }

        $tags   = null;

        if ($this->item->metakey)
        {
            $tags   = $this->item->metakey;
        }elseif($this -> listTags){
            foreach($this -> listTags as $tag){
                $tags[] = $tag -> alias;
            }
            $tags   = implode(',',$tags);
        }
        elseif (!$this->item->metakey && $this->params->get('menu-meta_keywords'))
        {
            $tags   = $this->params->get('menu-meta_keywords');
        }

        if ($this->params->get('robots'))
        {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }

        if ($app->getCfg('MetaAuthor') == '1')
        {
            $this->document->setMetaData('author', $this->item->author);
        }

        // Set metadata tags with prefix property "og:"
        $this -> document -> addCustomTag('<meta property="og:title" content="'.$title.'"/>');
        $this -> document -> addCustomTag('<meta property="og:url" content="'.$this -> item -> fullLink.'"/>');
        $this -> document -> addCustomTag('<meta property="og:type" content="article"/>');
        if($description){
            $this -> document -> addCustomTag('<meta property="og:description" content="'.$description.'"/>');
        }
        //// End set meta tags with prefix property "og:" ////

        // Set meta tags with prefix property "article:"
        $this -> document -> addCustomTag('<meta property="article:author" content="'.$this->item->author.'"/>');
        $this -> document -> addCustomTag('<meta property="article:published_time" content="'
            .$this->item->created.'"/>');
        $this -> document -> addCustomTag('<meta property="article:modified_time" content="'
            .$this->item->modified.'"/>');
        $this -> document -> addCustomTag('<meta property="article:section" content="'
            .$this->escape($this->item->category_title).'"/>');
        if($tags){
            $tags   = htmlspecialchars($tags);
            $this -> document-> setMetaData('keywords',$tags);
            $this -> document -> addCustomTag('<meta property="article:tag" content="'.$tags.'"/>');
        }
        ///// End set meta tags with prefix property "article:" ////

        $mdata = $this->item->metadata->toArray();
        foreach ($mdata as $k => $v)
        {
            if ($v)
            {
                $this->document->setMetadata($k, $v);
            }
        }

        // If there is a pagebreak heading or title, add it to the page title
        if (!empty($this->item->page_title))
        {
            $this->item->title = $this->item->title . ' - ' . $this->item->page_title;
            $this->document->setTitle($this->item->page_title . ' - ' . Text::sprintf('PLG_CONTENT_PAGEBREAK_PAGE_NUM', $this->state->get('list.offset') + 1));
        }

        if ($this->print)
        {
            $this->document->setMetaData('robots', 'noindex, nofollow');
        }
    }

    protected function FindUserItemId($_userid=null){
        $app		= Factory::getApplication();
        $menus		= $app->getMenu('site');
        $active     = $menus->getActive();
        if($_userid){
            $userid    = intval($_userid);
        }

        $component	= ComponentHelper::getComponent('com_tz_portfolio');
        $items		= $menus->getItems('component_id', $component->id);

        if($this -> params -> get('user_menu_active') && $this -> params -> get('user_menu_active') != 'auto'){
            return $this -> params -> get('user_menu_active');
        }

        foreach ($items as $item)
        {
            if (isset($item->query) && isset($item->query['view'])) {
                $view = $item->query['view'];

                if (isset($item -> query['created_by'])) {
                    if ($item->query['created_by'] == $userid) {
                        return $item -> id;
                    }
                }
                else{
                    if($item -> home == 1){
                        $homeId = $item -> id;
                    }
                }
            }
        }

        if(!isset($active -> id)) {
            if(isset($homeId)){
                return $homeId;
            } else {
                return 0;
            }
        }

        return $active -> id;
    }

    public function generateLayout(&$article,&$params,$dispatcher){
        if($template   = TZ_PortfolioTemplate::getTemplate(true)){
            $tplparams  = $template -> params;
            if($tplparams -> get('use_single_layout_builder',1)){

                $core_types         = AddonHelper::getCoreContentTypes();
                $this -> core_types = ArrayHelper::getColumn($core_types, 'value');
                $this->_generateLayout($article, $params, $dispatcher);
                return $this -> generateLayout;
            }
        }
        return false;
    }

    protected function _generateLayout(&$article,&$params, $dispatcher){
        if($template   = TZ_PortfolioTemplate::getTemplate(true)){
            $theme  = $template;
            $html   = null;
            
            /* @var WebAssetManager $wa */
            $wa = $this -> document -> getWebAssetManager();

            $offsetPrefixlg   = ($params -> get('bootstrapversion', 4) == 4)?' offset-lg-':' col-lg-offset-';
            $offsetPrefixmd   = ($params -> get('bootstrapversion', 4) == 4)?' offset-md-':' col-md-offset-';
            $offsetPrefixsm   = ($params -> get('bootstrapversion', 4) == 4)?' offset-sm-':' col-sm-offset-';
            $offsetPrefixxs   = ($params -> get('bootstrapversion', 4) == 4)?' offset-xs-':' col-xs-offset-';

            if($theme){
                if($tplParams  = $theme -> layout){
                    UIkitHelper::syncColumnLayoutToUIkit($tplParams);

                    foreach($tplParams as $tplItems){
                        $rows   = null;

                        $background = null;
                        $color      = null;
                        $margin     = null;
                        $padding    = null;
                        $childRows  = array();
                        $rowName    = null;
                        if(isset($tplItems -> name) && $tplItems -> name){
                            $rowName    = ApplicationHelper::stringURLSafe($tplItems -> name);
                        }

                        if($tplItems && isset($tplItems -> children)){
                            foreach($tplItems -> children as $children){
                                $html   = null;

                                if($children -> type && $children -> type !='none'){
                                    if(in_array($children -> type, $this -> core_types)) {
                                        $html = $this->loadTemplate($children->type);
                                    }else{
                                        $plugin = $children -> type;
                                        $layout = null;
                                        if(strpos($children -> type, ':') != false){
                                            list($plugin, $layout)  = explode(':', $children -> type);
                                        }

                                        if($plugin_obj = AddonHelper::getPlugin('content', $plugin)) {
                                            $className      = '\\TemPlaza\\Component\\TZ_Portfolio\\AddOn\\Content\\'.ucfirst($plugin).'\\Extension\\'.ucfirst($plugin);

                                            if(!class_exists($className)){
                                                AddonHelper::importPlugin('content', $plugin);
                                            }
                                            if(class_exists($className)) {
                                                $registry   = new Registry($plugin_obj -> params);

//                                                $addOnClass = AddonHelper::getInstance($plugin_obj -> type,
//                                                    $plugin_obj -> name, true, $dispatcher, array(
//                                                        'params'    => $registry
//                                                    ));
                                                $plgClass = AddonHelper::getInstance($plugin_obj -> type,
                                                    $plugin_obj -> name, true, $dispatcher, array(
                                                        'params'    => $registry
                                                    ));
//                                                $plgClass   = new $className($dispatcher,array('type' => ($plugin_obj -> type)
//                                                , 'name' => ($plugin_obj -> name), 'params' => $registry));

                                                if(method_exists($plgClass, 'onContentDisplayArticleView')) {
                                                    $html = $plgClass->onContentDisplayArticleView('com_tz_portfolio.'
                                                        .$this -> getName(), $this->item, $this->item->params
                                                        , $this->state->get('list.offset'), $layout);
                                                }
                                            }
                                            if(is_array($html)) {
                                                $html = implode("\n", $html);
                                            }
                                        }
                                    }
                                    $html   = $html ? trim($html) : '';
                                }

                                if(!empty($html) || (!empty($children -> children) and is_array($children -> children))){
                                    if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
                                        || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
                                        || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
                                        || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
                                        || !empty($children -> {"customclass"}) || $children -> responsiveclass){
                                        $childRows[] = '<div class="'
                                            .(!empty($children -> {"col-lg"})?'uk-width-'.$children -> {"col-lg"}:'')
                                            .(!empty($children -> {"col-md"})?' uk-width-'.$children -> {"col-md"}.'@l':'')
                                            .(!empty($children -> {"col-sm"})?' uk-width-'.$children -> {"col-sm"}.'@m':'')
                                            .(!empty($children -> {"col-xs"})?' uk-width-'.$children -> {"col-xs"}.'@s':'')
                                            .(!empty($children -> {"col-lg-offset"})?$offsetPrefixlg.$children -> {"col-lg-offset"}:'')
                                            .(!empty($children -> {"col-md-offset"})?$offsetPrefixmd.$children -> {"col-md-offset"}:'')
                                            .(!empty($children -> {"col-sm-offset"})?$offsetPrefixsm.$children -> {"col-sm-offset"}:'')
                                            .(!empty($children -> {"col-xs-offset"})?$offsetPrefixxs.$children -> {"col-xs-offset"}:'')
                                            .(!empty($children -> {"customclass"})?' '.$children -> {"customclass"}:'')
                                            .($children -> responsiveclass?' '.$children -> responsiveclass:'').'">';
                                    }

                                    $childRows[] = $html;

                                    if( !empty($children -> children) and is_array($children -> children) ){
                                        $this -> _childrenLayout($childRows,$children,$article,$params,$dispatcher);
                                    }

                                    if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
                                        || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
                                        || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
                                        || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
                                        || !empty($children -> {"customclass"}) || $children -> responsiveclass){
                                        $childRows[] = '</div>'; // Close col tag
                                    }
                                }
                            }
                        }


                        if(count($childRows)) {
                            if (isset($tplItems->backgroundcolor) && $tplItems->backgroundcolor
                                && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',
                                    trim($tplItems->backgroundcolor))
                            ) {
                                $background = 'background: ' . $tplItems->backgroundcolor . ';';
                            }
                            if (isset($tplItems->textcolor) && $tplItems->textcolor
                                && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',
                                    trim($tplItems->textcolor))
                            ) {
                                $color = 'color: ' . $tplItems->textcolor . ';';
                            }
                            if (isset($tplItems->margin) && !empty($tplItems->margin)) {
                                $margin = 'margin: ' . $tplItems->margin . ';';
                            }
                            if (isset($tplItems->padding) && !empty($tplItems->padding)) {
                                $padding = 'padding: ' . $tplItems->padding . ';';
                            }
                            if ($background || $color || $margin || $padding) {
                                $wa -> addInlineStyle('
                                    #tp-portfolio-template-' . ($rowName?$rowName:'') . '{
                                        ' . $background . $color . $margin . $padding . '
                                    }
                                ');
                            }
                            if (isset($tplItems->linkcolor) && $tplItems->linkcolor
                                && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i', trim($tplItems->linkcolor))
                            ) {
                                $wa -> addInlineStyle('
                                #tp-portfolio-template-' . ($rowName?$rowName:'') . ' a{
                                    color: ' . $tplItems->linkcolor . ';
                                }
                            ');
                            }
                            if (isset($tplItems->linkhovercolor) && $tplItems->linkhovercolor
                                && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i', trim($tplItems->linkhovercolor))
                            ) {
                                $wa -> addInlineStyle('
                                #tp-portfolio-template-' . ($rowName?$rowName:'') . ' a:hover{
                                    color: ' . $tplItems->linkhovercolor . ';
                                }
                            ');
                            }
                            $rows[] = '<div id="tp-portfolio-template-' . ($rowName?$rowName:'') . '"'
                                . ' class="' . ($tplItems->{"class"} ? ' ' . $tplItems->{"class"} : '')
                                . ((isset($tplItems->responsive) && $tplItems->responsive) ? ' ' . $tplItems->responsive : '') . '">';
                            if (isset($tplItems->containertype) && $tplItems->containertype) {
                                $rows[] = '<div class="' . UIkitHelper::mapBootstrapContainerWidth($tplItems->containertype) . '">';
                            }

                            $rows[] = '<div class="uk-grid-collapse" data-uk-grid>';

                            $rows = array_merge($rows, $childRows);

                            if (isset($tplItems->containertype) && $tplItems->containertype) {
                                $rows[] = '</div>';
                            }
                            $rows[] = '</div>';
                            $rows[] = '</div>';
                        }
                        if($rows) {
                            $this->generateLayout .= implode("\n", $rows);
                        }
                    }
                }
            }
        }
    }

    protected function _childrenLayout(&$rows,$children,&$article,&$params,$dispatcher){

        $offsetPrefixlg   = ($params -> get('bootstrapversion', 4) == 4)?' offset-lg-':' col-lg-offset-';
        $offsetPrefixmd   = ($params -> get('bootstrapversion', 4) == 4)?' offset-md-':' col-md-offset-';
        $offsetPrefixsm   = ($params -> get('bootstrapversion', 4) == 4)?' offset-sm-':' col-sm-offset-';
        $offsetPrefixxs   = ($params -> get('bootstrapversion', 4) == 4)?' offset-xs-':' col-xs-offset-';

        foreach($children -> children as $children){
            $background = null;
            $color      = null;
            $margin     = null;
            $padding    = null;
            $childRows  = array();
            $class      = null;
            $rowName    = null;
            $responsive = null;
            $wa         = Factory::getApplication()->getDocument() -> getWebAssetManager();

            if(isset($children -> name) && $children -> name){
                $rowName    = ApplicationHelper::stringURLSafe($children -> name);
            }
            if(isset($children->{"class"}) && $children->{"class"}){
                $class  = $children->{"class"};
            }
            if(isset($children->responsive) && $children->responsive){
                $class  = $children->responsive;
            }

            if (isset($children->backgroundcolor) && $children->backgroundcolor
                && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',
                    trim($children->backgroundcolor))) {
                $background = 'background: ' . $children->backgroundcolor . ';';
            }
            if (isset($children->textcolor) && $children->textcolor
                && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i', trim($children->textcolor))) {
                $color = 'color: ' . $children->textcolor . ';';
            }
            if (isset($children->margin) && !empty($children->margin)) {
                $margin = 'margin: ' . $children->margin . ';';
            }
            if (isset($children->padding) && !empty($children->padding)) {
                $padding = 'padding: ' . $children->padding . ';';
            }
            if ($background || $color || $margin || $padding) {
                $wa -> addInlineStyle('
                        #tp-portfolio-template-' . ($rowName?$rowName:'') . '-inner{
                            ' . $background . $color . $margin . $padding . '
                        }
                    ');
            }
            if (isset($children->linkcolor) && $children->linkcolor
                && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i', trim($children->linkcolor))) {
                $wa -> addInlineStyle('
                    #tp-portfolio-template-' . ($rowName?$rowName:'') . '-inner a{
                        color: ' . $children->linkcolor . ';
                    }
                ');
            }
            if (isset($children->linkhovercolor) && $children->linkhovercolor
                && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i', trim($children->linkhovercolor))) {
                $wa -> addInlineStyle('
                            #tp-portfolio-template-' . ($rowName?$rowName:'') . '-inner a:hover{
                                color: ' . $children->linkhovercolor . ';
                            }
                        ');
            }

            if(isset($children -> children) && $children -> children){
                foreach($children -> children as $children){
                    $html   = null;

                    if($children -> type && $children -> type !='none'){
                        if(in_array($children -> type, $this -> core_types)) {
                            $html = $this -> loadTemplate($children -> type);
                        }else{
                            $plugin = $children -> type;
                            $layout = null;
                            if(strpos($children -> type, ':') != false){
                                list($plugin, $layout)  = explode(':', $children -> type);
                            }

                            if($plugin_obj = AddonHelper::getPlugin('content', $plugin)) {
                                $className      = 'PlgTZ_Portfolio_PlusContent'.ucfirst($plugin);

                                if(!class_exists($className)){
                                    AddonHelper::importPlugin('content', $plugin);
                                }
                                if(class_exists($className)) {
                                    $registry   = new Registry($plugin_obj -> params);

                                    $plgClass   = new $className($dispatcher,array('type' => ($plugin_obj -> type)
                                    , 'name' => ($plugin_obj -> name), 'params' => $registry));

                                    if(method_exists($plgClass, 'onContentDisplayArticleView')) {
                                        $html = $plgClass->onContentDisplayArticleView('com_tz_portfolio.'.$this -> getName(),
                                            $this->item, $this->item->params, $this->state->get('list.offset'), $layout);
                                    }
                                }
                                if(is_array($html)) {
                                    $html = implode("\n", $html);
                                }
                            }
                        }
                        $html   = $html ? trim($html) : '';
                    }

                    if( !empty($html) || (!empty($children -> children) and is_array($children -> children))){
                        if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
                            || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
                            || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
                            || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
                            || !empty($children -> {"customclass"}) || $children -> responsiveclass){
                            $childRows[] = '<div class="'
                                .(!empty($children -> {"col-lg"})?'uk-width-'.$children -> {"col-lg"}:'')
                                .(!empty($children -> {"col-md"})?' uk-width-'.$children -> {"col-md"}.'@l':'')
                                .(!empty($children -> {"col-sm"})?' uk-width-'.$children -> {"col-sm"}.'@m':'')
                                .(!empty($children -> {"col-xs"})?' uk-width-'.$children -> {"col-xs"}.'@s':'')
                                .(!empty($children -> {"col-lg-offset"})?$offsetPrefixlg.$children -> {"col-lg-offset"}:'')
                                .(!empty($children -> {"col-md-offset"})?$offsetPrefixmd.$children -> {"col-md-offset"}:'')
                                .(!empty($children -> {"col-sm-offset"})?$offsetPrefixsm.$children -> {"col-sm-offset"}:'')
                                .(!empty($children -> {"col-xs-offset"})?$offsetPrefixxs.$children -> {"col-xs-offset"}:'')
                                .(!empty($children -> {"customclass"})?' '.$children -> {"customclass"}:'')
                                .($children -> responsiveclass?' '.$children -> responsiveclass:'').'">';
                        }
                        $childRows[] = $html;

                        if( !empty($children -> children) and is_array($children -> children) ){
                            $this -> _childrenLayout($childRows,$children,$article,$params,$dispatcher);
                        }

                        if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
                            || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
                            || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
                            || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
                            || !empty($children -> {"customclass"}) || $children -> responsiveclass){
                            $childRows[] = '</div>'; // Close col tag
                        }
                    }

                }
            }

            if(count($childRows)) {
                $rows[] = '<div id="tp-portfolio-template-' .($rowName?$rowName:'')
                    . '-inner" class="'. ($class?$class:'').
                    ($responsive ? ' ' . $responsive : '') . '">';
                $rows[] = '<div class="uk-grid-collapse" data-uk-grid>';
                $rows   = array_merge($rows, $childRows);

                $rows[] = '</div>';
                $rows[] = '</div>';
            }
        }
        return;
    }
}
