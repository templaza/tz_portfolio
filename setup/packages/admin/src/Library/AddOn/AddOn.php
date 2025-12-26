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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Library\AddOn;

defined('_JEXEC') or die;

use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Factory\LegacyFactory;
use Joomla\CMS\MVC\Factory\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryServiceInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryServiceTrait;
use Joomla\DI\Container;
use Joomla\Filesystem\Path;
use Joomla\Filesystem\File;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Filesystem\Folder;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Psr\Container\ContainerInterface;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioTemplate;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\ModuleHelper as TZ_PortfolioModuleHelper;

class AddOn extends CMSPlugin implements
    BootableExtensionInterface,
    MVCFactoryServiceInterface{

    use MVCFactoryServiceTrait;

    protected $special              = false;
    protected $vars                 = array();
    protected $data_manager         = false;
    protected $form;
    protected $item;
    protected $_adminPath = '';

    protected $_myFormDataBeforeSave;

    public function __construct($subject, $config = array())
    {
        parent::__construct($subject,$config);

        if(!empty($this -> _type) && !empty($this -> _name)) {
            \JLoader::registerNamespace('\\TemPlaza\\Component\\TZ_Portfolio\\AddOn\\'
                . ucfirst($this->_type) . '\\' . ucfirst($this->_name),
                $this->getAddOnPath('src'));

            \JLoader::registerNamespace('\\TemPlaza\\Component\\TZ_Portfolio\\AddOn\\'
                .ucfirst($this -> _type).'\\'.ucfirst($this -> _name).'\\Site',
                $this -> getAddOnPath('src'));
            if($adminPath = $this -> getAddOnPath('admin/src')) {
                \JLoader::registerNamespace('\\TemPlaza\\Component\\TZ_Portfolio\\AddOn\\'
                    . ucfirst($this->_type) . '\\' . ucfirst($this->_name) . '\\Administrator',
                    $adminPath);
                $this -> _adminPath = $adminPath;
            }
        }
    }

    /* @var Container $container */
    public function boot(ContainerInterface $container){}

    public function getDataManager(){
        if($plugin = AddonHelper::getAddOn($this -> _type, $this -> _name)){
            if(isset($plugin -> asset_id) &&  $plugin -> asset_id){
                $user   = Factory::getUser();
                if(!$user -> authorise('core.manage', 'com_tz_portfolio.addon.'.$plugin -> id)){
                    return false;
                }
            }
        }
        return $this -> data_manager;
    }

    public function onAddOnDisplayManager($task = null){

        $app    = $this -> getApplication();
        $input  = $app -> input;

        $addon_task   = $input -> get('addon_task');
        $mvc    = $this -> getMVCFactory();
        $config['factory'] = $mvc;

        $controller = $this -> getMVCFactory() -> createController($this -> _name, 'administrator', $config,
            $app, $input);
        if(!$controller){
            return '';
        }

        list($view, $task)  = explode('.', $addon_task);

//        tzportfolioplusimport('html.sidebar');
//        tzportfolioplusimport('controller.legacy');

//        $component_path = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components';

//        // Import addon_datas helper
//        JLoader::import('com_tz_portfolio.helpers.addon_datas',$component_path);
//
//        // Import addon_data model
//        JLoader::import('com_tz_portfolio.models.addon_data',$component_path);
//
//        // Import addon_datas model
//        JLoader::import('com_tz_portfolio.models.addon_datas',$component_path);

        ob_start();
        $controller -> execute($task);
        $controller -> redirect();
        $html   = ob_get_contents();
        ob_end_clean();

        if($html){
            $html   = trim($html);
            if(!empty($html)) {
                return $html;
            }
        }

        return false;
    }

    public function onAddContentType(){
        if($this -> _type == 'content') {
            $type = new \stdClass();
            $lang = Factory::getApplication() -> getLanguage();
            $lang_key = 'PLG_' . $this->_type . '_' . $this->_name . '_TITLE';
            $lang_key = strtoupper($lang_key);

            if ($lang->hasKey($lang_key)) {
                $type->text = Text::_($lang_key);
            } else {
                $type->text = $this->_name;
            }

            $type->value = $this->_name;

            return $type;
        }
    }

    public function onAddMediaType(){
        if($this -> _type == 'mediatype') {
            $type       = new \stdClass();
            $lang       = Factory::getApplication() -> getLanguage();
            $lang_key   = 'PLG_' . $this->_type . '_' . $this->_name . '_TITLE';
            $lang_key   = strtoupper($lang_key);

            if ($lang->hasKey($lang_key)) {
                $type->text = Text::_($lang_key);
            } else {
                $type->text = $this->_name;
            }

            $type->value = $this->_name;
            $type->special = $this->special;

            return $type;
        }
    }

    // Prepare form of the plugin ~ onContentPrepareForm of joomla
    public function onContentPrepareForm($form, $data){
        $app            = Factory::getApplication();
        $name           = $form -> getName();
        $extension      = null;
        $adminAllows    = array(
            'com_menus',
            'com_modules',
            'com_tz_portfolio',

        );

        if(strpos($name, '.')){
            list($extension, $view) = explode('.', $name, 2);
        }

        if (($app -> isClient('administrator') && in_array($extension, $adminAllows))
            || ($app -> isClient('site') && $name == 'com_tz_portfolio.form')) {

            $component_id   = null;
            if(!empty($data)){
                if(is_array($data) && isset($data['component_id'])){
                    $component_id   = $data['component_id'];
                }elseif(is_object($data) && isset($data -> component_id)){
                    $component_id   = $data -> component_id;
                }
            }
            $component = ComponentHelper::getComponent('com_tz_portfolio');

            // Load form for menu
            if($component_id && $name == 'com_menus.item' && $component -> id == $component_id){
                // Check if view of com_tz_portfolio
                $this -> menuPrepareForm($form, $data);
            }

            // Load form for module
            if($name == 'com_modules.module'){
                $this -> modulePrepareForm($form, $data);
            }

            // Load form for article and category create or edit form.
            if($name == 'com_tz_portfolio.article' || $name == 'com_tz_portfolio.category'
                || $name == 'com_tz_portfolio.form') {
                $this -> contentPrepareForm($form, $data);
            }
        }
        return true;
    }

    // Load xml form file for article view of the plugin (this trigger called in system tz_portfolio plugin)
    protected function contentPrepareForm($form, $data){
        $app        = Factory::getApplication();
        $context    = $form -> getName();

        if($app -> isClient('administrator') || ($app -> isClient('site') && $context  == 'com_tz_portfolio.form')){
            list($option, $viewName)    = explode('.', $context);

            // Load plugin's language
            AddonHelper::loadLanguage($this -> _name, $this -> _type);

            // Add plugin form's path
            Form::addFormPath(COM_TZ_PORTFOLIO_ADDON_PATH.'/'.$this -> _type.'/'.$this -> _name.'/admin/models/form');
            Form::addFormPath(COM_TZ_PORTFOLIO_ADDON_PATH.'/'.$this -> _type.'/'.$this -> _name.'/admin/models/forms');

            Form::addFieldPath(COM_TZ_PORTFOLIO_ADDON_PATH.'/'.$this -> _type.'/'.$this -> _name.'/admin/models/field');
            Form::addFieldPath(COM_TZ_PORTFOLIO_ADDON_PATH.'/'.$this -> _type.'/'.$this -> _name.'/admin/models/fields');

            if($app -> isClient('site') && $context  == 'com_tz_portfolio.form') {
                Form::addFormPath(COM_TZ_PORTFOLIO_ADDON_PATH . '/' . $this->_type . '/' . $this->_name . '/models/form');
                Form::addFormPath(COM_TZ_PORTFOLIO_ADDON_PATH . '/' . $this->_type . '/' . $this->_name . '/models/forms');

                Form::addFieldPath(COM_TZ_PORTFOLIO_ADDON_PATH . '/' . $this->_type . '/' . $this->_name . '/models/field');
                Form::addFieldPath(COM_TZ_PORTFOLIO_ADDON_PATH . '/' . $this->_type . '/' . $this->_name . '/models/fields');
            }

            $formPath   = COM_TZ_PORTFOLIO_ADDON_PATH.'/'.$this -> _type.'/'.$this -> _name.'/admin';

            if(file_exists($formPath.'/forms/views/'.$viewName.'.xml')){
                $file  = Path::clean($formPath.'/forms/views/'.$viewName.'.xml');
            }elseif(file_exists($formPath.'/form/view/'.$viewName.'.xml')){
                $file  = Path::clean($formPath.'/form/view/'.$viewName.'.xml');
            }elseif(file_exists($formPath.'/models/forms/'.$viewName.'.xml')){
                $file  = Path::clean($formPath.'/models/forms/'.$viewName.'.xml');
            }else{
                $file  = Path::clean($formPath.'/models/form/'.$viewName.'.xml');
            }

            // Load xml form file from above path
            if($viewName == 'article' || $viewName == 'form') {
                if(file_exists($file)){
                    // Get the plugin form.
                    if (!$form->loadFile($file, false, '/form/fields[@name="attribs"]'))
                    {
                        throw new \Exception(Text::_('JERROR_LOADFILE_FAILED'));
                    }
                }
            }else{
                $form->loadFile($file, false, '/form/fields[@name="params"]');
            }

        }
        return true;
    }

    // Load xml form file for menu in back-end of the plugin (this trigger called in system tz_portfolio plugin)
    protected function menuPrepareForm($form, $data){
        $app            = Factory::getApplication();
        if($app -> isClient('administrator')){
            $formFile       = false;
            $addonFormFile  = false;
            $link           = false;

            if($data){
                if(is_array($data) && isset($data['link']) && !empty($data['link'])){
                    $link   = $data['link'];
                }elseif(is_object($data) && isset($data -> link) && !empty($data -> link)){
                    $link = $data -> link;
                }
            }

            if($link){
                $base   = COM_TZ_PORTFOLIO_ADDON_PATH.'/'.$this -> _type.'/'.$this -> _name;

                $link   = htmlspecialchars_decode($link);

                // Parse the link arguments.
                $args = array();
                parse_str(parse_url(htmlspecialchars_decode($link), PHP_URL_QUERY), $args);

                // Load plugin's language
                AddonHelper::loadLanguage($this -> _name, $this -> _type);

                if (isset($args['view'])) {

                    $view = $args['view'];

                    // Determine the layout to search for.
                    if (isset($args['layout'])) {
                        $layout = $args['layout'];
                    } else {
                        $layout = 'default';
                    }

                    // Check for the layout XML file. Use standard xml file if it exists.
                    $tplFolders = array(
                        $base . '/view/' . $view . '/tmpl',
                        $base . '/views/' . $view . '/tmpl'
                    );

                    // Get addon view xml with don't views of core
                    if($args['view'] == 'addon' && isset($args['addon_view'])) {
                        $addonView   = $args['addon_view'];
                        if(isset($args['addon_layout'])){
                            $addOnlayout = $args['addon_layout'];
                        }
                        $tplFolders[]   = $base.'/view/'.$addonView.'/tmpl';
                        $tplFolders[]   = $base.'/views/'.$addonView.'/tmpl';
                    }

                    $path = Path::find($tplFolders, $layout . '.xml');

                    if (is_file($path))
                    {
                        $formFile = $path;
                    }

                    // Get addon view xml with don't views of core
                    if($args['view'] == 'addon' && isset($args['addon_view'])) {
                        $addonView   = $args['addon_view'];
                        if(isset($args['addon_layout'])){
                            $addOnLayout = $args['addon_layout'];
                        } else {
                            $addOnLayout = 'default';
                        }
                        $tpladdOnFolders = array(
                            $base . '/view/' . $addonView . '/tmpl',
                            $base . '/views/' . $addonView . '/tmpl'
                        );

                        $addonPath = Path::find($tpladdOnFolders, $addOnLayout . '.xml');

                        if (is_file($addonPath))
                        {
                            $addonFormFile = $addonPath;
                        }

                        if ($addonFormFile && $form->loadFile($addonFormFile, true, '/metadata'))
                        {
                            $form -> addFieldPath(COM_TZ_PORTFOLIO_ADDON_PATH.'/'.$this -> _type
                                .'/'.$this -> _name.'/admin/models/fields');
                        }
                    }
                }

                if($formFile) {
                    if ($form->loadFile($formFile, true))
                    {
                        return $form;
                    }
                }
            }
        }
        return true;
    }

    // Load xml form file for menu in back-end of the plugin (this trigger called in system tz_portfolio plugin)
    protected function modulePrepareForm($form, $data){
        $app            = Factory::getApplication();
        if($app -> isClient('administrator')){
            $formFile   = false;
            $link       = false;

            $base   = COM_TZ_PORTFOLIO_ADDON_PATH.'/'.$this -> _type.'/'.$this -> _name;

            // Load plugin's language
            AddonHelper::loadLanguage($this -> _name, $this -> _type);

            $modParams      = new Registry();
            $module_name    = null;

            if(!empty($data)){
                if(is_array($data) && isset($data['module'])){
                    $module_name   = $data['module'];
                    if(isset($data['params']) && is_string($data['params'])){
                        $modParams -> loadString($data['params']);
                    }else{
                        $modParams -> loadObject($data['params']);
                    }
                }elseif(is_object($data) && isset($data -> module)){
                    $module_name   = $data -> module;
                    if(isset($data -> params) && is_string($data -> params)){
                        $modParams -> loadString($data -> params);
                    }else{
                        $modParams -> loadObject($data -> params);
                    }
                }
            }else{
                $input  = $app -> input;
                $jform  = $input -> get($form -> getFormControl(),null, 'array');

                if($jform && isset($jform['module'])){
                    $module_name    = $jform['module'];
                }
            }

            // Load addon config for module from module
            $mAddonBasePath = JPATH_SITE.'/modules/'.$module_name.'/forms/ado_'.$this -> _type.'_'
                .$this -> _name.'.xml';

            if(!file_exists($mAddonBasePath)){
                $mAddonBasePath = JPATH_SITE.'/modules/'.$module_name.'/tmpl/plg_'.$this -> _type.'_'
                    .$this -> _name.'/config.xml';
            }

            if(file_exists($mAddonBasePath)){
                $form->loadFile($mAddonBasePath, true);
//                if ($form->loadFile($mAddonBasePath, true))
//                {
//                    return $form;
//                }
            }

            // Load addon config for module from addon
            // Get the modules if this addon support.
            if (is_dir($base)) {
                $folders = Folder::folders($base, '^module[s]?$', false, true);
            }

            $path = '';

            if (!empty($folders[0]))
            {
                $path = $folders[0];
            }

            if (is_dir($path))
            {
                $modules    = Folder::folders($path);
            }
            else
            {
                return false;
            }

            // Load config.xml file from modules in this addon
            $tplFolders = array(
                $base . '/admin/forms/modules',
                $base . '/admin/forms/module',
                $base . '/modules/' . $module_name,
                $base . '/module/' . $module_name
            );

            if($modPath = Path::find($tplFolders, $module_name.'.xml')){
                $form -> loadFile($modPath, true);
            }

            if($tplId = (int) $modParams -> def('template_id', 0)) {
                $tpTemplate = TZ_PortfolioTemplate::getTemplateById($tplId);
            }
            else{
                $tpTemplate = TZ_PortfolioTemplate::getTemplate(true);
            }

            $styleFolders         = array();

            if(isset($tpTemplate -> paths) && ($tpPaths = $tpTemplate -> paths)){
                foreach($tpPaths as $key => $_path){
                    $styleFolders[]  = $_path.'/ado_'.$this -> _type.'_'.$this -> _name;
                    $styleFolders[]  = $_path.'/plg_'.$this -> _type.'_'.$this -> _name;
                }
            }

            $path = Path::find($styleFolders, 'config.xml');

            if (is_file($path))
            {
                $formFile = $path;
            }

            if($formFile) {
                if ($form->loadFile($formFile, true))
                {
                    return $form;
                }
            }
        }
        return true;
    }

    // Display form upload image to add or edit of portfolio's article view
    public function onMediaTypeDisplayArticleForm($data=null){
        $html           = null;
        if($this -> _type == 'mediatype'){
            if($model = $this -> getModel()) {
                $model -> set('data', $data);

                if(method_exists($model, 'getForm')) {
                    $this->form = $model->getForm();
                }
                $this -> item   = $data;
                $path           = AddonHelper::getLayoutPath($this -> _type, $this -> _name, 'admin');

                if(file_exists($path)) {
                    ob_start();
                    require_once($path);
                    $html = ob_get_contents();
                    ob_end_clean();
                }

            }
        }
        return $html;
    }

    public function setVariable($variable, $value)
    {
        $this->vars[$variable] = $value;
    }

    public function getVariable($variable)
    {
        if(isset($this -> vars[$variable]) && $value = $this -> vars[$variable]){
            return $value;
        }
        return false;
    }

    // Display html for views in front-end.
    public function onContentDisplayMediaType($context, &$article, $params, $page = 0, $layout = 'default'){
        if($article){
            if(isset($article -> type) && $article -> type == $this -> _name) {
                list($extension, $vName) = explode('.', $context);
                if(in_array($extension, array('module', 'modules'))){

                    if($path = $this -> getModuleLayout($this -> _type, $this -> _name, $extension, $vName, $layout, $params)){

                        if ($this->vars)
                        {
                            extract($this->vars);
                        }

                        // Display html
                        ob_start();
                        include $path;
                        $html = ob_get_contents();
                        ob_end_clean();
                        $html = trim($html);
                        return $html;
                    }
                }else {
                    if($html = $this -> _getViewHtml($context,$article, $params, $layout)){
                        return $html;
                    }
                }
            }
        }
    }

    public function onContentDisplayArticleView($context, &$article, $params, $page = 0, $layout = null){
        if($this -> _type != 'mediatype'){
            list($extension, $vName)   = explode('.', $context);

            $item   = $article;

            if($extension == 'module' || $extension == 'modules'){
                if($path = $this -> getModuleLayout($this -> _type, $this -> _name, $extension, $vName, $layout, $params)){
                    // Display html
                    ob_start();
                    include $path;
                    $html = ob_get_contents();
                    ob_end_clean();
                    $html = trim($html);
                    return $html;
                }
            }else {
                if($html = $this -> _getViewHtml($context,$article, $params, $layout)){
                    return $html;
                }
            }
        }
    }

    protected function getModuleLayout($type, $name, $folder, $module, $layout = 'default', ?Registry $params = null, $tmpl=false): false|string
    {
        $path   = TZ_PortfolioModuleHelper::getAddOnModuleLayout($type, $name, $module, $layout, $folder, $params);
        return $path;
    }

    protected function _getViewHtml($context, &$article, $params, $layout = null){
        list($extension, $vName)   = explode('.', $context);

        $input      = Factory::getApplication()->input;
        if($addon = AddonHelper::getPlugin($this -> _type, $this -> _name)){

            // Check task with format: addon_name.addon_view.addon_task (example image.default.display);
            if($controller = AddonHelper::getAddonController($addon -> id, array(
                'article' => $article,
                'trigger_params' => $params
            ))){

                $task   = $input->get('addon_task', 'display');
                $input->set('addon_view', $vName);
                $input->set('addon_layout', 'default');
                if($layout) {
                    $input->set('addon_layout', $layout);
                }

                $html   = null;
                try {
                    ob_start();
                    $controller->execute($task);
                    $controller->redirect();
                    $html = ob_get_contents();
                    ob_end_clean();
                }catch (\Exception $e){
                    if($e -> getMessage()) {
//                        Factory::getApplication() ->enqueueMessage('Addon '.$this -> _name.': '.$e -> getMessage(), 'warning');
                    }
                }

                if($html){
                    $html   = trim($html);
                }
                $input -> set('addon_task', null);
                return $html;
            }
        }
    }

    protected function getModel($name = null, $clientType = '', $config = array('ignore_request' => true, 'client' => 'admin'))
    {
        $_name          = $name;
        if(!$name){
            $_name      = ucfirst($this -> _name);
        }
        $client = $clientType;
        if(!$clientType) {
            $client = (!isset($config['client']) || (isset($config['client'])
                    && $config['client'] != 'site'))?'Administrator':'Site';
        }
        $model  = $this -> mvcFactory -> createModel(ucfirst($_name), $client, $config);
        if (!$model) {
            $adminModelPath = $this->_adminPath . '/Model/' . ucfirst($_name) . 'Model.php';

            if (file_exists($adminModelPath)) {
                $declBefore = get_declared_classes();

                require_once $adminModelPath;

                $declAfter = array_diff(get_declared_classes(), $declBefore);

                // 1) Try fully-qualified expected class name
                $expectedFQCN = '\\TemPlaza\\Component\\TZ_Portfolio\\AddOn\\'
                    . ucfirst($this->_type) . '\\' . ucfirst($this->_name)
                    . '\\'.$client.'\\Model\\' . ucfirst($_name) . 'Model';

                if (class_exists($expectedFQCN, false)) {
                    $modelClass = $expectedFQCN;
                } else {
                    // 2) Fallback: detect any newly-declared class that ends with "Model"
                    $modelClass = null;
                    foreach ($declAfter as $c) {
                        if (strcasecmp(substr($c, -5), 'Model') === 0) {
                            $modelClass = $c;
                            break;
                        }
                    }

                    // 3) Also check short name (in case class was declared in global namespace)
                    if (!$modelClass && class_exists(ucfirst($_name) . 'Model', false)) {
                        $modelClass = ucfirst($_name) . 'Model';
                    }
                }

                if ($modelClass && class_exists($modelClass, false)) {
                    $model = new $modelClass($config);
                }
            }
        }

//        if(stripos($_name, 'Model') === false){
//            $_name .= 'Model';
//        }
//
//        $_prefix        = $prefix;
//        if(!$prefix) {
//            $_prefix = 'TemPlaza\Component\TZ_Portfolio\AddOn\\' . ucfirst($this->_type) . '\\'
//                . ucfirst($this->_name) . '\Administrator\Model\\';
//        }
//
//        $client = (!isset($config['client']) || (isset($config['client'])
//                && $config['client'] != 'site'))?DIRECTORY_SEPARATOR.'admin':'';
//        $path   = $this -> getAddOnPath($client.'/src/Model');
//
//        BaseDatabaseModel::addIncludePath($path, $_prefix);
//        $model  = BaseDatabaseModel::getInstance($_name,$_prefix, $config);

        if($model) {
            $model -> set('plugin_type', $this -> _type);
            $model->setState('params', $this->params);
            return $model;
        }
        return false;
    }

    // Function to load template file in back-end
    protected function loadTemplate($tpl = null){
        $layout = AddonHelper::getLayout();

        // Create the template file name based on the layout
        $file   = isset($tpl) ? $layout . '_' . $tpl : $layout;

        // Clean the file name
        $file   = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
        $path   = AddonHelper::getLayoutPath($this -> _type, $this -> _name, 'admin',$file);

        ob_start();
        require_once($path);
        $html   = ob_get_contents();
        ob_end_clean();
        return $html;

    }

    // Upload image and store data (from image form) in add or edit of portfolio's article view
    public function onContentAfterSave($context, $data, $isnew){
        if($context == 'com_tz_portfolio.article' || $context == 'com_tz_portfolio.form') {
            if($model  = $this -> getModel()) {
                if(method_exists($model,'save')) {
                    $model->save($data);
                }
            }
        }

    }

    public function onContentAfterDelete($context, $table){
        if($context == 'com_tz_portfolio.article' || $context == 'com_tz_portfolio.form') {
            if($model  = $this -> getModel()) {
                if(method_exists($model,'delete')) {
                    $model->delete($table);
                }
            }
        }
    }

    public function loadLanguage($extension = '', $basePath = JPATH_ADMINISTRATOR)
    {
        return AddonHelper::loadLanguage($this -> _name, $this -> _type);
    }

    public function onRenderAddonView(){
        $input      = Factory::getApplication() -> input;

        if($controller = AddonHelper::getAddonController($input -> get('addon_id'))){
            $adtask       = $input->get('addon_task');
            $task   = $adtask;
            if(strpos($adtask, '.')) {
                list($name, $task) = explode('.', $adtask);
            }
            $controller -> execute($task);
            $controller -> redirect();
        }
    }

    public function onAfterGetMenuTypeOptions(&$data, $object){
        $app    = Factory::getApplication();
        if($app -> isClient('administrator')){
            $input  = $app -> input;
            if($input -> get('option') == 'com_menus' && ($input -> get('view') == 'menutypes'
                    || $input -> get('view') == 'item')){
                $component  = COM_TZ_PORTFOLIO;
                if($data && isset($data[$component])){

                    $addon  = null;
                    $args   = array();
                    $views  = array();
                    $help   = null;

                    if($input -> get('view') == 'item') {
                        // Get Addon's information from data when create or edit menu
                        if($link   = $app -> getUserState('com_menus.edit.item.link')) {
                            parse_str(parse_url(htmlspecialchars_decode($link), PHP_URL_QUERY), $args);
                        }

                        if($id = $input -> getInt('id')){
                            $menus   = $app -> getMenu('site');
                            if($menu = $menus -> getItem($id)){
                                if(isset($menu -> query)){
                                    $args   = $menu -> query;
                                }
                            }
                        }

                        if(count($args) && isset($args['option']) && $args['option'] != $component){
                            return false;
                        }

                        if(isset($args['addon_id'])) {
                            $addon = AddonHelper::getPluginById($args['addon_id']);
                        }
                    }else{
                        // Get Addon's information when list menu types
                        $addon  = AddonHelper::getPlugin($this->_type, $this->_name);
                    }

                    if(!$addon){
                        return false;
                    }

                    $addonPath = COM_TZ_PORTFOLIO_ADDON_PATH . '/' . $addon->type . '/' . $addon->name;

                    // Get the views of this addon.
                    if (is_dir($addonPath)) {
                        $folders = Folder::folders($addonPath, '^view[s]?$', false, true);
                    }

                    $path = '';

                    if (!empty($folders[0])) {
                        $path = $folders[0];
                    }


                    if(isset($args['addon_view'])){
                        $views[]    = $args['addon_view'];
                    }else {
                        if (is_dir($path)) {
                            $views = Folder::folders($path);
                        } else {
                            return false;
                        }
                        // Filter views of addon with views of this component
                        $cViews = array('article', 'categories', 'date', 'form', 'portfolio',
                            'search', 'tags', 'users', 'addon');
                        $views = array_diff($views, $cViews);
                    }

                    foreach ($views as $view)
                    {
                        $options     = array();
                        $layouts     = array();

                        $lPath  = $path.'/'.$view.'/tmpl';

                        if (is_dir($lPath))
                        {
                            $layouts = array_merge($layouts, Folder::files($lPath, '.xml$', false, true));
                        }

                        // Build list of standard layout names
                        foreach ($layouts as $layout)
                        {

                            // Ignore private layouts.
                            if (strpos(basename($layout), '_') === false)
                            {
                                $file = $layout;

                                // Get the layout name.
                                $layout = basename($layout, '.xml');

                                // Create the menu option for the layout.
                                $o = new JObject;
                                $o->title       = ucfirst($layout);
                                $o->description = '';
                                $o->request     = array('option' => $component, 'view' => 'addon',
                                    'addon_id' => $addon -> id, 'addon_view' => $view);

                                // Load layout metadata if it exists.
                                if (is_file($file))
                                {
                                    // Attempt to load the xml file.
                                    if ($xml = simplexml_load_file($file))
                                    {
                                        // Look for the first view node off of the root node.
                                        if ($menu = $xml->xpath('layout[1]'))
                                        {
                                            $menu = $menu[0];

                                            // If the view is hidden from the menu, discard it and move on to the next view.
                                            if (!empty($menu['hidden']) && $menu['hidden'] == 'true')
                                            {
                                                unset($xml);
                                                unset($o);
                                                continue;
                                            }

                                            // Populate the title and description if they exist.
                                            if (!empty($menu['title']))
                                            {
                                                $title      = trim((string) $menu['title']);
                                                $o->title   = Text::_('COM_TZ_PORTFOLIO_ADDON')
                                                    .' - '.Text::_($title);
                                            }

                                            if (!empty($menu->message[0]))
                                            {
                                                $o->description = trim((string) $menu->message[0]);
                                            }
                                        }
                                    }
                                }
                                $object -> addReverseLookupUrl($o);
                                // Add the layout to the options array.
                                $options[] = $o;
                            }
                        }
                        if(count($options)){
                            $data[$component]   = array_merge($data[$component], $options);
                        }
                    }
                }
            }
        }
    }

    /*
     * Register form with position to article form
     * @article: the article data.
     * Return form with position:
     *  -title: title of form to display in article form
     *  -html: html of form to display in article form
     *  -position: position (description, before_description or after_description) to display in article form
     * Since v2.4.3
     * */
    public function onAddFormBeforeArticleDescription($article = null){
        return;
    }
    public function onAddFormToArticleDescription($article = null){
        return;
    }
    public function onAddFormAfterArticleDescription($article = null){
        return;
    }

    /*
     * Get article form data to set global
     * Since v2.4.3
     * */
    public function onUserBeforeDataValidation($form, $data){
        $this -> onContentValidateData($form, $data);
    }
    /*
     * Use for joomla 4.x
     * */
    public function onContentValidateData ($form, $data){
        $context    = $form -> getName();
        if($context == 'com_tz_portfolio.article' || $context == 'com_tz_portfolio.form') {
            $addon_data = (!empty($data) && isset($data['addon']))?$data['addon']:array();
            $mydata     = (isset($addon_data[$this -> _name]) && !empty($addon_data[$this -> _name]))?$addon_data[$this -> _name]:array();
            $this->_myFormDataBeforeSave = $mydata;
        }
    }
    /*
     * Generate object with title, position, html form... to add to article form
     * Since v2.4.3
     * */
    protected function __addFormToPosition($article = null, $position = 'before_description'){
        $_position  = new \stdClass();
        $lang       = Factory::getApplication() -> getLanguage();
        $lang_key   = 'PLG_' . $this->_type . '_' . $this->_name . '_TITLE';
        $lang_key   = strtoupper($lang_key);
        $model      = null;
        $this -> form   = null;

        if ($lang->hasKey($lang_key)) {
            $_position -> title = Text::_($lang_key);
        } else {
            $_position -> title = $this->_name;
        }

        $_position -> addon  = $this->_name;
        $_position -> group  = $this->_type;

        $_position -> position   = $position;
        if($model = $this -> getModel($this -> _name, 'Administrator')) {
            // Get addon info
            $addon      = AddonHelper::getPlugin($this -> _type, $this -> _name);
            $model->setState($this->_name . '.addon_id', $addon -> id);

            $table  = $model -> getTable();
            if($table -> load(array('extension_id' => $addon -> id, 'content_id' => $article -> id))) {
                $model->setState($this->_name . '.id', (int)$table->get('id'));
                $properties = $table->getProperties(1);

                $data = ArrayHelper::toObject($properties);

                if($data && isset($data -> value) && is_string($data -> value)){
                    $data -> value  = json_decode($data -> value);
                }
            }

            $path           = AddonHelper::getLayoutPath($this -> _type, $this -> _name, 'admin');

            if(method_exists($model, 'getForm')) {
                $this->form = $model->getForm();
            }else {
                $this->form->loadFile(COM_TZ_PORTFOLIO_ADDON_PATH . '/' . $this->_type . '/' . $this->_name
                    . '/admin/models/forms/' . $this->_name . '.xml', false);
            }
            if(!empty($this -> form)){
                $_data   = new \stdClass();
                $_data -> addon  = new \stdClass();
                if(isset($data)) {
                    $_data->addon->{$this->_name} = $data->value;
                }
                $this -> form -> bind($_data);
            }

            $this -> item   = $article;
            if(File::exists($path) && isset($this -> form) && $this -> form) {
                ob_start();
                require $path;
                $content = ob_get_contents();
                ob_end_clean();
                $_position -> html = $content;
            }

        }

        return $_position;
    }

    protected function getAddOnPath($key = ''){
        $path   = COM_TZ_PORTFOLIO_ADDON_PATH.'/'.$this -> _type.'/'.$this -> _name;
        $path  .= !empty($key)?'/'.str_replace('.', '/', $key):'';

        $path   = Path::clean($path);

        if(!is_dir($path)){
            return false;
        }

        return $path;
    }

}