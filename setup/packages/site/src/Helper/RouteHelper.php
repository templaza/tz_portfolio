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

namespace TemPlaza\Component\TZ_Portfolio\Site\Helper;

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Categories\CategoryNode;
use Joomla\CMS\Component\ComponentHelper;

/**
 * TZ Portfolio Component Route Helper
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since 1.5
 */
abstract class RouteHelper
{
    protected static $lookup = array();
    protected static $catid_bool    = array();
    protected static $views         = array();

    protected static $lang_lookup = array();

    /**
     * @param	int	The route of the content item
     */
    public static function getArticleRoute($id, $catid = 0, $language = 0)
    {
        $needles = array(
            'article'  => array((int) $id)
        );

        //Create the link
        $link = 'index.php?option=com_tz_portfolio&view=article&id='. $id;
        if ((int)$catid > 1)
        {
            $categories = Categories::getInstance('TZ_Portfolio');
            if($categories){
                $category   = $categories -> get((int)$catid);

                if($category)
                {
                    $needles['portfolio']    = array_reverse($category->getPath());
                    $needles['categories']  = $needles['portfolio'];
                    $needles['date']        = $needles['portfolio'];
                    $link .= '&catid='.$catid;
                }
            }
        }

        if ($language && $language != "*" && Multilanguage::isEnabled())
        {
            self::buildLanguageLookup();

            if (isset(self::$lang_lookup[$language]))
            {
                $link .= '&lang=' . self::$lang_lookup[$language];
                $needles['language'] = $language;
            }
        }

        if ($item = self::_findItem($needles)) {
            $link .= '&Itemid='.$item;
        }
        elseif ($item = self::_findItem()) {
            $link .= '&Itemid='.$item;
        }

        return $link;
    }

    public static function getCategoryRoute($catid, $language = 0)
    {
        if ($catid instanceof CategoryNode)
        {
            $id       = $catid->id;
            $category = $catid;
        }
        else
        {
            $id       = (int) $catid;
            $category = Categories::getInstance('TZ_Portfolio');
            $category = $category->get($id);
        }

        if ($id < 1 || !($category instanceof CategoryNode))
        {
            $link = '';
        }
        else
        {

            $needles    = array();
            $link       = 'index.php?option=com_tz_portfolio';
            $_catids[]  = $category -> id.':'.$category -> alias;

            // Remove parent categories
            $catids     = $_catids;

            $needles['portfolio']   = $catids;
            $needles['date']        = $catids;
            $needles['categories']  = $catids;

            if ($language && $language != "*" && Multilanguage::isEnabled())
            {
                self::buildLanguageLookup();

                if(isset(self::$lang_lookup[$language]))
                {
                    $link .= '&amp;lang=' . self::$lang_lookup[$language];
                    $needles['language'] = $language;
                }
            }

            if ($itemId = self::_findItem($needles))
            {
                // Assign view
                if(isset(self::$views[$id])){
                    $link .= '&amp;view='.self::$views[$id];
                }else{
                    $link .= '&amp;view=portfolio';
                }
                // Assign catid if don't have menu publish

                $app		= Factory::getApplication();
                $menus		= $app->getMenu('site');
                $menu       = $menus -> getItem($itemId);
                if($m_catids   = $menu -> getParams() -> get('catid')){
                    $m_catids   = array_filter($m_catids);
                    if(count($m_catids) != 1 || (count($m_catids) == 1 && !in_array($catid, $m_catids))){
                        $link   .= '&amp;id='.$id;
                    }
                }else {
                    $link .= '&amp;id=' . $id;
                }
                $link .= '&amp;Itemid=' . $itemId;
            }
        }

        return $link;
    }


    public static function getFormRoute($id)
    {
        //Create the link
        if ($id) {
            $link = 'index.php?option=com_tz_portfolio&amp;task=article.edit&amp;a_id='. $id;
        } else {
            $link = 'index.php?option=com_tz_portfolio&amp;task=article.edit&amp;a_id=0';
        }

        return $link;
    }

