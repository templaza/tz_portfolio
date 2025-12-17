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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Menu\SiteMenu;
use Joomla\Filesystem\Folder;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\CategoriesHelper as TZ_PortfolioHelperCategories;

class LayoutsModel extends ListModel
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 't.id',
                'name', 't.name',
                'home', 't.home',
                'published', 't.published',
                'template', 't.template',
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

    function populateState($ordering = 't.template', $direction = 'asc'){

        parent::populateState($ordering, $direction);

        $search  = $this -> getUserStateFromRequest($this -> context.'.filter_search','filter_search',null,'string');
        $this -> setState('filter.search',$search);

        $template  = $this -> getUserStateFromRequest($this -> context.'.filter.template','filter_template',null,'string');
        $this -> setState('filter.template',$template);
    }

    function getListQuery(){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select($this->getState(
            'list.select',
            't.*'
            )
        );

        $query -> select('(SELECT COUNT(xc2.template_id) FROM #__tz_portfolio_plus_templates AS t2'
            .' INNER JOIN #__tz_portfolio_plus_content AS xc2 ON t2.id = xc2.template_id WHERE t.id = t2.id)'
            .' AS content_assigned');
        $query -> select('(SELECT COUNT(c2.template_id) FROM #__tz_portfolio_plus_templates AS t3'
            .' INNER JOIN #__tz_portfolio_plus_categories AS c2 ON t3.id = c2.template_id WHERE t.id = t3.id)'
            .' AS category_assigned');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_templates').' AS t');

        $query -> join('INNER',$db -> quoteName('#__tz_portfolio_plus_extensions').' AS e ON t.template = e.element')
            -> where('e.published = 1')
            ->where('(e.type=' . $db->quote('tz_portfolio-style').' OR e.type = '
                .$db -> quote('tz_portfolio_plus-template').')');

        // Filter by search in name.
        $search = $this->getState('filter.search');

        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where('t.id = ' . (int) substr($search, 3));
            }
            else
            {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where(
                    '(' . $db->quoteName('t.title') . ' LIKE ' . $search . ')'
                );
            }
        }

        if($template = $this -> getState('filter.template')){
            $query -> where('t.template = '.$db -> quote($template));
        }

        /**
         * Filter by style from styles directory
         * @deprecated Will be removed when TZ Portfolio Plus wasn't supported
         */
        $filter_styles  = Folder::folders(COM_TZ_PORTFOLIO_STYLE_PATH);
        if(!empty($filter_styles)){
            $filter_styles  = array_map(function($value) use($db){
                return $db -> quote($value);
            }, $filter_styles);
            $query -> where('e.element IN('.implode(',', $filter_styles).')');
        }

        // Add the list ordering clause.
        $orderCol   = $this->getState('list.ordering','t.template');
        $orderDirn  = $this->getState('list.direction','asc');

        if(!empty($orderCol) && !empty($orderDirn)){
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

//        $query -> group('t.id');

        return $query;
    }

    public function getItems(){
        if($items = parent::getItems()){
            $component  = ComponentHelper::getComponent('com_tz_portfolio');
            $menus  = SiteMenu::getInstance('site');
            $menu_assigned  = array();
            if($menu_items  = $menus -> getItems(array('component_id'),$component -> id)){
                if(count($menu_items)){
                    foreach($menu_items as $m){
                        if(isset($m -> params)){
                            $params = $m -> params;
                            if($tpl_style_id = $params -> get('tz_template_style_id')){
                                if(!isset($menu_assigned[$tpl_style_id])){
                                    $menu_assigned[$tpl_style_id]   = 0;
                                }
                                $menu_assigned[$tpl_style_id] ++;
                            }
                        }
                    }
                }
            }

            foreach($items as $i => &$item){
                $item -> menu_assigned      = 0;
                if(isset($menu_assigned[$item -> id])){
                    $item -> menu_assigned  = $menu_assigned[$item -> id];
                }
            }

            return $items;
        }
        return false;
    }


}