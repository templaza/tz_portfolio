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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Extension\DummyPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactory;
use Joomla\Event\DispatcherAwareInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Filesystem\Path;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Plugin\PluginHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\AddOn\AddOnController;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\AddOn\AddOnTrait;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioTemplate;

class AddonHelper extends PluginHelper
{
    use AddOnTrait;

    protected static $layout            = 'default';
    protected static $plugins           = null;
    protected static $instances         = array();
    protected static $plugin_types      = null;
    protected static $languageLoaded    = array();

    protected static $myHelper          = array();

    public static function getInstance($type, $addon = null, $enabled=true, $dispatcher = null, $config = array()){

        if (isset(self::$instances[$type.$addon])) {
            return self::$instances[$type.$addon];
        }

        if ($plugin_obj = self::getAddOn($type, $addon, $enabled)) {

//            \JLoader::registerNamespace('\\TemPlaza\\Component\\TZ_Portfolio\\AddOn\\'
//                .ucfirst($type).'\\'.ucfirst($addon),
//                $this -> getAddOnPath('admin/src'));

            try{
                $addOnH = new self();
//                $addOnH = isset(static::$myHelper)?static::$myHelper:(new self());

//                if(!isset(static::$myHelper)){
//                    static::$myHelper   = $addOnH;
//                }

                $addOnEx= $addOnH -> bootAddon($addon, $type);
                return self::$instances[$type.$addon] = $addOnEx;
//                return $addOnEx;
            }catch (\Exception $exception){
                return false;
            }

//            // Ensure we have a dispatcher now so we can correctly track the loaded plugins
//            $dispatcher = $dispatcher ?: Factory::getApplication()->getDispatcher();
//
//            $namespace  = 'TemPlaza\Component\TZ_Portfolio\AddOn\\'.ucfirst($type) .'\\'. ucfirst($addon);
//            $className  = $namespace.'\Extension\\'. ucfirst($addon);
//
//            if(!class_exists($className)){
//                $adoPath    = COM_TZ_PORTFOLIO_ADDON_PATH.'/'.$type.'/'.$addon.'/src';
//                \JLoader::registerNamespace($namespace, $adoPath);
//            }
//
//            if (class_exists($className)) {
//                $args   = array();
//
//                if($type != 'extrafields') {
//                    $registry = new Registry($plugin_obj->params);
//
//                    $args[] = array(
//                        'type' => ($plugin_obj->type),
//                        'name' => ($plugin_obj->name),
//                        'params' => $registry
//                    );
//                }
//
//                $instance   = new $className($dispatcher, $args);
//
//                return self::$instances[$type.$addon] = $instance;
//            }
        }
        return false;
    }

