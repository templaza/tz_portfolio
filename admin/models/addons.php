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
jimport('joomla.filesystem.folder');

class TZ_Portfolio_PlusModelAddons extends JModelList
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'extension_id', 'e.extension_id',
                'name', 'e.name',
                'folder', 'e.folder',
                'element', 'e.element',
                'checked_out', 'e.checked_out',
                'checked_out_time', 'e.checked_out_time',
                'published', 'e.published',
                'ordering', 'e.ordering',
            );
        }

        parent::__construct($config);
    }

    function populateState($ordering = null, $direction = null){

//        parent::populateState($ordering,$direction);

        $search  = $this -> getUserStateFromRequest($this -> context.'.filter.search','filter_search',null,'string');
        $this -> setState('filter.search',$search);

        $status = $this->getUserStateFromRequest($this->context.'.filter.status', 'filter_status', '');
        $this->setState('filter.status', $status);

        $folder = $this->getUserStateFromRequest($this->context . '.filter.folder', 'filter_folder', null, 'cmd');
        $this->setState('filter.folder', $folder);

        $order  = $this -> getUserStateFromRequest($this -> context.'.filter_order','filter_order',null,'string');
        $this -> setState('filter_order',$order);

        $orderDir  = $this -> getUserStateFromRequest($this -> context.'.filter_order_Dir','filter_order_Dir','asc','string');
        $this -> setState('filter_order_Dir',$orderDir);

        // List state information.
        parent::populateState('folder', 'asc');
    }

    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.access');
        $id .= ':' . $this->getState('filter.state');
        $id .= ':' . $this->getState('filter.folder');
        $id .= ':' . $this->getState('filter.language');

        return parent::getStoreId($id);
    }

    function getListQuery(){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('e.*');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_extensions').' AS e');

        $query -> where('type = '.$db -> quote('tz_portfolio_plus-plugin'));

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor')
            ->join('LEFT', '#__users AS uc ON uc.id=e.checked_out');

        // Filter by search in name.
        $search = $this->getState('filter.search');

        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where('e.id = ' . (int) substr($search, 3));
            }
            else
            {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where(
                    '(' . $db->quoteName('e.name') . ' LIKE ' . $search . ')'
                );
            }
        }

        // Filter by published state
        $status = $this->getState('filter.status');
        if ($status != '')
        {
            if ($status == '2')
            {
                $query->where('protected = 1');
            }
            elseif ($status == '3')
            {
                $query->where('protected = 0');
            }
            else
            {
                $query->where('protected = 0')
                    ->where('published=' . (int) $status);
            }
        }

        // Filter by folder.
        if ($folder = $this->getState('filter.folder'))
        {
            $query->where('e.folder = ' . $db->quote($folder));
        }

        // Add the list ordering clause.
        $orderCol = $this->getState('list.ordering','e.id');
        $orderDirn = $this->getState('list.direction','desc');
        if ($orderCol == 'e.ordering')
        {
            $orderCol = 'e.name ' . $orderDirn . ', e.ordering';
        }

        if(!empty($orderCol) && !empty($orderDirn)){
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

    public function getItems(){
        if($items = parent::getItems()){
            $language   = JFactory::getLanguage();
            foreach($items as &$item){
                if (strlen($item -> manifest_cache))
                {
                    $data = json_decode($item -> manifest_cache);

                    if ($data)
                    {
                        foreach ($data as $key => $value)
                        {
                            if ($key == 'type')
                            {
                                // Ignore the type field
                                continue;
                            }

                            $item -> $key = $value;
                        }
                    }
                }


                $plugin = TZ_Portfolio_PlusPluginHelper::getInstance($item -> folder, $item -> element);

                $item -> data_manager        = false;
                if(method_exists($plugin, 'getDataManager')){
                    $item -> data_manager    = $plugin -> getDataManager();
                }

                $langPath   = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.DIRECTORY_SEPARATOR.$item -> folder
                    .DIRECTORY_SEPARATOR.$item -> element;
                $langKey    = 'plg_'.$item -> folder.'_'.$item -> element;

                if($loaded = $language -> load($langKey, $langPath)) {
                    $langKey = strtoupper($langKey);
                    if ($language->hasKey($langKey)) {
                        $item->name = JText::_($langKey);
                    }
                }

                $item -> author_info = @$item -> authorEmail . '<br />' . @$item -> authorUrl;
            }

            return $items;
        }
        return false;
    }

}