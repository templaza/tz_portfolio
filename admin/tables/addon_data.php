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

class TZ_Portfolio_PlusTableAddon_Data extends JTable
{
    public function __construct(JDatabaseDriver $db)
    {
        parent::__construct('#__tz_portfolio_plus_addon_data', 'id', $db);
    }


    public function bind($array, $ignore = '')
    {
        // Search for the {readmore} tag and split the text up accordingly.
        if (isset($array['value']) && is_array($array['value']))
        {
            $registry = new Registry;
            $registry->loadArray($array['value']);
            $array['value'] = (string) $registry;

        }

        return parent::bind($array, $ignore);
    }
}
