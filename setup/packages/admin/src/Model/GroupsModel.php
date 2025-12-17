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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Model;

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\CategoriesHelper as TZ_PortfolioHelperCategories;

/**
 * Groups Model
 */
class GroupsModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param	array $config	An array of configuration options (name, state, dbo, table_path, ignore_request).
     * @param   MVCFactoryInterface  $factory  The factory.
     *
     */
    public function __construct($config = array()){
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 'g.id',
                'name', 'g.name',
                'published', 'g.published',
                'ordering', 'g.ordering',
                'access', 'g.access',
            );
        }
        parent::__construct($config);

        // Set the model dbo
        if (array_key_exists('dbo', $config))
        {
            $this->_db = $config['dbo'];
        }
        else
        {
            $this->_db = Factory::getDbo();
        }
    }

    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.access');
        $id .= ':' . $this->getState('filter.published');

        return parent::getStoreId($id);
    }

    /**
     * Method to auto-populate the model state.
     *
     * This method should only be called once per instantiation and is designed
     * to be called on the first call to the getState() method unless the model
     * configuration flag to ignore the request is set.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string  $ordering   An optional ordering field.
     * @param   string  $direction  An optional direction (asc|desc).
     *
     * @return  void
     *
     */
    protected function populateState($ordering = 'g.id', $direction = 'DESC'){

        $app        = Factory::getApplication();

        $search  = $app -> getUserStateFromRequest($this->context.'.filter.search','filter_search',null,'string');
        $this -> setState('filter.search',$search);

        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        $access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', '');
        $this->setState('filter.access', $access);

        // List state information.
        parent::populateState($ordering, $direction);
    }

    /**
     * Method to get a DatabaseQuery object for retrieving the data set from a database.
     *
     * @return  \Joomla\Database\DatabaseQuery   A DatabaseQuery object to retrieve the data set.
     *
     */
    protected function getListQuery(){
        $db         = $this -> getDatabase();
        $query      = $db -> getQuery(true);
        $user       = Factory::getUser();

        $subQuery   = $db -> getQuery(true);
        $subQuery -> select('COUNT(DISTINCT f.id)');
        $subQuery -> from('#__tz_portfolio_plus_fields AS f');
        $subQuery -> join('INNER', '#__tz_portfolio_plus_field_fieldgroup_map AS m ON m.fieldsid = f.id');
        $subQuery -> where('g.id = m.groupid');
        $query->select(
            $this->getState(
                'list.select',
                'g.*'
            )
        );

        $query -> select('('.(string) $subQuery.') AS total');

        $query -> from('#__tz_portfolio_plus_fieldgroups AS g');

        // Join over the users for the checked out user.
        $query-> select('uc.name AS editor')
            ->join('LEFT', '#__users AS uc ON uc.id=g.checked_out');

        // Join over the asset groups.
        $query -> select('v.title AS access_level')
            ->join('LEFT', '#__viewlevels AS v ON v.id = g.access');

        // Filter by published state
        $published = $this->getState('filter.published');

        if (is_numeric($published))
        {
            $query->where('g.published = ' . (int) $published);
        }
        elseif ($published === '')
        {
            $query->where('(g.published IN (0, 1))');
        }

        // Filter by access level.
        $access = $this->getState('filter.access');
        if (is_numeric($access))
        {
            $query->where('g.access = ' . (int) $access);
        }
        elseif (is_array($access))
        {
            $access = ArrayHelper::toInteger($access);
            $access = implode(',', $access);
            $query->where('g.access IN (' . $access . ')');
        }

        // Implement View Level Access
        if (!$user->authorise('core.admin'))
        {
            $groups = implode(',', $user->getAuthorisedViewLevels());
            $query->where('g.access IN (' . $groups . ')');
        }

        // Filter by search in title
        $search = $this->getState('filter.search');

        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where('g.id = ' . (int) substr($search, 3));
            }
            else
            {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where('(g.name LIKE ' . $search . ')');
            }
        }

        // Add the list ordering clause.
        $orderCol	= $this -> getState('list.ordering', 'g.id');
        $orderDirn	= $this -> getState('list.direction', 'desc');

        $query->order($db->escape($orderCol).' '.$db->escape($orderDirn));

        return $query;
    }

    /**
     * Method to get an array of data items.
     *
     * @return  mixed  An array of data items on success, false on failure.
     *
     */
    public function getItems(){
        if($items = parent::getItems()){
            foreach($items as &$item){
                $item -> categories = null;
                if($categories = TZ_PortfolioHelperCategories::getCategoriesByGroupId($item -> id)){
                    $item -> categories = $categories;
                }
            }
            return $items;
        }
        return false;
    }

    /**
     * Get fields group with type array[key=groupid] = groupname
     *
     * @return mixed
    */
    public function getItemsArray(){
        $db     = $this -> getDatabase();
        $db -> setQuery($this -> getListQuery());

        if($items = $db -> loadObjectList()){
            foreach($items as $item){
                $list[$item -> id]  = $item -> name;
            }
            return $list;
        }
        return array();
    }

    /*
     * Get fields group name have had fields
     *
     * @return mixed
    */
    public function getGroupNamesContainFields(){
        // Get a storage key.
        $store = $this -> getStoreId('getGroupNamesContainFields');

        // Try to load the data from internal storage.
        if (isset($this -> cache[$store])) {
            return $this -> cache[$store];
        }

        $db     = $this -> getDatabase();
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
            if(!empty($list)){
                return $this -> cache[$store] = $list;
            }
        }
        return false;

    }

    /**
     * Get fields group have had fields
     *
     * @return mixed
    */
    public function getGroupsContainFields(){
        // Get a storage key.
        $store = $this -> getStoreId('getGroupsContainFields');

        // Try to load the data from internal storage.
        if (isset($this -> cache[$store])) {
            return $this -> cache[$store];
        }

        $db     = $this -> getDatabase();
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
            if(!empty($list)){
                return $this -> cache[$store] = $list;
            }
        }
        return false;

    }
}
