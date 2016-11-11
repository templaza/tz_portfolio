<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2013 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

defined('_JEXEC') or die;

use Joomla\Registry\Registry;
tzportfolioplusimport('template');

class TZ_Portfolio_PlusPluginHelper extends JPluginHelper{

    protected static $plugins       = null;
    protected static $layout        = 'default';
    protected static $plugin_types  = null;
    protected static $instances     = array();

    public static function getInstance($type, $plugin = null, $enabled=true, $dispatcher = null){
        if (!isset(self::$instances[$type.$plugin])) {
            if ($plugin_obj = self::getPlugin($type, $plugin, $enabled)) {
                if($type == 'extrafields'){
                    tzportfolioplusimport('fields.extrafield');
                }
                if(!$dispatcher){
                    $dispatcher = JEventDispatcher::getInstance();
                }
                $className = 'PlgTZ_Portfolio_Plus' . ucfirst($type) . ucfirst($plugin);
                if (!class_exists($className)) {
                    self::importPlugin($type, $plugin);
                }
                if (class_exists($className)) {
                    $registry = new JRegistry($plugin_obj->params);

                    self::$instances[$type.$plugin] = new $className($dispatcher, array('type' => ($plugin_obj->type)
                    , 'name' => ($plugin_obj->name), 'params' => $registry));
                    return self::$instances[$type.$plugin];
                }
            }
        }
        return false;
    }

    public static function getLayoutPath($type, $name, $client = 'site', $layout = 'default',$viewName = null)
    {
        $defaultLayout  = $layout;
        if($client == 'site' && $viewName && !empty($viewName)) {
            $_template  = TZ_Portfolio_PlusTemplate::getTemplate(true);
            $template   = $_template->template;
            $params     = $_template->params;

            if (strpos($layout, ':') !== false)
            {
                // Get the template and file name from the string
                $temp           = explode(':', $layout);
                $template       = ($temp[0] == '_') ? $_template -> template : $temp[0];
                $layout         = $temp[1];
                $defaultLayout  = ($temp[1]) ? $temp[1] : 'default';
            }

            self::$layout = $defaultLayout;

            // Build the template and base path for the layout
            $tPath = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH . '/' . $template . '/html/'.$params -> get('layout','default')
                .'/'.$viewName.'/plg_' . $type . '_' . $name . '/' . $layout . '.php';
                $bPath = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $type . '/' . $name
                    .'/views'.'/'.$viewName.'/tmpl'
                    .'/' . $defaultLayout . '.php';
                $dPath = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $type . '/' . $name
                    .'/views'.'/'.$viewName.'/tmpl'
                    .'/default.php';
        }elseif($client == 'admin'){
            $template = JFactory::getApplication()->getTemplate();

            if (strpos($layout, ':') !== false)
            {
                // Get the template and file name from the string
                $temp = explode(':', $layout);
                $template = ($temp[0] == '_') ? $template : $temp[0];
                $layout = $temp[1];
                $defaultLayout = ($temp[1]) ? $temp[1] : 'default';
            }

            // Build the template and base path for the layout
            $tPath = JPATH_THEMES . '/' . $template . '/html/plg_' . $type . '_' . $name . '/' . $layout . '.php';
            $bPath = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $type . '/' . $name . '/tmpl/' . $defaultLayout . '.php';
            $dPath = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $type . '/' . $name . '/tmpl/default.php';
        }

        // If the template has a layout override use it
        if (file_exists($tPath))
        {
            return $tPath;
        }
        elseif (file_exists($bPath))
        {
            return $bPath;
        }
        else
        {
            return $dPath;
        }
    }

    public static function getLayout(){
        return self::$layout;
    }

    public static function getPlugin($type, $plugin = null, $enabled=true)
    {
        $result = array();
        $plugins = static::load($enabled);

        // Find the correct plugin(s) to return.
        if (!$plugin)
        {
            foreach ($plugins as $p)
            {
                // Is this the right plugin?
                if ($p->type == $type)
                {
                    $result[] = $p;
                }
            }
        }
        else
        {
            foreach ($plugins as $p)
            {
                // Is this plugin in the right group?
                if ($p->type == $type && $p->name == $plugin)
                {
                    $result = $p;
                    break;
                }
            }
        }

        return $result;
    }

    public static function getPluginById($id, $enabled=true)
    {
        $result = array();
        $plugins = static::load($enabled);

        // Find the correct plugin(s) to return.
        if ($id)
        {
            foreach ($plugins as $p)
            {
                // Is this plugin in the right group?
                if ($p->id == $id)
                {
                    $result = $p;
                    break;
                }
            }
        }

        return $result;
    }

    public static function importPlugin($type, $plugin = null, $autocreate = true, JEventDispatcher $dispatcher = null)
    {
        static $loaded = array();

        // Check for the default args, if so we can optimise cheaply
        $defaults = false;

        if (is_null($plugin) && $autocreate == true && is_null($dispatcher))
        {
            $defaults = true;
        }

        if (!isset($loaded[$type]) || !$defaults)
        {
            $results = null;

            // Load the plugins from the database.
            $plugins = static::load();

            // Get the specified plugin(s).
            for ($i = 0, $t = count($plugins); $i < $t; $i++)
            {
                if (is_object($plugins[$i]) && $plugins[$i]->type == $type
                    && ($plugin === null || $plugins[$i]->name == $plugin))
                {
                    static::import($plugins[$i], $autocreate, $dispatcher);
                    $results = true;
                }
            }

            // Bail out early if we're not using default args
            if (!$defaults)
            {
                return $results;
            }

            $loaded[$type] = $results;
        }

        return $loaded[$type];
    }

