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

class TZ_Portfolio_PlusModelFields extends JModelList{
    public function __construct($config = array()){
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 'f.id',
                'title', 'f.title',
                'groupname', 'f.groupname',
                'type', 'f.type',
                'published', 'f.published',
                'ordering', 'f.ordering'
            );
        }
        parent::__construct($config);
    }

    public function populateState($ordering = null, $direction = null){

        parent::populateState('id','desc');

        $app        = JFactory::getApplication();
        $context    = 'com_tz_portfolio_plus.fields';

        $group  = $app -> getUserStateFromRequest($context.'.filter.group','filter_group',0,'int');
        $this -> setState('filter.group',$group);

        $published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        $type  = $app -> getUserStateFromRequest($context.'filter.type','filter_type',null,'string');
        $this -> setState('filter.type',$type);

        $search  = $app -> getUserStateFromRequest($context.'.filter_search','filter_search',null,'string');
        $this -> setState('filter_search',$search);
    }

    protected function getListQuery(){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('f.*, fg.id AS groupid');
        $query -> from('#__tz_portfolio_plus_fields AS f');
        $query -> join('LEFT','#__tz_portfolio_plus_field_fieldgroup_map AS x ON f.id=x.fieldsid');
        $query -> join('INNER','#__tz_portfolio_plus_fieldgroups AS fg ON fg.id=x.groupid');
        $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e ON e.element = f.type')
            -> where('e.type = '.$db -> quote('tz_portfolio_plus-plugin'))
            -> where('e.folder = '.$db -> quote('extrafields'))
            -> where('e.published = 1');

        if($search = $this -> getState('filter_search'))
            $query -> where('title LIKE '.$db -> quote('%'.$search.'%'));

        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where('f.published = ' . (int) $published);
        }
        elseif ($published === '') {
            $query->where('(f.published = 0 OR f.published = 1)');
        }

        if($filter_group = $this -> getState('filter.group')){
            if($filter_group!=-1){
                $query -> where('x.groupid ='.$filter_group);
            }
        }

        if($filter_type = $this -> getState('filter.type')){
            $query -> where('f.type='.$db -> quote($filter_type));
        }

        $query -> group('f.id');

        // Add the list ordering clause.
        $orderCol	= $this->state->get('list.ordering', 'f.id');
        $orderDirn	= $this->state->get('list.direction', 'desc');

        if(isset($filter_group) && $filter_group){
            $orderCol   = 'x.ordering';
            $query -> select('x.ordering AS ordering');
        }

        $query->order($db->escape($orderCol.' '.$orderDirn));

        return $query;
    }

    public function getItems(){
        if($items = parent::getItems()){
            $groupModel = JModelLegacy::getInstance('Groups','TZ_Portfolio_PlusModel');
            if($groups = $groupModel -> getItemsContainFields()){
                foreach($items as $item){
                    if(isset($groups[$item -> id])){
                        $item -> groupname      = $groups[$item -> id];
                    }
                }
            }
            return $items;
        }
    }

}