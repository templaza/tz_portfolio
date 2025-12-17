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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Field;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

class TzcheckboxField extends FormField
{

    protected $type = 'TZCheckbox';

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
        $field_name = $this -> fieldname;
        $element    = $this -> element;

        // Initialize some field attributes.
        $class     = !empty($this->class) ? ' class="' . $this->class . '"' : '';
        $disabled  = $this->disabled ? ' disabled' : '';
        $value     = !empty($this->value) ? $this->value : '1';
        $required  = $this->required ? ' required aria-required="true"' : '';
        $autofocus = $this->autofocus ? ' autofocus' : '';
        $checked   = $this->checked ? ' checked' : '';

        // Initialize JavaScript field attributes.
        $onclick  = !empty($this->onclick) ? ' onclick="' . $this->onclick . '"' : '';
        $onchange = !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';

        if(isset($element['index']) && $element['index'] != null){
            $this->__set('id', $field_name .$element['index']);
        }

        // Including fallback code for HTML5 non supported browsers.
        HTMLHelper::_('jquery.framework');
        HTMLHelper::_('script', 'system/html5fallback.js');

        $html   = array();
        if($this -> element['merge']) {
            $html[] = '<label class="checkbox" id="'.$this -> id.'-lbl" for="'.$this -> id.'">';
        }

        $html[] = '<input type="checkbox" name="' . $this->name . '" id="' . $this->id . '" value="'
            . (!empty($value)?htmlspecialchars($value, ENT_COMPAT, 'UTF-8'):$value) . '"' . $class . $checked . $disabled . $onclick . $onchange
            . $required . $autofocus . ' />';

        if($this -> element['merge']) {
            $html[] = $this -> getTitle();
            $html[] = '</label>';
        }

        return implode("\n",$html);
    }
}