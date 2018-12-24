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

use Joomla\Utilities\ArrayHelper;

jimport('joomla.application.component.modellist');
jimport('joomla.html.pagination');
JLoader::import('category',COM_TZ_PORTFOLIO_PLUS_PATH_SITE.DIRECTORY_SEPARATOR.'helpers');
 
class TZ_Portfolio_PlusModelUsers extends JModelList
{
    protected $parameter_fields = array();
    protected $parameter_merge_fields = array();

    public function __construct($config = array()){
        parent::__construct($config);

        $config['parameter_fields'] = array(
            'tz_use_image_hover' => array('tz_image_timeout'),
            'show_image_gallery' => array('image_gallery_animSpeed',
                'image_gallery_animation_duration',
                'image_gallery_startAt', 'image_gallery_itemWidth',
                'image_gallery_itemMargin', 'image_gallery_minItems',
                'image_gallery_maxItems'),
            'show_video' => array('video_width','video_height'),
            'tz_show_gmap' => array('tz_gmap_width', 'tz_gmap_height',
                'tz_gmap_latitude', 'tz_gmap_longitude',
                'tz_gmap_address','tz_gmap_custom_tooltip'),
            'useCloudZoom' => array('zoomWidth','zoomHeight',
                'adjustX','adjustY','tint','tintOpacity',
                'lensOpacity','smoothMove'),
            'show_comment' => array('disqusSubDomain','disqusApiSecretKey'),
            'show_audio' => array('audio_soundcloud_color','audio_soundcloud_theme_color',
                'audio_soundcloud_width','audio_soundcloud_height')
        );
        // Add the parameter fields white list.
        if (isset($config['parameter_fields']))
        {
            $this->parameter_fields = $config['parameter_fields'];
        }

        // Add the parameter fields white list.
        $this -> parameter_merge_fields = array(
            'show_extra_fields', 'field_show_type',
            'tz_portfolio_plus_redirect'
        );
    }

    function populateState($ordering = null, $direction = null){
        parent::populateState($ordering,$direction);

        $user   = JFactory::getUser();

        $input = JFactory::getApplication() -> input;
        $this -> setState('users.id',$input -> getInt('id'));

        $offset = $input -> getUInt('limitstart',0);
        $this -> setState('offset', $offset);

        $this->setState('list.start', $input -> getInt('limitstart', 0));

        $app    = JFactory::getApplication('site');
        $params = $app -> getParams('com_tz_portfolio_plus');

        // Set value again for option tz_portfolio_plus_redirect
        if($params -> get('tz_portfolio_plus_redirect') == 'default'){
            $params -> set('tz_portfolio_plus_redirect','article');
        }

        if($params -> get('show_limit_box',0)){
            $limit  = $app->getUserStateFromRequest('com_tz_portfolio_plus.users.limit','limit',10);
        }else{
            $limit  = $params -> get('tz_article_limit', 10);
        }

        if ((!$user->authorise('core.edit.state', 'com_tz_portfolio_plus')) &&  (!$user->authorise('core.edit', 'com_tz_portfolio_plus'))){
            // limit to published for people who can't edit or edit.state.
            $this->setState('filter.published', 1);
        }
        else {
            $this->setState('filter.published', array(0, 1, 2));
        }

        $this->setState('filter.language', $app->getLanguageFilter());

        $params -> set('access-view',true);

        $this -> setState('params',$params);
        $this -> setState('list.limit',$limit);
        $this -> setState('users.catid',null);
        $this -> setState('filter.char',$input -> getString('char',null));

        $orderby    = '';
        $secondary  = TZ_Portfolio_PlusHelperQuery::orderbySecondary($params -> get('orderby_sec', 'rdate'));
        $primary    = TZ_Portfolio_PlusHelperQuery::orderbyPrimary($params -> get('orderby_pri'));

        $orderby .= $primary . ' ' . $secondary;

        $this -> setState('list.ordering', $orderby);
        $this -> setState('list.direction', null);

    }

