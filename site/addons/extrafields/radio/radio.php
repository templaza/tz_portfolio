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

class TZ_Portfolio_PlusExtraFieldRadio extends TZ_Portfolio_PlusExtraField{
    protected $multiple_option  = true;

    public function getInput($fieldValue = null, $group = null){

        if(!$this -> isPublished()){
            return "";
        }

        $options = $this->getFieldValues();
        $value   = !is_null($fieldValue) ? $fieldValue : $this->value;

        $this->setAttribute("type", "radio", "input");

        $this->setVariable('options', $options);
        $this->setVariable('value', $value);

        return $this->loadTmplFile('input.php', __CLASS__);
    }
}