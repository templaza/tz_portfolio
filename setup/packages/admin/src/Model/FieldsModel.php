<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    Sonny

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
 * Fields Model
 */
class FieldsModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param	array $config	An array of configuration options (name, state, dbo, table_path, ignore_request).
     * @param MVCFactoryInterface|null $factory  The factory.
     *
     */
    public function __construct($config = array(), ?MVCFactoryInterface $factory = null){
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 'f.id',
                'title', 'f.title',
                'groupname', 'f.groupname',
                'type', 'f.type',
                'list_view', 'f.list_view',
                'detail_view', 'f.detail_view',
                'advanced_search', 'f.advanced_search',
                'published', 'f.published',
                'ordering', 'f.ordering'
            );
        }
        parent::__construct($config, $factory);

        // Set the model dbo
        if (array_key_exists('dbo', $config))
        {
            $this->_db = $config['dbo'];
        }
        else
        {
            $this->_db = $this->getDatabase();
        }
    }

    public function populateState($ordering = 'f.id', $direction = 'desc'){

        parent::populateState($ordering, $direction);
        $app = Factory::getApplication();


        $group  = $this -> getUserStateFromRequest($this->context.'.filter.group','filter_group',0);
        $this -> setState('filter.group',$group);

        $published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        $search  = $this -> getUserStateFromRequest($this->context.'.filter_search','filter_search',null,'string');
        $this -> setState('filter.search',$search);

        $access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', '');
        $this->setState('filter.access', $access);


        $formSubmited = $app->input->post->get('form_submited');

        $type  = $this -> getUserStateFromRequest($this->context.'filter.type','filter_type','');
        if ($formSubmited) {
            $type = $app->input->post->get('type');
            $this -> setState('filter.type', $type);
        }
    }


    /**
     * Method to get a DatabaseQuery object for retrieving the data set from a database.
     *
     * @return  \Joomla\Database\DatabaseQuery   A DatabaseQuery object to retrieve the data set.
     *
     */
    protected function getListQuery(){

        $db     = $this -> getDatabase();
        /* @var Joomla\Database\Mysqli\MysqliQuery $query */
        $query  = $db -> getQuery(true);
        $user   = Factory::getApplication()->getIdentity();

        $query->select(
            $this->getState(
                'list.select',
                'DISTINCT f.id, f.*'
            )
        );

        $query -> from('#__tz_portfolio_plus_fields AS f');
        $query -> join('LEFT','#__tz_portfolio_plus_field_fieldgroup_map AS x ON f.id=x.fieldsid');

        $query -> join('LEFT','#__tz_portfolio_plus_fieldgroups AS fg ON fg.id=x.groupid');

        $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e ON e.element = f.type')
            -> where('(e.type = '.$db -> quote('tz_portfolio-addon').' OR e.type = '
                .$db -> quote('tz_portfolio_plus-plugin').')')
            -> where('e.folder = '.$db -> quote('extrafields'))
            -> where('e.published = 1');

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor')
            ->join('LEFT', '#__users AS uc ON uc.id=f.checked_out');

        // Join over the asset groups.
        $query -> select('v.title AS access_level')
            ->join('LEFT', '#__viewlevels AS v ON v.id = f.access');

        // Join over the users for the author.
        $query->select('ua.name AS author_name')
            ->join('LEFT', '#__users AS ua ON ua.id = f.created_by');

        if($search = $this -> getState('filter.search'))
            $query -> where('f.title LIKE '.$db -> quote('%'.$search.'%'));

        // Filter by published state
        $published = $this->getState('filter.published');

        if (is_numeric($published))
        {
            $query->where('f.published = ' . (int) $published);
        }
        elseif ($published === '')
        {
            $query->where('(f.published IN (0, 1))');
        }

        // Filter by group
        if($group  = $this->getState('filter.group')){
            if (is_numeric($group))
            {
                $query -> where('x.groupid = ' . (int) $group);
            }
            elseif (is_array($group))
            {
                $group  = ArrayHelper::toInteger($group);
                $group  = implode(',', $group);
                $query -> where('x.groupid IN (' . $group . ')');
            }
        }

        // Filter by field's type
        if($type  = $this->getState('filter.type')){
            if (is_string($type))
            {
                $query -> where('f.type = ' . $db -> quote($type));
            }
            elseif (is_array($type))
            {
                foreach($type as $i => $t) {
                    $type[$i]  = 'f.type = '.$db -> quote($t);
                }
                $query -> andWhere($type);
            }
        }

        // Filter by access level.
        $access = $this->getState('filter.access');
        if (is_numeric($access))
        {
            $query->where('f.access = ' . (int) $access);
        }
        elseif (is_array($access))
        {
            $access = ArrayHelper::toInteger($access);
            $access = implode(',', $access);
            $query->where('f.access IN (' . $access . ')');
        }

        // Implement View Level Access
        if (!$user->authorise('core.admin'))
        {
            $groups     = implode(',', $user->getAuthorisedViewLevels());
            $subquery   = $db -> getQuery(true);
            $subquery -> select('subg.id');
            $subquery -> from('#__tz_portfolio_plus_fieldgroups AS subg');
            $subquery -> where('subg.access IN('.$groups.')');

            $query -> where('f.access IN('.$groups.')');
            $query -> where('fg.id IN('.((string) $subquery).')');
            $query -> where('e.access IN('.$groups.')');
        }


        /**
         * Filter by add-ons from add-ons directory
         * @deprecated Will be removed when TZ Portfolio Plus wasn't supported
         */
        $filter_addons  = glob(COM_TZ_PORTFOLIO_ADDON_PATH.'/*/*', GLOB_ONLYDIR);
        if(!empty($filter_addons)){
            $filter_addons  = array_map(function($value) use($db){
                $new_value  = basename(dirname($value));
                $new_value .= '/'.basename($value);
                return $db -> quote($new_value);
            }, $filter_addons);
            $query -> where('CONCAT(e.folder, "/", e.element) IN('.implode(',', $filter_addons).')');
        }

        // Add the list ordering clause
        $listOrdering   = $this->getState('list.ordering', 'f.id');
        $listDirn       = $this->getState('list.direction', 'DESC');

        if(isset($group) && $group){
            $listOrdering   = 'x.ordering';
            $query -> select('x.ordering AS ordering');
        }

        $query->order($db->escape($listOrdering) . ' ' . $db->escape($listDirn));

//        var_dump(get_class($query));
//        var_dump($query -> dump());
//        die(__FILE__);

        return $query;
    }

    public function getItems(){
        if($items = parent::getItems()){
            $groupModel  = Factory::getApplication()->bootComponent('tz_portfolio')
                ->getMVCFactory()->createModel('Groups', 'Administrator');

            if($groupNames = $groupModel -> getGroupNamesContainFields()){
                $groups = $groupModel -> getGroupsContainFields();
                foreach($items as $item){
                    if(isset($groupNames[$item -> id])){
                        $item -> groupname  = $groupNames[$item -> id];
                        if($groups && isset($groups[$item -> id])) {
                            $groupIds   = $groups[$item -> id];
                            if(is_array($groups[$item -> id])) {
                                $groupIds = array_keys($groups[$item -> id]);
                            }

                            $item->groupid = (count($groupIds) == 1)?$groupIds[0]:$groupIds;
                        }
                    }
                }
            }
            return $items;
        }
    }
}
