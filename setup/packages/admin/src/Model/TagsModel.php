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

/**
 * Tags Model
 */
class TagsModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param	array $config	An array of configuration options (name, state, dbo, table_path, ignore_request).
     * @param   MVCFactoryInterface  $factory  The factory.
     *
     */
    public function __construct($config = array(), MVCFactoryInterface $factory = null){
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 't.id',
                'title', 't.title',
                'published', 't.published'
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
            $this->_db = Factory::getDbo();
        }
    }

    function populateState($ordering = 'id', $direction = 'desc'){

        parent::populateState($ordering, $direction);

        $state  = $this -> getUserStateFromRequest($this -> context.'.filter_published','filter_published',null,'string');
        $this -> setState('filter.published',$state);

        $search  = $this -> getUserStateFromRequest($this -> context.'.filter.search','filter_search',null,'string');
        $this -> setState('filter.search',$search);
    }

    protected function getListQuery(){
        $db = $this -> getDatabase();
        $query  = $db -> getQuery(true);
        $query -> select($this->getState(
            'list.select',
            't.*'
        )
        );
        $query -> from($db->quoteName('#__tz_portfolio_plus_tags', 't'));


        // Count Items
        $subQueryCountTaggedItems = $db->getQuery(true);
        $subQueryCountTaggedItems
            ->select('COUNT(' . $db->quoteName('tag_map.contentid') . ')')
            ->from($db->quoteName('#__tz_portfolio_plus_tag_content_map', 'tag_map'))
            ->where($db->quoteName('tag_map.tagsid') . ' = ' . $db->quoteName('t.id'));
        $query->select('(' . (string) $subQueryCountTaggedItems . ') AS ' . $db->quoteName('countTaggedItems'));

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

        // Filter by published state
        $published = $this->getState('filter.published');

        if (is_numeric($published))
        {
            $query->where('published = ' . (int) $published);
        }
        elseif ($published === '')
        {
            $query->where('(published IN (0, 1))');
        }

        // Add the list ordering clause
        $listOrdering   = $this->getState('list.ordering', 'f.id');
        $listDirn       = $this->getState('list.direction', 'DESC');

        $query -> order($db->escape($listOrdering) . ' ' . $db->escape($listDirn));

        return $query;

    }
}
