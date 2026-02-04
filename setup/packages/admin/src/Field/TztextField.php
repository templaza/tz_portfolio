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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Field;

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\TextField;

class TZTextField extends TextField {

    protected $type = 'TZText';

    protected function getInput(){
        $element        = $this -> element;

        if($this -> multiple){
            $this -> __set('name',$this -> fieldname);
            if(is_array($this -> value)){
                $this -> value  = array_shift($this -> value);
            }
        }

        if($element && isset($element['data-provide'])){
            $str    = '" data-provide="'.(string) $element['data-provide'];
            $this -> id = $this->id.$str;
        }
        $html   = parent::getInput();
        return $html;
    }

}