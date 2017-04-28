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

use Joomla\Registry\Registry;

class TZ_Portfolio_PlusHelperCategories
{
	protected static $cache	= array();
	/**
	 * Configure the Submenu links.
	 *
	 * @param	string	The extension being used for the categories.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function addSubmenu($extension)
	{
//		// Avoid nonsense situation.
//		if ($extension == 'com_categories') {
//			return;
//		}

		$parts = explode('.', $extension);
		$component = $parts[0];

		if (count($parts) > 1) {
			$section = $parts[1];
		}

		// Try to find the component helper.
		$eName	= str_replace('com_', '', $component);
		$file	= JPath::clean(JPATH_ADMINISTRATOR.'/components/'.$component.'/helpers/'.$eName.'.php');

		if (file_exists($file)) {
			require_once $file;

			$prefix	= ucfirst(str_replace('com_', '', $component));
			$cName	= $prefix.'Helper';

			if (class_exists($cName)) {

				if (is_callable(array($cName, 'addSubmenu'))) {
					$lang = JFactory::getLanguage();
					// loading language file from the administrator/language directory then
					// loading language file from the administrator/components/*extension*/language directory
						$lang->load($component, JPATH_BASE, null, false, false)
					||	$lang->load($component, JPath::clean(JPATH_ADMINISTRATOR.'/components/'.$component), null, false, false)
					||	$lang->load($component, JPATH_BASE, $lang->getDefault(), false, false)
					||	$lang->load($component, JPath::clean(JPATH_ADMINISTRATOR.'/components/'.$component), $lang->getDefault(), false, false);
 					call_user_func(array($cName, 'addSubmenu'), 'categories'.(isset($section)?'.'.$section:''));
				}
			}
		}
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	string	$extension	The extension.
	 * @param	int		$categoryId	The category ID.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions($extension, $categoryId = 0)
	{
		$user		= JFactory::getUser();
		$result		= new JObject;
		$parts		= explode('.', $extension);
		$component	= $parts[0];

		if (empty($categoryId)) {
			$assetName = $component;
		}
		else {
			$assetName = $component.'.category.'.(int) $categoryId;
		}

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}

    public static function getAssociations($pk, $extension = 'com_tz_portfolio_plus')
    {
        $associations = array();
        $db = JFactory::getDbo();

        $query = $db->getQuery(true)
            ->from('#__tz_portfolio_plus_categories as c')
            ->join('INNER', '#__associations as a ON a.id = c.id AND a.context=' . $db->quote('com_tz_portfolio_plus.categories.item'))
            ->join('INNER', '#__associations as a2 ON a.key = a2.key')
            ->join('INNER', '#__tz_portfolio_plus_categories as c2 ON a2.id = c2.id AND c2.extension = ' . $db->quote($extension))
            ->where('c.id =' . (int)$pk)
            ->where('c.extension = ' . $db->quote($extension));

        $select = array(
            'c2.language',
            $query->concatenate(array('c2.id', 'c2.alias'), ':') . ' AS id'
        );
        $query->select($select);
        $db->setQuery($query);
        $contentitems = $db->loadObjectList('language');

        // Check for a database error.
        if ($error = $db->getErrorMsg())
        {
            JFactory::getApplication()  -> enqueueMessage($error, 'error');

            return false;
        }

        foreach ($contentitems as $tag => $item)
        {
            // Do not return itself as result
            if ((int) $item->id != $pk)
            {
                $associations[$tag] = $item->id;
            }
        }

        return $associations;
    }

	public static function getCategoriesById($catid, $options = array()){
		if($catid) {
			if(is_array($catid)) {
				$storeId = md5(__METHOD__ . '::'.implode(',', $catid).'::'.implode(',',$options));
			}else{
				$storeId = md5(__METHOD__ . '::'.$catid.'::'.implode(',',$options));
			}

			if(!isset(self::$cache[$storeId])){
				$db     =  JFactory::getDbo();
				$query  =  $db -> getQuery(true);
				$query  -> select('*');
				$query  -> from('#__tz_portfolio_plus_categories');

				if(is_array($catid)) {
					$query -> where('cc.id IN('.implode(',', $catid) .')');
				}else{
					$query -> where('cc.id = '.$catid);
				}

				if(count($options)){
					if(isset($options['orderby'])){
						if(!empty($order)) {
							$query->order($options['orderby']);
						}
					}
				}

				$db -> setQuery($query);
				if($categories = $db -> loadObjectList()){
					self::$cache[$storeId]  = $categories;
					return $categories;
				}

				self::$cache[$storeId]  = false;
			}

			return self::$cache[$storeId];
		}
		return false;
	}

	public static function getCategoriesByArticleId($articleId, $main = false, $options = array()){
		if($articleId) {
			$_options	= '';
			if(count($options)) {
				$_options = new Registry();
				$_options -> loadArray($options);
				$_options	= $_options -> toString('ini');
			}
			if(is_array($articleId)) {
				$storeId = md5(__METHOD__ . '::'.implode(',', $articleId).'::'.$main.'::'.$_options);
			}else{
				$storeId = md5(__METHOD__ . '::'.$articleId.'::'.$main.'::'.$_options);
			}

			if(!isset(self::$cache[$storeId])){
				$db     =  JFactory::getDbo();
				$query  =  $db -> getQuery(true);
				$query  -> select('c.*');
				$query  -> from('#__tz_portfolio_plus_categories AS c');
				$query  -> join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.catid = c.id');
				$query  -> join('INNER', '#__tz_portfolio_plus_content AS cc ON cc.id = m.contentid');

				if(is_array($articleId)) {
					$query -> where('cc.id IN('.implode(',', $articleId) .')');
				}else{
					$query -> where('cc.id = '.$articleId);
				}

				if($main){
					$query -> where('m.main = 1');
				}else{
					$query -> where('m.main = 0');
				}

				if(count($options)){
					if(isset($options['condition']) && $options['condition']){
						$query -> where($options['condition']);
					}
					if(isset($options['orderby']) && isset($options['orderby'])){
						$query->order($options['orderby']);
					}
				}

				$query -> group('c.id');

				$db -> setQuery($query);
				if($categories = $db -> loadObjectList()){
					if($main){
						$categories	= array_shift($categories);
					}
					self::$cache[$storeId]  = $categories;
					return $categories;
				}

				self::$cache[$storeId]  = false;
			}

			return self::$cache[$storeId];
		}
		return false;
	}

	public static function getMainCategoryByArticleId($articleId){

		$storeId	= md5(__METHOD__.'::'.$articleId);

		if($articleId){
			if(!isset(self::$cache[$storeId])){
				if($articleId){
					$db		= JFactory::getDbo();
					$query	= $db -> getQuery(true);
					$query -> select('c.*');
					$query -> from('#__tz_portfolio_plus_categories AS c');
					$query -> join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.catid = c.id');
					$query -> join('LEFT', '#__tz_portfolio_plus_content AS cc ON cc.id = m.contentid');
					if(is_array($articleId)){
						$query -> where('cc.id IN('.implode(',',$articleId).')');
					}else{
						$query -> where('cc.id = '.$articleId);
					}
					$query -> where('m.main = 1');

					$db -> setQuery($query);
					if($items = $db -> loadObjectList()){
						self::$cache[$storeId]	= $items;
						return self::$cache[$storeId];
					}
				}

				self::$cache[$storeId]	= false;
			}
		}
		return self::$cache[$storeId];
	}

	public static function resetCache(){
		if(count(self::$cache)){
			self::$cache	= array();
		}
		return true;
	}

	public static function getCategoriesByGroupId($groupid){
		if(!$groupid){
			return false;
		}
		$storeId	= __METHOD__;
		if(is_array($groupid) || is_object($groupid)){
			$storeId	.= '::'.json_encode($groupid);
		}else{
			$storeId	.= '::'.$groupid;
		}

		$storeId	= md5($storeId);

		if(!isset(self::$cache[$storeId])) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query -> select('c.*');
			$query -> from('#__tz_portfolio_plus_categories AS c');
			$query -> join('INNER', '#__tz_portfolio_plus_fieldgroups AS fg ON c.groupid = fg.id');
			if (is_numeric($groupid)) {
				$query -> where('fg.id = '.$groupid);
			}elseif(is_array($groupid) && count($groupid)){
				$query -> where('fg.id IN('.implode(',', $groupid).')');
			}

			$db -> setQuery($query);
			if($data = $db -> loadObjectList()){
				self::$cache[$storeId]	= $data;
				return $data;
			}
			self::$cache[$storeId]	= false;
		}
		return self::$cache[$storeId];
	}
}
