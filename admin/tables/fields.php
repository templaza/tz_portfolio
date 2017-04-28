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

class TZ_Portfolio_PlusTableFields extends JTable
{

    function __construct(&$db) {
        parent::__construct('#__tz_portfolio_plus_fields','id',$db);

    }

    public function updateState($pks = null, $state = 1, $userId = 0)
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
                    $this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));

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
            catch (RuntimeException $e)
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
    
}