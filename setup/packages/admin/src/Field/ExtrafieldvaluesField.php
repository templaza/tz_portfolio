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
use TemPlaza\Component\TZ_Portfolio\Site\Helper\ExtraFieldsFrontHelper;

// No direct access
defined('_JEXEC') or die;

class ExtraFieldValuesField extends FormField
{
    protected $type = "ExtraFieldValues";

    public function getInput(){
        if($type = $this -> form -> getValue('type')){
            $field  = $this -> form -> getData();

            $field_class    = ExtraFieldsFrontHelper::getExtraField($field -> toObject(), 0, array(
                'control'   => $this -> formControl
            ));

            if($field_class){
                return $field_class -> getInputDefault($this -> fieldname);
            }

//            $field_class    = 'TZ_Portfolio_PlusExtraField' . ucfirst($type);
//            if(class_exists($field_class)) {
//                if($field_class    = new $field_class($field -> toObject(), 0,
//                    array('control' => $this -> formControl))){
//                    return $field_class -> getInputDefault($this -> fieldname);
//                }
//            }
        }
    }
}