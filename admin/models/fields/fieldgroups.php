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
JLoader::import('com_tz_portfolio_plus.helpers.groups', JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components');

class JFormFieldFieldGroups extends JFormFieldList
{
    protected $type = 'FieldGroups';

    protected function getOptions(){
        $options    = array();

        if($items = TZ_Portfolio_PlusHelperGroups::getGroups()) {

            $user   = JFactory::getUser();
            $jinput = JFactory::getApplication()->input;

            foreach ($items as $i => $item) {
//                if($jinput -> get('id')){
//                    if(!$user->authorise('core.edit', 'com_tz_portfolio_plus.group.' . $item->id)
//                        && (!$user -> authorise('core.edit.own', 'com_tz_portfolio_plus.group.'.$item -> id))){
//                        continue;
//                    }
//                }else{
//                    if(!$user->authorise('core.create', 'com_tz_portfolio_plus.group.' . $item->id) ){
//                        continue;
//                    }
//                }
                $options[$i] = new stdClass();
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