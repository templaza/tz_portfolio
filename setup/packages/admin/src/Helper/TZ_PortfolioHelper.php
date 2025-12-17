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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Helper;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Table\Asset;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Path;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\Registry\Registry;

class TZ_PortfolioHelper  extends ContentHelper{

    protected static $cache    = array();

    public static function getActions($section = '', $id = 0, $parent_section = null)
    {
        $user	    = Factory::getUser();
        $result     = new CMSObject();
        $component  = 'com_tz_portfolio';

        $path = JPATH_ADMINISTRATOR . '/components/'.$component.'/access.xml';

        $assetName = $component;

        if ($section && $id)
        {
            $assetName  = $component . '.' . $section . '.' . (int) $id;
            $asset      = new Asset(Factory::getDbo(), Factory::getApplication() -> getDispatcher());

            if(!$asset -> loadByName($assetName)){
                $assetName  = $component . '.' . $parent_section;
            }
        }elseif (empty($id))
        {
            $assetName = $component . '.' . $section;
        }

        $actions = Access::getActionsFromFile($path, "/access/section[@name='component']/");
//        $actions = Access::getActionsFromFile($path, "/access/section[@name='".(!empty($section)?$section:'component')."']/");

        if ($actions === false)
        {
            Log::add(
                Text::sprintf('JLIB_ERROR_COMPONENTS_ACL_CONFIGURATION_FILE_MISSING_OR_IMPROPERLY_STRUCTURED', $component), Log::ERROR, 'jerror'
            );

            return $result;
        }

        foreach ($actions as $action)
        {
            $result->set($action->name, $user->authorise($action->name, $assetName));
        }

        return $result;
    }

    public static function getDataFromServer($url = null, $method = 'get'){

        $url        = trim($url);
        $storeId    = __METHOD__.'::'.$url;
        $storeId   .= '::'.$method;
        $storeId    = md5($storeId);

        if(!isset(self::$cache[$storeId])){
            self::$cache[$storeId]  = false;
        }

        if($url){
            if($data = static::__getDataFromServer($url, $method)){
                return self::$cache[$storeId]   = $data;
            }
        }
        return self::$cache[$storeId];
    }

    public static function checkConnectServer($url, $method = 'get'){

        $url    = trim($url);

        $store  = __METHOD__.'::'.$url;
        $store .= '::'.$method;
        $store  = md5($store);
        $store2 = __CLASS__.'::__getDataFromServer::'.$url;
        $store2.= '::'.$method;
        $store2 = md5($store2);

        if(!isset(self::$cache[$store])){
            self::$cache[$store]    = false;
        }

        try {
            if($method == 'post'){
                $response = HttpFactory::getHttp()->post($url, array());
            }else {
                $response = HttpFactory::getHttp()->get($url, array());
            }
            self::$cache[$store2]   = $response;
        }
        catch (\RuntimeException $exception){

            self::$cache[$store]    = false;
            Log::add(Text::sprintf('JLIB_INSTALLER_ERROR_DOWNLOAD_SERVER_CONNECT', $exception->getMessage()),
                Log::WARNING, 'jerror');

            return false;
        }

        if (200 == $response->code)
        {
            self::$cache[$store]    = true;
        }

        return self::$cache[$store];
    }

    public static function introGuideSkipped($view){
        if(!$view){
            return false;
        }

        $filePath   = Path::clean(COM_TZ_PORTFOLIO_ADMIN_PATH.'/cache'.'/introguide.json');

        if(!file_exists($filePath)){
            return false;
        }

        $introGuide = file_get_contents($filePath);
        $introGuide = json_decode($introGuide);
        if($introGuide && isset($introGuide -> {$view}) && $introGuide -> {$view}) {
            return true;
        }

        return false;
    }

    /**
     *  Get license info
     *  @return object|array|bool
     * */
    public static function getLicense(){

        $storeId    = __METHOD__;

        $file    = JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/includes/license.php';

        $storeId   .= ':'.$file;
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        if(file_exists($file)){
            $license    = @file_get_contents($file);
            $license    = str_replace('<?php die("Access Denied"); ?>#x#', '', $license);
            $license    = unserialize(trim($license));

            if(!empty($license)){
                $license -> isExpired           = static::isLicenseExpired('expire', $license);
                $license -> isSupportExpired    = static::isLicenseExpired('support_expire', $license);
            }

            self::$cache[$storeId]  = $license;

            return $license;
        }

        return false;
    }

    /**
     *  Check license expired
     *  @return bool
     * */
    public static function isLicenseExpired($type, $license = null){

        $license    = !empty($license)?$license:static::getLicense();

        $storeId    = __METHOD__;
        $storeId   .= ':'.$type;
        $storeId   .= ':'.serialize($license);
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        if($license){
            $nowDate    = Factory::getDate() -> toSql();
            if($license -> {$type} < $nowDate){
                return true;
            }
        }
        return false;
    }

