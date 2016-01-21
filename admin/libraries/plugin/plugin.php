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

class TZ_Portfolio_PlusPlugin extends JPlugin{
    protected $special              = false;
    protected $vars                 = array();

    public function __construct(&$subject, $config = array())
    {
        JModelLegacy::addIncludePath(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.DIRECTORY_SEPARATOR.$config['type']
            .DIRECTORY_SEPARATOR.$config['name'].DIRECTORY_SEPARATOR.'models','PlgTZ_Portfolio_Plus'.$config['type'].'Model');

        JLoader::register('TZ_Portfolio_PlusPluginHelper',COM_TZ_PORTFOLIO_PLUS_LIBRARIES
            .DIRECTORY_SEPARATOR.'plugin'.DIRECTORY_SEPARATOR.'helper.php');

        parent::__construct($subject,$config);
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

    public function onAlwaysLoadDocument($context){
        try{

            list($option,$vName) = explode('.',$context);

            if($option != 'module' && $option != 'modules'){
                if($view = $this -> getView($vName)) {
                    if(method_exists($view, 'addDocument')){
                        $view -> addDocument();
                        return true;
                    }
                }
            }
        }
        catch(Exception $e){
            $this -> setError($e -> getMessage());
            return false;
        }
        return false;
    }

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
        if($model = $this -> getModel()) {
            $model -> set('data', $data);
            $this->form     = $model->getForm();

            $this -> item   = $data;
            $path           = TZ_Portfolio_PlusPluginHelper::getLayoutPath($this -> _type, $this -> _name, 'admin');

            ob_start();
            require_once($path);
            $html   = ob_get_contents();
            ob_end_clean();

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
                    if ($view = $this->getView($vName, $layout, $article, $params)) {
                        // Display html
                        ob_start();
                        $view->display();
                        $html = ob_get_contents();
                        ob_end_clean();
                        $html = trim($html);
                        return $html;
                    }
                }
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

    protected function getView($vName, $layout = null, $article = null, $params = null)
    {
//        if ($article && $article -> type == $this -> _name) {
            $plugin_path = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . DIRECTORY_SEPARATOR . $this->_type . DIRECTORY_SEPARATOR
                . $this->_name;

            // Create view's class
            $prefix = 'PlgTZ_Portfolio_Plus' . ucfirst($this->_type) . ucfirst($this->_name);

            $doc = JFactory::getDocument();
            $vType = $doc->getType();

            // Create template path of tz_portfolio_plus
            $template = TZ_Portfolio_PlusTemplate::getTemplate(true);
            $tplparams = $template->params;

            // Create TZ Portfolio Plus template's path
            $tpath = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH . DIRECTORY_SEPARATOR . $template->template
                . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . $tplparams->get('layout', 'default')
                . DIRECTORY_SEPARATOR . $vName . DIRECTORY_SEPARATOR . 'plg_' . $this->_type . '_' . $this->_name;

            // Create default template of tz_portfolio_plus
            $dTemplate = TZ_Portfolio_PlusTemplate::getTemplateDefault();
            $defaultPath = null;

            if ($template->id != $dTemplate->id) {
                $dtplparams = $dTemplate->params;
                $defaultPath = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH . DIRECTORY_SEPARATOR . $dTemplate->template
                    . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . $dtplparams->get('layout', 'default')
                    . DIRECTORY_SEPARATOR . $vName . DIRECTORY_SEPARATOR . 'plg_' . $this->_type . '_' . $this->_name;
            }

            // Merge plugin params and article params
            $_params = clone($this->params);
            $_params->merge($params);

            $controller = new JControllerLegacy(array('name' => $prefix, 'format' => $vType,
                'base_path' => $plugin_path));

        try{
            if ($view = $controller->getView($vName, $vType, $prefix . 'View')) {
                $vpaths = $view->get('_path');
                $vpaths = $vpaths['template'];
                $view->set('_path', array('template' => array()));

                $plgVPath = $plugin_path . DIRECTORY_SEPARATOR . 'views'
                    . DIRECTORY_SEPARATOR . $vName . DIRECTORY_SEPARATOR . 'tmpl';

                if (!in_array($plgVPath, $vpaths)) {
                    $view->addTemplatePath($plgVPath);
                }

                // Add default template path
                if ($defaultPath && !in_array($defaultPath, $vpaths)) {
                    $view->addTemplatePath($defaultPath);
                }

                // Create template path from template site
                $_template = JFactory::getApplication()->getTemplate();
                $tPathSite = JPATH_SITE . '/templates/' . $_template . '/html/com_tz_portfolio_plus/'
                    . $vName . '/plg_' . $this->_type . '_' . $this->_name;


                if (!$tplparams->get('override_html_template_site', 0)) {
                    // Add template path which chosen in menu
                    if (!in_array($tpath, $vpaths)) {
                        $view->addTemplatePath($tpath);
                    }
                    if (!in_array($tPathSite, $vpaths)) {
                        $view->addTemplatePath($tPathSite);
                    }
                } else {
                    // Add template path from template site
                    if (!in_array($tPathSite, $vpaths)) {
                        $view->addTemplatePath($tPathSite);
                    }
                    // Add template path which chosen in menu
                    if (!in_array($tpath, $vpaths)) {
                        $view->addTemplatePath($tpath);
                    }
                }

                // Get model
                $controller->addModelPath($plugin_path . DIRECTORY_SEPARATOR . 'models', $prefix . 'Model');

                tzportfolioplusimport('plugin.modelitem');

                if ($model = $controller->getModel($vName, $prefix.'Model', array('ignore_request' => true))) {

                    // Set params for model
                    $model->setState('params', $_params);

                    if ($article && !empty($article)) {
                        $model -> set('article', $article);
                    }

                    // Push the model into the view (as default)
                    $view->setModel($model, true);
                }

                if ($layout) {
                    $view->setLayout($layout);
                }else{
                    $view -> setLayout('default');
                }

                return $view;
            }
        }
        catch(Exception $e){
            $this -> setError($e -> getMessage());
            return false;
        }
//        }
        return false;
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
                $model->save($data);
            }
        }

    }

    public function onContentAfterDelete($context, $table){
        if($context == 'com_tz_portfolio_plus.article') {
            if($model  = $this -> getModel()) {
                $model->delete($table);
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