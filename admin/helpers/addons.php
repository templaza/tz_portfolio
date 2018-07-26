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

use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

class TZ_Portfolio_PlusHelperAddons{
    public static function folderOptions()
    {
        $db = TZ_Portfolio_PlusDatabase::getDbo();

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

    public static function getAddons($options = array()){
        $db     = TZ_Portfolio_PlusDatabase::getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('e.*');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_extensions').' AS e');

        $query -> where('type = '.$db -> quote('tz_portfolio_plus-plugin'));

        if(count($options)){
            if(isset($options['published'])){
                if(is_array($options['published'])) {
                    $query->where('published IN('.implode($options['published']).')');
                }else{
                    $query -> where('published='.$options['published']);
                }
            }else{
                $query -> where('published = 0 OR published = 1');
            }
            if(isset($options['protected'])){
                if(is_array($options['protected'])) {
                    $query->where('protected IN('.implode($options['protected']).')');
                }else{
                    $query -> where('protected='.$options['protected']);
                }
            }
            if(isset($options['folder']) && $options['folder']){
                $query -> where('folder='.$db -> quote($options['folder']));
            }
        }
        $db -> setQuery($query);
        if($data = $db -> loadObjectList()){
            return $data;
        }
        return false;
    }
}