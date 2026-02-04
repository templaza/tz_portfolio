<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    Sonny

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Library\AddOn;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSWebApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Language\LanguageAwareInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\LegacyFactory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\User\CurrentUserInterface;
use Joomla\Filesystem\Path;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioTemplate;

class AddOnController extends BaseController {

    protected static $instance;

    protected $addon;
    protected $context;

    protected $article;

    protected $trigger_params;

    protected $core_view_list;

    public function __construct($config = array())
    {
        if(isset($config['addon'])){
            $this -> addon          = $config['addon'];
            AddonHelper::loadLanguage($this->addon->name, $this->addon->type);
        }

        $config['base_path'] = COM_TZ_PORTFOLIO_ADDON_PATH.'/'.$this -> addon -> type
            .'/'.$this -> addon -> name;

        parent::__construct($config);


        if(isset($config['article'])){
            $this -> article        = $config['article'];
        }
        if(isset($config['trigger_params'])){
            $this -> trigger_params = $config['trigger_params'];
        }

        // Guess the list view as the suffix, eg: OptionControllerSuffix.
        if (empty($this->core_view_list))
        {
            $view   = $this -> input -> getCmd('view');
            $this->core_view_list = strtolower($view);
        }

        $this->paths['view']    = '';

        if(isset($config['factory'])){
            $this -> factory    = $config['factory'];
        }
    }

    public function display($cachable = false, $urlparams = array())
    {
        $app        = Factory::getApplication();
        $document   = Factory::getApplication() -> getDocument();
        $viewType   = $document->getType();
        $viewName   = $this->input->get('addon_view', $this->default_view);
        $viewLayout = $this->input->get('addon_layout', 'default', 'string');

        if($view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath,
            'layout' => $viewLayout))){

            // Check manage permission if the addon have manage datas (only use of back-end)
            if($app -> isClient('administrator') && $addon_id = $this -> input -> get('addon_id', 0, 'int')){
                if($plugin = AddonHelper::getPluginById($addon_id)){
                    $user   = Factory::getUser();

                    if(isset($plugin -> asset_id) &&$plugin -> asset_id && !$user -> authorise('core.manage',
                            'com_tz_portfolio.addon.'.$plugin -> id)){

                        // Somehow the person just went to the form - we don't allow that.
                        $this->setError(Text::_('JERROR_ALERTNOAUTHOR'));
                        $this->setMessage($this->getError(), 'error');

                        $this->setRedirect(Route::_('index.php?option=com_tz_portfolio&view=addons', false));

                        return false;
                    }
                }
            }
        }
        $view -> setLayout($viewLayout);

        // Get/Create the model
        if ($model = $this->getModel($viewName, '', [
            'base_path' => $this->basePath,
            'addon'     => $this -> addon,
            'article'     => $this -> article
        ])) {
            // Push the model into the view (as default)
            $view->setModel($model, true);
        }

