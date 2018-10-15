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

use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\Registry\Registry;

jimport('joomla.filesytem.file');
JLoader::import('framework',JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/includes');
tzportfolioplusimport('plugin.modeladmin');
tzportfolioplusimport('model.admin');
tzportfolioplusimport('route');

class TZ_Portfolio_PlusPlugin extends JPlugin{
    protected $special              = false;
    protected $vars                 = array();
    protected $data_manager         = false;

    public function __construct(&$subject, $config = array())
    {
        JModelLegacy::addIncludePath(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.DIRECTORY_SEPARATOR.$config['type']
            .DIRECTORY_SEPARATOR.$config['name'].DIRECTORY_SEPARATOR.'models','PlgTZ_Portfolio_Plus'.$config['type'].'Model');

        JLoader::register('TZ_Portfolio_PlusPluginHelper',COM_TZ_PORTFOLIO_PLUS_LIBRARIES
            .DIRECTORY_SEPARATOR.'plugin'.DIRECTORY_SEPARATOR.'helper.php');

        parent::__construct($subject,$config);
    }

    public function getDataManager(){
        if($plugin = TZ_Portfolio_PlusPluginHelper::getPlugin($this -> _type, $this -> _name)){
            if(isset($plugin -> asset_id) &&  $plugin -> asset_id){
                $user   = TZ_Portfolio_PlusUser::getUser();
                if(!$user -> authorise('core.manage', 'com_tz_portfolio_plus.addon.'.$plugin -> id)){
                    return false;
                }
            }
        }
        return $this -> data_manager;
    }

    public function onAddOnDisplayManager($task = null){

        tzportfolioplusimport('html.sidebar');
        tzportfolioplusimport('controller.legacy');

        $component_path = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components';

        // Import addon_datas helper
        JLoader::import('com_tz_portfolio_plus.helpers.addon_datas',$component_path);

        // Import addon_data model
        JLoader::import('com_tz_portfolio_plus.models.addon_data',$component_path);

        // Import addon_datas model
        JLoader::import('com_tz_portfolio_plus.models.addon_datas',$component_path);

        ob_start();
        JLoader::import($this -> _type.'.'.$this -> _name.'.admin.'.$this -> _name,COM_TZ_PORTFOLIO_PLUS_ADDON_PATH);
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
            $type = new stdClass();
            $lang = JFactory::getLanguage();
            $lang_key = 'PLG_' . $this->_type . '_' . $this->_name . '_TITLE';
            $lang_key = strtoupper($lang_key);

            if ($lang->hasKey($lang_key)) {
                $type->text = JText::_($lang_key);
            } else {
                $type->text = $this->_name;
            }

            $type->value = $this->_name;

            return $type;
        }
    }

    public function onAddMediaType(){
        if($this -> _type == 'mediatype') {
            $type = new stdClass();
            $lang = JFactory::getLanguage();
            $lang_key = 'PLG_' . $this->_type . '_' . $this->_name . '_TITLE';
            $lang_key = strtoupper($lang_key);

            if ($lang->hasKey($lang_key)) {
                $type->text = JText::_($lang_key);
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
        $app    = JFactory::getApplication();
        $name   = $form -> getName();

        if ($app->isAdmin() || ($app -> isSite() && $name == 'com_tz_portfolio_plus.form')) {

            $component_id   = null;
            if(!empty($data)){
                if(is_array($data) && isset($data['component_id'])){
                    $component_id   = $data['component_id'];
                }elseif(is_object($data) && isset($data -> component_id)){
                    $component_id   = $data -> component_id;
                }
            }
            $component = JComponentHelper::getComponent('com_tz_portfolio_plus');

            // Load form for menu
            if($component_id && $name == 'com_menus.item' && $component -> id == $component_id){
                // Check if view of com_tz_portfolio_plus
                $this -> menuPrepareForm($form, $data);
            }

            // Load form for module
            if($name == 'com_modules.module'){
                $this -> modulePrepareForm($form, $data);
            }

            // Load form for article and category create or edit form.
            if($name == 'com_tz_portfolio_plus.article' || $name == 'com_tz_portfolio_plus.category'
                || $name == 'com_tz_portfolio_plus.form') {
                $this -> contentPrepareForm($form, $data);
            }
        }
        return $form;
    }

    // Load xml form file for article view of the plugin (this trigger called in system tz_portfolio_plus plugin)
    protected function contentPrepareForm($form, $data){
        $app        = JFactory::getApplication();
        $context    = $form -> getName();

        if($app -> isAdmin() || ($app -> isSite() && $context  == 'com_tz_portfolio_plus.form')){
            list($option, $viewName)    = explode('.', $context);

            // Load plugin's language
            $language   = JFactory::getLanguage();
            $language -> load('plg_'.$this -> _type.'_'.$this -> _name);

            // Add plugin form's path
            JForm::addFormPath(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.'/'.$this -> _type.'/'.$this -> _name.'/admin/models/form');
            JForm::addFormPath(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.'/'.$this -> _type.'/'.$this -> _name.'/admin/models/forms');

            JForm::addFieldPath(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.'/'.$this -> _type.'/'.$this -> _name.'/admin/models/field');
            JForm::addFieldPath(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.'/'.$this -> _type.'/'.$this -> _name.'/admin/models/fields');

            if($app -> isSite() && $context  == 'com_tz_portfolio_plus.form') {
                JForm::addFormPath(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $this->_type . '/' . $this->_name . '/models/form');
                JForm::addFormPath(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $this->_type . '/' . $this->_name . '/models/forms');

                JForm::addFieldPath(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $this->_type . '/' . $this->_name . '/models/field');
                JForm::addFieldPath(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $this->_type . '/' . $this->_name . '/models/fields');
            }

            // Load xml form file from above path
            if($viewName == 'article' || $viewName == 'form') {
                $form->loadFile($viewName, false, '/form/fields[@name="attribs"]');
            }else{
                $form->loadFile($viewName, false, '/form/fields[@name="params"]');
            }
        }
        return true;
    }

    // Load xml form file for menu in back-end of the plugin (this trigger called in system tz_portfolio_plus plugin)
    protected function menuPrepareForm($form, $data){
        $app            = JFactory::getApplication();
        if($app -> isAdmin()){
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
                $base   = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.'/'.$this -> _type.'/'.$this -> _name;

                $link   = htmlspecialchars_decode($link);

                // Parse the link arguments.
                $args = array();
                parse_str(parse_url(htmlspecialchars_decode($link), PHP_URL_QUERY), $args);

                // Load plugin's language
                $language   = JFactory::getLanguage();
                $language -> load('plg_'.$this -> _type.'_'.$this -> _name);

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

                    $path = JPath::find($tplFolders, $layout . '.xml');

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

                        $addonPath = JPath::find($tpladdOnFolders, $addOnLayout . '.xml');

                        if (is_file($addonPath))
                        {
                            $addonFormFile = $addonPath;
                        }

                        if ($addonFormFile && $form->loadFile($addonFormFile, true, '/metadata'))
                        {
                            $form -> addFieldPath(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.'/'.$this -> _type
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

    // Load xml form file for menu in back-end of the plugin (this trigger called in system tz_portfolio_plus plugin)
    protected function modulePrepareForm($form, $data){
        $app            = JFactory::getApplication();
        if($app -> isAdmin()){
            $formFile   = false;
            $link       = false;


            $base   = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.'/'.$this -> _type.'/'.$this -> _name;

            // Load plugin's language
            $language   = JFactory::getLanguage();
            $language -> load('plg_'.$this -> _type.'_'.$this -> _name);

            $module_name   = null;
            if(!empty($data)){
                if(is_array($data) && isset($data['module'])){
                    $module_name   = $data['module'];
                }elseif(is_object($data) && isset($data -> module)){
                    $module_name   = $data -> module;
                }
            }else{
                $input  = $app -> input;
                $jform  = $input -> get($form -> getFormControl(),null, 'array');

                if($jform && isset($jform['module'])){
                    $module_name    = $jform['module'];
                }
            }

            // Load addon config for module from module
            $mAddonBasePath = JPATH_SITE.'/modules/'.$module_name.'/tmpl/plg_'.$this -> _type.'_'
                .$this -> _name.'/config.xml';
            if(file_exists($mAddonBasePath)){
                if ($form->loadFile($mAddonBasePath, true))
                {
                    return $form;
                }
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

            if(!$module_name || ($module_name && !in_array($module_name, $modules))){
                return false;
            }

            // Load config.xml file from modules in this addon
            $tplFolders = array(
                $base . '/modules/' . $module_name,
                $base . '/module/' . $module_name
            );

            $path = JPath::find($tplFolders, 'config.xml');

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
                $path           = TZ_Portfolio_PlusPluginHelper::getLayoutPath($this -> _type, $this -> _name, 'admin');

                if(\JFile::exists($path)) {
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

                    tzportfolioplusimport('plugin.modelitem');

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
                tzportfolioplusimport('plugin.modelitem');

                if($html = $this -> _getViewHtml($context,$article, $params, $layout)){
                    return $html;
                }
            }
        }
    }

    protected function getModuleLayout($type, $name, $folder, $module, $layout = 'default', Registry $params = null,$tmpl=false){
        $path   = TZ_Portfolio_PlusModuleHelper::getAddOnModuleLayout($type, $name, $module, $layout, $folder, $params);
        return $path;
    }

    protected function _getViewHtml($context, &$article, $params, $layout = null){
        list($extension, $vName)   = explode('.', $context);

        $input      = JFactory::getApplication()->input;
        $addon      = TZ_Portfolio_PlusPluginHelper::getPlugin($this -> _type, $this -> _name);

        // Check task with format: addon_name.addon_view.addon_task (example image.default.display);
        if($controller = TZ_Portfolio_PlusPluginHelper::getAddonController($addon -> id, array(
            'article' => $article,
            'trigger_params' => $params
        ))){

            $task   = $input->get('addon_task');
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
            }catch (Exception $e){
                if($e -> getMessage()) {
//                        JFactory::getApplication() ->enqueueMessage('Addon '.$this -> _name.': '.$e -> getMessage(), 'warning');
                }
            }

            if($html){
                $html   = trim($html);
            }
            $input -> set('addon_task', null);
            return $html;
        }
    }

    protected function getModel($name = null, $prefix = null, $config = array('ignore_request' => true))
    {
        $_name          = $name;
        if(!$name){
            $_name      = ucfirst($this -> _name);
        }
        $_prefix        = $prefix;
        if(!$prefix){
            $_prefix    = 'PlgTZ_Portfolio_Plus'.ucfirst($this -> _type).'Model';
        }
        JModelLegacy::addIncludePath(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.DIRECTORY_SEPARATOR.$this -> _type
            .DIRECTORY_SEPARATOR.$this -> _name.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'models',$_prefix);
        if($model  = JModelLegacy::getInstance($_name,$_prefix, $config)) {
            $model -> set('plugin_type', $this -> _type);
            $model->setState('params', $this->params);
            return $model;
        }
        return false;
    }

    // Function to load template file in back-end
    protected function loadTemplate($tpl = null){
        $layout = TZ_Portfolio_PlusPluginHelper::getLayout();

        // Create the template file name based on the layout
        $file   = isset($tpl) ? $layout . '_' . $tpl : $layout;

        // Clean the file name
        $file   = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
        $path   = TZ_Portfolio_PlusPluginHelper::getLayoutPath($this -> _type, $this -> _name, 'admin',$file);

        ob_start();
        require_once($path);
        $html   = ob_get_contents();
        ob_end_clean();
        return $html;

    }

    // Upload image and store data (from image form) in add or edit of portfolio's article view
    public function onContentAfterSave($context, $data, $isnew){
        if($context == 'com_tz_portfolio_plus.article' || $context == 'com_tz_portfolio_plus.form') {
            if($model  = $this -> getModel()) {
                if(method_exists($model,'save')) {
                    $model->save($data);
                }
            }
        }

    }

    public function onContentAfterDelete($context, $table){
        if($context == 'com_tz_portfolio_plus.article' || $context == 'com_tz_portfolio_plus.form') {
            if($model  = $this -> getModel()) {
                if(method_exists($model,'delete')) {
                    $model->delete($table);
                }
            }
        }
    }

    public function loadLanguage($extension = '', $basePath = JPATH_ADMINISTRATOR)
    {
        if (empty($extension))
        {
            $extension = 'plg_' . $this->_type . '_' . $this->_name;
        }

        $lang   = JFactory::getLanguage();
        $load   = $lang->load(strtolower($extension), COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $this->_type . '/' . $this->_name, null, false, true);

        return $load;
    }

    public function onRenderAddonView(){

        tzportfolioplusimport('plugin.modelitem');

        $input      = JFactory::getApplication() -> input;

        if($controller = TZ_Portfolio_PlusPluginHelper::getAddonController($input -> get('addon_id'))){
            $task       = $input->get('addon_task');
            $controller -> execute($task);
            $controller -> redirect();
        }
    }

    public function onAfterGetMenuTypeOptions(&$data, $object){
        $app    = JFactory::getApplication();
        if($app -> isAdmin()){
            $input  = $app -> input;
            if($input -> get('option') == 'com_menus' && ($input -> get('view') == 'menutypes'
                    || $input -> get('view') == 'item')){
                $component  = COM_TZ_PORTFOLIO_PLUS;
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

                        if(count($args) && $args['option'] != $component){
                            return false;
                        }

                        if(isset($args['addon_id'])) {
                            $addon = TZ_Portfolio_PlusPluginHelper::getPluginById($args['addon_id']);
                        }
                    }else{
                        // Get Addon's information when list menu types
                        $addon  = TZ_Portfolio_PlusPluginHelper::getPlugin($this->_type, $this->_name);
                    }

                    if(!$addon){
                        return false;
                    }

                    $addonPath = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $addon->type . '/' . $addon->name;

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
                                                $o->title   = JText::_('COM_TZ_PORTFOLIO_PLUS_ADDON')
                                                    .' - '.JText::_($title);
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

}