    public static function getLetterRoute($view, $character){
        $app        = Factory::getApplication('site');
        $input      = $app -> input;
//        $menus		= new SiteMenu();
        $menus		= $app -> getMenu('site');
        $active     = $menus->getActive();
        $id         = $input -> getCmd('id');

        $id_link    = null;
        if($id){
            $id_link    = '&amp;id='.$id;
        }else {
            if ($year = $input->getInt('year')) {
                $id_link .= '&amp;year='.$year;
            }
            if ($month = $input->getInt('month')) {
                $id_link .= '&amp;month='.$month;
            }
        }
        $link   = 'index.php?option=com_tz_portfolio&amp;view='.$view.$id_link.'&amp;char='
            .mb_strtolower(trim($character)).($active?'&amp;Itemid='.$active -> id:'');
        return $link;
    }

    public static function getTagRoute($tagid, $language = 0, $menuActive = null){
        $id = (int) $tagid;

        if ($id < 1)
        {
            $link = '';
        }
        else
        {
            $link = 'index.php?option=com_tz_portfolio&view=portfolio&tid=' . $id;

            if ($language && $language !== '*' && Multilanguage::isEnabled())
            {
                $link .= '&lang=' . $language;
            }
        }
        $itemId = null;
        if($_itemId = self::_findItem(array('portfolio' => array(0)))){
            $itemId    = $_itemId;
        }
        if($menuActive && $menuActive != 'auto'){
            $itemId    = $menuActive;
        }
        if($itemId) {
            $link .= '&Itemid=' . $itemId;
        }
        return $link;
    }

    public static function getUserRoute($userid, $language = 0, $menuActive = null){
        $id = (int) $userid;

        if ($id < 1)
        {
            $link = '';
        }
        else
        {
            $link = 'index.php?option=com_tz_portfolio&view=portfolio&uid=' . $id;

            if ($language && $language !== '*' && Multilanguage::isEnabled())
            {
                $link .= '&lang=' . $language;
            }
        }
        $itemId = null;
        if($_itemId = self::_findItem(array('portfolio' => array(0)))){
            $itemId    = $_itemId;
        }
        if($menuActive && $menuActive != 'auto'){
            $itemId    = $menuActive;
        }
        if($itemId) {
            $link .= '&Itemid=' . $itemId;
        }
        return $link;
    }


    public static function getTagLegacyRoute($id, $language = 0, $menuActive = null){

        $itemId = self::_findTagItemId($id, $menuActive);
        $link   = 'index.php?option=com_tz_portfolio&amp;view=tags&amp;id='.$id.'&amp;Itemid='.$itemId;
        return $link;
    }

    public static function getUserLegacyRoute($id, $menuActive = null){
        $itemId = self::_findUserItemId($id, $menuActive);
        $link   = 'index.php?option=com_tz_portfolio&amp;view=users&amp;id='.$id.'&amp;Itemid='.$itemId;
        return $link;
    }

    public static function getDateRoute($year = null, $month = null, $language = 0, $menuActive = 'auto' ){
        $itemId = self::_findDateItemId($menuActive);
        $link   = 'index.php?option=com_tz_portfolio&amp;view=date'
            .($year?'&amp;year='.$year:'').(($year && $month)?'&amp;month='.$month:'').'&amp;Itemid='.$itemId;

        return $link;
    }

    public static function getSearchRoute(){
        $menu   = Factory::getApplication() -> getMenu();

        //Create the link
        $link = 'index.php?option=com_tz_portfolio&view=search';

        $menuItems  = $menu -> getItems('link', $link);

        if(count($menuItems)){
            $link   .= '&Itemid='.$menuItems[0] -> id;
        }

        return $link;
    }

    public static function getMyArticlesRoute(){
        $menu   = Factory::getApplication() -> getMenu();

        //Create the link
        $link = 'index.php?option=com_tz_portfolio&view=myarticles';

        $menuItems  = $menu -> getItems('link', $link);

        if(count($menuItems)){
            $link   .= '&Itemid='.$menuItems[0] -> id;
        }

        return $link;
    }

    public static function getAddonRoute($addon_id = null){
        $link   = 'index.php?option=com_tz_portfolio&view=addon'.($addon_id?'&addon_id='.$addon_id:'');
        return $link;
    }

    protected static function buildLanguageLookup()
    {
        if (count(self::$lang_lookup) == 0)
        {
            $db    = Factory::getContainer()->get(DatabaseInterface::class);
            $query = $db->getQuery(true)
                ->select('a.sef AS sef')
                ->select('a.lang_code AS lang_code')
                ->from('#__languages AS a');

            $db->setQuery($query);
            $langs = $db->loadObjectList();

            foreach ($langs as $lang)
            {
                self::$lang_lookup[$lang->lang_code] = $lang->sef;
            }
        }
    }


