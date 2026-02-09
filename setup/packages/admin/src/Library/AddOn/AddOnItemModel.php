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
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Registry\Registry;
use Joomla\CMS\MVC\Model\ItemModel;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;

class AddOnItemModel extends ItemModel {

    protected $addon            = null;
    protected $article          = null;
    protected $trigger_params   = null;

    public function __construct($config = [], ?MVCFactoryInterface $factory = null)
    {
        parent::__construct($config, $factory);

        if(isset($config['article'])){
            $this -> article  = $config['article'];
        }
        if(isset($config['addon'])){
            $this -> addon  = $config['addon'];
        }
        if(isset($config['trigger_params'])){
            $this -> trigger_params  = $config['trigger_params'];
        }
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $input  = Factory::getApplication() -> input;
        $return = $input -> get('return', null, 'default', 'base64');
        $this->setState('return_page', ($return ? base64_decode($return) : ''));

        $this -> setState($this -> getName().'.addon', $this -> addon);
        $this -> setState($this -> getName().'.article', $this -> article);
        $params    = null;
        if($this -> addon){
            if(is_string($this -> addon -> params)) {
                $params = new Registry($this->addon->params);
            }else{
                $params = $this -> addon -> params;
            }
            $this -> addon -> params    = clone($params);
        }

        $this -> setState($this -> getName().'.addon', $this -> addon);

        if($trigger_params = $this -> trigger_params){
            if(is_string($trigger_params)){
                $trigger_params = new Registry($trigger_params);
            }
            if($params){
                $params -> merge($trigger_params);
            }else{
                $params    = $trigger_params;
            }
        }
        if(!empty($params)) {
            $this->setState('params', clone($params));
        }

        parent::populateState();
    }

    public function getItem($pk = null){
        if($article = $this -> article){
            return $this -> article;
        }
        return false;
    }

    public function getReturnPage()
    {
        return base64_encode($this->getState('return_page'));
    }

    public function getAddon(){
        return $this -> addon;
    }

    protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('DISTINCT d.*');
        $query->from($db->quoteName('#__tz_portfolio_plus_addon_data').' AS d');
        $query -> join('INNER', '#__tz_portfolio_plus_content AS c ON c.id = d.content_id');
        $query ->join('INNER', '#__tz_portfolio_plus_content_category_map AS cm ON cm.contentid = c.id');
        $query ->join('INNER', '#__tz_portfolio_plus_categories AS cc ON cc.id = cm.catid');
        $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e ON e.id = d.extension_id');

        if($addon = AddonHelper::getPlugin($this->addon->type, $this->addon->name)) {
            $query->where('d.extension_id =' .(int) $addon -> id);
        }
        $query -> where('d.element ='.$db -> quote($this->addon->name));

        $item = $this -> getItem();

        if($content_id = $item->id){
            $query -> where('d.content_id = '.$content_id);
        }
        $query -> where('d.published = 1');

        if($catid = $this -> getState('filter.catid', null)) {
            if(is_array($catid)){
                $query -> where('cc.id IN('.implode(',', $catid).')');
            }else{
                $query -> where('cc.id = '.(int) $catid);
            }
        }
        return $query;
    }

    public function getAddonData()
    {
        $db = $this->getDatabase();
        $db->setQuery($this->getListQuery());
        $data = $db->loadObject();
        $return = new \stdClass();
        if (!empty($data->value)) {
            $return = json_decode($data->value);
        }
        return $return;
    }
}