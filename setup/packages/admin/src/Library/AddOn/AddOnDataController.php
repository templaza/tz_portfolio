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
use Joomla\CMS\MVC\View\ViewInterface;
use Joomla\CMS\Router\Route;
use Joomla\CMS\User\CurrentUserInterface;
use Joomla\Filesystem\Path;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioTemplate;

class AddOnDataController extends BaseController {
    protected static $instance;

    protected $addon;
    protected $context;

    protected $article;

    protected $trigger_params;

    protected $default_view = '';
    protected $base_view = null;
    public function __construct($config = array())
    {
        if(isset($config['addon'])){
            $this -> addon = $config['addon'];
        }

        $config['base_path'] = COM_TZ_PORTFOLIO_ADDON_PATH.'/'.$this -> addon -> type
            .'/'.$this -> addon -> name .(Factory::getApplication()->isClient('administrator') ? '/admin' : '');

        parent::__construct($config);

        if(isset($config['article'])){
            $this -> article        = $config['article'];
        }
        if(isset($config['trigger_params'])){
            $this -> trigger_params = $config['trigger_params'];
        }

        $this->paths['view']    = '';

        if(isset($config['factory'])){
            $this -> factory    = $config['factory'];
        }

        AddonHelper::loadLanguage($this->addon->name, $this->addon->type);
    }

    public function display($cachable = false, $urlparams = array()) {
        $document   = $this->app->getDocument();
        $viewType   = $document->getType();
        $viewName   = $this->input->get('addon_view', $this->default_view);
        $viewLayout = $this->input->get('layout', 'default', 'string');
        $view = $this->getView($viewName, $viewType, '', ['base_path' => $this->basePath, 'layout' => $viewLayout]);
        // Set models for the View
        $this->prepareViewModel($view);
        $view->display();
    }

    public function getView($name = '', $type = '', $prefix = '', $config = array())
    {
        $app = Factory::getApplication();
        $prefix = empty($prefix) ? ($app->isClient('administrator') ? 'Administrator' : 'Site') : $prefix;
        $viewClass = '\\TemPlaza\\Component\\TZ_Portfolio\\AddOn\\' . ucfirst($this -> addon -> type) . '\\' . ucfirst($this -> addon -> name) . '\\'.$prefix.'\\View\\' . ucfirst($name) . '\\HtmlView';

        if (!class_exists($viewClass)) {
            $file = Path::clean($config['base_path'] . '/View/' . ucfirst($name) . '/HtmlView.php');

            if (file_exists($file)) {
                require_once $file;
            } else {
                return '';
            }
        }

        if (!class_exists($viewClass)) {
            return '';
        }
        return new $viewClass($config);
    }

    protected function prepareViewModel(ViewInterface $view)
    {
        if (!method_exists($view, 'setModel')) {
            return;
        }
        $viewName = $view->getName();
        // Get/Create the model
        if ($model = $this->getModel($viewName, '', ['base_path' => $this->basePath])) {
            // Push the model into the view (as default)
            $view->setModel($model, true);
        }
    }

    public function getModel($name = '', $prefix = '', $config = [])
    {
        if (empty($name)) {
            $name = $this->getName();
        }

        if (!$prefix) {
            $app = Factory::getApplication();
            $prefix = $app->isClient('administrator') ? 'Administrator' : 'Site';
        }

        if ($model = $this->createModel($name, $prefix, $config)) {
            // Task is a reserved state
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
                    $params = $menu->getParams($item->id);

                    // Set default state data
                    $model->setState('parameters.menu', $params);
                }
            }
        }
        return $model;
    }
    protected function createModel($name, $prefix = '', $config = [])
    {
        $app = Factory::getApplication();
        $prefix = empty($prefix) ? ($app->isClient('administrator') ? 'Administrator' : 'Site') : $prefix;

        $modelClass = '\\TemPlaza\\Component\\TZ_Portfolio\\AddOn\\' . ucfirst($this -> addon -> type) . '\\' . ucfirst($this -> addon -> name) . '\\'.$prefix.'\\Model\\' . ucfirst($name) . 'Model';

        if (!class_exists($modelClass)) {
            $file = Path::clean($config['base_path'] . '/Model/' . ucfirst($name) . 'Model.php');

            if (file_exists($file)) {
                require_once $file;
            } else {
                return '';
            }
        }

        if (!class_exists($modelClass)) {
            return '';
        }

        $model = new $modelClass($config);

        if ($model === null) {
            return false;
        }

        if ($model instanceof CurrentUserInterface && $this->app->getIdentity()) {
            $model->setCurrentUser($this->app->getIdentity());
        }

        return $model;
    }
}