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

// No direct access
defined('JPATH_PLATFORM') or die;

use Joomla\Registry\Registry;


/**
 * Content table
 *
 * @since       11.1
 * @deprecated  Class will be removed upon completion of transition to UCM
 */
class TZ_Portfolio_PlusTableContent extends JTable
{
    var $catid  = null;
    /**
     * Constructor
     *
     * @param   JDatabaseDriver  $db  A database connector object
     *
     * @since   11.1
     */
    public function __construct(JDatabaseDriver $db)
    {
        parent::__construct('#__tz_portfolio_plus_content', 'id', $db);

//        JTableObserverTags::createObserver($this, array('typeAlias' => 'com_tz_portfolio_plus.article'));
//        JTableObserverContenthistory::createObserver($this, array('typeAlias' => 'com_tz_portfolio_plus.article'));

        // Set the alias since the column is called state
        $this->setColumnAlias('published', 'state');
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

        return 'com_tz_portfolio_plus.article.' . (int) $this->$k;
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
     * Method to get the parent asset id for the record
     *
     * @param   JTable   $table  A JTable object (optional) for the asset parent
     * @param   integer  $id     The id (optional) of the content.
     *
     * @return  integer
     *
     * @since   11.1
     */
//    protected function _getAssetParentId(JTable $table = null, $id = null)
//    {
//        $assetId = null;
//
//        // This is a article under a category.
//        if ($this->catid)
//        {
//            // Build the query to get the asset id for the parent category.
//            $query = $this->_db->getQuery(true)
//                ->select($this->_db->quoteName('asset_id'))
//                ->from($this->_db->quoteName('#__tz_portfolio_plus_categories'))
//                ->where($this->_db->quoteName('id') . ' = ' . (int) $this->catid);
//
//            // Get the asset id from the database.
//            $this->_db->setQuery($query);
//
//            if ($result = $this->_db->loadResult())
//            {
//                $assetId = (int) $result;
//            }
//        }
//
//        // Return the asset id.
//        if ($assetId)
//        {
//            return $assetId;
//        }
//        else
//        {
//            return parent::_getAssetParentId($table, $id);
//        }
//    }

    /**
     * Overloaded bind function
     *
     * @param   array  $array   Named array
     * @param   mixed  $ignore  An optional array or space separated list of properties
     *                          to ignore while binding.
     *
     * @return  mixed  Null if operation was satisfactory, otherwise returns an error string
     *
     * @see     JTable::bind()
     * @since   11.1
     */
    public function bind($array, $ignore = '')
    {
        // Search for the {readmore} tag and split the text up accordingly.
        if (isset($array['articletext']))
        {
            $pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
            $tagPos = preg_match($pattern, $array['articletext']);

            if ($tagPos == 0)
            {
                $this->introtext = $array['articletext'];
                $this->fulltext = '';
            }
            else
            {
                list ($this->introtext, $this->fulltext) = preg_split($pattern, $array['articletext'], 2);
            }
        }

        if (isset($array['attribs']) && is_array($array['attribs']))
        {
            $registry = new Registry;
            $registry->loadArray($array['attribs']);
            $array['attribs'] = (string) $registry;
        }

        if (isset($array['media']) && is_array($array['media']))
        {
            $registry = new Registry;
            $registry->loadArray($array['media']);
            $array['media'] = (string) $registry;
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
            $rules = new JAccessRules($array['rules']);
            $this->setRules($rules);
        }

        return parent::bind($array, $ignore);
    }

    /**
     * Overloaded check function
     *
     * @return  boolean  True on success, false on failure
     *
     * @see     JTable::check()
     * @since   11.1
     */
    public function check()
    {
        if (trim($this->title) == '')
        {
            $this->setError(JText::_('COM_CONTENT_WARNING_PROVIDE_VALID_NAME'));

            return false;
        }

        if (trim($this->alias) == '')
        {
            $this->alias = $this->title;
        }

        $this->alias = JApplicationHelper::stringURLSafe($this->alias);

        if (trim(str_replace('-', '', $this->alias)) == '')
        {
            $this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
        }

        if (trim(str_replace('&nbsp;', '', $this->fulltext)) == '')
        {
            $this->fulltext = '';
        }

        /**
         * Ensure any new items have compulsory fields set. This is needed for things like
         * frontend editing where we don't show all the fields or using some kind of API
         */
        if (!$this->id)
        {
            // Images can be an empty json string
            if (!isset($this->images))
            {
                $this->images = '{}';
            }

            // URLs can be an empty json string
            if (!isset($this->urls))
            {
                $this->urls = '{}';
            }

            // Attributes (article params) can be an empty json string
            if (!isset($this->attribs))
            {
                $this->attribs = '{}';
            }

            // Media (article media) can be an empty json string
            if (!isset($this->media))
            {
                $this->media = '{}';
            }

            // Metadata can be an empty json string
            if (!isset($this->metadata))
            {
                $this->metadata = '{}';
            }

            // If we don't have any access rules set at this point just use an empty JAccessRules class
            if (!$this->getRules())
            {
                $rules = $this->getDefaultAssetValues('com_tz_portfolio_plus');
                $this->setRules($rules);
            }
        }

        // Check the publish down date is not earlier than publish up.
        if ($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up)
        {
            // Swap the dates.
            $temp = $this->publish_up;
            $this->publish_up = $this->publish_down;
            $this->publish_down = $temp;
        }

        // Clean up keywords -- eliminate extra spaces between phrases
        // and cr (\r) and lf (\n) characters from string
        if (!empty($this->metakey))
        {
            // Only process if not empty

            // Array of characters to remove
            $bad_characters = array("\n", "\r", "\"", "<", ">");

            // Remove bad characters
            $after_clean = JString::str_ireplace($bad_characters, "", $this->metakey);

            // Create array using commas as delimiter
            $keys = explode(',', $after_clean);

            $clean_keys = array();

            foreach ($keys as $key)
            {
                if (trim($key))
                {
                    // Ignore blank keywords
                    $clean_keys[] = trim($key);
                }
            }
            // Put array back together delimited by ", "
            $this->metakey = implode(", ", $clean_keys);
        }

        return true;
    }

    public function load($keys = null, $reset = true)
    {
        // Implement JObservableInterface: Pre-processing by observers
        $this->_observers->update('onBeforeLoad', array($keys, $reset));

        if (empty($keys))
        {
            $empty = true;
            $keys  = array();

            // If empty, use the value of the current key
            foreach ($this->_tbl_keys as $key)
            {
                $empty      = $empty && empty($this->$key);
                $keys[$key] = $this->$key;
            }

            // If empty primary key there's is no need to load anything
            if ($empty)
            {
                return true;
            }
        }
        elseif (!is_array($keys))
        {
            // Load by primary key.
            $keyCount = count($this->_tbl_keys);

            if ($keyCount)
            {
                if ($keyCount > 1)
                {
                    throw new InvalidArgumentException('Table has multiple primary keys specified, only one primary key value provided.');
                }

                $keys = array($this->getKeyName() => $keys);
            }
            else
            {
                throw new RuntimeException('No table keys defined.');
            }
        }

        if ($reset)
        {
            $this->reset();
        }

        // Initialise the query.
        $query = $this->_db->getQuery(true)
            ->select('c.*, m.catid')
            ->from($this->_tbl.' AS c');

        $query -> join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.contentid = c.id AND m.main = 1');

        $fields = array_keys($this->getProperties());

        foreach ($keys as $field => $value)
        {
            // Check that $field is in the table.
            if (!in_array($field, $fields) && $field != 'catid')
            {
                throw new UnexpectedValueException(sprintf('Missing field in database: %s &#160; %s.', get_class($this), $field));
            }
            // Add the search tuple to the query.
            if($field == 'catid'){
                $query->where('m.' . $this->_db->quoteName($field) . ' = ' . $this->_db->quote($value));
            }else {
                $query->where('c.' . $this->_db->quoteName($field) . ' = ' . $this->_db->quote($value));
            }
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

        // Implement JObservableInterface: Post-processing by observers
        $this->_observers->update('onAfterLoad', array(&$result, $row));

        return $result;
    }

    public function reorder($where = '')
    {
        // If there is no ordering field set an error and return false.
        if (!property_exists($this, 'ordering'))
        {
            throw new UnexpectedValueException(sprintf('%s does not support ordering.', get_class($this)));
        }

        $k = $this->_tbl_key;

        $tbl_keys   = array();
        if(is_array($this -> _tbl_keys)){
            foreach($this -> _tbl_keys as $key){
                $tbl_keys[] = 'c.'.$key;
            }
        }

        // Get the primary keys and ordering values for the selection.
        $query = $this->_db->getQuery(true)
            ->select(implode(',', $tbl_keys) . ', c.ordering, m.catid')
            ->from($this->_tbl.' AS c')
            ->join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.contentid = c.id')
            ->where('c.ordering >= 0')
            ->where('m.main = 1')
            ->order('c.ordering');

        // Setup the extra where and ordering clause data.
        if ($where)
        {
            $query->where($where);
        }

        $this->_db->setQuery($query);
        $rows = $this->_db->loadObjectList();

        // Compact the ordering values.
        foreach ($rows as $i => $row)
        {
            // Make sure the ordering is a positive integer.
            if ($row->ordering >= 0)
            {
                // Only update rows that are necessary.
                if ($row->ordering != $i + 1)
                {
                    // Update the row ordering field.
                    $query->clear()
                        ->update($this->_tbl)
                        ->set('ordering = ' . ($i + 1));
                    $this->appendPrimaryKeys($query, $row);
                    $this->_db->setQuery($query);
                    $this->_db->execute();
                }
            }
        }

        return true;
    }

    /**
     * Gets the default asset values for a component.
     *
     * @param   $string  $component  The component asset name to search for
     *
     * @return  JAccessRules  The JAccessRules object for the asset
     */
    protected function getDefaultAssetValues($component)
    {
        // Need to find the asset id by the name of the component.
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__assets'))
            ->where($db->quoteName('name') . ' = ' . $db->quote($component));
        $db->setQuery($query);
        $assetId = (int) $db->loadResult();

        return JAccess::getAssetRules($assetId);
    }

    /**
     * Overrides JTable::store to set modified data and user id.
     *
     * @param   boolean  $updateNulls  True to update fields even if they are null.
     *
     * @return  boolean  True on success.
     *
     * @since   11.1
     */
    public function store($updateNulls = false)
    {
        $date = JFactory::getDate();
        $user = JFactory::getUser();

        $this->modified = $date->toSql();

        if ($this->id)
        {
            // Existing item
            $this->modified_by = $user->get('id');
        }
        else
        {
            // New article. An article created and created_by field can be set by the user,
            // so we don't touch either of these if they are set.
            if (!(int) $this->created)
            {
                $this->created = $date->toSql();
            }

            if (empty($this->created_by))
            {
                $this->created_by = $user->get('id');
            }
        }

        if(isset($this -> catid)){
            unset($this -> catid);
        }

        return parent::store($updateNulls);
    }


}
