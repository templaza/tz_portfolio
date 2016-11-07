<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

use Joomla\Registry\Registry;

JLoader::import('com_tz_portfolio_plus.helpers.route', JPATH_SITE . '/components');
JLoader::import('com_tz_portfolio_plus.helpers.tags', JPATH_SITE . '/components');
JLoader::import('com_tz_portfolio_plus.helpers.categories', JPATH_SITE . '/components');
JLoader::import('com_tz_portfolio_plus.libraries.plugin.helper', JPATH_ADMINISTRATOR.'/components');

class modTZ_Portfolio_PlusArticlesHelper
{
    protected static $cache;

    public static function getList(&$params)
    {
        // Get the dbo
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('c.*, c.id as content_id, u.name as user_name, u.id as user_id');
        $query->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as slug');
        $query->select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
        $query->select('CASE WHEN CHAR_LENGTH(c.fulltext) THEN c.fulltext ELSE null END as readmore');

        $query->from('#__tz_portfolio_plus_content AS c');

        $query->join('INNER', $db->quoteName('#__tz_portfolio_plus_content_category_map') . ' AS m ON m.contentid=c.id');
        $query->join('LEFT', $db->quoteName('#__tz_portfolio_plus_categories') . ' AS cc ON cc.id=m.catid');
        $query->join('LEFT', $db->quoteName('#__tz_portfolio_plus_tag_content_map') . ' AS x ON x.contentid=c.id');
        $query->join('LEFT', $db->quoteName('#__tz_portfolio_plus_tags') . ' AS t ON t.id=x.tagsid');
        $query->join('LEFT', $db->quoteName('#__users') . ' AS u ON u.id=c.created_by');

        $query->where('c.state= 1');

        if($params -> get('category_filter', 2) == 2){
            $query -> where('(m.main = 0 OR m.main = 1)');
        }elseif($params -> get('category_filter',2) == 1){
            $query -> where('m.main = 1');
        }else{
            $query -> where('m.main = 0');
        }


        $nullDate = $db->Quote($db->getNullDate());
        $nowDate = $db->Quote(JFactory::getDate()->toSQL());

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

        $catids = $params->get('catid');
        if (is_array($catids)) {
            $catids = array_filter($catids);
            if (count($catids)) {
                $query->where('m.catid IN(' . implode(',', $catids) . ')');
            }
        } else {
            $query->where('m.catid IN(' . $catids . ')');
        }

        switch ($params->get('orderby_sec', 'rdate')) {
            default:
                $orderby = 'c.id DESC';
                break;
            case 'rdate':
                $orderby = 'c.created DESC';
                break;
            case 'date':
                $orderby = 'c.created ASC';
                break;
            case 'alpha':
                $orderby = 'c.title ASC';
                break;
            case 'ralpha':
                $orderby = 'c.title DESC';
                break;
            case 'author':
                $orderby = 'u.name ASC';
                break;
            case 'rauthor':
                $orderby = 'u.name DESC';
                break;
            case 'hits':
                $orderby = 'c.hits DESC';
                break;
            case 'rhits':
                $orderby = 'c.hits ASC';
                break;
            case 'order':
                $orderby = 'c.ordering ASC';
                break;
        }

        if ($params->get('random_article', 0)) {
            $query->order('RAND()');
        }

        $query->order($orderby);
        $query->group('c.id');
        $db->setQuery($query, 0, $params->get('article_limit', 5));
        $items = $db->loadObjectList();

        if ($items) {

            $dispatcher = JDispatcher::getInstance();
            JPluginHelper::importPlugin('content');
            TZ_Portfolio_PlusPluginHelper::importPlugin('content');
            TZ_Portfolio_PlusPluginHelper::importPlugin('mediatype');

            $dispatcher -> trigger('onAlwaysLoadDocument', array('modules.mod_tz_portfolio_plus_articles'));
            $dispatcher -> trigger('onLoadData', array('modules.mod_tz_portfolio_plus_articles', $items, $params));

            foreach ($items as $i => &$item) {
                $item->link = JRoute::_(TZ_Portfolio_PlusHelperRoute::getArticleRoute($item->slug, $item->catslug));
                $item->fullLink = JRoute::_(TZ_Portfolio_PlusHelperRoute::getArticleRoute($item->slug, $item->catslug), true, -1);
                $item->author_link = JRoute::_(TZ_Portfolio_PlusHelperRoute::getUserRoute($item->user_id, $params->get('usermenuitem', 'auto')));

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
                $item -> event  = new stdClass();

                //Call trigger in group content
                $results = $dispatcher->trigger('onContentPrepare', array ('modules.mod_tz_portfolio_plus_articles', &$item, &$params, 0));
                $item->introtext = $item->text;

                if($introtext_limit = $params -> get('introtext_limit')){
                    $item -> introtext  = '<p>'.JHtml::_('string.truncate', $item->introtext, $introtext_limit, true, false).'</p>';
                }

//                $results = $dispatcher->trigger('onContentAfterTitle', array('modules.mod_tz_portfolio_plus_articles', &$item, &$params, 0));
//                $item->event->afterDisplayTitle = trim(implode("\n", $results));
//
                $results = $dispatcher->trigger('onContentBeforeDisplay', array('modules.mod_tz_portfolio_plus_articles',
                    &$item, &$params, 0, $params->get('layout', 'default')));
                $item->event->beforeDisplayContent = trim(implode("\n", $results));

                $results = $dispatcher->trigger('onContentAfterDisplay', array('modules.mod_tz_portfolio_plus_articles',
                    &$item, &$params, 0, $params->get('layout', 'default')));
                $item->event->afterDisplayContent = trim(implode("\n", $results));

                // Process the tz portfolio's content plugins.
                $results    = $dispatcher -> trigger('onBeforeDisplayAdditionInfo',array('modules.mod_tz_portfolio_plus_articles',
                    &$item, &$params, 0, $params->get('layout', 'default')));
                $item -> event -> beforeDisplayAdditionInfo   = trim(implode("\n", $results));

                $results    = $dispatcher -> trigger('onAfterDisplayAdditionInfo',array('modules.mod_tz_portfolio_plus_articles',
                    &$item, &$params, 0, $params->get('layout', 'default')));
                $item -> event -> afterDisplayAdditionInfo   = trim(implode("\n", $results));

                $results    = $dispatcher -> trigger('onContentDisplayListView',array('modules.mod_tz_portfolio_plus_articles',
                    &$item, &$params, 0, $params->get('layout', 'default')));
                $item -> event -> contentDisplayListView   = trim(implode("\n", $results));

                //Call trigger in group tz_portfolio_plus_mediatype
                $results    = $dispatcher -> trigger('onContentDisplayMediaType',array('modules.mod_tz_portfolio_plus_articles',
                    &$item, &$params, 0, $params->get('layout', 'default')));
                if(isset($item) && $item){
                    $item -> event -> onContentDisplayMediaType    = trim(implode("\n", $results));
                    if($results    = $dispatcher -> trigger('onAddMediaType')){
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
            }
            return $items;
        }
        return false;
    }

    protected static function __getArticleByKey($article, $key = 'id')
    {
        $contentId	= JArrayHelper::getColumn($article, $key);
        $storeId = md5(__METHOD__ . '::' . $key.'::'.implode(',',$contentId));
        if (!isset(modTZ_Portfolio_PlusArticlesHelper::$cache[$storeId])) {
            modTZ_Portfolio_PlusArticlesHelper::$cache[$storeId] = JArrayHelper::getColumn($article, $key);
            return modTZ_Portfolio_PlusArticlesHelper::$cache[$storeId];
        }
        return modTZ_Portfolio_PlusArticlesHelper::$cache[$storeId];
    }

    public static function getCategoriesByArticle($params)
    {
        if ($articles = modTZ_Portfolio_PlusArticlesHelper::getList($params)) {
            $contentId = modTZ_Portfolio_PlusArticlesHelper::__getArticleByKey($articles, 'content_id');
            return TZ_Portfolio_PlusFrontHelperCategories::getCategoriesByArticleId($contentId, array('reverse_contentid' => true));
        }
        return false;
    }

    public static function getTagsByArticle($params)
    {
        if ($articles = modTZ_Portfolio_PlusArticlesHelper::getList($params)) {
            $contentId = modTZ_Portfolio_PlusArticlesHelper::__getArticleByKey($articles, 'content_id');
            return TZ_Portfolio_PlusFrontHelperTags::getTagsByArticleId($contentId, array(
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
            $tags = TZ_Portfolio_PlusFrontHelperTags::getTagsByCategoryId($catids);
            return $tags;
        }else {
            return false;
        }
    }

}
