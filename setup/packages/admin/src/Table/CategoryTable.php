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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Table;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Access\Rules;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Nested;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use Joomla\CMS\Application\ApplicationHelper;

/**
 * Category table
 *
 * @since  11.1
 */
class CategoryTable extends Nested
{
    /**
     * Constructor
     *
     * @param   DatabaseDriver  $db  Database driver object.
     *
     * @since   11.1
     */
    public function __construct($db, $dispatcher = null)
    {
        
        $this->typeAlias = '{extension}.category';
        parent::__construct('#__tz_portfolio_plus_categories', 'id', $db);
        $this->access = (int) Factory::getConfig()->get('access');
    }

    /**
     * Method to compute the default name of the asset.
     * The default name is in the form table_name.id
     * where id is the value of the primary key of the table.
     *
     * @return  string
     *
     * @since   11.1
     */
    protected function _getAssetName()
    {
        $k = $this->_tbl_key;

        return $this->extension . '.category.' . (int) $this->$k;
    }

    /**
     * Method to return the title to use for the asset table.
     *
     * @return  string
     *
     * @since   11.1
     */
    protected function _getAssetTitle()
    {
        return $this->title;
    }

    /**
     * Get the parent asset id for the record
     *
     * @param Table|null $table  A JTable object for the asset parent.
     * @param   integer  $id     The id for the asset
     *
     * @return  integer  The id of the asset's parent
     *
     * @since   11.1
     */
    protected function _getAssetParentId(?Table $table = null, $id = null): int
    {
        $assetId = null;

        // This is a category under a category.
        if ($this->parent_id > 1)
        {
            // Build the query to get the asset id for the parent category.
            $query = $this->_db->getQuery(true)
                ->select($this->_db->quoteName('asset_id'))
                ->from($this->_db->quoteName('#__tz_portfolio_plus_categories'))
                ->where($this->_db->quoteName('id') . ' = ' . $this->parent_id);

            // Get the asset id from the database.
            $this->_db->setQuery($query);

            if ($result = $this->_db->loadResult())
            {
                $assetId = (int) $result;
            }
        }
        // This is a category that needs to parent with the extension.
        elseif ($assetId === null)
        {
            // Build the query to get the asset id for the parent category.
            $query = $this->_db->getQuery(true)
                ->select($this->_db->quoteName('id'))
                ->from($this->_db->quoteName('#__assets'))
                ->where($this->_db->quoteName('name') . ' = ' . $this->_db->quote($this->extension.'.category'));

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

    /**
     * Override check function
     *
     * @return  boolean
     *
     * @see     JTable::check()
     * @since   11.1
     */
    public function check()
    {
        // Check for a title.
        if (trim($this->title) == '')
        {
            $this->setError(Text::_('JLIB_DATABASE_ERROR_MUSTCONTAIN_A_TITLE_CATEGORY'));

            return false;
        }

        $this->alias = trim($this->alias);

        if (empty($this->alias))
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

    /**
     * Overloaded bind function.
     *
     * @param   array   $array   named array
     * @param   string  $ignore  An optional array or space separated list of properties
     *                           to ignore while binding.
     *
     * @return  mixed   Null if operation was satisfactory, otherwise returns an error
     *
     * @see     JTable::bind()
     * @since   11.1
     */
    public function bind($array, $ignore = '')
    {
        if (isset($array['params']) && is_array($array['params']))
        {
            $registry = new Registry;
            $registry->loadArray($array['params']);
            $array['params'] = (string) $registry;
        }

        if (isset($array['metadata']) && is_array($array['metadata']))
        {
            $registry = new Registry;
            $registry->loadArray($array['metadata']);
            $array['metadata'] = (string) $registry;
        }

        // Bind the rules.
        if (isset($array['rules']) && is_array($array['rules']))
        {
            $rules = new Rules($array['rules']);
            $this->setRules($rules);
        }

        if(isset($array['groupid']) && !$array['groupid']){
            $array['groupid']   = 0;
        }

        return parent::bind($array, $ignore);
    }

    /**
     * Overridden JTable::store to set created/modified and user id.
     *
     * @param   boolean  $updateNulls  True to update fields even if they are null.
     *
     * @return  boolean  True on success.
     *
     * @since   11.1
     */
    public function store($updateNulls = false)
    {
        $date = Factory::getDate();
        $user = Factory::getUser();

        $this->modified_time = $date->toSql();

        if ($this->id)
        {
            // Existing category
            $this->modified_user_id = $user->get('id');
        }
        else
        {
            // New category
            $this->created_time = $date->toSql();
            $this->created_user_id = $user->get('id');
        }

        // Verify that the alias is unique
//        $table = Table::getInstance('Category', '\TemPlaza\Component\TZ_Portfolio\Administrator', array('dbo' => $this->getDbo()));
        $table = new CategoryTable($this->getDbo(), $this->getDispatcher());

        if ($table->load(array('alias' => $this->alias, 'parent_id' => $this->parent_id, 'extension' => $this->extension))
            && ($table->id != $this->id || $this->id == 0))
        {
            $this->setError(Text::_('JLIB_DATABASE_ERROR_CATEGORY_UNIQUE_ALIAS'));

            return false;
        }

        return parent::store($updateNulls);
    }

}