//        // Get/Create the model
//        if ($model = $this->getModel($viewName))
//        {
//            // Push the model into the view (as default)
//            $view->setModel($model, true);
//        }

        $view->document = $document;

        $conf = Factory::getConfig();

        // Display the view
        if ($cachable && $viewType != 'feed' && $conf->get('caching') >= 1)
        {
            $option = $this->input->get('option');
            $cache = Factory::getCache($option, 'addon_view');

            if (is_array($urlparams))
            {
                $app = Factory::getApplication();

                if (!empty($app->registeredurlparams))
                {
                    $registeredurlparams = $app->registeredurlparams;
                }
                else
                {
                    $registeredurlparams = new \stdClass;
                }

                foreach ($urlparams as $key => $value)
                {
                    // Add your safe url parameters with variable type as value {@see JFilterInput::clean()}.
                    $registeredurlparams->$key = $value;
                }

                $app->registeredurlparams = $registeredurlparams;
            }

            try {
                /** @var \Joomla\CMS\Cache\Controller\ViewController $cache */
                $cache = Factory::getCache($option, 'view');
                $cache->get($view, 'display');
            } catch (CacheExceptionInterface $exception) {
                $view->display();
            }
        }
        else
        {
            $view->display();
        }

        return $this;
    }

    public function getView($name = '', $type = '', $prefix = '', $config = array())
    {
        // @note We use self so we only access stuff in this class rather than in all classes.
        if (!isset(self::$views)) {
            self::$views = [];
        }

        if (empty($name)) {
            $name = $this->getName();
        }

        if (!$prefix) {
            if ($this->factory instanceof LegacyFactory) {
                $prefix = $this->getName() . 'View';
            } elseif (!empty($config['base_path']) && strpos(Path::clean($config['base_path']), JPATH_ADMINISTRATOR) === 0) {
                // When the front uses an administrator view
                $prefix = 'Administrator';
            } else {
                $prefix = $this->app->getName();
            }
        }

        if (empty(self::$views['addon'][$name][$type][$prefix])) {
            if ($view = $this->createView($name, $prefix, $type, $config)) {

                if($view){
                    if($addon = $this -> addon){
                        $plugin_path = COM_TZ_PORTFOLIO_ADDON_PATH . DIRECTORY_SEPARATOR
                            . $addon -> type . DIRECTORY_SEPARATOR
                            . $addon -> name;

                        // Create template path of tz_portfolio
                        $template = TZ_PortfolioTemplate::getTemplate(true);
                        $tplparams = $template->params;

                        // Create default template of tz_portfolio
                        $defaultPath    = null;
                        $tpath          = null;
                        $orgTpath       = null;
                        $prefix         = 'ado_';
                        $orgPrefix      = 'plg_';

//                        if(isset($template -> base_path) && $template -> base_path){
//                            $tpath   = $template -> base_path. DIRECTORY_SEPARATOR
//                                .($name?$name . DIRECTORY_SEPARATOR:'') . $prefix
//                                . $addon -> type . '_' . $addon -> name;
//                            $orgTpath    = $template -> base_path. DIRECTORY_SEPARATOR
//                                .($name?$name . DIRECTORY_SEPARATOR:'') . $orgPrefix
//                                . $addon -> type . '_' . $addon -> name;
//                        }

                        $vpaths = $view->get('_path');
                        $vpaths = $vpaths['template'];
                        $adoPath    = isset($vpaths[1])?$vpaths[1]:'';
                        $view->set('_path', array('template' => array()));

                        $plgVPath = $plugin_path . DIRECTORY_SEPARATOR . 'views'
                            . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'tmpl';
//
//                        $adoVPath = $plugin_path . DIRECTORY_SEPARATOR . 'views'
//                            . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'tmpl';


                        if (!in_array($plgVPath, $vpaths)) {
                            $view->addTemplatePath($plgVPath);
                        }
                        if (!empty($adoPath)) {
                            $view->addTemplatePath($adoPath);
                        }

                        // Add default template path
                        if ($defaultPath && !in_array($defaultPath, $vpaths)) {
                            $view->addTemplatePath($defaultPath);
                        }
                        foreach($template -> paths as $tmpPath){
                            $path   = $tmpPath.'/'.$name.'/'.$orgPrefix.$addon -> type . '_' . $addon -> name;
                            if(!in_array($path, $vpaths)) {
                                $view->addTemplatePath($path);
                            }
                            $path   = $tmpPath.'/'.$name.'/'.$prefix.$addon -> type . '_' . $addon -> name;
                            if(!in_array($path, $vpaths)) {
                                $view->addTemplatePath($path);
                            }
                        }
                    }
                }

                self::$views['addon'][$name][$type][$prefix] = &$view;
            } else {
                throw new \Exception(Text::sprintf('JLIB_APPLICATION_ERROR_VIEW_NOT_FOUND', $name, $type, $prefix), 404);
            }
        }
        return self::$views['addon'][$name][$type][$prefix];
    }

    protected function createView($name, $prefix = '', $type = '', $config = [])
    {
        $config['paths'] = $this->paths['view'];

        $view = $this->factory->createView($name, $prefix, $type, $config);

        if ($view instanceof CurrentUserInterface && $this->app->getIdentity()) {
            $view->setCurrentUser($this->app->getIdentity());
        }

        if ($view instanceof LanguageAwareInterface && $this->app->getLanguage()) {
            $view->setLanguage($this->app->getLanguage());
        }

        return $view;
    }