    public static function getLayoutPath($type, $name, $client = 'site', $layout = 'default',$viewName = null)
    {
        $defaultLayout      = $layout;

        $layoutPrefix       = 'ado_';
        $orgLayoutPrefix    = 'plg_';

        if($client == 'site' && $viewName && !empty($viewName)) {
            $_template  = TZ_PortfolioTemplate::getTemplate(true);
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

            // Create default template of tz_portfolio
            if(isset($template -> home_path) && $template -> home_path){
                $_tmpPath    = $template -> home_path. '/'
                    . $layoutPrefix.$type.'_'. $name. '/' .  $layout . '.php';
                if(!file_exists($_tmpPath)){
                    $_tmpPath    = $template -> home_path. '/'
                        . $orgLayoutPrefix.$type.'_'. $name. '/' .  $layout . '.php';
                }
                if(file_exists($_tmpPath)){
                    $tPath    = $_tmpPath;
                }
            }
            if(isset($template -> base_path) && $template -> base_path){
                $_tmpPath    = $template -> base_path. '/'
                    .$layoutPrefix.$type.'_'.$name. '/' .  $layout . '.php';

                if(!file_exists($_tmpPath)) {
                    $_tmpPath = $template->base_path . '/'
                        . $orgLayoutPrefix . $type . '_' . $name . '/' . $layout . '.php';
                }

                if(file_exists($_tmpPath)){
                    $tPath    = $_tmpPath;
                }
            }
            if(isset($template -> jpath_home) && $template -> jpath_home){
                $_tmpPath    = $template -> jpath_home. '/'
                    .$layoutPrefix.$type.'_'.$name. '/' .  $layout . '.php';

                if(!file_exists($_tmpPath)) {
                    $_tmpPath = $template->jpath_home . '/'
                        . $orgLayoutPrefix . $type . '_' . $name . '/' . $layout . '.php';
                }

                if(file_exists($_tmpPath)){
                    $tPath    = $_tmpPath;
                }
            }
            if(isset($template -> jpath_base) && $template -> jpath_base){
                $_tmpPath    = $template -> jpath_base. '/'
                    .$layoutPrefix.$type.'_'.$name. '/' .  $layout . '.php';

                if(!file_exists($_tmpPath)) {
                    $_tmpPath = $template->jpath_base . '/'
                        . $orgLayoutPrefix . $type . '_' . $name . '/' . $layout . '.php';
                }

                if(file_exists($_tmpPath)){
                    $tPath    = $_tmpPath;
                }
            }

            $bPath = COM_TZ_PORTFOLIO_ADDON_PATH . '/' . $type . '/' . $name . '/tmpl/core/'
                .$defaultLayout.'.php';
            if(!file_exists($bPath)) {
                $bPath = COM_TZ_PORTFOLIO_ADDON_PATH . '/' . $type . '/' . $name . '/views' . '/' . $viewName
                    . '/tmpl/' . $defaultLayout . '.php';
            }
            $dPath = COM_TZ_PORTFOLIO_ADDON_PATH . '/' . $type . '/' . $name.'/tmpl/'.$viewName.'/default.php';
            if(!file_exists($dPath)) {
                $dPath = COM_TZ_PORTFOLIO_ADDON_PATH . '/' . $type . '/' . $name . '/views' . '/' . $viewName
                    . '/tmpl' . '/default.php';
            }

        }elseif($client == 'admin'){
            $template = Factory::getApplication()->getTemplate();

            if (strpos($layout, ':') !== false)
            {
                // Get the template and file name from the string
                $temp = explode(':', $layout);
                $template = ($temp[0] == '_') ? $template : $temp[0];
                $layout = $temp[1];
                $defaultLayout = ($temp[1]) ? $temp[1] : 'default';
            }

            // Build the template and base path for the layout
            $tPath  = JPATH_THEMES . '/' . $template . '/html/'.$layoutPrefix . $type . '_' . $name . '/' . $layout . '.php';
            if(!file_exists($tPath)) {
                $tPath = JPATH_THEMES . '/' . $template . '/html/'.$orgLayoutPrefix . $type . '_' . $name . '/' . $layout . '.php';
            }
            $bPath  = COM_TZ_PORTFOLIO_ADDON_PATH . '/' . $type . '/' . $name . '/admin/tmpl/' . $defaultLayout . '.php';
            if(!file_exists($bPath)) {
                $bPath = COM_TZ_PORTFOLIO_ADDON_PATH . '/' . $type . '/' . $name . '/tmpl/' . $defaultLayout . '.php';
            }
            $dPath  = COM_TZ_PORTFOLIO_ADDON_PATH . '/' . $type . '/' . $name . '/admin/tmpl/default.php';
            if(!file_exists($dPath)) {
                $dPath = COM_TZ_PORTFOLIO_ADDON_PATH . '/' . $type . '/' . $name . '/tmpl/default.php';
            }
        }

        // If the template has a layout override use it
        if (file_exists($tPath))
        {
            return Path::clean($tPath);
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

    public static function getAddOn($type, $addon = null, $enabled=true)
    {
        $result = array();
        $plugins = static::load($enabled);

        // Find the correct plugin(s) to return.
        if (!$addon)
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
            if($plugins){
                foreach ($plugins as &$p)
                {
                    Factory::getApplication() -> triggerEvent('onTPAddOnProcess', array(&$p));
                    // Is this plugin in the right group?
                    if ($p && $p->type == $type && $p->name == $addon)
                    {
                        $result = $p;
                        break;
                    }
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

    public static function getCoreContentTypes(){
        $content_types	= array();
        $array			= array(
            'none' => Text::_('JNONE'),
            'hits' => Text::_('JGLOBAL_HITS'),
            'title' => Text::_('JGLOBAL_TITLE'),
            'author' => Text::_('JAUTHOR'),
            'author_about' => Text::_('COM_TZ_PORTFOLIO_ABOUT_AUTHOR'),
            'tags' => Text::_('COM_TZ_PORTFOLIO_TAGS'),
            'icons' => Text::_('COM_TZ_PORTFOLIO_ICONS'),
            'media' => Text::_('COM_TZ_PORTFOLIO_TAB_MEDIA'),
            'extrafields' => Text::_('COM_TZ_PORTFOLIO_TAB_FIELDS'),
            'introtext' => Text::_('COM_TZ_PORTFOLIO_FIELD_INTROTEXT'),
            'fulltext' => Text::_('COM_TZ_PORTFOLIO_FIELD_FULLTEXT'),
            'category' => Text::_('JCATEGORY'),
            'created_date' => Text::_('JGLOBAL_FIELD_CREATED_LABEL'),
            'modified_date' => Text::_('COM_TZ_PORTFOLIO_MODIFIED_DATE'),
            'related' => Text::_('COM_TZ_PORTFOLIO_FIELD_RELATED_ARTICLE'),
            'published_date' => Text::_('COM_TZ_PORTFOLIO_PUBLISHED_DATE'),
            'parent_category' => Text::_('COM_TZ_PORTFOLIO_PARENT_CATEGORY'),
            'project_link' => Text::_('COM_TZ_PORTFOLIO_PROJECT_LINK_LABEL')
        );

        $std				= new \stdClass();
        foreach($array as $key => $text){
            $std -> value		= $key;
            $std -> text		= $text;
            $content_types[]	= clone($std);
        }

        return $content_types;
    }

    public static function getContentTypes(){
        if($core_types = self::getCoreContentTypes()) {

            $includeTypes   = $core_types;
            $types          = ArrayHelper::getColumn($core_types, 'value');

            if ($contentPlugins = self::importPlugin('content')) {
                if ($pluginTypes = Factory::getApplication()->triggerEvent('onAddContentType')) {
                    if(count($pluginTypes)){
                        $pluginTypes    = array_filter($pluginTypes);
                    }
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

        $user = Factory::getUser();
        $cache = Factory::getCache('com_tz_portfolio', '');

        $levels = implode(',', $user->getAuthorisedViewLevels());

        if (!(static::$plugins = $cache->get($levels)))
        {
            $db     = Factory::getDbo();
            $query  = $db->getQuery(true)
                ->select('id, folder AS type, element AS name, params, manifest_cache, asset_id')
                ->from('#__tz_portfolio_plus_extensions')
                ->where('(type =' . $db->quote('tz_portfolio-addon').' OR type =  '.
                    $db -> quote('tz_portfolio_plus-plugin').')')
                ->where('access IN(' . $levels.')')
                ->order('ordering');

            if($enabled){
                $query -> where('published = 1');
            }

            /**
             * Filter by add-ons from add-ons directory
             * @deprecated Will be removed when TZ Portfolio Plus wasn't supported
             */
            $filter_addons  = glob(COM_TZ_PORTFOLIO_ADDON_PATH.'/*/*', GLOB_ONLYDIR);
            if(!empty($filter_addons)){
                $filter_addons  = array_map(function($value) use($db){
                    $new_value  = basename(dirname($value));
                    $new_value .= '/'.basename($value);
                    return $db -> quote($new_value);
                }, $filter_addons);
                $query -> where('CONCAT(folder, "/", element) IN('.implode(',', $filter_addons).')');
            }

            $db -> setQuery($query);

            if($plugins = $db->loadObjectList()){
                foreach($plugins as &$item){
                    $item -> manifest_cache = json_decode($item -> manifest_cache);
                }

                Factory::getApplication() -> triggerEvent('onTPAddOnIsLoaded', array(&$plugins));

                static::$plugins = $plugins;
            }else{
                static::$plugins    = false;
            }

            $cache->store(static::$plugins, $levels);
        }

        return static::$plugins;
    }

    public static function getAddonController($addon_id, $config = array()){
        if($addon_id){

            if($addon  = self::getPluginById($addon_id)){

                $app    = Factory::getApplication();
                $input  = $app -> input;
                $result = true;

                // Check task with format: addon_name.addon_view.addon_task (example image.default.display);
                $adtask     = $input -> get('addon_task', 'display');
                if($adtask && strpos($adtask,'.') > 0 && substr_count($adtask,'.') > 1){
                    list($plgname,$adtask) = explode('.',$adtask,2);
                    if($plgname == $addon -> name){
                        $input -> set('addon_task',$adtask);
                    }
                }
                $basePath   = Path::clean(COM_TZ_PORTFOLIO_ADDON_PATH.'/'.$addon -> type
                    .'/'.$addon -> name.'/src');

//                $_config['base_path']    = $basePath;
                $_config['addon']    = $addon;

                $config = array_merge($_config, $config);

//                /* @var MVCFactory $mvc
//                 *TemPlaza\Component\TZ_Portfolio\AddOn
//                 */
//                if($controller = AddOnController::getInstance(ucfirst($addon -> name), $config)){
//                    return $controller;
//                }
//                if($controller = AddOnController::getInstance(ucfirst($addon -> type).'\\'.ucfirst($addon -> name)
//                    .'\\Controller\\'.ucfirst($addon -> name), $config)){
//                    return $controller;
//                }

                $addonO = static::getInstance($addon -> type, $addon -> name);
//                $addOnH = new self();
//                $addonO = $addOnH -> bootAddon($addon->name, $addon->type);
//                if(!$addonO instanceof DummyPlugin) {
                    $mvc = $addonO->getMVCFactory();
                    $config['factory'] = $mvc;
                    $name   = $adtask;
                    if(strpos($adtask, '.')) {
                        list($name, $task) = explode('.', $adtask);
                    }
                    if($addonO && ($controller = $mvc -> createController($name, 'site', $config, $app, $input))){
                        return $controller;
                    }
//                }
                /*else{
                    var_dump($addon -> type);
                    var_dump($addon -> name);
                    die(__FILE__);
                }*/
                return false;

//                var_dump(get_class($mvc));
//                var_dump($adtask);
//                var_dump(__METHOD__);
//
//                require_once COM_TZ_PORTFOLIO_ADDON_PATH.'/mediatype/image/src/Controller/DisplayController.php';
//                var_dump(\JLoader::getNamespaces()); die(__FILE__);
//                var_dump($adtask);
//                var_dump(get_class($mvc -> createController($adtask, 'site', $config, $app, $input)));
//////                var_dump($mvc -> createController($adtask, ucfirst($addon -> name), $config, $app, $input));
//                die(__FILE__);
//                var_dump(get_class($addonO));
//                var_dump(method_exists($addonO, 'getApplication'));
//                die(__FILE__);


//                if($addon -> name == 'image') {
////                $mvc    = Factory::getApplication() -> bootComponent('tz_portfolio')
////                    -> getMVCFactory();
////
////                var_dump(get_class($mvc));
////                die(__FILE__);
//                    $addOnH = new self();
//                    $addonO = $addOnH -> bootAddon($addon->name, $addon->type);
//                    $mvc    = $addonO  -> getMVCFactory();
//
//                    if($controller = $mvc -> createController('display', ucfirst($addon -> name), $_config, $app, $input)){
//                        return $controller;
//                    }
////                    var_dump($controller);
////
//////                    var_dump($controller);
//////                    var_dump($controller);
////                    var_dump(method_exists($mvc, 'createController'));
////                    die(__FILE__);
//
////
//////                \JLoader::registerNamespace('TemPlaza\Component\TZ_Portfolio\\AddOn\\'
//////                    .ucfirst($addon -> type).'\\'.ucfirst($addon -> name), COM_TZ_PORTFOLIO_ADDON_PATH
//////                    .'/'.$addon -> type.'/'.$addon -> name.'/src');
//////                $container  = Factory::getContainer() -> get('TemPlaza\Component\TZ_Portfolio\\AddOn\\'
//////                    .ucfirst($addon -> type).'\\'.ucfirst($addon -> name));
////                    var_dump('get_class($mvc)');
//////                    var_dump($controller);
////                    var_dump(get_class($mvc));
////                    var_dump($addon->type);
////                    var_dump($addon->name);
//////                var_dump(get_class(Factory::getApplication() -> bootComponent('tz_portfolio')));
//////                die(__METHOD__);
////
//////                    $mvc -> setMVCFactory('TemPlaza\Component\TZ_Portfolio\\AddOn\\'
//////                    .ucfirst($addon -> type).'\\'.ucfirst($addon -> name));
////                var_dump(method_exists($mvc, 'createController'));
//////                var_dump($mvc -> getMVCFactory());
////                    die(__METHOD__);
//////                'TemPlaza\Component\TZ_Portfolio\\AddOn\\'.ucfirst($addon -> type)
//////                var_dump('TemPlaza\Component\TZ_Portfolio\\AddOn\\'.ucfirst($addon -> type)
//////                    .'\\'.ucfirst($addon -> name));
//////                die(__METHOD__);
////                    $client = 'AddOn\\' . ucfirst($addon->type)
////                        . '\\' . ucfirst($addon->name);
////                    $controller = $mvc->createController('display', ucfirst($addon->name), $_config, $app, $app->input);
////                    $namespace  = 'TemPlaza\Component\TZ_Portfolio\\AddOn\\'
////                        .ucfirst($addon -> type).'\\'.ucfirst($addon -> name).'\\Controller';
////                    var_dump($namespace);
////                    die(__METHOD__);
////                    $controller = AddOnController::getInstance(ucfirst($addon -> name), $_config);
////                    $client = preg_replace('/[^A-Z0-9_]/i', '', $client);
//                    var_dump('$client');
////                    var_dump($client);
////                    var_dump($adtask);
////                    var_dump('AddOn\\' . ucfirst($addon->type)
////                        . '\\' . ucfirst($addon->name));
////                    var_dump($addon->type);
////                    var_dump($addon->name);
////                    var_dump($_config);
//                    var_dump($controller);
//                    die(__FILE__);
//                }

//                if($controller = TZ_Portfolio_Plus_AddOnControllerLegacy::getInstance('PlgTZ_Portfolio_Plus'
//                    .ucfirst($addon -> type).ucfirst($addon -> name)
//                    , $config)) {
//                    tzportfolioplusimport('plugin.modelitem');
//
//                    return $controller;
//                }
            }
        }
        return false;
    }

    public static function loadLanguage($element, $type){

        $lang           = Factory::getApplication() -> getLanguage();
        $tag            = $lang -> getTag();
        $prefix         = 'tp_addon_';
        $basePath       = JPATH_SITE . '/components/com_tz_portfolio/add-ons' . '/' . $type . '/' . $element;
        $_filename      = $type . '_' . $element;

        $__files    = array();
        if(is_dir($basePath.'/language/'.$tag)) {
            $__files = Folder::files($basePath . '/language/'.$tag, 'plg_.*.ini$', false);
        }elseif(is_dir($basePath.'/language/en-GB')) {
            $__files = Folder::files($basePath . '/language/en-GB', 'plg_.*.ini$', false);
        }

        if($__files && count($__files)){
            $prefix = 'plg_';
        }
        $extension = $prefix . $_filename;

        if(isset(self::$languageLoaded[$extension])){
            return self::$languageLoaded[$extension];
        }

        $load   = $lang->load(strtolower($extension), $basePath, null, false, true);

        if($load) {
            self::$languageLoaded[$extension] = $load;
        }

        return $load;
    }

    protected static function import($plugin, $autocreate = true, DispatcherInterface $dispatcher = null)
    {
        static $plugins = array();

        // Get the dispatcher's hash to allow paths to be tracked against unique dispatchers
        $hash = spl_object_hash($dispatcher) . $plugin->type . $plugin->name;

        if (\array_key_exists($hash, $plugins))
        {
            return;
        }

        $plugins[$hash] = true;

        try{
////            $addOnH = new self();
//            $addOnH = isset(static::$myHelper)?static::$myHelper:(new self());
//
//            if(!isset(static::$myHelper)){
//                static::$myHelper   = $addOnH;
//            }
//
//            $addOn  = $addOnH -> bootAddon($plugin -> name, $plugin -> type);

            $addOn = static::getInstance($plugin -> type, $plugin -> name);

            if ($dispatcher && $addOn instanceof DispatcherAwareInterface)
            {
                $addOn->setDispatcher($dispatcher);
            }

            if (!$autocreate)
            {
                return;
            }
            if(!empty($addOn) && method_exists($addOn, 'registerListeners')) {
                $addOn->registerListeners();
            }
        }catch (\Exception $exception){
            return;
        }

//        if(TZ_Portfolio_PlusPluginHelperBase::import($plugin, $dispatcher)){
//            if ($autocreate)
//            {
//                $className = 'PlgTZ_Portfolio_Plus' . $plugin->type . $plugin->name;
//
//                if (class_exists($className))
//                {
//                    // Load the plugin from the database.
//                    if (!isset($plugin->params))
//                    {
//                        // Seems like this could just go bye bye completely
//                        $plugin = static::getPlugin($plugin->type, $plugin->name);
//                    }
//
//                    // Instantiate and register the plugin.
//                    new $className($dispatcher, (array) ($plugin));
//                }
//            }
//        }
    }

    public static function importPlugin($type, $plugin = null, $autocreate = true, DispatcherInterface $dispatcher = null)
    {
        static $loaded = [];

        // Check for the default args, if so we can optimise cheaply
        $defaults = false;

        if ($plugin === null && $autocreate === true && $dispatcher === null)
        {
            $defaults = true;
        }

        // Ensure we have a dispatcher now so we can correctly track the loaded plugins
        $dispatcher = $dispatcher ?: Factory::getApplication()->getDispatcher();

        // Get the dispatcher's hash to allow plugins to be registered to unique dispatchers
        $dispatcherHash = spl_object_hash($dispatcher);

        if (!isset($loaded[$dispatcherHash]))
        {
            $loaded[$dispatcherHash] = [];
        }

        if (!$defaults || !isset($loaded[$dispatcherHash][$type]))
        {
            $results = null;

            // Load the plugins from the database.
            $plugins = static::load();

            // Get the specified plugin(s).
            for ($i = 0, $t = \count($plugins); $i < $t; $i++)
            {
                if ($plugins[$i]->type === $type && ($plugin === null || $plugins[$i]->name === $plugin))
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

            $loaded[$dispatcherHash][$type] = $results;
        }

        return $loaded[$dispatcherHash][$type];
    }

    /* Import all add-ons
    *  Since v2.4.3
    */
    public static function importAllAddOns(){
        $imported   = false;
        // Get
        if (!file_exists(COM_TZ_PORTFOLIO_ADDON_PATH)) return false;
        $folders = Folder::folders(COM_TZ_PORTFOLIO_ADDON_PATH);
        if(count($folders)){
            foreach ($folders as $group){
                $imported   = self::importPlugin($group);
            }
        }
        return $imported;
    }
}