    protected function getListQuery(){
        $params = $this -> getState('params');

        $user		= JFactory::getUser();

        $db     = JFactory::getDbo();
        $query  = $db -> getQuery(true);

        $query -> select('c.*,cc.title AS category_title,cc.parent_id');
        $query -> select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as slug');
        $query -> select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
        $query -> select('CASE WHEN CHAR_LENGTH(c.fulltext) THEN c.fulltext ELSE null END as readmore');

        $query -> from('#__tz_portfolio_plus_content AS c');

        $query -> join('LEFT', '#__tz_portfolio_plus_content_category_map AS m ON m.contentid = c.id');

        $query -> select('cc.id AS catid');
        $query -> join('LEFT','#__tz_portfolio_plus_categories AS cc ON cc.id = m.catid');

        // Join over the categories to get parent category titles
        $query->select('parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias');
        $query->join('LEFT', '#__tz_portfolio_plus_categories as parent ON parent.id = cc.parent_id');


        $query->select('u.name AS author');
        $query -> join('LEFT','#__users AS u ON u.id=c.created_by');

         // Filter by published state
        $published = $this->getState('filter.published');

        if (is_numeric($published)) {
            // Use article state if badcats.id is null, otherwise, force 0 for unpublished
            $query->where('c.state = ' . (int) $published);
        }elseif (is_array($published)) {
            $published  = ArrayHelper::toInteger($published);
            $published  = implode(',', $published);
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

        $query -> where('c.created_by='.$this -> getState('users.id'));

        if($char   = $this -> getState('filter.char')){
            $query -> where('ASCII(SUBSTR(LOWER(c.title),1,1)) = ASCII("'.mb_strtolower($char).'")');
        }

        $query->order($this->getState('list.ordering', 'c.created') . ' ' . $this->getState('list.direction', null));

        // Filter by language
        if ($this->getState('filter.language')) {
            $query->where('c.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
//            $query->where('(contact.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').') OR contact.language IS NULL)');
        }

        $query -> group('c.id');

        return $query;
    }

    public function getItems(){
        if($items   = parent::getItems()){
            $categories     = new TZ_Portfolio_PlusCategories();
            foreach($items as &$item){
                $params     = clone($this -> getState('params'));
                $temp       = clone($this -> getState('params'));

                // Get the global params
                $globalParams = JComponentHelper::getParams('com_tz_portfolio_plus', true);

                /*** New source ***/
                $category   = $categories->get($item -> catid);
                $catParams  = new JRegistry($category -> params);

                $articleParams = new JRegistry;
                $articleParams->loadString($item->attribs);

//                if($temp -> get('menuInheritFrom', 'none') == 'articles'){
//
//                    if($inheritCatid = $catParams -> get('inheritFrom')){
//                        if($inheritCategory = $categories -> get($inheritCatid)){
//                            $inheritCatParams   = new JRegistry($inheritCategory -> params);
//                            $item -> params     = clone($inheritCatParams);
//                        }
//                    }else{
//                        $item -> params = clone($catParams);
//                    }
//                    $item -> params -> merge($articleParams);
//                }else{
                    $item->params   = clone($params);
//                }

                // Create new options "link" and "fullLink" for article
                $tmpl   = null;
                if($item -> params -> get('tz_use_lightbox',0)){
                    $tmpl   = '&amp;tmpl=component';
                }

                $config = JFactory::getConfig();
                $ssl    = -1;
                if($config -> get('force_ssl')){
                    $ssl    = 1;
                }

                $item ->link        = JRoute::_(TZ_Portfolio_PlusHelperRoute::getArticleRoute($item -> slug, $item -> catid).$tmpl);
                $item -> fullLink   = JRoute::_(TZ_Portfolio_PlusHelperRoute::getArticleRoute($item -> slug, $item -> catid),true,$ssl);
                /** End Create new options **/

                $media      = $item -> media;
                $registry   = new JRegistry;
                $registry -> loadString($media);

                $media              = $registry -> toObject();
                $item -> media      = $media;

                $item -> mediatypes = array();
            }
            return $items;
        }
        return false;
    }

    function getUsers(){
        return $this -> getItems();
    }

    function getFindType($_cid=null)
	{
        $cid    = $this -> getState('users.catid');
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');
        $cid        =   intval($cid);
        if($_cid){
            $cid    = intval($_cid);
        }

        $component	= JComponentHelper::getComponent('com_tz_portfolio_plus');
		$items		= $menus->getItems('component_id', $component->id);

        foreach ($items as $item)
        {
            if (isset($item->query) && isset($item->query['view'])) {
                $view = $item->query['view'];

                if (isset($item->query['id'])) {
                    if ($item->query['id'] == $cid) {
                        return 0;
                    }
                } else {

                    $catids = $item->params->get('tz_catid');
                    if ($view == 'portfolio' && $catids) {
                        if (is_array($catids)) {
                            for ($i = 0; $i < count($catids); $i++) {
                                if ($catids[$i] == 0 || $catids[$i] == $cid) {
                                    return 1;
                                }
                            }
                        } else {
                            if ($catids == $cid) {
                                return 1;
                            }
                        }
                    }
                }
            }
        }

		return 0;
	}

    function getFindItemId($_cid=null)
	{
        $cid    = $this -> getState('users.catid');
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');
        $active     = $menus->getActive();
        $cid        =   intval($cid);
        if($_cid){
            $cid    = intval($_cid);
        }

        $component	= JComponentHelper::getComponent('com_tz_portfolio_plus');
		$items		= $menus->getItems('component_id', $component->id);


        foreach ($items as $item)
        {

            if (isset($item->query) && isset($item->query['view'])) {
                $view = $item->query['view'];


                if (isset($item->query['id'])) {
                    if ($item->query['id'] == $cid) {
                        return $item -> id;
                    }
                } else {

                    $catids = $item->params->get('tz_catid');
                    if ($view == 'portfolio' && $catids) {
                        if (is_array($catids)) {
                            for ($i = 0; $i < count($catids); $i++) {
                                if ($catids[$i] == 0 || $catids[$i] == $cid) {
                                    return $item -> id;
                                }
                            }
                        } else {
                            if ($catids == $cid) {
                                return $item -> id;
                            }
                        }
                    }
                    elseif($view == 'category' && $catids){
                        return $item -> id;
                    }
                }
            }
        }

		return $active -> id;
	}
}