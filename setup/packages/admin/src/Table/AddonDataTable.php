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
use Joomla\CMS\Factory;

// No direct access
defined('_JEXEC') or die;

class AddonDataTable extends Table
{
    public function __construct(&$db)
    {
        $this->_jsonEncode = ['value'];
        parent::__construct('#__tz_portfolio_plus_addon_data', 'id', $db);
    }
    public function load($keys = null, $reset = true) {
        $fields = array_keys($this->getProperties());
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($this->_tbl.' AS ad');
        if (is_array($keys)) {
            foreach ($keys as $field => $value)
            {
                // Check that $field is in the table.
                if (!in_array($field, $fields))
                {
                    throw new \UnexpectedValueException(sprintf('Missing field in database: %s &#160; %s.', get_class($this), $field));
                }
                // Add the search tuple to the query.
                $query->where('ad.' . $db->quoteName($field) . ' = ' . $db->quote($value));
            }
        } else {
            $query->where('ad.id = ' . (int) $keys);
        }

        $db->setQuery($query);
        $row = $db->loadAssoc();
        // Check that we have a result.
        if (empty($row)) {
            $result = false;
        } else {
            // Bind the object with the row and return.
            $result = $this->bind($row);
        }
        return $result;
    }

    public function bind($src, $ignore = [])
    {
        parent::bind($src, $ignore);
        if (empty($this->created)) {
            $this->created = Factory::getDate()->toSql();
        }
        if (empty($this->created_by)) {
            $this->created_by = Factory::getApplication()->getIdentity()->id;
        }
        if (empty($this->publish_up)) {
            $this->publish_up = Factory::getDate()->toSql();
        }
        return true;
    }
}