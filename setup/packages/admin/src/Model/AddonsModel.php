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
use Joomla\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Filesystem\Folder;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\AddonsHelper;
use Joomla\Filesystem\Path;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;

/**
 * About Page Model
 */
class AddonsModel extends ListModel
{
    public function getItems(){
        if($items = parent::getItems()){
            $language   = Factory::getApplication() -> getLanguage();
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

                $addon  = AddonHelper::getInstance($item -> folder, $item -> element);

                $item -> data_manager        = false;
                if(is_object($addon) && method_exists($addon, 'getDataManager')){
                    $item -> data_manager    = $addon -> getDataManager();
                }

                $langKey    = 'plg_'.$item -> folder.'_'.$item -> element;
                if($loaded = AddonHelper::loadLanguage($item -> element, $item -> folder)) {
                    $langKey = strtoupper($langKey);
                    if ($language->hasKey($langKey)) {
                        $item->name = Text::_($langKey);
                    }
                }

                $item -> author_info = @$item -> authorEmail . '<br />' . @$item -> authorUrl;


            }

            return $items;
        }
        return false;
    }

    public function getItemsUpdate(){

        $storeId    = __METHOD__;
        $storeId    = md5($storeId);

        if(isset($this -> cache[$storeId])){
            return $this -> cache[$storeId];
        }

        $addons = AddonsHelper::getAddons();

        if(!$addons){
            return false;
        }

        $data   = array();

        foreach($addons as $item){

            $adoFinded = $this -> findAddOnFromServer($item);

            $item -> new_version    = null;
            $manifest   = json_decode($item -> manifest_cache);
            if($adoFinded && $adoFinded -> pProduces){
                if($pProduces = $adoFinded -> pProduces) {
                    if(isset($pProduces -> pProduce) && $pProduces -> pProduce
                        && version_compare($manifest -> version, $pProduces -> pProduce -> pVersion, '<')) {
                        $item -> new_version    = $pProduces -> pProduce -> pVersion;
                        $data[] = $item;
                    }
                }
            }
        }

        if(!empty($data)){
            return $this -> cache[$storeId]    = $data;
        }

        return false;
    }

    protected function getListQuery(){
        $db     = $this -> getDatabase();
        $user   = Factory::getApplication()->getIdentity();
        $query  = $db -> getQuery(true);
        $query -> select('e.*');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_extensions').' AS e');

        $query -> where('(type = '.$db -> quote('tz_portfolio-addon').' OR type ='.
            $db -> quote('tz_portfolio_plus-plugin').')');

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor')
            ->join('LEFT', '#__users AS uc ON uc.id=e.checked_out');

        // Join over the asset addons.
        $query -> select('v.title AS access_level')
            ->join('LEFT', '#__viewlevels AS v ON v.id = e.access');

        // Implement View Level Access
        if (!$user->authorise('core.admin'))
        {
            $level = implode(',', $user->getAuthorisedViewLevels());
            $query -> where('e.access IN (' . $level . ')');
        }

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
                $query->where('published=' . (int) $status);
            }
        }

        // Filter by folder.
        if ($folder = $this->getState('filter.folder'))
        {
            $query->where('e.folder = ' . $db->quote($folder));
        }

        // Filter by ids if exists.
        if ($excludeIds = $this->getState('filter.exclude_ids'))
        {
            if(count($excludeIds)) {
                $query->where('e.id NOT IN('.implode(',', $excludeIds).')');
            }
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

        // Add the list ordering clause.
        $orderCol   = $this->getState('list.ordering','e.folder');
        $orderDirn  = $this->getState('list.direction','asc');
        if ($orderCol == 'e.ordering')
        {
            $orderCol = 'e.name ' . $orderDirn . ', e.ordering';
        }

        if(!empty($orderCol) && !empty($orderDirn)){
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }
    
    protected function findAddOnFromServer($addon){

        $finded     = false;
        $adoFinded  = false;

        $options = array(
            'defaultgroup'	=> $this -> option,
            'storage' 		=> 'file',
            'caching'		=> true,
            'lifetime'      => 30 * 60,
            'cachebase'		=> JPATH_ADMINISTRATOR.'/cache'
        );
        $cache = Cache::getInstance('', $options);

        $factory = $this->getMVCFactory();
        $model  = $factory->createModel('Addon', 'Administrator', ['ignore_request' => true]);

        $page   = 1;
        while(!$finded){
            $addons = $cache -> get('addons_server:'.$page);
            if(!$addons){
                $url    = $model -> getUrlFromServer();
                if($page > 1) {
                    $prevAddon  = $cache -> get('addons_server:'.($page - 1));
                    $url .= '&start=' . (($page - 1) * $prevAddon -> limit );
                }

                $response = TZ_PortfolioHelper::getDataFromServer($url);

                if($response){
                    $addons   = json_decode($response -> body);
                    $cache -> store($addons, 'addons_server:'.$page);
                }
            }

            if($addons){
                if($page > ceil($addons -> total / $addons -> limit) - 1){
                    $finded = true;
                }
                foreach($addons -> items as $item){
                    if($item -> pElement == $addon -> element){
                        $finded     = true;
                        $adoFinded  = $item;

                        break;
                    }
                }
            }

            $page++;
        }
        return $adoFinded;
    }
}
