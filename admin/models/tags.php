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

//no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class TZ_Portfolio_PlusModelTags extends JModelList
{
    function populateState($ordering = null, $direction = null){

        parent::populateState('id','desc');

        $app    = JFactory::getApplication();

        $state  = $this -> getUserStateFromRequest('com_tz_portfolio_plus.tags.filter_state','filter_state',null,'string');
        $this -> setState('filter_state',$state);

        $search  = $this -> getUserStateFromRequest('com_tz_portfolio_plus.tags.filter.search','filter_search',null,'string');
        $this -> setState('filter.search',$search);

        $order  = $this -> getUserStateFromRequest('com_tz_portfolio_plus.tags.filter_order','filter_order',null,'string');
        $this -> setState('filter_order',$order);

        $orderDir  = $this -> getUserStateFromRequest('com_tz_portfolio_plus.tags.filter_order_Dir','filter_order_Dir','asc','string');
        $this -> setState('filter_order_Dir',$orderDir);
    }

    protected function getListQuery(){
        $db = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('*');
        $query -> from('#__tz_portfolio_plus_tags');

        // Filter by search in name.
        $search = $this->getState('filter.search');

        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where('id = ' . (int) substr($search, 3));
            }
            else
            {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where(
                    '(' . $db->quoteName('title') . ' LIKE ' . $search . ')'
                );
            }
        }

        switch ($this -> getState('filter_state')){
            default:
                $query -> where('published>=0');
                break;
            case 'P':
                $query -> where('published=1');
                break;
            case 'U':
                $query -> where('published=0');
                break;
        }

        if($order = $this -> getState('filter_order','id')){
            $query -> order($order.' '.$this -> getState('filter_order_Dir','DESC'));
        }

        return $query;

    }

    public function getItems(){
        return parent::getItems();
    }


}