//    public function execute($task)
//    {
//        $this->task = $task;
//
//        $task = $task ? strtolower($task) : '';
//
//        if (isset($this->taskMap[$task]))
//        {
//            $doTask = $this->taskMap[$task];
//        }
//        elseif (isset($this->taskMap['__default']))
//        {
//            $doTask = $this->taskMap['__default'];
//        }
//        else
//        {
//            throw new \Exception(Text::sprintf('JLIB_APPLICATION_ERROR_TASK_NOT_FOUND', $task), 404);
//        }
//
//        // Record the actual task being fired
//        $this->doTask = $doTask;
//
//        return $this->$doTask();
//    }

    public function getModel($name = '', $prefix = '', $config = [])
    {
        if (empty($name)) {
            $name = $this->getName();
        }

        if (!$prefix) {
            if ($this->factory instanceof LegacyFactory) {
                $prefix = $this->model_prefix;
            } elseif (!empty($config['base_path']) && strpos(Path::clean($config['base_path']), JPATH_ADMINISTRATOR) === 0) {
                // When the frontend uses an administrator model
                $prefix = 'Administrator';
            } else {
                $prefix = $this->app->getName();
            }
        }

        if ($model = $this->createModel($name, $prefix, $config)) {
            // Task is a reserved state
//            $model->setState('addon_task', $this->task);
            $model->setState('task', $this->task);

            // We don't have the concept on a menu tree in the api app, so skip setting it's information and
            // return early
            if ($this->app->isClient('api')) {
                return $model;
            }

            if ($this->app instanceof CMSWebApplicationInterface) {
                // Let's get the application object and set menu information if it's available
                $menu = $this->app->getMenu();

                if (\is_object($menu) && $item = $menu->getActive()) {
                    // Let's get the application object and set menu information if it's available
                    $menu = $this->app->getMenu();

                    if (\is_object($menu) && $item = $menu->getActive()) {
                        $params = $menu->getParams($item->id);

                        // Set default state data
                        $model->setState('parameters.menu', $params);
                    }
                }
            }
        }

        return $model;
    }

//    public function getModel($name = '', $prefix = '', $config = array())
//    {
////        $config['name'] = $name;
////        $config['base_path']    =  $this->basePath;
//
//        $model = $this->factory->createModel($name, $prefix, $config);
//        var_dump($name);
//        var_dump($prefix);
//        if($name == 'vote') {
//            var_dump($model);
//        }
////        var_dump($config);
//        var_dump(__FILE__);
//
//
//        $model = parent::getModel($name, $prefix, $config);
//
//        if($model){
//            if($this -> addon){
//                $model -> set('addon',$this -> addon);
//            }
//            if($this -> article){
//                $model -> set('article',$this -> article);
//            }
//            if($this -> trigger_params){
//                $model -> set('trigger_params',$this -> trigger_params);
//            }
//        }
//
//        return $model;
//    }

