<?php
/**
 * Created by PhpStorm.
 * User: Ngoc Tu
 * Date: 11/26/2015
 * Time: 12:01 PM
 */

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