    protected static function _findTagItemId($_tagid=null, $menuActive = 'auto')
    {
        $tagid      = null;
        $app		= Factory::getApplication();
        $menus		= $app->getMenu('site');
        $active     = $menus->getActive();

        if($_tagid){
            $tagid    = intval($_tagid);
        }

        $component	= ComponentHelper::getComponent('com_tz_portfolio');
        $items		= $menus->getItems('component_id', $component->id);

        if($menuActive && $menuActive != 'auto'){
            return $menuActive;
        }

        foreach ($items as $item)
        {

            if (isset($item->query) && isset($item->query['view'])) {
                $view = $item->query['view'];


                if (isset($item->query['id'])) {
                    if ($item->query['id'] == $tagid) {
                        return $item -> id;
                    }
                } else {
                    $catids = $item->getParams()->get('catid');
                    if ($view == 'tags' && $catids) {
                        if (is_array($catids)) {
                            for ($i = 0; $i < count($catids); $i++) {
                                if ($catids[$i] == 0 || $catids[$i] == $tagid) {
                                    return $item -> id;
                                }
                            }
                        } else {
                            if ($catids == $tagid) {
                                return $item -> id;
                            }
                        }
                    }
                }
            }
        }

        $language   = '*';

        if ($active
            && $active->component == 'com_tz_portfolio'
            && ($language == '*' || in_array($active->language, array('*', $language)) || !Multilanguage::isEnabled()))
        {
            return $active->id;
        }

        // If not found, return language specific home link
        $default = $menus->getDefault($language);

        return !empty($default->id) ? $default->id : null;
    }

    protected static function _findUserItemId($id, $menuActive = 'auto'){

        $app		= Factory::getApplication();
        $menus		= $app->getMenu('site');
        $active     = $menus->getActive();
        $userid     = intval($id);

        $component	= ComponentHelper::getComponent('com_tz_portfolio');
        $items		= $menus->getItems('component_id', $component->id);

        if($menuActive && $menuActive != 'auto'){
            return $menuActive;
        }

        foreach ($items as $item)
        {
            if (isset($item->query) && isset($item->query['view'])) {
                $view = $item->query['view'];

                if ($view == 'users' && isset($item -> query['id'])) {
                    if ($item->query['id'] == $userid) {
                        return $item -> id;
                    }
                }
            }
        }

        $language   = '*';

        if ($active
            && $active->component == 'com_tz_portfolio'
            && ($language == '*' || in_array($active->language, array('*', $language)) || !Multilanguage::isEnabled()))
        {
            return $active->id;
        }

        // If not found, return language specific home link
        $default = $menus->getDefault($language);

        return !empty($default->id) ? $default->id : null;
    }

    protected static function _findDateItemId( $menuActive = 'auto'){

        $app		= Factory::getApplication();
        $menus		= $app->getMenu('site');
        $active     = $menus->getActive();

        $component	= ComponentHelper::getComponent('com_tz_portfolio');
        $items		= $menus->getItems('component_id', $component->id);

        if($menuActive && $menuActive != 'auto'){
            return $menuActive;
        }

        foreach ($items as $item)
        {
            if (isset($item->query) && isset($item->query['view'])) {
                $view = $item->query['view'];

                if ($view == 'date') {
                    return $item -> id;
                }
            }
        }

        if($active) {
            return $active->id;
        }else{

            // If not found, return language specific home link
            $default = $menus->getDefault();
            return !empty($default->id) ? $default->id : null;
        }
    }

