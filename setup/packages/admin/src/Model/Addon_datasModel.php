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

use Joomla\CMS\Cache\Cache;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\View\ViewInterface;
use Joomla\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Utilities\ArrayHelper;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\Path;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;

/**
 * About Page Model
 */
class Addon_datasModel extends ListModel
{
    protected $addon_element   = null;
    public $addon = null;
    public function __construct($config = [], ?MVCFactoryInterface $factory = null)
    {
        $addon_id   = Factory::getApplication()->input->getInt('addon_id');
        if($addon_id) {
            $addon_info  = AddonHelper::getPluginById($addon_id);
            $this->addon  = AddonHelper::getInstance($addon_info -> type, $addon_info -> name);
        }
        parent::__construct($config, $factory);
    }

    protected function populateState($ordering = 'id', $direction = 'desc'){

        $addon_id   = Factory::getApplication()->input->getInt('addon_id');
        $this -> setState($this -> getName().'.addon_id',$addon_id);

        if($addon_id) {
            $addon  = AddonHelper::getPluginById($addon_id);
            $this->setState($this->getName() . '.addon', $addon);
            $this->addon_element = Factory::getApplication()->input->getString('element');
            $this -> setState($addon->name.'filter.element',$this->addon_element);
        }

        $published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        // List state information.
        parent::populateState($ordering, $direction);
    }
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        if($access = $this -> getState('filter.access')) {
            $id .= ':' . $this->getState('filter.access');
        }
        $id .= ':' . $this->getState('filter.published');

        return parent::getStoreId($id);
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

    public function getAddon()
    {
        return $this->addon;
    }

    public function getAddonView(){
        return $this->addon -> onAddOnDisplayManager();
    }

    public function getDataMenu()
    {
        return $this->addon-> getDataMenu();
    }

    protected function getListQuery(){
        $db     = $this -> getDatabase();
        $addonId = $this -> getState($this -> getName().'.addon_id');
        $query  = $db -> getQuery(true)
            -> select('d.*')
            -> from($db -> quoteName('#__tz_portfolio_plus_addon_data').' AS d')
            -> where('d.extension_id ='.$addonId);
        if($element = $this -> addon_element){
            $query -> where('d.element ='.$db -> quote($element));
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

    public function getAddOnItem($pk = null){
        $pk         = (!empty($pk)) ? $pk : (int) $this->getState($this -> getName().'.addon_id');
        $storeId    = __METHOD__.'::' .$pk;

        if (!isset($this->cache[$storeId]))
        {
            $false	= false;

            // Get a row instance.
            $table = $this->getTable('Extensions');

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
            $this->cache[$storeId] = ArrayHelper::toObject($properties);

//            $dispatcher     = TZ_Portfolio_PlusPluginHelper::getDispatcher();
//            if($plugin         = TZ_Portfolio_PlusPluginHelper::getInstance($table -> folder,
//                $table -> element, false, $dispatcher)){
//                if(method_exists($plugin, 'onAddOnDisplayManager')) {
//                    $this->cache[$storeId]->manager = $plugin->onAddOnDisplayManager();
//                }
//            }
        }

        return $this->cache[$storeId];
    }
}
