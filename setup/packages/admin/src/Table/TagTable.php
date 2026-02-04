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

use Complex\Exception;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Event\AbstractEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

//no direct access
defined('_JEXEC') or die();

class TagTable extends Table
{
    public function __construct(&$db) {
        parent::__construct('#__tz_portfolio_plus_tags','id',$db);
    }

    public function store($updateNulls = false)
    {
        if(!isset($this -> params) || is_null($this -> params)){
            $this -> params = '';
        }
        if(!isset($this -> description) || is_null($this -> description)){
            $this -> description = '';
        }

        return parent::store($updateNulls);
    }

    public function publish($pks = null,$state=1,$userId = 0){
        $k      = $this -> _tbl_key;

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks))
        {
            if ($this->$k)
            {
                $pks = array($this->$k);
            }
            // Nothing to set publishing state on, return false.
            else
            {
                $this->setError(Text::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
                return false;
            }
        }

        // Build the WHERE clause for the primary keys.
        $where = $k . '=' . implode(' OR ' . $k . '=', $pks);

        $query  = $this -> _db -> getQuery(true);
        $query -> update($this -> _db -> quoteName($this -> _tbl));
        $query -> set($this->_db->quoteName('published') . ' = ' . (int) $state);
        $query -> where('(' . $where . ')');
        $this -> _db -> setQuery($query);

        $this -> _db -> execute();

        return true;
    }

    public function check()
    {
        if (trim($this->alias) == '')
        {
            $this->alias = $this->title;
        }

        $this->alias = ApplicationHelper::stringURLSafe($this->alias);

        if (trim(str_replace('-', '', $this->alias)) == '')
        {
            $this->alias = Factory::getDate()->format('Y-m-d-H-i-s');
        }

        return true;
    }

    public function delete($pk = null)
    {
        if (\is_null($pk)) {
            $pk = array();

            foreach ($this->_tbl_keys as $key) {
                $pk[$key] = $this->$key;
            }
        } elseif (!\is_array($pk)) {
            $pk = array($this->_tbl_key => $pk);
        }

        foreach ($this->_tbl_keys as $key) {
            $pk[$key] = \is_null($pk[$key]) ? $this->$key : $pk[$key];

            if ($pk[$key] === null) {
                throw new \UnexpectedValueException('Null primary key not allowed.');
            }

            $this->$key = $pk[$key];
        }

        // Pre-processing by observers
        $event = AbstractEvent::create(
            'onTableBeforeDelete',
            [
                'subject'   => $this,
                'pk'        => $pk,
            ]
        );
        $this->getDispatcher()->dispatch('onTableBeforeDelete', $event);


        try{
            // Delete the row by primary key from tags_xref table
            $query = $this->_db->getQuery(true);
            $query->delete();
            $query->from($this -> _db -> quoteName('#__tz_portfolio_plus_tag_content_map')) ;
            $this->appendPrimaryKeys($query, $pk);
            $this ->_db->setQuery($query);

            $this -> _db -> execute();
        }catch (\InvalidArgumentException $e)
        {
            $this->setError(Text::sprintf('JLIB_DATABASE_ERROR_DELETE_FAILED', get_class($this), $e -> getMessage()));
            return false;
        }

        // If tracking assets, remove the asset first.
        if ($this->_trackAssets) {
            // Get the asset name
            $name  = $this->_getAssetName();

            /** @var Asset $asset */
            $asset = self::getInstance('Asset');

            if ($asset->loadByName($name)) {
                if (!$asset->delete()) {
                    $this->setError($asset->getError());

                    return false;
                }
            }
        }

        // Delete the row by primary key.
        $query = $this->_db->getQuery(true)
            ->delete($this->_tbl);
        $this->appendPrimaryKeys($query, $pk);

        $this->_db->setQuery($query);

        // Check for a database error.
        $this->_db->execute();

        // Post-processing by observers
        $event = AbstractEvent::create(
            'onTableAfterDelete',
            [
                'subject'   => $this,
                'pk'        => $pk,
            ]
        );
        $this->getDispatcher()->dispatch('onTableAfterDelete', $event);

        return true;
    }
}