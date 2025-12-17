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

use Joomla\CMS\Form\Field\ListField;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\GroupsHelper;

// No direct access
defined('_JEXEC') or die;

class FieldGroupsField extends ListField
{
    protected $type = 'FieldGroups';

    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        $setup  = parent::setup($element, $value, $group);

        if($this -> multiple){
            $this -> layout = 'joomla.form.field.list-fancy-select';
        }

        return $setup;
    }

    protected function getOptions(){
        $options    = array();

        if($items = GroupsHelper::getGroups()) {
            foreach ($items as $i => $item) {
                $options[$i] = new \stdClass();
                $options[$i]->value = $item->id;
                if($item -> published ) {
                    $options[$i]->text = $item->name;
                }else{
                    $options[$i] -> text    = '['.$item -> name.']';
                }
            }
        }

        return array_merge(parent::getOptions(),$options);
    }
}