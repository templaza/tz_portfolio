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

// No direct access
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

JModelList::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_jvisualcontent/models');

class JFormFieldFieldGroups extends JFormFieldList
{
    protected $type = 'FieldGroups';

    protected function getOptions(){
        $options    = array();

        $model      = JModelList::getInstance('Groups','TZ_Portfolio_PlusModel',array('ignore_request' => true));
//        $model -> setState('filter.published','*');
        if($items  = $model -> getItems()){
            foreach($items as $i => $item){
                $options[$i]                = new stdClass();
                $options[$i] -> value       = $item -> id;

//                if ($item -> published == 1)
//                {
                    $options[$i] -> text = $item -> name;
//                }
//                else
//                {
//                    $options[$i] -> text = '[' .$item -> title . ']';
//                }
            }
        }

        return array_merge(parent::getOptions(),$options);
    }
}