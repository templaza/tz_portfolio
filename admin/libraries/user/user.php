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

use Joomla\Registry\Registry;

jimport('joomla.user.user');

class TZ_Portfolio_PlusUser extends JUser{

    public static function getUser($id = null)
    {
        $instance = JFactory::getSession()->get('user');

        if (is_null($id))
        {
            if (!($instance instanceof TZ_Portfolio_PlusUser))
            {
                if($instance instanceof JUser) {
                    $instance   = self::getInstance($instance->id);
                }else{
                    $instance   = self::getInstance();
                }
            }
        }
        // Check if we have a string as the id or if the numeric id is the current instance
        elseif (is_string($id) || $instance->id !== $id)
        {
            $instance = self::getInstance($id);
        }

        return $instance;
    }

    public static function getInstance($identifier = 0, JUserWrapperHelper $userHelper = null)
    {
        if (null === $userHelper)
        {
            if(class_exists('JUserWrapperHelper')) {
                $userHelper = new JUserWrapperHelper;
            }else{
                $userHelper = null;
            }
        }

        // Find the user id
        if (!is_numeric($identifier))
        {
            $id = null;
            if($userHelper){
                $id = $userHelper->getUserId($identifier);
            }else{
                $id = JUserHelper::getUserId($identifier);
            }
            if (!$id)
            {
                // If the $identifier doesn't match with any id, just return an empty JUser.
                return new TZ_Portfolio_PlusUser;
            }
        }
        else
        {
            $id = $identifier;
        }

        // If the $id is zero, just return an empty JUser.
        // Note: don't cache this user because it'll have a new ID on save!
        if ($id === 0)
        {
            return new TZ_Portfolio_PlusUser;
        }

        // Check if the user ID is already cached.
        if (empty(self::$instances[$id]))
        {
            $user = new TZ_Portfolio_PlusUser($id, $userHelper);
            self::$instances[$id] = $user;
        }

        return self::$instances[$id];
    }

    public function getAuthorisedCategories($component = 'com_tz_portfolio_plus', $action)
    {
        // Brute force method: get all published category rows for the component and check each one
        // TODO: Modify the way permissions are stored in the db to allow for faster implementation and better scaling
        $db = JFactory::getDbo();

        $subQuery = $db->getQuery(true)
            ->select('id,asset_id')
            ->from('#__tz_portfolio_plus_categories')
            ->where('extension = ' . $db->quote($component))
            ->where('published = 1');

        $query = $db->getQuery(true)
            ->select('c.id AS id, a.name AS asset_name')
            ->from('(' . $subQuery->__toString() . ') AS c')
            ->join('INNER', '#__assets AS a ON c.asset_id = a.id');
        $db->setQuery($query);
        $allCategories = $db->loadObjectList('id');
        $allowedCategories = array();

        foreach ($allCategories as $category)
        {
            if ($this->authorise($action, $category->asset_name))
            {
                $allowedCategories[] = (int) $category->id;
            }
        }

        return $allowedCategories;
    }
}