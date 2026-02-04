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

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Access\Rules;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

class FieldTable extends Table
{
    function __construct(&$db) {
        parent::__construct('#__tz_portfolio_plus_fields','id',$db);
    }

    public function updateState($pks = null, $state = 1, $userId = 0): bool
    {
        // Sanitize input
        $userId = (int) $userId;
        $state  = (int) $state;

        if (!is_null($pks))
        {
            if (!is_array($pks))
            {
                $pks = array($pks);
            }

            foreach ($pks as $key => $pk)
            {
                if (!is_array($pk))
                {
                    $pks[$key] = array($this->_tbl_key => $pk);
                }
            }
        }

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks))
        {
            $pk = array();

            foreach ($this->_tbl_keys as $key)
            {
                if ($this->$key)
                {
                    $pk[$key] = $this->$key;
                }
                // We don't have a full primary key - return false
                else
                {
                    $this->setError(Text::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));

                    return false;
                }
            }

            $pks = array($pk);
        }

        $updateField = $this->getColumnAlias('updatestate');

        foreach ($pks as $pk)
        {
            // Update the publishing state for rows with the given primary keys.
            $query = $this->_db->getQuery(true)
                ->update($this->_tbl)
                ->set($this->_db->quoteName($updateField) . ' = ' . (int) $state);

            // Build the WHERE clause for the primary keys.
            $this->appendPrimaryKeys($query, $pk);

            $this->_db->setQuery($query);

            try
            {
                $this->_db->execute();
            }
            catch (\RuntimeException $e)
            {
                $this->setError($e->getMessage());

                return false;
            }

            // If the JTable instance value is in the list of primary keys that were set, set the instance.
            $ours = true;

            foreach ($this->_tbl_keys as $key)
            {
                if ($this->$key != $pk[$key])
                {
                    $ours = false;
                }
            }

            if ($ours)
            {
                $this->$updateField = $state;
            }
        }

        $this->setError('');

        return true;
    }

    protected function _getAssetName()
    {
        $k = $this->_tbl_key;

        return 'com_tz_portfolio.field.' . (int) $this->$k;
    }

    protected function _getAssetTitle()
    {
        return $this->title;
    }

    protected function _getAssetParentId(?Table $table = null, $id = null): int
    {
        $assetId = null;

        if ($assetId === null)
        {
            $query = $this->_db->getQuery(true)
                ->select($this->_db->quoteName('id'))
                ->from($this->_db->quoteName('#__assets'))
                ->where($this->_db->quoteName('name') . ' = ' . $this->_db->quote('com_tz_portfolio.group'));

            // Get the asset id from the database.
            $this->_db->setQuery($query);

            if ($result = $this->_db->loadResult())
            {
                $assetId = (int) $result;
            }
        }

        // Return the asset id.
        if ($assetId)
        {
            return $assetId;
        }
        else
        {
            return parent::_getAssetParentId($table, $id);
        }
    }


    public function store($updateNulls = false){

        $date = Factory::getDate();
        $user = Factory::getUser();

        if (!(int) $this -> created)
        {
            $this -> created = $date -> toSql();
        }

        if ($this -> id)
        {
            $this -> modified       = $date->toSql();
            $this -> modified_by  = $user -> get('id');
        }
        else
        {
            if (empty($this -> created_by))
            {
                $this -> created_by = $user -> get('id');
            }
        }
        return parent::store($updateNulls);
    }

    public function bind($array, $ignore = ''){
        // Bind the rules.
        if (isset($array['rules']) && is_array($array['rules']))
        {
            $rules = new Rules($array['rules']);
            $this->setRules($rules);
        }
        return parent::bind($array, $ignore);
    }
    
}