//    protected function createModel($name, $prefix = '', $config = [])
//    {
//        $model = $this->factory->createModel($name, $prefix, $config);
//
//        if ($model === null) {
//            return false;
//        }
//
//        if ($model instanceof CurrentUserInterface && $this->app->getIdentity()) {
//            $model->setCurrentUser($this->app->getIdentity());
//        }
//
//        return $model;
//    }


    public static function getInstance($prefix, $config = array())
    {
        if(self::$instance && isset(self::$instance[$prefix])){
            if (is_object(self::$instance[$prefix]))
            {
                $_class = self::$instance[$prefix];
                $reflection = new \ReflectionClass($_class);

                return $reflection ->newInstance($config);
            }
        }else{
            self::$instance[$prefix]    = false;
        }

        $app    = Factory::getApplication();
        $input  = $app -> input;

        // Get the environment configuration.
        $basePath = array_key_exists('base_path', $config) ? $config['base_path'] : COM_TZ_PORTFOLIO_ADDON_PATH;
        $format   = $input->getWord('format');
        $command  = $input->get('addon_task', 'display');

        // Check for array format.
        $filter = InputFilter::getInstance();

        if (is_array($command))
        {
            $command = $filter->clean(array_pop(array_keys($command)), 'cmd');
        }
        else
        {
            $command = $filter->clean($command, 'cmd');
        }

        // Check for a controller.task command.
        if (strpos($command, '.') !== false)
        {
            // Explode the controller.task command.
            list ($type, $task) = explode('.', $command);

            // Define the controller filename and path.
            $file = self::createFileName('controller', array('name' => $type, 'format' => $format));
            $path = $basePath . '/controllers/' . $file;
            $backuppath = $basePath . '/controller/' . $file;

            // Reset the task without the controller context.
            $input->set('addon_view', $type);
            $input->set('addon_task', $task);
        }
        else
        {
            // Base controller.
            $type = null;

            // Define the controller filename and path.
            $file       = self::createFileName('controller', array('name' => 'controller', 'format' => $format));

            $path       = $basePath . '/' . $file;
            $backupfile = self::createFileName('controller', array('name' => 'controller'));
            $backuppath = $basePath . '/' . $backupfile;
        }

        $_prefix    = explode('\\', $prefix, -1);
        $_prefix    = join('\\', $_prefix);

        // Get the controller class name.
        $class = ($prefix ? ucfirst($prefix) : '') . 'Controller' . ($type ? ucfirst($type) : '');

//        var_dump($class);
////        var_dump($basePath);
//        var_dump($path);
        var_dump($backuppath);
        var_dump(is_file($backuppath));
////        var_dump(Factory::getContainer()->has($class));
////        var_dump(Factory::getContainer()->has('DisplayController'));
        die(__METHOD__);

        // Include the class if not present.
        if (!class_exists($class)) {
            // If the controller file path exists, include it.
            if (is_file($path)) {
                require_once $path;
            } elseif (isset($backuppath) && is_file($backuppath)) {
                require_once $backuppath;
            } else {
                throw new \InvalidArgumentException(Text::sprintf('JLIB_APPLICATION_ERROR_INVALID_CONTROLLER', $type, $format));
            }
        }

//        // Instantiate the class.
//        if (!class_exists($class)) {
//            throw new \InvalidArgumentException(Text::sprintf('JLIB_APPLICATION_ERROR_INVALID_CONTROLLER_CLASS', $class));
//        }
//
//        // Check for a possible service from the container otherwise manually instantiate the class
//        if (Factory::getContainer()->has($class)) {
//            self::$instance = Factory::getContainer()->get($class);
//        } else {
//            self::$instance = new $class($config, null, $app, $input);
//        }


        $namespace  = 'TemPlaza\Component\TZ_Portfolio\AddOn';
        $class = $namespace.'\\'.$class;


        $backupClass    = $namespace.'\\'.$_prefix.'\\DisplayController';
////        var_dump($type);
////        var_dump($prefix);
//        var_dump();
        var_dump($class);
        var_dump(class_exists($class));
        var_dump(__METHOD__);
////        var_dump(($backupClass));
////        var_dump(class_exists($backupClass));
////        var_dump($backuppath);
//        die(__FILE__);

        // Include the class if not present.
        if (!class_exists($class))
        {
            // If the controller file path exists, include it.
            if (file_exists($path))
            {
                require_once $path;
            }
            elseif (isset($backuppath) && file_exists($backuppath))
            {
                require_once $backuppath;
            }
        }

        if(!class_exists($class)){
            $class  = $backupClass;
        }
        var_dump($class);
        var_dump(class_exists($class));
        var_dump(__METHOD__);
        die(__FILE__);

        // Instantiate the class.
        if (class_exists($class))
        {
            $controller = new $class($config);
            self::$instance[$prefix] = $controller;
//            self::$instance[$prefix] = new $class($config);
        }

        return self::$instance[$prefix];
    }

    protected function getCoreRedirect(){
        $link   = '';
        if($coreView = $this -> core_view_list){
            $link   = 'index.php?option='.$this -> option.'&view='.$this -> core_view_list;
        }
        return $link;
    }

    protected function getAddonRedirect($addon_view = null){
        $link   = $this -> getCoreRedirect();

        if($addon_id = $this->input -> getInt('addon_id')){
            $link   .= '&addon_id='.$addon_id;
        };

        if($addon_view){
            $link   .= '&addon_view='.$addon_view;
        }

        return $link;
    }
}