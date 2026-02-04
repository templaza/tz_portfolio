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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Library\AddOn;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;

class AddOnListModel extends ListModel {
    protected function populateState($ordering = 'id', $direction = 'desc'){

        $addon_id   = Factory::getApplication()->input->getInt('addon_id');
        $this -> setState($this -> getName().'.addon_id',$addon_id);

        if($addon_id) {
            $addon  = AddonHelper::getPluginById($addon_id);
            $this->setState($this->getName() . '.addon', $addon);
        }
        $published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        // List state information.
        parent::populateState($ordering, $direction);
    }
    protected function getListQuery(){
        $db     = $this -> getDatabase();
        $addonId = $this -> getState($this -> getName().'.addon_id');
        $query  = $db -> getQuery(true)
            -> select('d.*')
            -> from($db -> quoteName('#__tz_portfolio_plus_addon_data').' AS d')
            -> where('d.extension_id ='.$addonId);
        $element = $this -> getState($this -> getName().'.element');
        if (!empty($element)) {
            $query->where('d.element = ' . $db->quote($element));
        }

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor')
            ->join('LEFT', '#__users AS uc ON uc.id=d.checked_out');

        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where('d.published = ' . (int) $published);
        }
        elseif ($published === '') {
            $query->where('(d.published = 0 OR d.published = 1 OR d.published = -1)');
        }

        // Add the list ordering clause.
        $orderCol = $this->getState('list.ordering','id');
        $orderDirn = $this->getState('list.direction','desc');

        if(!empty($orderCol) && !empty($orderDirn)){
            if(strpos($orderCol,'value.') !== false) {
                $fields     = explode('.',$orderCol);
                $orderCol   = array_pop($fields);
                $query->order('substring_index(d.value,' . $db->quote('"'.$orderCol.'":') . ',-1) '. $orderDirn);
            }else{
                $query->order($db->escape($orderCol . ' ' . $orderDirn));
            }
        }

        return $query;
    }
    public function getItems(){
        if($items = parent::getItems()){
            foreach($items as &$item){
                $item -> value  = json_decode($item -> value);
            }
            return $items;
        }
        return false;
    }
}