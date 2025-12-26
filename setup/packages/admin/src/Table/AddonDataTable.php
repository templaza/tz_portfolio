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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Table;

use Joomla\CMS\Event\AbstractEvent;
use Joomla\CMS\Table\Table;

// No direct access
defined('_JEXEC') or die;

class AddonDataTable extends Table
{
    public function __construct(&$db)
    {
        parent::__construct('#__tz_portfolio_plus_addon_data', 'id', $db);
    }
    public function load($keys = null, $reset = true) {
        $event = AbstractEvent::create(
            'onTableBeforeLoad',
            [
                'subject'	=> $this,
                'keys'		=> $keys,
                'reset'		=> $reset,
            ]
        );
        $this->getDispatcher()->dispatch('onTableBeforeLoad', $event);
        $fields = array_keys($this->getProperties());
        $query = $this->_db->getQuery(true)
            ->select('*')
            ->from($this->_tbl.' AS ad');

        foreach ($keys as $field => $value)
        {
            // Check that $field is in the table.
            if (!in_array($field, $fields))
            {
                throw new \UnexpectedValueException(sprintf('Missing field in database: %s &#160; %s.', get_class($this), $field));
            }
            // Add the search tuple to the query.
            $query->where('ad.' . $this->_db->quoteName($field) . ' = ' . $this->_db->quote($value));
        }
        $this->_db->setQuery($query);
        $row = $this->_db->loadAssoc();

        // Check that we have a result.
        if (empty($row))
        {
            $result = false;
        }
        else
        {
            // Bind the object with the row and return.
            $result = $this->bind($row);
        }
        $event = AbstractEvent::create(
            'onTableAfterLoad',
            [
                'subject'		=> $this,
                'result'		=> &$result,
                'row'			=> $row,
            ]
        );
        $this->getDispatcher()->dispatch('onTableAfterLoad', $event);

        return $result;
    }
}