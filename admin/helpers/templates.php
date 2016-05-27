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
defined('_JEXEC') or die;

class TZ_Portfolio_PlusHelperTemplates{
    public static function getTemplateOptions()
    {
        // Build the filter options.
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('element as value, name as text, id as e_id')
            ->from('#__tz_portfolio_plus_extensions')
            ->where('type = ' . $db->quote('tz_portfolio_plus-template'))
            ->where('published = 1')
            ->order('name');
        $db->setQuery($query);
        $options = $db->loadObjectList();

        return $options;
    }
}