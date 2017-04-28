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

//no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\Registry\Registry;

JLoader::import('article',COM_TZ_PORTFOLIO_PLUS_SITE_HELPERS_PATH);

class TZ_Portfolio_PlusExtraField{

    protected $id                   = null;
    protected $id_suffix            = null;
    protected $name                 = null;
    protected $field                = null;
    protected $fieldtype            = null;
    protected $multiple             = false;
    protected $multiple_option      = false;
    protected $params               = null;
    private   $head                 = false;
    protected $attributes           = array();
    protected $formcontrol          = 'jform';
    protected $group                = 'extrafields';
    protected $vars                 = array();
    protected $article              = array();
    protected $plugin_params        = null;
    protected static $cache         = array();
    protected $fieldvalue_column    = null;
    protected $dataSearch           = array();

    public function __construct($field = null, $article = null, $option = array())
    {

        if (!is_object($field))
        {
            return false;
        }

        if(count($option)){
            if(isset($option['control'])){
                $this -> formcontrol    = $option['control'];
            }
            if(isset($option['group'])){
                $this -> group    = $option['group'];
            }
        }

        $this->id           = $field->id;

        $app                    = JFactory::getApplication();
        if($field -> type) {
            $plugin = TZ_Portfolio_PlusPluginHelper::getPlugin('extrafields', $field->type);
            $this->plugin_params = new JRegistry($plugin->params);
        }

        if(isset($field -> params) && !empty($field -> params)) {
            $params         = new JRegistry($field->params);
            if($app -> isSite()) {
                $this->params = $this->plugin_params->merge($params);
            }else{
                $this -> params = $params;
            }
        }else{
            if($app -> isSite()) {
                $this->params   = $this->plugin_params;
            }else{
                $this -> params = new JRegistry();
            }
        }

        $this -> fieldname = $field -> type;

        $this->name         = $this -> formcontrol.'['.$this -> group.'][' . $this->id . ']';

        if($this -> multiple){
            $this -> name   .= '[]';
        }

        // Create search field name
        $this->fieldvalue_column = "field_values_" . $this->id . ".value";

        // Create datasearch if it have
        $app    = JFactory::getApplication();
        $input  = $app -> input;
        if($datasearch = $input -> get('fields', array(), 'array')){
            $this -> dataSearch = $datasearch;
        }

        $this -> field  = $field;

        $this->loadLanguage($field -> type);

        return true;
    }

    public function loadLanguage($fieldFolder){
        $storeId = md5(__METHOD__ . "::" . $fieldFolder);

        if (!isset(self::$cache[$storeId]))
        {
            $fieldXmlPath = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.DIRECTORY_SEPARATOR.'extrafields'
                .DIRECTORY_SEPARATOR . $fieldFolder . DIRECTORY_SEPARATOR . $fieldFolder . '.xml';

            if (JFile::exists($fieldXmlPath))
            {
                $lang           = JFactory::getLanguage();

                $langPath   = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.DIRECTORY_SEPARATOR.'extrafields'
                    .DIRECTORY_SEPARATOR . $fieldFolder;
                $lang -> load('plg_extrafields_'.$fieldFolder,$langPath);
            }

            self::$cache[$storeId] = true;

            return self::$cache[$storeId];
        }

        return self::$cache[$storeId];
    }

