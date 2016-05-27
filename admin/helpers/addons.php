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

class TZ_Portfolio_PlusHelperAddons{
    public static function folderOptions()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('DISTINCT(folder) AS value, folder AS text')
            ->from('#__tz_portfolio_plus_extensions')
            ->where($db->quoteName('type') . ' = ' . $db->quote('tz_portfolio_plus-plugin'))
            ->order('folder');

        $db->setQuery($query);

        try
        {
            $options = $db->loadObjectList();
        }
        catch (RuntimeException $e)
        {
            JFactory::getApplication()  -> enqueueMessage($e->getMessage(), 'error');
        }

        return $options;
    }


}