    /**
     * Get xml manifest
     * @return bool|object
     * */
    public static function getXMLManifest($comp = 'tz_portfolio'){

            $storeId    = md5(__METHOD__.'::'.$comp);

            $file   = Path::clean(COM_TZ_PORTFOLIO_ADMIN_PATH.'/'.$comp.'.xml');

            if(!file_exists($file)){
                return false;
            }

            if($xml = simplexml_load_file($file, 'SimpleXMLElement', LIBXML_NOCDATA)){
                return static::$cache[$storeId] = $xml;
            }

            return false;
    }

    public static function jsAddSlashes($s)
    {
        $o="";
        $l=strlen($s);
        for($i=0;$i<$l;$i++)
        {
            $c=$s[$i];
            switch($c)
            {
                case '<': $o.='\\x3C'; break;
                case '>': $o.='\\x3E'; break;
                case '\'': $o.='\\\''; break;
                case '\\': $o.='\\\\'; break;
                case '"':  $o.='\\"'; break;
                case "\n": $o.='\\n'; break;
                case "\r": $o.='\\r'; break;
                default:
                    $o.=$c;
            }
        }
        return $o;
    }

    public static function getMenuLinks($menuType = null, $parentId = 0, $mode = 0, $published = array(), $languages = array())
    {
        $db     = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('a.id AS value, a.title AS text, a.alias, a.level, a.component_id,'
                .' a.menutype, a.type, a.template_style_id, a.checked_out, a.params')
            ->from('#__menu AS a')
            ->join('LEFT', $db->quoteName('#__menu') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt')
            -> join('LEFT', $db -> quoteName('#__extensions').' AS e ON e.extension_id = a.component_id')
            -> where('e.name='.$db -> quote('com_tz_portfolio'));

        // Filter by the type
        if ($menuType)
        {
            $query->where('(a.menutype = ' . $db->quote($menuType) . ' OR a.parent_id = 0)');
        }

        if ($parentId)
        {
            if ($mode == 2)
            {
                // Prevent the parent and children from showing.
                $query->join('LEFT', '#__menu AS p ON p.id = ' . (int) $parentId)
                    ->where('(a.lft <= p.lft OR a.rgt >= p.rgt)');
            }
        }

        if (!empty($languages))
        {
            if (is_array($languages))
            {
                $languages = '(' . implode(',', array_map(array($db, 'quote'), $languages)) . ')';
            }

            $query->where('a.language IN ' . $languages);
        }

        if (!empty($published))
        {
            if (is_array($published))
            {
                $published = '(' . implode(',', $published) . ')';
            }

            $query->where('a.published IN ' . $published);
        }

        $query->where('a.published != -2')
            ->group('a.id, a.title, a.alias, a.level, a.menutype, a.type,a.template_style_id')
            ->group('a.checked_out, a.lft, a.component_id, a.params')
            ->order('a.lft ASC');

        // Get the options.
        $db->setQuery($query);

        try
        {
            $links = $db->loadObjectList();
        }
        catch (\RuntimeException $e)
        {
            Factory::getApplication() -> enqueueMessage($e->getMessage(), 'error');

            return false;
        }

        if (empty($menuType))
        {
            // If the menutype is empty, group the items by menutype.
            $query->clear()
                ->select('*')
                ->from('#__menu_types')
                ->where('menutype <> ' . $db->quote(''))
                ->order('title, menutype');
            $db->setQuery($query);

            try
            {
                $menuTypes = $db->loadObjectList();
            }
            catch (\RuntimeException $e)
            {
                Factory::getApplication() -> enqueueMessage($e->getMessage(), 'error');

                return false;
            }

            // Create a reverse lookup and aggregate the links.
            $rlu = array();

            foreach ($menuTypes as &$type)
            {
                $rlu[$type->menutype] = & $type;
                $type->links = array();
            }

            // Loop through the list of menu links.
            foreach ($links as $i => &$link)
            {
                $registry       = new Registry($link -> params);
                $link -> params = $registry;
                if (isset($rlu[$link->menutype]))
                {
                    $rlu[$link->menutype]->links[] = &$link;

                    // Cleanup garbage.
                    unset($link->menutype);
                }
            }

            // Remove all menus group don't have menu items
            if(count($menuTypes)){
                foreach($menuTypes as $i => $item){
                    if(!$item -> links || ($item -> links && !count($item -> links))){
                        unset($menuTypes[$i]);
                    }
                }
            }

            return $menuTypes;
        }
        else
        {
            return $links;
        }
    }

    protected static function __getDataFromServer($url, $method = 'get'){
        $storeId    = __METHOD__;
        $storeId   .= '::'.$url;
        $storeId   .= '::'.$method;
        $storeId    = md5($storeId);

        if(isset(static::$cache[$storeId])){
            return static::$cache[$storeId];
        }

        if($connected = static::checkConnectServer($url, $method)){
            return static::$cache[$storeId];
        }

        return false;
    }
}