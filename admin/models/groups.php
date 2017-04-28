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

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class TZ_Portfolio_PlusModelGroups extends JModelList{
    public function __construct($config = array()){
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 'g.id',
                'name', 'g.name',
                'published', 'g.published',
                'ordering', 'g.ordering'
            );
        }
        parent::__construct($config);
    }

    public function populateState($ordering = null, $direction = null){

        parent::populateState('id','desc');

        $app        = JFactory::getApplication();
        $context    = 'com_tz_portfolio_plus.groups';

        $state  = $app -> getUserStateFromRequest($context.'filter_state','filter_state',null,'string');
        $this -> setState('filter_state',$state);
        $search  = $app -> getUserStateFromRequest($context.'.filter_search','filter_search',null,'string');
        $this -> setState('filter_search',$search);
    }

    protected function getListQuery(){
        $db         = $this -> getDbo();
        $query      = $db -> getQuery(true);

        $query -> select('g.*, COUNT(f.id) AS total');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_fieldgroups').' AS g');
        $query -> join('LEFT', '#__tz_portfolio_plus_field_fieldgroup_map AS m ON m.groupid = g.id');
        $query -> join('LEFT', '#__tz_portfolio_plus_fields AS f ON f.id = m.fieldsid');
        $query -> group('g.id');

        // Add the list ordering clause.
        $orderCol	= $this->state->get('list.ordering', 'g.id');
        $orderDirn	= $this->state->get('list.direction', 'desc');

        $query->order($db->escape($orderCol.' '.$orderDirn));

        return $query;
    }

    public function getItems(){
        if($items = parent::getItems()){
            foreach($items as &$item){
                $item -> categories = null;
                if($categories = TZ_Portfolio_PlusHelperCategories::getCategoriesByGroupId($item -> id)){
                    $item -> categories = $categories;
                }
            }
            return $items;
        }
        return false;
    }

    // Get fields group with type array[key=groupid] = groupname
    public function getItemsArray(){
        $db     = $this -> getDbo();
        $db -> setQuery($this -> getListQuery());

        if($items = $db -> loadObjectList()){
            foreach($items as $item){
                $list[$item -> id]  = $item -> name;
            }
            return $list;
        }
        return array();
    }

    // Get fields group name have had fields
    public function getItemsContainFields(){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('g.*,x.fieldsid');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_fieldgroups').' AS g');
        $query -> join('INNER',$db -> quoteName('#__tz_portfolio_plus_field_fieldgroup_map').' AS x ON x.groupid=g.id');
        $query -> order('x.fieldsid ASC');
        $db -> setQuery($query);

        if($items = $db -> loadObjectList()){
            $list   = array();
            foreach($items as $i => $item){
                if(isset($items[$i-1]) && ($items[$i - 1] -> fieldsid == $items[$i] -> fieldsid)){
                    $list[$item -> fieldsid]    .= ', '.$item -> name;
                }
                else{
                    $list[$item -> fieldsid]    = $item -> name;
                }
            }
            return $list;
        }
        return;

    }

    // Get fields group name have had fields
    public function getGroupsContainFields(){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('g.*,x.fieldsid');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_fieldgroups').' AS g');
        $query -> join('INNER',$db -> quoteName('#__tz_portfolio_plus_field_fieldgroup_map').' AS x ON x.groupid=g.id');
        $query -> order('x.fieldsid ASC');
        $db -> setQuery($query);

        if($items = $db -> loadObjectList()){
            $list   = array();
            foreach($items as $i => $item){
                if(!isset($list[$item -> fieldsid])) {
                    $list[$item->fieldsid] = array();
                }
                if(!isset($list[$item -> fieldsid][$item -> id])){
                    $list[$item -> fieldsid][$item -> id]   = $item;
                }
            }
            return $list;
        }
        return;

    }
}