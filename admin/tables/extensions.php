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

class TZ_Portfolio_PlusTableExtensions extends JTable
{
    function __construct(&$db)
    {
        parent::__construct('#__tz_portfolio_plus_extensions', 'id', $db);
    }

    public function find($options = array())
    {
        // Get the JDatabaseQuery object
        $query = $this->_db->getQuery(true);

        foreach ($options as $col => $val)
        {
            $query->where($col . ' = ' . $this->_db->quote($val));
        }

        $query->select($this->_db->quoteName('id'))
            ->from($this->_db->quoteName('#__tz_portfolio_plus_extensions'));
        $this->_db->setQuery($query);

        return $this->_db->loadResult();
    }
}
?>