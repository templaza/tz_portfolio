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
defined('_JEXEC') or die();

use Joomla\Registry\Registry;

jimport('joomla.application.component.modellist');
jimport('joomla.html.pagination');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class TZ_Portfolio_PlusModelPortfolio extends JModelList
{
    protected $pagNav                   = null;
    protected $rowsTag                  = null;
    protected $categories               = null;

    public function __construct($config = array()){
        parent::__construct($config);
    }

    function populateState($ordering = null, $direction = null){
        parent::populateState($ordering,$direction);

        $app    = JFactory::getApplication('site');
        $params = $app -> getParams('com_tz_portfolio_plus');

        $global_params    = JComponentHelper::getParams('com_tz_portfolio_plus');

        if($layout_type = $params -> get('layout_type',array())){

            if(!count($layout_type)){
                $params -> set('layout_type',$global_params -> get('layout_type',array()));
            }
        }else{
            $params -> set('layout_type',$global_params -> get('layout_type',array()));
        }

        $user		= JFactory::getUser();

        $offset = $app -> input -> getUInt('limitstart',0);

        if($params -> get('show_limit_box',0)  && $params -> get('tz_portfolio_plus_layout') == 'default'){
            $limit  = $app->getUserStateFromRequest('com_tz_portfolio_plus.portfolio.limit','limit',$params -> get('tz_article_limit',10));
        }
        else{
            $limit  = (int) $params -> get('tz_article_limit',10);
        }

        $db		= $this->getDbo();
        $query	= $db->getQuery(true);

        if ((!$user->authorise('core.edit.state', 'com_tz_portfolio_plus')) &&  (!$user->authorise('core.edit', 'com_tz_portfolio_plus'))){
            // limit to published for people who can't edit or edit.state.
            $this->setState('filter.published', 1);
            // Filter by start and end dates.
            $nullDate = $db->Quote($db->getNullDate());
            $nowDate = $db->Quote(JFactory::getDate()->toSQL());

            $query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
            $query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');
        }
        else {
            $this->setState('filter.published', array(0, 1, 2));
        }

        $this->setState('filter.language', JLanguageMultilang::isEnabled());

        $this -> setState('params',$params);
        $this -> setState('list.start', $offset);
        $this -> setState('Itemid',$params -> get('id'));
        $this -> setState('list.limit',$limit);
        $this -> setState('catid',$params -> get('catid'));
        $this -> setState('filter.char',$app -> input -> getString('char',null));
        $this -> setState('filter.tagId',null);
        $this -> setState('filter.userId',null);
        $this -> setState('filter.featured',null);
        $this -> setState('filter.year',null);
        $this -> setState('filter.month',null);
        $this -> setState('filter.category_id',$app -> input -> getInt('id'));
    }

    protected function getListQuery(){
        $params = $this -> getState('params');

        $user		= JFactory::getUser();

        $db     = JFactory::getDbo();
        $query  = $db -> getQuery(true);

        $query -> select('c.*,t.title AS tagName, m.catid AS catid ,cc.title AS category_title,u.name AS author');
        $query -> select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as slug');
        $query -> select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
        $query -> select('CASE WHEN CHAR_LENGTH(c.fulltext) THEN c.fulltext ELSE null END as readmore');

        $query -> from($db -> quoteName('#__tz_portfolio_plus_content').' AS c');

        $query -> join('INNER',$db -> quoteName('#__tz_portfolio_plus_content_category_map').' AS m ON m.contentid=c.id AND m.main = 1');
        $query -> join('LEFT',$db -> quoteName('#__tz_portfolio_plus_categories').' AS cc ON cc.id=m.catid');
        $query -> join('LEFT',$db -> quoteName('#__tz_portfolio_plus_tag_content_map').' AS x ON x.contentid=c.id');
        $query -> join('LEFT',$db -> quoteName('#__tz_portfolio_plus_tags').' AS t ON t.id=x.tagsid');
        $query -> join('LEFT',$db -> quoteName('#__users').' AS u ON u.id=c.created_by');

        // Condition for sql
//        $query -> where('c.state=1');

        // Join over the categories to get parent category titles
        $query->select('parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias');
        $query->join('LEFT', '#__tz_portfolio_plus_categories as parent ON parent.id = cc.parent_id');

        // Filter by published state
        $published = $this->getState('filter.published');

        if (is_numeric($published)) {
            // Use article state if badcats.id is null, otherwise, force 0 for unpublished
            $query->where('c.state = ' . (int) $published);
        }
        elseif (is_array($published)) {
            JArrayHelper::toInteger($published);
            $published = implode(',', $published);
            // Use article state if badcats.id is null, otherwise, force 0 for unpublished
            $query->where('c.state IN ('.$published.')');
        }

        if ((!$user->authorise('core.edit.state', 'com_tz_portfolio_plus')) &&  (!$user->authorise('core.edit', 'com_tz_portfolio_plus'))){
            // Filter by start and end dates.
            $nullDate = $db->Quote($db->getNullDate());
            $nowDate = $db->Quote(JFactory::getDate()->toSQL());

            $query->where('(c.publish_up = ' . $nullDate . ' OR c.publish_up <= ' . $nowDate . ')');
            $query->where('(c.publish_down = ' . $nullDate . ' OR c.publish_down >= ' . $nowDate . ')');
        }

        // Filter by access level.
        if (!$params->get('show_noauth')) {
            $groups	= implode(',', $user->getAuthorisedViewLevels());
            $query->where('c.access IN ('.$groups.')');
            $query->where('cc.access IN ('.$groups.')');
        }

        $catids = $params -> get('catid');

        if($this -> getState('filter.category_id')){
            $catids = $this -> getState('filter.category_id');
        }

        if(is_array($catids)){
            $catids = array_filter($catids);
            if(count($catids)){
                $query -> where('m.catid IN('.implode(',',$catids).')');
            }
        }
        elseif(!empty($catids)){
            $query -> where('m.catid IN('.$catids.')');
        }

        if($types = $params -> get('media_types',array())){
            $types  = array_filter($types);
            if(count($types)) {
                $media_conditions   = array();
                foreach($types as $type){
                    $media_conditions[] = 'type='.$db -> quote($type);
                }
                if(count($media_conditions)){
                    $query -> where('('.implode(' OR ', $media_conditions).')');
                }
            }
        }

        if($char   = $this -> getState('filter.char')){
            $query -> where('c.title LIKE '.$db -> quote(urldecode(mb_strtolower($char)).'%'));
            $query -> where('ASCII(SUBSTR(LOWER(c.title),1,1)) = ASCII('.$db -> quote(mb_strtolower($char)).')');
        }

        // Order by artilce
        switch ($params -> get('orderby_pri')){
            default:
                $cateOrder  = null;
                break;
            case 'alpha' :
                $cateOrder = 'cc.path, ';
                break;

            case 'ralpha' :
                $cateOrder = 'cc.path DESC, ';
                break;

            case 'order' :
                $cateOrder = 'cc.lft ASC, ';
                break;
        }

        switch ($params -> get('orderby_sec', 'rdate')){
            default:
                $orderby    = 'c.id DESC';
                break;
            case 'rdate':
                $orderby    = 'c.created DESC';
                break;
            case 'date':
                $orderby    = 'c.created ASC';
                break;
            case 'alpha':
                $orderby    = 'c.title ASC';
                break;
            case 'ralpha':
                $orderby    = 'c.title DESC';
                break;
            case 'author':
                $orderby    = 'u.name ASC';
                break;
            case 'rauthor':
                $orderby    = 'u.name DESC';
                break;
            case 'hits':
                $orderby    = 'c.hits DESC';
                break;
            case 'rhits':
                $orderby    = 'c.hits ASC';
                break;
            case 'order':
                $orderby    = 'c.ordering ASC';
                break;
        }

        $query -> order($cateOrder.$orderby);

        // Filter by language
        if ($this->getState('filter.language')) {
            $query->where('c.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
        }

        $query -> group('c.id');

        return $query;
    }

    public function getItems(){
        if($items = parent::getItems()){

            $user	        = JFactory::getUser();
            $userId	        = $user->get('id');
            $guest	        = $user->get('guest');

            $params         = $this -> getState('params');

            JLoader::import('category',COM_TZ_PORTFOLIO_PLUS_PATH_SITE.DIRECTORY_SEPARATOR.'helpers');

            $_params        = null;

            $threadLink     = null;
            $comments       = null;

            if(count($items)>0){
                $content_ids        = JArrayHelper::getColumn($items, 'id');
                $mainCategories     = TZ_Portfolio_PlusFrontHelperCategories::getCategoriesByArticleId($content_ids,
                    array('main' => true));
                $second_categories  = TZ_Portfolio_PlusFrontHelperCategories::getCategoriesByArticleId($content_ids,
                    array('main' => false));

                $tags   = null;
                if(count($content_ids) && $params -> get('show_tags',1)) {
                    $tags = TZ_Portfolio_PlusFrontHelperTags::getTagsByArticleId($content_ids, array(
                            'orderby' => 'm.contentid',
                            'menuActive' => $params -> get('menu_active', 'auto'),
                            'reverse_contentid' => true
                        )
                    );
                }

                $dispatcher	= JDispatcher::getInstance();

                JPluginHelper::importPlugin('content');
                TZ_Portfolio_PlusPluginHelper::importPlugin('mediatype');
                TZ_Portfolio_PlusPluginHelper::importPlugin('content');

                $dispatcher -> trigger('onAlwaysLoadDocument', array('com_tz_portfolio_plus.portfolio'));
                $dispatcher -> trigger('onLoadData', array('com_tz_portfolio_plus.portfolio', $items, $params));

                // Get the global params
                $globalParams = JComponentHelper::getParams('com_tz_portfolio_plus', true);

                JLoader::import('extrafields', COM_TZ_PORTFOLIO_PLUS_SITE_HELPERS_PATH);

                foreach($items as $i => &$item){

                    $_params        = clone($params);

                    $item->params   = clone($_params);

                    $articleParams = new JRegistry;
                    $articleParams->loadString($item->attribs);

                    if($mainCategories && isset($mainCategories[$item -> id])){
                        $mainCategory   = $mainCategories[$item -> id];
                        if($mainCategory){
                            $item -> catid          = $mainCategory -> id;
                            $item -> category_title = $mainCategory -> title;
                            $item -> catslug        = $mainCategory -> id.':'.$mainCategory -> alias;
                            $item -> category_link  = $mainCategory -> link;

                            // Merge main category's params to article
                            $catParams  = new JRegistry($mainCategory ->  params);
                            if($inheritFrom = $catParams -> get('inheritFrom', 0)){
                                if($inheritCategory    = TZ_Portfolio_PlusFrontHelperCategories::getCategoriesById($inheritFrom)) {
                                    $inheritCatParams   = new JRegistry($inheritCategory->params);
                                    $catParams          = clone($inheritCatParams);
                                }
                            }
                            $item -> params -> merge($catParams);
                        }
                    }else {
                        // Create main category's link
                        $item -> category_link      = JRoute::_(TZ_Portfolio_PlusHelperRoute::getCategoryRoute($item -> catid));

                        // Merge main category's params to article
                        if($mainCategory = TZ_Portfolio_PlusFrontHelperCategories::getCategoriesById($item -> catid)) {
                            $catParams = new JRegistry($mainCategory->params);
                            if ($inheritFrom = $catParams->get('inheritFrom', 0)) {
                                if ($inheritCategory = TZ_Portfolio_PlusFrontHelperCategories::getCategoriesById($inheritFrom)) {
                                    $inheritCatParams = new JRegistry($inheritCategory->params);
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

                    $config = JFactory::getConfig();
                    $ssl    = -1;
                    if($config -> get('force_ssl')){
                        $ssl    = 1;
                    }

                    // Create Article Link
                    $item ->link        = JRoute::_(TZ_Portfolio_PlusHelperRoute::getArticleRoute($item -> slug, $item -> catid).$tmpl);
                    $item -> fullLink   = JRoute::_(TZ_Portfolio_PlusHelperRoute::getArticleRoute($item -> slug, $item -> catid),true,$ssl);

                    // Create author Link
                    $item -> author_link    = JRoute::_(TZ_Portfolio_PlusHelperRoute::getUserRoute($item -> created_by,
                        $params -> get('user_menu_active','auto')));

                    // Compute the asset access permissions.
                    // Technically guest could edit an article, but lets not check that to improve performance a little.
                    if (!$guest) {
                        $asset	= 'com_tz_portfolio_plus.article.'.$item->id;

                        // Check general edit permission first.
                        if ($user->authorise('core.edit', $asset)) {
                            $item->params->set('access-edit', false);
                        }
                        // Now check if edit.own is available.
                        elseif (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
                            // Check for a valid user and that they are the owner.
                            if ($userId == $item->created_by) {
                                $item->params->set('access-edit', false);
                            }
                        }
                    }

                    $media      = $item -> media;
                    if($item -> media && !empty($item -> media)) {
                        $registry   = new JRegistry($item -> media);
                        $obj        = $registry->toObject();
                        $item->media = clone($obj);
                    }

                    $item -> mediatypes = array();

                    // Add feed links
                    if (JFactory::getApplication() -> input -> getCmd('format',null) != 'feed') {

                        // Old plugins: Ensure that text property is available
                        if (!isset($item->text))
                        {
                            $item->text = $item->introtext;
                        }

                        //
                        // Process the content plugins.
                        //

                        $dispatcher->trigger('onContentPrepare', array ('com_tz_portfolio_plus.portfolio', &$item, &$item -> params, $this -> getState('list.start')));
                        $item->introtext = $item->text;

                        $item->event = new stdClass();
                        $results = $dispatcher->trigger('onContentAfterTitle', array('com_tz_portfolio_plus.portfolio', &$item, &$item -> params, $this -> getState('list.start')));
                        $item->event->afterDisplayTitle = trim(implode("\n", $results));

                        $results = $dispatcher->trigger('onContentBeforeDisplay', array('com_tz_portfolio_plus.portfolio', &$item, &$item -> params, $this -> getState('list.start')));
                        $item->event->beforeDisplayContent = trim(implode("\n", $results));

                        $results = $dispatcher->trigger('onContentAfterDisplay', array('com_tz_portfolio_plus.portfolio', &$item, &$item -> params, $this -> getState('list.start')));
                        $item->event->afterDisplayContent = trim(implode("\n", $results));

                        // Process the tz portfolio's content plugins.
                        $results    = $dispatcher -> trigger('onContentDisplayVote',array('com_tz_portfolio_plus.portfolio',
                            &$item, &$item -> params, $this -> getState('list.start')));
                        $item -> event -> contentDisplayVote   = trim(implode("\n", $results));

                        $results    = $dispatcher -> trigger('onBeforeDisplayAdditionInfo',array('com_tz_portfolio_plus.portfolio',
                            &$item, &$item -> params, $this -> getState('list.start')));
                        $item -> event -> beforeDisplayAdditionInfo   = trim(implode("\n", $results));

                        $results    = $dispatcher -> trigger('onAfterDisplayAdditionInfo',array('com_tz_portfolio_plus.portfolio',
                            &$item, &$item -> params, $this -> getState('list.start')));
                        $item -> event -> afterDisplayAdditionInfo   = trim(implode("\n", $results));

                        $results = $dispatcher->trigger('onContentDisplayListView', array('com_tz_portfolio_plus.portfolio',
                            &$item, &$item -> params, $this->getState('list.start')));
                            $item->event->contentDisplayListView = trim(implode("\n", $results));

                        // Process the tz portfolio's mediatype plugins.
                        $results    = $dispatcher -> trigger('onContentDisplayMediaType',array('com_tz_portfolio_plus.portfolio',
                            &$item, &$item -> params, $this -> getState('list.start')));
                        if($item){
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

                    if($item && strlen(trim($item -> introtext)) && $introLimit = $params -> get('tz_article_intro_limit')){
                        $item -> introtext   = '<p>'.JHtml::_('string.truncate', $item->introtext, $introLimit, true, false).'</p>';
                    }

                    // Get article's extrafields
                    $extraFields    = TZ_Portfolio_PlusFrontHelperExtraFields::getExtraFields($item, $item -> params,
                        false, array('filter.list_view' => true, 'filter.group' => $params -> get('order_fieldgroup', 'rdate')));
                    $item -> extrafields    = $extraFields;

                }

                return $items;
            }
        }
        return false;
    }

    function ajaxtags($limitstart=null) {

		$input		= JFactory::getApplication() -> input;
        $params     = JComponentHelper::getParams('com_tz_portfolio_plus');

        $Itemid 	= $input -> getInt('Itemid');
        $page   	= $input -> getInt('page');
        $char       = $input -> getString('char');
        $curTags    = stripslashes($input -> getString('tags'));
        $curTags    = json_decode($curTags);

        $menu       = JMenu::getInstance('site');
        $menuParams = $menu -> getParams($Itemid);

        $params -> merge($menuParams);

        $limit      =   $params -> get('tz_article_limit', 10);
        $limitstart =   $limit * ($page-1);

        $offset = (int) $limitstart;

        $user   = JFactory::getUser();
        if ((!$user->authorise('core.edit.state', 'com_tz_portfolio_plus')) &&  (!$user->authorise('core.edit', 'com_tz_portfolio_plus'))){
            // limit to published for people who can't edit or edit.state.
            $this->setState('filter.published', 1);
        }
        else {
            $this->setState('filter.published', array(0, 1, 2));
        }

        $this -> setState('list.limit',$limit);
        $this -> setState('list.start',$offset);
        $this -> setState('params',$params);
        $this -> setState('filter.char',$char);

        $this -> getItems();

        $newTags    = null;
        $tags       = null;

        if($curTags && is_array($curTags) && count($curTags)){
            array_shift($curTags);
        }

        $newTags    = $this ->getTagsByArticle($curTags);

        return $newTags;
    }

    public function ajax(){

        $list   = null;
        $data   = null;

        $params = JComponentHelper::getParams('com_tz_portfolio_plus');

        // Set value again for option tz_portfolio_plus_redirect
        if($params -> get('tz_portfolio_plus_redirect') == 'default'){
            $params -> set('tz_portfolio_plus_redirect','article');
        }

		$input		= JFactory::getApplication() -> input;
        $Itemid     = $input -> getInt('Itemid');
        $page       = $input -> getInt('page');
        $layout     = $input -> getString('layout');
        $char       = $input -> getString('char');
        $catid      = $input -> getInt('id');

        $menu       = JMenu::getInstance('site');
        $menuParams = $menu -> getParams($Itemid);

        $params -> merge($menuParams);

        $limit  = (int) $params -> get('tz_article_limit', 10);

        $offset = $limit * ($page - 1);

        $user   = JFactory::getUser();
        if ((!$user->authorise('core.edit.state', 'com_tz_portfolio_plus')) &&  (!$user->authorise('core.edit', 'com_tz_portfolio_plus'))){
            // limit to published for people who can't edit or edit.state.
            $this->setState('filter.published', 1);
        }
        else {
            $this->setState('filter.published', array(0, 1, 2));
        }

        $app    = JFactory::getApplication();

        $this->setState('filter.language', $app->getLanguageFilter());

        $this -> setState('list.limit',$limit);
        $this -> setState('list.start',$offset);
        $this -> setState('params',$params);
        $this -> setState('filter.char',$char);
        $this -> setState('filter.category_id',$catid);

        if($offset >= $this -> getTotal()){
            return false;
        }

        return true;
    }

    function ajaxCategories(){
        $params     = JComponentHelper::getParams('com_tz_portfolio_plus');

		$input		= JFactory::getApplication() -> input;
        $Itemid 	= $input -> getInt('Itemid');
        $page   	= $input -> getInt('page');
        $curCatids  = $input -> getString('catIds');
        $curCatids  = json_decode($curCatids);

        $menu       = JMenu::getInstance('site');
        $menuParams = $menu -> getParams($Itemid);

        $params -> merge($menuParams);

        $limit      =   $params -> get('tz_article_limit', 10);
        $limitstart =   $limit * ($page-1);

        $offset = (int) $limitstart;

        $user   = JFactory::getUser();
        if ((!$user->authorise('core.edit.state', 'com_tz_portfolio_plus')) &&  (!$user->authorise('core.edit', 'com_tz_portfolio_plus'))){
            // limit to published for people who can't edit or edit.state.
            $this->setState('filter.published', 1);
        }
        else {
            $this->setState('filter.published', array(0, 1, 2));
        }

        $this -> setState('list.limit',$limit);
        $this -> setState('list.start',$offset);
        $this -> setState('params',$params);

        $newCatids    = null;
        $catIds       = null;

        $newCatids    = $this -> getCategoriesByArticle();

        // Filter new tags
        if(isset($newCatids) && $newCatids && count($newCatids)){
            $count  = count($curCatids);
            foreach($newCatids as $key => $newCatid){
                if(isset($curCatids) && count($curCatids) > 0){
                    if(!in_array($newCatid -> id,$curCatids)){
                        $newCatid -> order  = $count;
                        $catIds[] = $newCatid;
                        $count++;
                    }
                }
            }
        }

        return $catIds;
    }

    protected function __getArticleByKey($article, $key = 'id'){
        $storeId    = md5(__METHOD__.'::'.$key);
        if(!isset($this -> cache[$storeId])){
            $this -> cache[$storeId]    = JArrayHelper::getColumn($article, $key);
            return $this -> cache[$storeId];
        }
        return $this -> cache[$storeId];
    }

    public function getCategoriesByArticle(){
        if($articles   = $this -> getItems()){
            $contentId  = $this -> __getArticleByKey($articles, 'id');

            $params     = $this -> getState('params');
            $orderby    = null;
            // Order by artilce
            switch ($params -> get('orderby_pri')){
                case 'alpha' :
                    $orderby    = 'title';
                    break;

                case 'ralpha' :
                    $orderby    = 'title DESC';
                    break;

                case 'order' :
                    $orderby    = 'lft';
                    break;
            }

            $options    = array('orderby' => $orderby, 'reverse_contentid' => false, 'groupby' => 'c.id');
            if(!$params -> get('filter_second_category', 1)){
                return TZ_Portfolio_PlusFrontHelperCategories::getMainCategoriesByArticleId($contentId);
            }

            return TZ_Portfolio_PlusFrontHelperCategories::getCategoriesByArticleId($contentId, $options);
        }
        return false;
    }

    public function getAllCategories(){
        $params     = $this -> getState('params');
        $orderby    = null;

        // Order by artilce
        switch ($params -> get('orderby_pri')){
            case 'alpha' :
                $orderby    = 'c.title';
                break;

            case 'ralpha' :
                $orderby    = 'c.title DESC';
                break;

            case 'order' :
                $orderby    = 'c.lft';
                break;
        }

        if($catid = $params -> get('catid')){
            $catid  = array_unique($catid);
            $catid  = array_filter($catid);

            $options    = array('second_by_article' => true, 'orderby' => $orderby);
            if(!$params -> get('filter_second_category', 1)){
                $options['second_by_article']   = false;
            }

            if(count($catid) && $categories = TZ_Portfolio_PlusFrontHelperCategories::getCategoriesById($catid, $options)){
                return $categories;
            }else{
                return TZ_Portfolio_PlusFrontHelperCategories::getAllCategories($options);
            }

        }
        return false;
    }

    public function getTagsByArticle($filterAlias = null){
        if($articles   = $this -> getItems()){
            $contentId  = $this -> __getArticleByKey($articles, 'id');
            $tags   = TZ_Portfolio_PlusFrontHelperTags::getTagsFilterByArticleId($contentId, $filterAlias);
            return $tags;
        }
        return false;
    }

    public function getAllTags(){
        $params = $this -> getState('params');
        return TZ_Portfolio_PlusFrontHelperTags::getTagsByCategoryId($params -> get('catid'));
    }

    function getAvailableLetter(){
        $params = $this -> getState('params');
        if($params -> get('use_filter_first_letter',1)){
            if($letters = $params -> get('tz_letters','a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z')){
                $db = JFactory::getDbo();
                $letters = explode(',',$letters);
                $arr    = null;
                if($catids = $params -> get('catid')){
                    if(count($catids) > 1){
                        if(empty($catids[0])){
                            array_shift($catids);
                        }
                        $catids = implode(',',$catids);
                    }
                    else{
                        if(!empty($catids[0])){
                            $catids = $catids[0];
                        }
                        else
                            $catids = null;
                    }
                }

                $where  = null;
                if($catids){
                    $where  = ' AND cc.id IN('.$catids.')';
                }

                if($featured = $this -> getState('filter.featured')){
                    if(is_array($featured)){
                        $featured   = implode(',',$featured);
                    }
                    $where  .= ' AND c.featured IN('.$featured.')';
                }

                if($tagId = $this -> getState('filter.tagId')){
                    $where  .= ' AND t.id='.$tagId;
                }

                if($userId = $this -> getState('filter.userId')){
                    $where  .= ' AND c.created_by='.$userId;
                }

                if($year = $this -> getState('filter.year')){
                    $where  .= ' AND YEAR(c.created) = '.$year;
                }

                if($month = $this -> getState('filter.month')){
                    $where  .= ' AND MONTH(c.created) = '.$month;
                }

                foreach($letters as $i => &$letter){
                    $letter = trim($letter);
                    $query  = 'SELECT c.*'
                          .' FROM #__tz_portfolio_plus_content AS c'
                          .' LEFT JOIN #__tz_portfolio_plus_content_category_map AS m ON m.contentid = c.id'
                          .' LEFT JOIN #__tz_portfolio_plus_categories AS cc ON cc.id=m.catid'
                          .' LEFT JOIN #__tz_portfolio_plus_tag_content_map AS x ON x.contentid=c.id'
                          .' LEFT JOIN #__tz_portfolio_plus_tags AS t ON t.id=x.tagsid'
                          .' LEFT JOIN #__users AS u ON c.created_by=u.id'
                          .' WHERE c.state=1'
                              .$where
                              .' AND ASCII(SUBSTR(LOWER(c.title),1,1)) = ASCII("'.mb_strtolower($letter).'")'
                          .' GROUP BY c.id';
                    $db -> setQuery($query);
                    $count  = $db -> loadResult();
                    $arr[$i]    = false;
                    if($count){
                        $arr[$i]  = true;
                    }
                }

                return $arr;

            }
        }
        return false;
    }

    public function ajaxComments(){
		$input	= JFactory::getApplication() -> input;
        $data   = json_decode(base64_decode($input -> getString('url')));
        $id     = json_decode(base64_decode($input -> getString('id')));
        if($data){
            require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'
                .DIRECTORY_SEPARATOR.'phpclass'.DIRECTORY_SEPARATOR.'http_fetcher.php');
            require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'
                .DIRECTORY_SEPARATOR.'phpclass'.DIRECTORY_SEPARATOR.'readfile.php');

            $params     = JComponentHelper::getParams('com_tz_portfolio_plus');

            $Itemid     = $input -> getInt('Itemid');

            $menu       = JMenu::getInstance('site');
            $menuParams = $menu -> getParams($Itemid);

            $params -> merge($menuParams);

            $threadLink = null;

            $_id    = null;

            if(is_array($data) && count($data)){
                foreach($data as $i => &$contentUrl){
                    if(!preg_match('/http\:\/\//i',$contentUrl)){
                        $uri    = JUri::getInstance();
                        $contentUrl    = $uri -> getScheme().'://'.$uri -> getHost().$contentUrl;
                    }

                    if(preg_match('/(.*?)(\?tmpl\=component)|(\&tmpl\=component)/i',$contentUrl)){
                        $contentUrl = preg_replace('/(.*?)(\?tmpl\=component)|(\&tmpl\=component)/i','$1',$contentUrl);
                    }

                    $_id[$contentUrl]  = $id[$i];

                    if($params -> get('tz_comment_type','disqus') == 'facebook'){
                        $threadLink .= '&urls[]='.$contentUrl;
                    }elseif($params -> get('tz_comment_type','disqus') == 'disqus'){
                        $threadLink .= '&thread[]=link:'.$contentUrl;
                    }
                }
            }

            if(!is_array($data)){
                $threadLink = $data;
            }

            $fetch       = new Services_Yadis_Plainhttp_fetcher();
            $comments    = null;

            if($params -> get('tz_show_count_comment',1) == 1){
                // From Facebook
                if($params -> get('tz_comment_type','disqus') == 'facebook'){
                    if($threadLink){
                        $url        = 'http://api.facebook.com/restserver.php?method=links.getStats'
                                      .$threadLink;
                        $content    = $fetch -> get($url);

                        if($content){
                            if($bodies = $content -> body){
                                if(preg_match_all('/\<link_stat\>(.*?)\<\/link_stat\>/ims',$bodies,$matches)){
                                    if(isset($matches[1]) && !empty($matches[1])){
                                        foreach($matches[1]as $val){
                                            $match  = null;
                                            if(preg_match('/\<url\>(.*?)\<\/url\>.*?\<comment_count\>(.*?)\<\/comment_count\>/msi',$val,$match)){
                                                if(isset($match[1]) && isset($match[2])){
                                                    if(in_array($match[1],$data)){
                                                        $comments[$_id[$match[1]]]    = $match[2];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                // Disqus Comment count
                if($params -> get('tz_comment_type','disqus') == 'disqus'){

                    $url        = 'https://disqus.com/api/3.0/threads/list.json?api_secret='
                                  .$params -> get('disqusApiSecretKey','4sLbLjSq7ZCYtlMkfsG7SS5muVp7DsGgwedJL5gRsfUuXIt6AX5h6Ae6PnNREMiB')
                                  .'&forum='.$params -> get('disqusSubDomain','templazatoturials')
                                  .$threadLink.'&include=open';

                    if($_content = $fetch -> get($url)){

                        $body    = json_decode($_content -> body);
                        if(isset($body -> response)){
                            if($responses = $body -> response){
                                foreach($responses as $response){
                                    if(in_array($response ->link,$data)){
                                        $comments[$_id[$response ->link]]    = $response -> posts;
                                    }
                                }
                            }

                        }
                    }
                }

                if($comments){
                    if(is_array($comments)){
                        return json_encode($comments);
                    }
                    return 0;
                }
                return 0;
            }
        }
    }
}
?>