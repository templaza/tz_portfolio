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
        return $this -> data_manager;
    }

    public function onAddOnDisplayManager($task = null){
        tzportfolioplusimport('html.sidebar');
        tzportfolioplusimport('controller.legacy');

        $component_path = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components';

        // Import addon_datas controller
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
        $app = JFactory::getApplication();
        if ($app->isAdmin()) {
            $name           = $form -> getName();

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
            if($name == 'com_tz_portfolio_plus.article' || $name == 'com_tz_portfolio_plus.category') {
                $this -> contentPrepareForm($form, $data);
            }
        }
        return $form;
    }

//    public function onAlwaysLoadDocument($context){
//        try{
//
//            list($option,$vName) = explode('.',$context);
//
//            if($option != 'module' && $option != 'modules'){
//                if($view = $this -> getView($vName)) {
//                    if(method_exists($view, 'addDocument')){
//                        $view -> addDocument();
//                        return true;
//                    }
//                }
//            }
//        }
//        catch(Exception $e){
//            $this -> setError($e -> getMessage());
//            return false;
//        }
//        return false;
//    }

    // Load xml form file for article view of the plugin (this trigger called in system tz_portfolio_plus plugin)
    protected function contentPrepareForm($form, $data){
        $app            = JFactory::getApplication();
        if($app -> isAdmin()){
            $context    = $form -> getName();
            list($option, $viewName)    = explode('.', $context);

            // Load plugin's language
            $language   = JFactory::getLanguage();
            $language -> load('plg_'.$this -> _type.'_'.$this -> _name);

            // Add plugin form's path
            JForm::addFormPath(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.'/'.$this -> _type.'/'.$this -> _name.'/admin/models/form');
            JForm::addFormPath(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.'/'.$this -> _type.'/'.$this -> _name.'/admin/models/forms');

            JForm::addFieldPath(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.'/'.$this -> _type.'/'.$this -> _name.'/admin/models/field');
            JForm::addFieldPath(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.'/'.$this -> _type.'/'.$this -> _name.'/admin/models/fields');

            // Load xml form file from above path
            if($viewName == 'article') {
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
            $formFile   = false;
            $link       = false;

            if($data){
                if(isset($data['link']) && !empty($data['link'])){
                    $link   = $data['link'];
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
                        $base . '/views/' . $view . '/tmpl',
                        $base . '/view/' . $view . '/tmpl'
                    );
                    $path = JPath::find($tplFolders, $layout . '.xml');

                    if (is_file($path))
                    {
                        $formFile = $path;
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
            }

            if($module_name) {
                    JFactory::getSession()->set('com_tz_portfolio_plus.plugin.module_name', $module_name);
            }

            $tplFolders = array(
                $base . '/modules/' . JFactory::getSession() -> get('com_tz_portfolio_plus.plugin.module_name'),
                $base . '/module/' . JFactory::getSession() -> get('com_tz_portfolio_plus.plugin.module_name')
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

                if(JFile::exists($path)) {
                    ob_start();
                    require_once($path);
                    $html = ob_get_contents();
                    ob_end_clean();
                }

            }
        }
        return $html;
    }

    public function onExtensionAfterSave($context, $data, $isnew){
        $app = JFactory::getApplication();
        if ($app->isAdmin()) {
            JFactory::getSession() -> clear('com_tz_portfolio_plus.plugin.module_name');
        }
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

                    if($path = $this -> getModuleLayout($this -> _type, $this -> _name, $extension, $vName, $layout)){

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
        list($extension, $vName)   = explode('.', $context);

        $item   = $article;

        if($extension == 'module' || $extension == 'modules'){
            if($path = $this -> getModuleLayout($this -> _type, $this -> _name, $extension, $vName, $layout)){
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

    protected function getModuleLayout($type, $name, $folder, $module, $layout = 'default',$tmpl=false){
        $template = JFactory::getApplication()->getTemplate();
        $defaultLayout = $layout;

        if (strpos($layout, ':') !== false)
        {
            // Get the template and file name from the string
            $temp = explode(':', $layout);
            $template = ($temp[0] == '_') ? $template : $temp[0];
            $layout = $temp[1];
            $defaultLayout = ($temp[1]) ? $temp[1] : 'default';
        }

        $tmpl_folder    = null;
        if($tmpl){
            $tmpl_folder    = '/tmpl';
        }

        if(!$layout){
            $layout = 'default';
        }

        // Build the template and base path for the layout
        $tPath = JPATH_THEMES . '/' . $template . '/html/'.$module.'/plg_' . $type . '_' . $name . '/' . $layout . '.php';
        $bPath = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $type . '/' . $name . '/'.$folder.'/'.$module
            .$tmpl_folder.'/'. $defaultLayout . '.php';
        $dPath = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $type . '/' . $name . '/'.$folder.'/'.$module
            .$tmpl_folder.'/default.php';

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
            if(file_exists($dPath)) {
                return $dPath;
            }
        }
        return false;
    }

    protected function _getViewHtml($context, &$article, $params, $layout = null){
        list($extension, $vName)   = explode('.', $context);

        $input      = JFactory::getApplication()->input;
        $addon_id   = $input -> getInt('addon_id');
        $addon      = TZ_Portfolio_PlusPluginHelper::getPlugin($this -> _type, $this -> _name);

        if(!$addon_id || ($addon_id && $addon_id == $addon -> id)){
            tzportfolioplusimport('controller.legacy');
            $result = true;
            // Check task with format: addon_name.addon_view.addon_task (example image.default.display);
            $adtask     = $input -> get('addon_task');
            if($adtask && strpos($adtask,'.') > 0 && !$addon_id){
                list($plgname,$adtask) = explode('.',$adtask,2);
                if($plgname == $this -> _name){
                    $result = true;
                    $input -> set('addon_task',$adtask);
                }else{
                    $result = false;
                }
            }
            if($result && $controller = TZ_Portfolio_Plus_AddOnControllerLegacy::getInstance('PlgTZ_Portfolio_Plus'
                    .ucfirst($this -> _type).ucfirst($this -> _name)
                    , array('base_path' => COM_TZ_PORTFOLIO_PLUS_ADDON_PATH
                        .DIRECTORY_SEPARATOR.$this -> _type
                        .DIRECTORY_SEPARATOR.$this -> _name))) {
                tzportfolioplusimport('plugin.modelitem');

                $controller -> set('addon', $addon);
                $controller -> set('article', $article);
                $controller -> set('trigger_params', $params);

                $task   = $input->get('addon_task');

                if(!$task && !$addon_id) {
                    $input->set('addon_view', $vName);
                    $input->set('addon_layout', 'default');
                    if($layout) {
                        $input->set('addon_layout', $layout);
                    }
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
                        JFactory::getApplication() ->enqueueMessage('Addon '.$this -> _name.': '.$e -> getMessage(), 'warning');
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
        if($context == 'com_tz_portfolio_plus.article') {
            if($model  = $this -> getModel()) {
                if(method_exists($model,'save')) {
                    $model->save($data);
                }
            }
        }

    }

    public function onContentAfterDelete($context, $table){
        if($context == 'com_tz_portfolio_plus.article') {
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
}