    public static function getCoreContentTypes(){
        $content_types	= array();
        $array			= array('none' => JText::_('JNONE'), 'hits' => JText::_('JGLOBAL_HITS')
        , 'title' => JText::_('JGLOBAL_TITLE')
        , 'author' => JText::_('JAUTHOR')
        , 'author_about' => JText::_('COM_TZ_PORTFOLIO_PLUS_ABOUT_AUTHOR')
        ,'tags' => JText::_('COM_TZ_PORTFOLIO_PLUS_TAGS')
        , 'icons' => JText::_('COM_TZ_PORTFOLIO_PLUS_ICONS')
        , 'media' => JText::_('COM_TZ_PORTFOLIO_PLUS_TAB_MEDIA')
        , 'extrafields' => JText::_('COM_TZ_PORTFOLIO_PLUS_TAB_FIELDS')
        , 'introtext' => JText::_('COM_TZ_PORTFOLIO_PLUS_FIELD_INTROTEXT')
        , 'fulltext' => JText::_('COM_TZ_PORTFOLIO_PLUS_FIELD_FULLTEXT')
        , 'category' => JText::_('JCATEGORY')
        , 'created_date' => JText::_('JGLOBAL_FIELD_CREATED_LABEL')
        , 'modified_date' => JText::_('COM_TZ_PORTFOLIO_PLUS_MODIFIED_DATE')
        , 'related' => JText::_('COM_TZ_PORTFOLIO_PLUS_FIELD_RELATED_ARTICLE')
        , 'published_date' => JText::_('COM_TZ_PORTFOLIO_PLUS_PUBLISHED_DATE')
        , 'parent_category' => JText::_('COM_TZ_PORTFOLIO_PLUS_PARENT_CATEGORY')
        );

        $std				= new stdClass();
        foreach($array as $key => $text){
            $std -> value		= $key;
            $std -> text		= $text;
            $content_types[]	= clone($std);
        }

        return $content_types;
    }

    public static function getContentTypes(){
        if($core_types             = self::getCoreContentTypes()) {
            $types = JArrayHelper::getColumn($core_types, 'value');
            $includeTypes = $core_types;
            $dispatcher = JEventDispatcher::getInstance();

            if ($contentPlugins = self::importPlugin('content')) {
                if ($pluginTypes = $dispatcher->trigger('onAddContentType')) {
                    foreach ($pluginTypes as $i => $plgType) {
                        if (is_array($plgType) && count($plgType)) {
                            foreach ($plgType as $j => $type) {
                                if (in_array($type->value, $types)) {
                                    unset ($pluginTypes[$i][$j]);
                                }
                            }
                        } else {
                            if (in_array($plgType->value, $types)) {
                                unset($pluginTypes[$i]);
                            }
                        }
                    }
                    $includeTypes = array_merge($includeTypes, $pluginTypes);
                    return $includeTypes;
                }
            }
            return $core_types;
        }
        return false;
    }

    protected static function load($enabled=true)
    {
        if (static::$plugins !== null)
        {
            return static::$plugins;
        }

        $user = JFactory::getUser();
        $cache = JFactory::getCache('com_tz_portfolio_plus', '');

        $levels = implode(',', $user->getAuthorisedViewLevels());

        if (!(static::$plugins = $cache->get($levels)))
        {
            $db     = JFactory::getDbo();
            $query  = $db->getQuery(true)
                ->select('id, folder AS type, element AS name, params, manifest_cache')
                ->from('#__tz_portfolio_plus_extensions')
                ->where('type =' . $db->quote('tz_portfolio_plus-plugin'))
                ->order('ordering');

            if($enabled){
                $query -> where('published = 1');
            }
            $db -> setQuery($query);

            if($plugins = $db->loadObjectList()){
                foreach($plugins as &$item){
                    $item -> manifest_cache = json_decode($item -> manifest_cache);
                }
                static::$plugins = $plugins;
            }else{
                static::$plugins    = false;
            }

            $cache->store(static::$plugins, $levels);
        }

        return static::$plugins;
    }

    protected static function import($plugin, $autocreate = true, JEventDispatcher $dispatcher = null)
    {
        static $paths = array();

        $plugin->type = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->type);
        $plugin->name = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->name);

        $path = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $plugin->type . '/' . $plugin->name . '/' . $plugin->name . '.php';

        if (!isset($paths[$path]))
        {
            if (file_exists($path))
            {
                if (!isset($paths[$path]))
                {
                    require_once $path;
                }

                $paths[$path] = true;

                if ($autocreate)
                {
                    // Makes sure we have an event dispatcher
                    if (!is_object($dispatcher))
                    {
                        $dispatcher = JEventDispatcher::getInstance();
                    }

                    $className = 'PlgTZ_Portfolio_Plus' . $plugin->type . $plugin->name;

                    if (class_exists($className))
                    {
                        // Load the plugin from the database.
                        if (!isset($plugin->params))
                        {
                            // Seems like this could just go bye bye completely
                            $plugin = static::getPlugin($plugin->type, $plugin->name);
                        }

                        // Instantiate and register the plugin.
                        new $className($dispatcher, (array) ($plugin));
                    }
                }
            }
            else
            {
                $paths[$path] = false;
            }
        }
    }

}