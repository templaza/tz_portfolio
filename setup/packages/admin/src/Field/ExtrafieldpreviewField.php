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

use Joomla\CMS\Form\FormField;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\ExtraFieldsHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\ExtraFieldsFrontHelper;

// No direct access
defined('_JEXEC') or die;

class ExtraFieldPreviewField extends FormField
{
    protected $type = "ExtraFieldPreview";

    public function getInput(){
        if($field  = $this -> getField()) {
            return $field->getInput();
        }

        return null;
    }

    public function getLabel(){
        if($field  = $this -> getField()) {
            return $field->getLabel();
        }

        return null;
    }

    protected function getField()
    {
        if($type = $this -> form -> getValue('type')){
            $field  = $this -> form -> getData();

            $field_class    = ExtraFieldsFrontHelper::getExtraField($field -> toObject(), 0, array(
                'control'   => $this -> formControl
            ));

            return $field_class;
        }
        return false;
    }
}