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
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

JLoader::register('TZ_Portfolio_PlusHelperCategories', JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/helpers/categories.php');

class TZ_Portfolio_PlusTemplate {

    protected static $cache    = array();

    public static function getTemplate($params = false)
    {
        $storeId    = __METHOD__;
        $storeId    .= ':'.$params;
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        $templateId = self::getTemplateId();
        $template   = new stdClass;

        JTable::addIncludePath(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.DIRECTORY_SEPARATOR.'tables');

        $table  = JTable::getInstance('Templates','TZ_Portfolio_PlusTable');

        $template -> template   = 'system';
        $template -> params     = new JRegistry();
        $template -> layout     = null;

        $db                     = TZ_Portfolio_PlusDatabase::getDbo();
        $query                  = $db -> getQuery(true);

        $query -> select('COUNT(t.id)');
        $query -> from('#__tz_portfolio_plus_templates AS t');
        $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e On e.element = t.template');
        $query -> where('e.type = '.$db -> quote('tz_portfolio_plus-template'));
        $query -> where('e.published = 1');
        $query -> where('t.id =' . $templateId);
        $query -> group('t.id');
        $db -> setQuery($query);

        if(!$db -> loadResult()){
            $templateId = null;
        }

        if($home = $table -> getHome()){
            $default_params = new JRegistry;
            $default_params -> loadString($home -> params);
            $home -> params = clone($default_params);
        }

        if($templateId){
            $table -> load($templateId);
            $template -> id         = $templateId;
            $template -> template   = $table -> template;
            if($table -> params && !empty($table -> params)) {
                $_params    = $table -> params;
                if(is_string($_params)) {
                    $_params = new JRegistry($_params);
                }
                $template->params = $_params;
            }
            if($table -> layout){
                $template -> layout = json_decode($table -> layout);
            }
        }else{
            if($home){
                $template -> id         = $home -> id;
                $template -> template   = $home -> template;
                if($home -> params && !empty($home -> params)) {
                    $_params    = $home -> params;
                    if(is_string($_params)) {
                        $_params = new JRegistry($_params);
                    }
                    $template->params = $_params;
                }
                if($home -> layout){
                    $template -> layout = json_decode($home -> layout);
                }
            }
        }

        $tplparams      = $template -> params;

        $template -> base_path  = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH.DIRECTORY_SEPARATOR
            . $template->template. DIRECTORY_SEPARATOR . 'html'. DIRECTORY_SEPARATOR
            . $template->params -> get('layout','default');

        if($home){
            if($home -> template != $template -> template) {
                $template->home_path = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH . DIRECTORY_SEPARATOR
                    . $home->template . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR
                    . $tplparams->get('layout', 'default');
            }else{
                $template->home_path = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH . DIRECTORY_SEPARATOR
                    . $home->template . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR
                    . $home -> params->get('layout', 'default');
            }
        }

        if ($params)
        {
            self::$cache[$storeId]  = $template;
            return $template;
        }

        self::$cache[$storeId]  = $template -> template;
        return $template->template;
    }

    public static function getTemplateDefault(){

        $storeId    = __METHOD__;
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        $template   = new stdClass;

        $template -> template   = 'system';
        $template -> params     = new JRegistry();
        $template -> layout     = null;
        $table  = JTable::getInstance('Templates','TZ_Portfolio_PlusTable');

        if($home = $table -> getHome()){
            $template -> id         = $home -> id;
            $template -> template   = $home -> template;
            if($home -> params && !empty($home -> params)) {
                $_params    = new JRegistry($home -> params);
                $template->params = $_params;
            }
            if($home -> layout){
                $template -> layout = json_decode($home -> layout);
            }
        }

        self::$cache[$storeId]  = $template;
        return $template;
    }

    public static function getTemplateById($id, $params = true){

        if(!$id){
            return self::getTemplate($params);
        }

        $storeId    = __METHOD__;
        $storeId    .= ':'.$id;
        $storeId    .= ':'.$params;
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        JTable::addIncludePath(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.DIRECTORY_SEPARATOR.'tables');

        $table  = JTable::getInstance('Templates','TZ_Portfolio_PlusTable');

        $table -> reset();

        if(!$table -> load($id)){
            return false;
        }
        if($db = $table -> getDbo()){
            $query  = $db -> getQuery();
            $db -> setQuery($query);
            $template   = $db -> loadObject();
            if(is_string($template -> params)){
                $_params = new Registry();
                $_params -> loadString($template -> params);
                $template -> params = $_params;
            }

            $tplparams      = $template -> params;

            $template -> base_path  = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH.DIRECTORY_SEPARATOR
                . $template->template. DIRECTORY_SEPARATOR . 'html'. DIRECTORY_SEPARATOR
                . $template->params -> get('layout','default');

            if($home = $table -> getHome()){
                $default_params = new JRegistry;
                $default_params -> loadString($home -> params);
                $home -> params = clone($default_params);
            }

            if($home){
                if($home -> template != $template -> template) {
                    $template->home_path = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH . DIRECTORY_SEPARATOR
                        . $home->template . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR
                        . $tplparams->get('layout', 'default');
                }else{
                    $template->home_path = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH . DIRECTORY_SEPARATOR
                        . $home->template . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR
                        . $home -> params->get('layout', 'default');
                }
            }

            if ($params)
            {
                self::$cache[$storeId]  = $template;
                return $template;
            }

            self::$cache[$storeId]  = $template -> template;
            return $template -> template;
        }
        return false;
    }

    public static function getTemplateByOption($option){
        if(!$option){
            return false;
        }

        $storeId    = __METHOD__;
        $storeId    .= ':'.serialize($option);
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }


        $option = array_merge(array('type' => 'tz_portfolio_plus-template'), $option );

        $table  = JTable::getInstance('Extensions', 'TZ_Portfolio_PlusTable');

        if(!$table -> load($option)){
            return false;
        }

        $data   = $table -> getDbo() -> loadObject();
        if(isset($data -> manifest_cache) && $data -> manifest_cache && is_string($data -> manifest_cache)){
            $data -> manifest_cache    = json_decode($data -> manifest_cache);
        }

        self::$cache[$storeId]  = $data;
        return $data;
    }


    protected static function getTemplateId($artId = null,$catId = null){

        $db         = TZ_Portfolio_PlusDatabase::getDbo();
        $app        = JFactory::getApplication('site');
        $templateId = null;
        $_catId     = null;
        $_artId     = null;

        if($app -> isSite()) {
            $params = $app->getParams();
            $templateId = $params->get('tz_template_style_id');
        }

        $input  = $app -> input;
        if($input -> get('option') == 'com_tz_portfolio_plus'){
            switch($input -> get('view')){
                case 'article':
                case 'p_article':
                    $_artId = $input -> get('id',null,'int');
                    if($_catId = TZ_Portfolio_PlusHelperCategories::getCategoriesByArticleId($_artId, true)){
                        $_catId = $_catId -> id;
                    }
                    break;
            }
        }

        if(!empty($catId)){
            $_catId = $catId;
        }
        if(!empty($artId)){
            $_artId = $artId;
        }

        if($_catId){
            $query  = $db -> getQuery(true);
            $query -> select($db -> quoteName('template_id'));
            $query -> from($db -> quoteName('#__tz_portfolio_plus_categories'));
            $query -> where($db -> quoteName('id').'='.$_catId);
            $db -> setQuery($query);
            if($crow = $db -> loadObject()){
                if($crow -> template_id){
                    $templateId = $crow -> template_id;
                }
            }
        }
        if($_artId){
            $query  = $db -> getQuery(true);
            $query -> select($db -> quoteName('template_id'));
            $query -> from($db -> quoteName('#__tz_portfolio_plus_content'));
            $query -> where($db -> quoteName('id').'='.$_artId);
            $db -> setQuery($query);
            if($row = $db -> loadObject()){
                if($row -> template_id){
                    $templateId = $row -> template_id;
                }
            }
        }
        return (int) $templateId;
    }
}