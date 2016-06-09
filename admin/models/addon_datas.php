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
defined('_JEXEC') or die;

use Joomla\Registry\Registry;

class TZ_Portfolio_PlusModelAddon_Datas extends JModelList{

    protected $addon_element   = null;

    protected function populateState($ordering = null, $direction = null){
        $addon_id   = JFactory::getApplication()->input->getInt('addon_id');
        $this -> setState($this -> getName().'.addon_id',$addon_id);

        $published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        // List state information.
        parent::populateState($ordering, $direction);
    }

    public function getListQuery(){
        if($addonId = $this -> getState($this -> getName().'.addon_id')){
            $db     = $this -> getDbo();
            $query  = $db -> getQuery(true)
                -> select('*')
                -> from($db -> quoteName('#__tz_portfolio_plus_addon_data'))
                -> where('extension_id ='.$addonId);
            if($element = $this -> addon_element){
                $query -> where('element ='.$db -> quote($element));
            }

            // Filter by published state
            $published = $this->getState('filter.published');
            if (is_numeric($published)) {
                $query->where('published = ' . (int) $published);
            }
            elseif ($published === '') {
                $query->where('(published = 0 OR published = 1 OR published = -1)');
            }

            // Add the list ordering clause.
            $orderCol = $this->getState('list.ordering','id');
            $orderDirn = $this->getState('list.direction','desc');

            if(!empty($orderCol) && !empty($orderDirn)){
                if(strpos($orderCol,'value.') !== false) {
                    $fields     = explode('.',$orderCol);
                    $orderCol   = array_pop($fields);
                    $query->order('substring_index(value,' . $db->quote('"'.$orderCol.'":') . ',-1) '. $orderDirn);
                }else{
                    $query->order($db->escape($orderCol . ' ' . $orderDirn));
                }
            }
            return $query;
        }
        return false;
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

    public function getAddOnItem($pk = null){
        $pk         = (!empty($pk)) ? $pk : (int) $this->getState($this -> getName().'.addon_id');
        $storeId    = __METHOD__.'::' .$pk;

        if (!isset($this->cache[$storeId]))
        {
            $false	= false;

            // Get a row instance.
            $table = $this->getTable('Extensions','TZ_Portfolio_PlusTable');

            // Attempt to load the row.
            $return = $table->load($pk);

            // Check for a table object error.
            if ($return === false && $table->getError())
            {
                $this->setError($table->getError());

                return $false;
            }

            // Convert to the JObject before adding other data.
            $properties = $table->getProperties(1);
            $this->cache[$storeId] = JArrayHelper::toObject($properties, 'JObject');

            $dispatcher     = JEventDispatcher::getInstance();
            if($plugin         = TZ_Portfolio_PlusPluginHelper::getInstance($table -> folder,
                $table -> element, false, $dispatcher)){
                if(method_exists($plugin, 'onAddOnDisplayManager')) {
                    $this->cache[$storeId]->manager = $plugin->onAddOnDisplayManager();
                }
            }
        }

        return $this->cache[$storeId];
    }


}