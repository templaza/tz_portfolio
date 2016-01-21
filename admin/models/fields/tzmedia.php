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

defined('JPATH_PLATFORM') or die;

class JFormFieldTZMedia extends JFormFieldMedia{
    protected $type = 'TZMedia';

//    public function __construct($form = null)
//    {
//        parent::__construct($form);
//    }
    protected function getName($fieldName)
    {
        $name   = parent::getName($fieldName);
        $element    = $this -> element;

        if(isset($element['index']) && $element['index'] != null){
            $name   = preg_replace('/\[\]$/','['.$element['index'].']',$name);
        }
        return $name;
    }

    protected function getInput()
    {
        $attr       = '';
        $field_name = $this -> fieldname;

        // Tooltip for INPUT showing whole image path
        $options = array(
            'onShow' => 'jMediaRefreshImgpathTip',
        );
        JHtml::_('behavior.tooltip', '.hasTipImgpath', $options);

        if (!empty($this->class))
        {
            $this->class .= ' hasTipImgpath';
        }
        else
        {
            $this->class = 'hasTipImgpath';
        }

        $attr .= ' title="' . htmlspecialchars('<span id="TipImgpath"></span>', ENT_COMPAT, 'UTF-8') . '"';

        // Initialize some field attributes.
        $attr .= !empty($this->class) ? ' class="input-small ' . $this->class . '"' : ' class="input-small"';
        $attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';

        // Initialize JavaScript field attributes.
        $attr .= !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';

        $html   = array();


        // Initialize some field attributes.
        $class = !empty($this->class) ? ' class="' . $this->class . '"' : '';
        $disabled = $this->disabled ? ' disabled' : '';

        // Initialize JavaScript field attributes.
        $onchange = $this->onchange ? ' onchange="' . $this->onchange . '"' : '';

        $this -> __set('name',$field_name.'_client');

        $element    = $this -> element;

        if(isset($element['index']) && $element['index'] != null){
            $this->__set('id', $field_name . '_client'.$element['index']);
        }else {
            $this->__set('id', $field_name . '_client');
        }

        $html[] = '	<input type="file" name="' . $this -> name . '" id="' . $this->id . '"' . $attr . ' />';

        $this -> __set('name',$field_name.'_server');
        if(isset($element['index']) && $element['index'] != null){
            $this->__set('id', $field_name . '_server'.$element['index']);
        }else {
            $this -> __set('id',$field_name.'_server');
        }

        $value  = $this -> value;
        $this -> value  = '';
        $html[] = '<div style="padding-top: 5px;">'.parent::getInput();

        if($value && !empty($value) && is_string($value)){
            $html[] = '<a href="'.JUri::root().str_replace('.'
                    .JFile::getExt($value),($this -> element['img_prefix']?'_'.$this -> element['img_prefix']:'')
                    .'.'.JFile::getExt($value),$value).'?time='.time().'"'
                .' class="tz-image-preview modal" rel="{handler: \'image\'}" style="display: table; padding-top: 5px;">';

            $html[] = '<img src="' . JUri::root() . str_replace('.' . JFile::getExt($value),
                    ($this->element['img_prefix'] ? '_' . $this->element['img_prefix'] : '')
                    . '.' . JFile::getExt($value), $value) . '?time=' . time()
                . '" style="' . ($this->element['img_max-width'] ? 'max-width: 200px; ' : '') . 'cursor: pointer;" title="">';
            $html[] = '</a>';
        }
        $html[] = '</div>';

        $this -> __set('name',$field_name);
        $this -> __set('id',$field_name);

        $html[] = '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="'
            . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"' . $class . $disabled . $onchange . ' />';


        return implode("\n", $html);

    }
}