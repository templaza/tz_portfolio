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

namespace TemPlaza\Component\TZ_Portfolio\Module\Portfolio\Site\Helper;

// no direct access
defined('_JEXEC') or die;

use Akeeba\WebPush\WebPush\VAPID;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Cache\Controller\OutputController;
use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Plugin\PluginHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\AddOn\AddOn;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\QueryHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\RouteHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\CategoriesHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\TagHelper;

class PortfolioHelper
{
    protected static $cache;

    protected $module;

    public function __construct($config = [])
    {
        $this -> module = $config['module'];
    }

    public function getList(Registry $params, SiteApplication $app)
    {
//        $storeId    = __METHOD__;
//        $storeId   .= ':'.serialize($params);
//        $storeId    = md5($storeId);
//
//        if(isset(self::$cache[$storeId])){
//            return self::$cache[$storeId];
//        }

        $cacheKey = md5(serialize([$params->toString(), $this->module->module, $this->module->id]));

        /** @var OutputController $cache */
        $cache = Factory::getContainer()->get(CacheControllerFactoryInterface::class)
            ->createCacheController('output', ['defaultgroup' => 'mod_tz_portfolio']);

        if ($cache->contains($cacheKey)) {
            // Return the cached output
            return $cache->get($cacheKey);
        }

        // Get the dbo
        $app    = Factory::getApplication();
        $db     = Factory::getDbo();
        $query  = $db -> getQuery(true);
        $module = $this -> module;

        $query->select('c.*, c.id as content_id, m.catid AS catid, u.name as user_name, u.id as user_id');
        $query->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as slug');
        $query->select('CASE WHEN CHAR_LENGTH(cc.alias) AND m.main=1 THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
        $query->select('CASE WHEN CHAR_LENGTH(c.fulltext) THEN c.fulltext ELSE null END as readmore');

        $query->from('#__tz_portfolio_plus_content AS c');

        $query->join('INNER', $db->quoteName('#__tz_portfolio_plus_content_category_map') . ' AS m ON m.contentid=c.id');
        $query->join('LEFT', $db->quoteName('#__tz_portfolio_plus_categories') . ' AS cc ON cc.id=m.catid AND m.main = 1');
        $query->join('LEFT', $db->quoteName('#__tz_portfolio_plus_tag_content_map') . ' AS x ON x.contentid=c.id');
        $query->join('LEFT', $db->quoteName('#__tz_portfolio_plus_tags') . ' AS t ON t.id=x.tagsid');
        $query->join('LEFT', $db->quoteName('#__users') . ' AS u ON u.id=c.created_by');

        $query->where('c.state= 1');

        $query -> where('(CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END) IS NOT NULL');

        if($params -> get('category_filter', 2) == 2){
            $query -> where('(m.main = 0 OR m.main = 1)');
        }elseif($params -> get('category_filter',2) == 1){
            $query -> where('m.main = 1');
        }else{
            $query -> where('m.main = 0');
        }

        $nullDate = $db->Quote($db->getNullDate());
        $nowDate = $db->Quote(Factory::getDate()->toSQL());

        $query->where('(c.publish_up = ' . $nullDate . ' OR c.publish_up <= ' . $nowDate . ')');
        $query->where('(c.publish_down = ' . $nullDate . ' OR c.publish_down >= ' . $nowDate . ')');

        if($types = $params -> get('media_types',array())){
            $types  = array_filter($types);
            if(count($types)) {
                $media_conditions   = array();
                foreach($types as $type){
                    $media_conditions[] = 'c.type='.$db -> quote($type);
                }
                if(count($media_conditions)){
                    $query -> where('('.implode(' OR ', $media_conditions).')');
                }
            }
        }

        if (!$params->get('show_featured', 1)) {
            $query -> where('c.featured = 0');
        } elseif ($params->get('show_featured', 1) == 2) {
            $query -> where('c.featured = 1');
        }

        $catids = $params->get('catid', array());

        if(!empty($catids)){
            if (is_array($catids)) {
                $catids = array_filter($catids);
                if (count($catids)) {
                    $query->where('m.catid IN(' . implode(',', $catids) . ')');
                }
            } else {
                $query->where('m.catid IN(' . $catids . ')');
            }
        }

        $primary    = QueryHelper::orderbyPrimary($params -> get('orderby_pri'));
        $secondary  = QueryHelper::orderbySecondary($params -> get('orderby_sec', 'rdate'),
            $params -> get('order_date', 'created'));

        if ($params->get('random_article', 0)) {
            $query->order('RAND()');
        }

        $orderby = $primary . ' ' . $secondary;

        $query->order($orderby);
        $query->group('c.id');

        $db->setQuery($query, 0, $params->get('article_limit', 5));
        $items = $db->loadObjectList();

        if ($items) {
            PluginHelper::importPlugin('content');
            AddonHelper::importPlugin('content');
            AddonHelper::importPlugin('mediatype');

            $app -> triggerEvent('onAlwaysLoadDocument', array('modules.mod_tz_portfolio'));
            $app -> triggerEvent('onLoadData', array('modules.mod_tz_portfolio', $items, $params));

            foreach ($items as $i => &$item) {
                $item -> params = clone($params);

                $app -> triggerEvent('onTPContentBeforePrepare', array('modules.mod_tz_portfolio',
                    &$item, &$item -> params));

                $config = Factory::getConfig();
                $ssl    = 2;
                if($config -> get('force_ssl')){
                    $ssl    = $config -> get('force_ssl');
                }
                $uri    = Uri::getInstance();
                if($uri -> isSsl()){
                    $ssl    = 1;
                }

                $item->link = Route::_(RouteHelper::getArticleRoute($item->slug, $item->catslug, $item->language));
                $item->fullLink = Route::_(RouteHelper::getArticleRoute($item->slug, $item->catslug, $item->language), true, $ssl);
                $item->author_link = Route::_(RouteHelper::getUserRoute($item->user_id, $params->get('usermenuitem', 'auto')));

                $media      = $item -> media;
                if(!empty($media)) {
                    $registry = new Registry($media);

                    $media = $registry->toObject();
                    $item->media = $media;
                }

                $item -> mediatypes = array();


                // Old plugins: Ensure that text property is available
                if (!isset($item->text))
                {
                    $item->text = $item->introtext;
                }
                $item -> event  = new \stdClass();

                //Call trigger in group content
                $results = $app -> triggerEvent('onContentPrepare', array ('modules.mod_tz_portfolio', &$item, &$item -> params, 0));
                $item->introtext = $item->text;

                if($introtext_limit = $item -> params -> get('introtext_limit')){
                    $item -> introtext  = '<p>'.HTMLHelper::_('string.truncate', $item->introtext, $introtext_limit, true, false).'</p>';
                }

                $results = $app -> triggerEvent('onContentBeforeDisplay', array('modules.mod_tz_portfolio',
                    &$item, &$item -> params, 0, $params->get('layout', 'default'), $module));
                if(is_array($results)){
                    $results    = array_unique($results);
                }
                $item->event->beforeDisplayContent = trim(implode("\n", $results));

                $results = $app -> triggerEvent('onContentAfterDisplay', array('modules.mod_tz_portfolio',
                    &$item, &$item -> params, 0, $params->get('layout', 'default'), $module));
                if(is_array($results)){
                    $results    = array_unique($results);
                }
                $item->event->afterDisplayContent = trim(implode("\n", $results));


                // Process the tz portfolio's content plugins.
                $results    = $app -> triggerEvent('onBeforeDisplayAdditionInfo',array('modules.mod_tz_portfolio',
                    &$item, &$item -> params, 0, $params->get('layout', 'default'), $module));
                if(is_array($results)){
                    $results    = array_unique($results);
                }
                $item -> event -> beforeDisplayAdditionInfo   = trim(implode("\n", $results));

                $results    = $app -> triggerEvent('onAfterDisplayAdditionInfo',array('modules.mod_tz_portfolio',
                    &$item, &$item -> params, 0, $params->get('layout', 'default'), $module));
                if(is_array($results)){
                    $results    = array_unique($results);
                }
                $item -> event -> afterDisplayAdditionInfo   = trim(implode("\n", $results));

                $results    = $app -> triggerEvent('onContentDisplayListView',array('modules.mod_tz_portfolio',
                    &$item, &$item -> params, 0, $params->get('layout', 'default'), $module));
                if(is_array($results)){
                    $results    = array_unique($results);
                }
                $item -> event -> contentDisplayListView   = trim(implode("\n", $results));

                //Call trigger in group tz_portfolio_mediatype
                $results    = $app -> triggerEvent('onContentDisplayMediaType',array('modules.mod_tz_portfolio',
                    &$item, &$item -> params, 0, $params->get('layout', 'default'), $module));
                if(is_array($results)){
                    $results    = array_unique($results);
                }
                if(isset($item) && $item){
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

                $app -> triggerEvent('onTPContentAfterPrepare', array('modules.mod_tz_portfolio',
                    &$item, &$item -> params, 0, $params->get('layout', 'default'), $module));
            }

            // Cache the output and return
            $cache->store($items, $cacheKey);

            return $items;
        }

        return false;
    }

    protected function __getArticleByKey($article, $key = 'id')
    {
        $contentId	= ArrayHelper::getColumn($article, $key);
//        $storeId = md5(__METHOD__ . '::' . $key.'::'.implode(',',$contentId));

        $cacheKey = md5(serialize([$contentId, $key, $this->module->module, $this->module->id]));

        /** @var OutputController $cache */
        $cache = Factory::getContainer()->get(CacheControllerFactoryInterface::class)
            ->createCacheController('output', ['defaultgroup' => 'mod_tz_portfolio', 'method' => __FUNCTION__]);

        if (!$cache->contains($cacheKey)) {
            // Return the cached output
            return $cache->get($cacheKey);
        }

        $data   = ArrayHelper::getColumn($article, $key);

        // Cache the output and return
        $cache -> store($data, $cacheKey);

        return $data;
    }

    public function getCategoriesByArticle($params, SiteApplication $app)
    {
        if ($articles = $this -> getList($params, $app)) {
            $contentId  = ArrayHelper::getColumn($articles, 'content_id');
//            $contentId = self::__getArticleByKey($articles, 'content_id');
            return CategoriesHelper::getCategoriesByArticleId($contentId, array('reverse_contentid' => true));
        }
        return false;
    }

    public function getCategoriesGroupByArticle($params, SiteApplication $app)
    {
        if ($articles = $this -> getList($params, $app)) {
            $contentId  = ArrayHelper::getColumn($articles, 'content_id');
//            $contentId = self::__getArticleByKey($articles, 'content_id');
            return CategoriesHelper::getCategoriesByArticleId($contentId, array('reverse_contentid' => true, 'groupby' => 'c.id'));
        }
        return false;
    }

    public function getTagsByArticle($params, SiteApplication $app)
    {
        if ($articles = $this -> getList($params, $app)) {
            $contentId  = ArrayHelper::getColumn($articles, 'content_id');
//            $contentId = self::__getArticleByKey($articles, 'content_id');
            return TagHelper::getTagsByArticleId($contentId, array(
                    'orderby' => 'm.contentid',
                    'menuActive' => $params->get('tagmenuitem', 'auto'),
                    'reverse_contentid' => true
                )
            );
        }
    }

    public static function getTagsByCategory($params)
    {
        $catids = $params->get('catid');
        if(isset($catids)) {
            $tags = TagHelper::getTagsByCategoryId($catids);
            return $tags;
        }else {
            return false;
        }
    }

    public function getTagsFilterByArticle($params, SiteApplication $app)
    {
        if ($articles = $this -> getList($params, $app)) {
            $contentId  = ArrayHelper::getColumn($articles, 'content_id');
//            $contentId = $this -> __getArticleByKey($articles, 'content_id');
            return TagHelper::getTagsFilterByArticleId($contentId);
        }
        return false;
    }

    public function getCategoriesFilterByArticle($params, SiteApplication $app)
    {
        if ($articles = $this -> getList($params, $app)) {
            $contentId  = ArrayHelper::getColumn($articles, 'content_id');
//            $contentId = $this -> __getArticleByKey($articles, 'content_id');
            return CategoriesHelper::getCategoriesByArticleId($contentId, array('reverse_contentid' => false,
                'groupby' => 'c.id'));
        }
        return false;
    }
}