    // This function to display html in back-end (it will be add to field's form when edit article)
    public function getInputDefault($group = null){

        $this -> group  = $group?$group:$this -> group;

        $fieldValues    = $this -> getFieldValues();

        if ($this->getAttribute("type", "", "input") == "")
        {
            $this->setAttribute("type", "text", "input");
        }
        $this->setVariable('value', $fieldValues);

        if($html = $this -> loadTmplFile('input_default', __CLASS__)){
            return $html;
        }

        $input_def_path = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.DIRECTORY_SEPARATOR.'extrafields'
            .DIRECTORY_SEPARATOR.$this -> fieldname.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'input_default.php';
        if(JFile::exists($input_def_path)){
            ob_start();
            require_once $input_def_path;
            $html   = ob_get_contents();
            ob_end_clean();
        }else{
            if($this -> multiple_option){
                $default_type   = 'radio';
                if($this -> multiple) {
                    $default_type   = 'checkbox';
                }

                if(!$this -> head) {
                    $id_head_text   = $this -> formcontrol.'_'
                        .($group?$group:$this -> group).'_$i_text';

                    $html       = '<tr>';
                    $html           .= '<td class="center"><i class="icon-menu" style="cursor: move;"></i></td>';
                    $html           .= '<td><input type="text" name="'.$this -> formcontrol.'['
                                        .($group?$group:$this -> group).'][$i][text]" id="'.$id_head_text
                                        .'" class="input-medium required"'
                                        .' required="" size="35"/>'.'<label id="'.$id_head_text
                                        .'-lbl" class="required" for="'.$id_head_text.'" style="display: none;">'
                                        .JText::_('COM_TZ_PORTFOLIO_PLUS_OPTION_FIELD_VALUE_LABEL').'</label></td>';
                    $html           .= '<td><input type="text" name="'.$this -> formcontrol.'['
                                        .($group?$group:$this -> group).'][$i][value]" class="input-mini" size="15"/></td>';
                    $html           .= '<td class="center">';
                    if($this -> multiple) {
                        $html .= '<input type="checkbox" name="' . $this->formcontrol . '['
                            . ($group ? $group : $this->group) . '][$i][default]" value="1"/>';
                    }else{
                        $html .= '<input type="radio" name="' . $this->formcontrol . '['
                            . ($group ? $group : $this->group) . '][default]" value="$i"/>';
                    }
                    $html           .= '</td>';
                    $html           .= '<td><button type="button" class="btn btn-danger btn-mini tz_remove-option"><i class="icon-minus"></i>';
                    $html           .= JText::_('COM_TZ_PORTFOLIO_PLUS_REMOVE').'</button></td>';
                    $html       .= '</tr>';

                    $doc = JFactory::getDocument();
                    $doc->addScript(TZ_Portfolio_PlusUri::base(true, true) . '/js/jquery-ui.min.js');
                    $doc -> addStyleDeclaration('#jform_'.$group.' .table{
                        margin-top: 5px;
                    }');
                    $doc -> addScriptDeclaration('
                    (function($){
                        $(document).ready(function(){
                            function tz_extrafields(){
                                var $i  = '.(($fieldValues && count($fieldValues))?count($fieldValues):0).';
                                function tzFieldRemove(){
                                    $("#jform_'.$group.' .tz_remove-option").unbind("click").bind("click",function(e){
                                        $(this).parents("tr").first().remove();
                                    });
                                }
                                tzFieldRemove();
                                $("#jform_'.$group.' .tz_add-option").on("click",function(e){
                                    var html    = "'.jsPlusAddSlashes($html).'";
                                    $("#jform_'.$group.' .table tbody").first().append(html.replace(/\$i/mg, $i));
                                    tzFieldRemove();
                                    $i ++;
                                });
                                $("#jform_'.$group.' .table tbody").sortable({
                                    handle: ".icon-menu",
                                    cursor: "move",
                                    items: "tr",
                                    axis: "y",
                                    placeholder: "ui-state-highlight",
                                    forcePlaceholderSize: true,
                                    forceHelperSize: true,
                                    distance: 2
                                    ,start: function(event,ui){
                                        $.each(ui.helper.find("td"),function(){
                                            $(this).width($(this).innerWidth());
                                        });
                                        $.each(ui.item.find("td"),function(){
                                            $(this).width($(this).innerWidth());
                                        });
                                    },
                                    stop: function(event,ui){
                                        ui.item.children().width("");
                                    }
                                });
                            }
                            tz_extrafields();

                        });
                    })(jQuery);
                    ');
                }

                $html   = '<button type="button" class="btn btn-mini tz_add-option"><i class="icon-plus"></i>';
                $html   .= JText::_('COM_TZ_PORTFOLIO_PLUS_ADD_AN_OPTION').'</button>';
                $html   .= '<div class="max-height-300">';
                $html   .= '<table class="table table-striped table-bordered">';
                $html   .= '<thead>';
                $html       .= '<tr>';
                $html           .= '<th>';
                $html               .= JText::_('COM_TZ_PORTFOLIO_PLUS_SORT');
                $html           .= '</th>';
                $html           .= '<th>';
                $html               .= JText::_('COM_TZ_PORTFOLIO_PLUS_FIELD_TEXT');
                $html               .= '<span class="star">&nbsp;*</span>';
                $html           .= '</th>';
                $html           .= '<th>';
                $html               .= JText::_('COM_TZ_PORTFOLIO_PLUS_VALUE');
                $html           .= '</th>';
                $html           .= '<th>';
                $html               .= JText::_('JDEFAULT');
                $html           .= '</th>';
                $html           .= '<th>';
                $html               .= JText::_('JSTATUS');
                $html           .= '</th>';
                $html       .= '</tr>';
                $html   .= '</thead>';
                $html   .= '<tbody>';

                if($fieldValues && !is_string($fieldValues)){
                    foreach($fieldValues as $key => $value){
                        $name_text  = $this -> formcontrol.'['
                            .($group?$group:$this -> group).']['.$key.'][text]';
                        $id_text    = JApplicationHelper::stringURLSafe($name_text);
                        $id_text    = preg_replace('#\W#', '_', $id_text);

                        $name_value = $this -> formcontrol.'['
                            .($group?$group:$this -> group).']['.$key.'][value]';
                        $id_value   = JApplicationHelper::stringURLSafe($name_value);
                        $id_value   = preg_replace('#\W#', '_', $id_value);

                        $html       .= '<tr>';
                        $html           .= '<td class="center"><i class="icon-menu" style="cursor: move;"></i></td>';
                        $html           .= '<td><input type="text" id="'.$id_text.'" name="'.$this -> formcontrol.'['
                                            .($group?$group:$this -> group).']['.$key.'][text]" required=""'
                                            .' class="input-medium required" size="35" value="'.htmlspecialchars($value -> text).'"/>'
                                            .'<label id="'.$id_text.'-lbl" class="required" for="'.$id_text.'" style="display: none;">'
                                            .JText::_('COM_TZ_PORTFOLIO_PLUS_OPTION_FIELD_VALUE_LABEL').'</label></td>';
                        $html           .= '<td><input type="text" id="'.$id_value.'" name="'.$name_value.'"'
                                            .' class="input-mini" size="15" value="'.htmlspecialchars($value -> value).'"/></td>';

                        $html           .= '<td class="center">';
                        if($this -> multiple) {
                            $html .= '<input type="' . $default_type . '" name="' . $this->formcontrol . '['
                                . ($group ? $group : $this->group) . '][' . $key . '][default]"'
                                . ' value="1"' . ((isset($value->default) && $value->default == 1)
                                    ? 'checked="checked"' : '') . '/>';
                        }else{
                            $html .= '<input type="radio" name="' . $this->formcontrol . '['
                                . ($group ? $group : $this->group) . '][default]"'
                                . ' value="'.$key.'"' . ((isset($value->default) && $value->default == 1)
                                    ? 'checked="checked"' : '') . '/>';
                        }
                        $html           .= '</td>';

                        $html           .= '<td><button type="button" class="btn btn-danger btn-mini tz_remove-option"><i class="icon-minus"></i>';
                        $html           .= JText::_('COM_TZ_PORTFOLIO_PLUS_REMOVE').'</button></td>';
                        $html       .= '</tr>';
                    }
                }

                $html   .= '</tbody>';
                $html   .= '</table>';
                $html   .= '</div>';
            }else{
                $html   = '<input type="text" name="'.$this -> formcontrol.'['.($group?$group:$this -> group).']"'
                    .' value="'. htmlspecialchars($this -> getDefaultValues()).'"/>';
            }
        }

        return $html;
    }

    public function getFieldValues(){
        $storeId = md5(__METHOD__ . "::" . $this->id);
        if (!isset(self::$cache[$storeId]))
        {

            $fieldValues = $this -> field -> value;

            self::$cache[$storeId] = $this->parseDefaultValues($fieldValues);
        }

        return self::$cache[$storeId];
    }

    public function getDefaultValues()
    {
        $options = $this -> getFieldValues();
        if($this -> multiple_option){
            $return  = null;
            if($this -> multiple){
                $return = array();
            }

            if ($options)
            {
                foreach ($options AS $option)
                {
                    if (isset($option->default) && $option ->default == 1)
                    {
                        if($this -> multiple) {
                            $return[]   = htmlspecialchars($option -> value);
                        }else{
                            $return     = htmlspecialchars($option -> value);
                        }
                    }
                }
            }
            return $return;
        }

        return $options;
    }

    public function __set($name, $value)
    {
        switch ($name)
        {
            case 'article_id':
                $this->article->id = (int) $value;
                break;
            case 'name':
            case 'field':
            case 'fieldname':
            case 'multiple':
            case 'multiple_option':
            case 'params':
            case 'head':
            case 'value':
            case 'attributes':
            case 'formcontrol':
            case 'group':
                $this->$name = $value;
                break;

            default:

                if (!is_object($this->field))
                {
                    $this->field = new stdClass();
                }

                $this->field->$name = $value;
                break;
        }
    }

    public function __get($name)
    {
        switch ($name)
        {
            case 'article_id':
                if (isset($this->article->id))
                {
                    return $this->article->id;
                }
                else
                {
                    return null;
                }
                break;
            case 'name':
            case 'field':
            case 'fieldname':
            case 'multiple':
            case 'multiple_option':
            case 'params':
            case 'head':
            case 'attributes':
            case 'formcontrol':
            case 'group':
                return $this->$name;
                break;

            case 'value':

                $storeId = md5("FieldValue::" . $this->article_id.'::'.$this -> id);
                if (!isset(self::$cache[$storeId]))
                {
                    if ($this->article_id)
                    {
                        $value       = $this->getValue();
                        if(!$value){
                            $value  = $this->getDefaultValues();
                        }
                        $this->value = $this->parseDefaultValues($value);
                        unset($value);
                    }else {
                        $this->value = $this->getDefaultValues();
                    }
                    self::$cache[$storeId] = $this->value;
                }

                $this->value = self::$cache[$storeId];

                return $this -> value;
                break;

            default:
                if (isset($this->field->$name))
                {
                    return $this->field->$name;
                }
                break;
        }

        return null;
    }

    public function getLabel(){

        if(!$this -> isPublished()){
            return "";
        }

        $html   = '';
        if($this -> params -> get('show_label',1)) {
            $title = '';
            if ($desc = $this->description) {
                if(preg_match('/(<img\s[^>]*?src\s*=\s*[\'\"])([^\'\"]*?)([\'\"][^>]*?>)/',$desc, $match)){
                    if(count($match)) {
                        if(isset($match[2])){
                            $src    = $match[2];
                            if(!preg_match('/^^(http|https):\/\//',$src) && !preg_match('/^\//',$src)) {
                                $desc = preg_replace('/(<img\s[^>]*?src\s*=\s*[\'\"])([^\'\"]*?)([\'\"][^>]*?>)/'
                                    , '$1' . JUri::root() . '$2$3', $desc);
                            }
                        }
                    }
                }
                $desc   = htmlspecialchars($desc);
                $text   = htmlspecialchars(JText::_($this->getTitle()));
                $title  = ' title="' . JHtml::tooltipText(trim($text, ':'), $desc, 0, 0) . '"';
            }

            $html = '<label for="' . $this->getId() . '" id="' . $this->getId() . '-lbl" class="hasTooltip"' . $title . '>' .
                $this->getTitle()
                . ($this->isRequired() ? '<span class="star">&#160;*</span>' : '') . '</label>';
        }
        return $html;
    }

    // Display form's control to put data
    public function getInput($fieldValue = null, $group = null)
    {

        if(!$this -> isPublished()){
            return "";
        }

        $this -> group  = $group?$group:$this -> group;

        if ($this->getAttribute("type", "", "input") == "")
        {
            $this->setAttribute("type", "text", "input");
        }

        $value = $this -> getVariable('value');
        if(!$value || ($value && empty($value))) {
            $value = !is_null($fieldValue) ? $fieldValue : $this->value;
            $this->setVariable('value', $value);
        }

        if($html = $this -> loadTmplFile('input')){
            return $html;
        }

        $html           = '<input name="'.$this -> name.'" id="'.$this -> getId().'" '
            .($this -> isRequired()?' required=""':''). $this->getAttribute(null, null, "input") .'/>';

        return $html;
    }

    public function getOutput($options = array()){

        if (!$this->isPublished())
        {
            return '';
        }

        $html   = '';

        $value = $this -> getVariable('value');
        if(!$value || ($value && empty($value))) {
            $this->setVariable('value', $this -> value);
        }
        $_options = $this -> getVariable('options');
        if(!$_options || ($_options && empty($_options))) {
            $_options   = $this -> getFieldValues();
            if(count($options)){
                $_options   = $options;
            }
            $this->setVariable('options', $_options);
        }

        if($html = $this -> loadTmplFile('output')){
            return $html;
        }

        $value = $this -> getVariable('value');
        if($this -> multiple_option) {
            if (count($_options)) {
                if($this->multiple) {
                    $html .= '<ul class="value-list">';
                }
                foreach ($_options as $option) {
                    if ($this->multiple) {
                        if((is_array($value) && in_array($option->value, $value))
                            || (!is_array($value) && $option->value == $value)){
                            $html .= '<li ' . $this->getAttribute(null, null, "output") . '>' . $option->text . '</li>';
                        }
                    }elseif(!$this->multiple && $option->value == $value){
                        $html   .= $option -> text;
                    }
                }
                if($this->multiple) {
                    $html .= '</ul>';
                }
            }
        } else {
            $html .= '<div '. $this->getAttribute(null, null, "output") . '>'.$value.'</div>';
        }

        return $html;
    }

    public function getListing($options = array()){

        if (!$this->isPublished())
        {
            return '';
        }

        $html   = '';

        $value = $this -> getVariable('value');
        if(!$value || ($value && empty($value))) {
            $this->setVariable('value', $this -> value);
        }
        $_options = $this -> getVariable('options');
        if(!$_options || ($_options && empty($_options))) {
            $_options   = $this -> getFieldValues();
            if(count($options)){
                $_options   = $options;
            }
            $this->setVariable('options', $_options);
        }

        if($html = $this -> loadTmplFile('listing')){
            return $html;
        }

        $value = $this -> getVariable('value');
        if($this -> multiple_option) {
            if (count($_options)) {
                if($this->multiple) {
                    $html .= '<ul class="value-list">';
                }
                foreach ($_options as $option) {
                    if ($this->multiple) {
                        if((is_array($value) && in_array($option->value, $value))
                            || (!is_array($value) && $option->value == $value)){
                            $html .= '<li ' . $this->getAttribute(null, null, "listing") . '>' . $option->text . '</li>';
                        }
                    }elseif(!$this->multiple && $option->value == $value){
                        $html   .= $option -> text;
                    }
                }
                if($this->multiple) {
                    $html .= '</ul>';
                }
            }
        } else {
            $html .= '<div '. $this->getAttribute(null, null, "listing") . '>'.$value.'</div>';
        }

        return $html;
    }

    public function getInputClass()
    {
        $class = array();

        if ($this->isRequired())
        {
            $class[] = 'required';
        }

        if ($class)
        {
            return implode(' ', $class);
        }
        else
        {
            return "";
        }
    }

    public function getGroupName(){
        if($this -> group){
            if($this -> multiple_option){
                return $this -> formcontrol.'['.$this -> group.'][]';
            }
            return $this -> formcontrol.'['.$this -> group.']';
        }
        return null;
    }

    public function getName(){
        if($this -> name){
            return $this -> name;
        }
        return null;
    }

    public function hasTitle(){
        if($this -> params -> get('show_title',1)){
            return true;
        }
        return false;
    }

    public function getTitle(){
        if($this -> title){
            return $this -> title;
        }
        return null;
    }

    public function getId(){
        return $this -> formcontrol.'_'.$this -> group.'_'.$this -> id;
    }

    protected function getValue()
    {
        $value = null;

        $db     = JFactory::getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('value');
        $query -> from('#__tz_portfolio_plus_field_content_map');
        $query -> where('contentid = '.(int) $this -> article -> id);
        $query -> where('fieldsid = '.(int) $this -> id);
        $db -> setQuery($query);

        if($fieldValue = $db->loadResult()) {
            $value = $fieldValue;
        }

        return $value;
    }

    protected function isRequired(){
        if($params = $this -> params){
            if($params -> get('required',0)){
                return true;
            }
        }
        return false;
    }

    protected function getTmplFile($file = 'output', $class = null)
    {
        $folder = null;

        if (!is_null($class))
        {
            $folder = str_replace(strtolower(__CLASS__), '', strtolower($class));
        }else{
            $folder = $this -> type;
        }

        if($folder) {
            if(stripos($file,'.') == false){
                $file   .= '.php';
            }
            $tmpPath = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . DIRECTORY_SEPARATOR . $this -> group
                . DIRECTORY_SEPARATOR .$folder.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.$file;

            // Create template path of tz_portfolio_plus
            $template = TZ_Portfolio_PlusTemplate::getTemplate(true);
            $tplparams = $template->params;

            // Create default template of tz_portfolio_plus
            if(isset($template -> home_path) && $template -> home_path){
                $_tmpPath    = $template -> home_path. DIRECTORY_SEPARATOR
                    . 'plg_'.$this -> group.'_'. $this -> type. DIRECTORY_SEPARATOR . $file;
                if(JFile::exists($_tmpPath)){
                    $tmpPath    = $_tmpPath;
                }
            }
            if(isset($template -> base_path) && $template -> base_path){
                $_tmpPath    = $template -> base_path. DIRECTORY_SEPARATOR
                    .'plg_'.$this -> group.'_'.$this -> type. DIRECTORY_SEPARATOR . $file;

                if(JFile::exists($_tmpPath)){
                    $tmpPath    = $_tmpPath;
                }
            }

            // Create template path from template site
            if ($tplparams->get('override_html_template_site', 0)) {
                $_template = JFactory::getApplication()->getTemplate();
                $_tmpPath    = JPATH_SITE . '/templates/' . $_template . '/html/com_tz_portfolio_plus/plg_'
                    .$this -> group.'_' . $this -> type. DIRECTORY_SEPARATOR.$file;
                if(JFile::exists($_tmpPath)){
                    $tmpPath    = $_tmpPath;
                }
            }

            if (JFile::exists($tmpPath))
            {
                return $tmpPath;
            }
        }
    }

    protected function loadTmplFile($file = 'output', $class = null){
        $html   = null;

        if(!JFile::exists($file)){
            $file   = $this -> getTmplFile($file, $class);
        }
        unset($class);

        if ($this->vars)
        {
            extract($this->vars);
        }

        ob_start();

        if (JFile::exists($file))
        {
            include($file);
        }

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    public function loadArticle($article, $resetCache = false)
    {

        if (is_numeric($article) && $article > 0)
        {
            $_article = TZ_Portfolio_PlusContentHelper::getArticleById($article, $resetCache);
            if(is_object($_article)){
                $article    = clone $_article;
            }
        }

        if (is_object($article) || is_null($article))
        {
            $this->article = $article;
        }
    }

    public function setAttribute($name, $value, $type = 'output'){
        $ignoredAttributes = array('id', 'name');

        if (in_array($name, $ignoredAttributes))
        {
            return false;
        }

        if (!isset($this->attributes[$type])){
            $this -> attributes[$type]  = new Registry();
        }

        if (is_null($value))
        {
            $attributeArray = $this->attributes[$type];
            $attributeArray = $attributeArray->toArray();
            unset($attributeArray[$name]);

            $this->attributes[$type] = new JRegistry($attributeArray);

            return true;
        }
        else
        {
            if(is_string($value)) {
                $value = trim($value);
            }
            return $this -> attributes[$type] -> set($name, $value);
        }
    }


    public function getAttribute($name = null, $default = null, $type = 'output', $returnType = 'string')
    {
        if (!isset($this->attributes[$type])){
            $this -> attributes[$type]  = new Registry();
        }

        $ignoredAttributes = array('id', 'name');

        if ($name)
        {
            $name = strtolower($name);

            if (in_array($name, $ignoredAttributes))
            {
                return null;
            }

            return $this -> attributes[$type]->get($name, $default);
        }
        else
        {
            if ($returnType == 'registry')
            {
                return $this -> attributes[$type];
            }
            elseif ($returnType == 'array')
            {
                return $this -> attributes[$type]->toArray();
            }
            else
            {
                return $this -> attributes[$type]->toString('ini');
            }
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

    protected function parseDefaultValues($defaultValues)
    {
        if(!$this -> multiple_option) {
            return (string) $defaultValues;
        }

        if ($defaultValues === "")
        {
            return "";
        }
        elseif (is_numeric($defaultValues))
        {
            return $defaultValues;
        }
        elseif (is_string($defaultValues))
        {
            if (json_decode($defaultValues))
            {
                return json_decode($defaultValues);
            }
            elseif (strpos($defaultValues, "|"))
            {
                return explode("|", $defaultValues);
            }

            else
            {
                return $defaultValues;
            }
        }

        else
        {
            return $defaultValues;
        }
    }

    public function isPublished()
    {
        $storeId = md5(__METHOD__ . "::" . $this->id);
        if (!isset(self::$cache[$storeId]))
        {
            if (!$this->published)
            {
                self::$cache[$storeId] = false;

                return self::$cache[$storeId];
            }

            if($fieldGroupObj = TZ_Portfolio_PlusFrontHelperExtraFields::getFieldGroupsById($this->groupid)){
                self::$cache[$storeId] = false;
                foreach($fieldGroupObj as $fieldgroup){
                    if($fieldgroup -> published){
                        self::$cache[$storeId] = true;
                        break;
                    }
                }
                return self::$cache[$storeId];
            }

            self::$cache[$storeId] = true;

            return self::$cache[$storeId];
        }

        return self::$cache[$storeId];
    }

    public function onSave($data)
    {
        if($this -> multiple_option) {

            $value = $data['value'];
            if ($value) {
                if(!$this -> multiple) {
                    $default = $value['default'];
                    unset($value['default']);
                    if (isset($value[$default]['value'])) {
                        $value[$default]['default'] = 1;
                    } else {
                        $value[0]['default'] = 1;
                    }
                    $value = array_values($value);
                }

                $i = 0;
                foreach ($value AS $key => &$val) {

                    if(trim($val['text']) != '' && $val['value'] == ''){
                        $val['value']   = $val['text'];
                    }

                    if (($val["value"] == "" && $i > 0)) {
                        unset($val[$key]);
                    } else {
                        $value[$key]["value"] = str_replace(array("|", ","), "", trim($val["value"]));
                    }

                    $i++;
                }

                $data['value'] = !empty($value) ? array_values($value) : $data['value'];
            }
        }

        return $data;
    }

    protected function prepareFieldValue($value = '')
    {
        if (is_array($value))
        {
            $value = implode("|", $value);
        }

        if (is_object($value))
        {
            $value = json_encode($value);
        }

        return $value;
    }


    public function onSaveArticleFieldValue($value)
    {

        if (!$this -> article_id)
        {
            return false;
        }

        $_value = $this -> prepareFieldValue($value);

        $db         = JFactory::getDbo();
        $query      = $db -> getQuery(true);
        $result     = true;
        $table_name = '#__tz_portfolio_plus_field_content_map';
        $query      -> select('m.*');
        $query      -> from($table_name.' AS m');
        $query      -> where('m.fieldsid = '.$this -> id);
        $query      -> where('m.contentid = ' . $this -> article_id);

        $db         -> setQuery($query);

        $countData  = $db -> loadResult();
        if($countData > 0){

            if($groupid    = TZ_Portfolio_PlusFrontHelperExtraFields::getFieldGroupsByArticleId($this -> article_id)) {
                $groupid = JArrayHelper::getColumn($groupid, 'id');
                if(count($groupid)){
                    $query -> join('INNER', '#__tz_portfolio_plus_field_fieldgroup_map AS fm ON fm.fieldsid = m.fieldsid');
                    $query -> where('fm.groupid IN('.implode(',', $groupid).')');
                }
            }

            $db         -> setQuery($query);

            $countGData  = $db -> loadResult();

            $query  -> clear();
            if($_value !== "" && !is_null($_value) && $countGData > 0) {
                $query->update($table_name);
                $query->set('value = ' . $db->quote($_value));
                $query->where('fieldsid = ' . $this -> id);
                $query->where('contentid = ' . $this -> article_id);
                $db->setQuery($query);
                $result = $db->execute();
            }else{
                $query -> delete($table_name);
                $query -> where('fieldsid = '.$this -> id);
                $query->where('contentid = ' . $this -> article_id);
                $db->setQuery($query);
                $result = $db->execute();
            }
        }else{
            if($_value !== "" && !is_null($_value)) {
                $query  -> clear();
                $query->insert($table_name);
                $query->columns('fieldsid, contentid, value, ordering');
                $query->values($this->id . ',' . $this -> article_id . ',' . $db -> quote($_value).', 0');
                $db->setQuery($query);
                $result = $db->execute();
            }
        }

        return $result;
    }

    protected function removeDefaultOption($options){
        if($options && is_array($options)){
            foreach($options as $i => &$option){
                if(isset($option ->default) && $option ->default){
                    unset($options[$i] ->default);
                }
            }
        }
        return $options;
    }

    public function getSearchName(){
        if($this -> multiple){
            return 'fields['.$this -> id.'][]';
        }
        return 'fields['.$this -> id.']';
    }

    public function getSearchId(){
        return 'fields_'.$this -> id;
    }

    public function getSearchInput($defaultValue = '')
    {
        if (!$this->isPublished())
        {
            return '';
        }

        if ($this->getAttribute('type', '', 'search') == '')
        {
            $this->setAttribute('type', 'text', 'search');
        }

        if ((int) $this->params->get('size', 32))
        {
            $this->setAttribute('size', (int) $this->params->get('size', 32), 'search');
        }

        if(isset($this -> dataSearch[$this -> id])){
            $defaultValue  = $this -> dataSearch[$this -> id];
        }

        $this->setVariable('defaultValue', $defaultValue);
        $this -> setAttribute('value', $defaultValue, 'search');

        $value      = !is_null($defaultValue) ? $defaultValue : $this->value;
        if($this -> multiple){
            $value  = (array) $value;
        }

        if($this -> multiple_option) {
            $options    = $this->getFieldValues();

            $this->setVariable('options', $options);
        }
        $this->setVariable('value', $value);

        if($html = $this -> loadTmplFile('searchinput')){
            return $html;
        }

        $html   = '<label class="group-label">'.$this -> getTitle().'</label>';

        $this -> setAttribute('class', 'form-control', 'search');

        $html  .= '<input name="'.$this -> getSearchName().'" id="'.$this -> getSearchId().'" '
            .($this -> isRequired()?' required=""':''). $this->getAttribute(null, null, 'search') .'/>';

        return $html;
    }

    public function onSearch(&$query, &$where, $search, $forceModifyQuery = false){
        if ($search === '' || empty($search) || !$this -> isPublished())
        {
            return '';
        }

        $storeId = md5(__METHOD__ . "::" . $this->id);
        if (!isset(self::$cache[$storeId]) || $forceModifyQuery)
        {
            $query -> join('LEFT', '#__tz_portfolio_plus_field_content_map AS field_values_'.$this -> id
                . ' ON (c.id = field_values_' . $this -> id . '.contentid AND field_values_' . $this -> id
                . '.fieldsid = ' . $this -> id . ')');

            self::$cache[$storeId] = true;
        }

        $db     = JFactory::getDbo();

        if (is_string($search))
        {
            $where[] = $this->fieldvalue_column . ' LIKE "%' . $db->escape($search, true) . '%"';
        }elseif(is_array($search) && count($search)){
            $_where = array();
            foreach ($search AS $value)
            {
                if ($value !== '')
                {
                    if($this -> multiple_option){
                        $_where[] = "( " . $this->fieldvalue_column . " = " . $db->quote($value) .
                            " OR " . $this->fieldvalue_column . " LIKE '" . $db->escape($value, true) . "|%'" .
                            " OR " . $this->fieldvalue_column . " LIKE '%|" . $db->escape($value, true) . "|%'" .
                            " OR " . $this->fieldvalue_column . " LIKE '%|" . $db->escape($value, true) . "' )";
                    }else {
                        $_where[] = $this->fieldvalue_column . ' = ' . $db->quote($value);
                    }
                }
            }

            if (!empty($_where))
            {


                $where[] = '(' . implode(" OR ", $_where) . ')';
            }
        }
    }
}