    protected static function _findItem($needles = null)
    {
        $app		= Factory::getApplication();
        $menus		= $app->getMenu('site');
        $active     = $menus->getActive();
        $language   = isset($needles['language']) ? $needles['language'] : '*';

        // Prepare the reverse lookup array.
        if (!isset(self::$lookup[$language])) {
            self::$lookup[$language] = array();
        }

        $component	= ComponentHelper::getComponent('com_tz_portfolio');

        $attributes = array('component_id');
        $values     = array($component->id);

        if ($language != '*')
        {
            $attributes[] = 'language';
            $values[]     = array($needles['language'], '*');
        }

        $items = $menus->getItems($attributes, $values);

        $tzCatids   = null;
        // Find menus have choose some category
        foreach($items as $i => $sItem){
            if (isset($sItem->query) && isset($sItem->query['view']))
            {
                $sView = $sItem->query['view'];

                if (!isset(self::$lookup[$language][$sView])) {
                    self::$lookup[$language][$sView] = array();
                }

                if (!isset($sItem->query['id'])) {
                    if($needles){
                        $sParams        = $sItem -> getParams();
                        $sCatids		= $sParams->get('tz_catid');
                        if($sParams -> get('catid')){
                            $sCatids  = $sParams -> get('catid');
                        }

                        if ($sCatids) {
                            if (is_array($sCatids)) {
                                $sCatids = array_filter($sCatids);
                                if(count($sCatids)){
                                    foreach($sCatids as $sc){
                                        if(!isset(self::$lookup[$language][$sView][$sc]) ||
                                            (isset(self::$lookup[$language][$sView][$sc]) && count($sCatids) == 1)){
                                            $tzCatids[] = $sc;
                                            self::$lookup[$language][$sView][$sc] = $sItem -> id;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($items as $i => $item)
        {
            if (isset($item->query) && isset($item->query['view']))
            {
                $view = $item->query['view'];

                if (isset($item->query['id'])) {
                    if (!isset(self::$lookup[$language][$view][$item->query['id']]) || $item->language != '*')
                    {
                        self::$lookup[$language][$view][$item->query['id']] = $item->id;
                    }
                } else {
                    $catids = null;
                    if($item -> getParams() -> get('catid')){
                        $catids  = $item -> getParams() -> get('catid');
                    }

                    if ($catids) {
                        if (is_array($catids)) {
                            $catids = array_filter($catids);
                            if(!count($catids)){
                                if($needles){

                                    if($view == 'portfolio' && isset($needles['portfolio'])
                                        && isset($needles['portfolio'][0]) && empty($needles['portfolio'][0])){
                                        self::$lookup[$language][$view][0]  = $item -> id;
                                    }

                                    // Find menus choose all category
                                    if($_catids = self::getCatIds()){
                                        if($tzCatids){
                                            // Filter category with menus chose some category
                                            $_catids    = array_diff($_catids,$tzCatids);
                                            $_catids    = array_reverse($_catids);
                                        }
                                        // Set Itemid for category
                                        foreach($_catids as $c){
                                            if(!isset(self::$lookup[$language][$view][$c])){
                                                self::$lookup[$language][$view][$c] = $item->id;
                                            }
                                        }
                                    }
                                }
                            }
                        }else {
                            if(!isset(self::$lookup[$language][$view][$catids])){
                                self::$lookup[$language][$view][$catids] = $item->id;
                            }
                        }
                    }else{
                        $k_id   = (int) $needles['portfolio'][0];
                        if(!isset(self::$lookup[$language][$view][$k_id])) {
                            self::$lookup[$language][$view][$k_id] = $item -> id;
                        }
                    }
                }

                if ($active && $active->component == 'com_tz_portfolio') {
                    if (isset($active->query) && isset($active->query['view'])){

                        if (isset($active->query['id'])) {
                            if(!isset(self::$lookup[$language][$active->query['view']][$active->query['id']])){
                                self::$lookup[$language][$active->query['view']][$active->query['id']] = $active->id;
                            }
                        }
                    }
                }

            }

        } // End for

        // Return menu's ids were found in above
        if ($needles)
        {
            foreach ($needles as $view => $ids)
            {
                if (isset(self::$lookup[$language][$view]))
                {
                    foreach ($ids as $id)
                    {
                        if (isset(self::$lookup[$language][$view][(int) $id]))
                        {
                            self::$catid_bool[(int) $id]  = false;
                            self::$views[(int) $id]       = $view;
                            return self::$lookup[$language][$view][(int) $id];
                        }
                    }
                }
            }
        }

        if ($active && $active->component == 'com_tz_portfolio'
            && ($language == '*' || in_array($active->language, array('*', $language)) || !Multilanguage::isEnabled()))
        {
            return $active->id;
        }

        // If not found, return language specific home link
        $default = $menus->getDefault($language);

        return !empty($default->id) ? $default->id : null;
    }


    protected static function getCatIds(){
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__tz_portfolio_plus_categories')
            -> where('extension = '.$db -> quote("com_tz_portfolio_plus"))
            -> where('published = 1');

        $db->setQuery($query);
        if($catids = $db->loadColumn()){
            return $catids;
        }
        return false;
    }
}