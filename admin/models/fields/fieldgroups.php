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
            foreach ($items as $i => $item) {
                $options[$i] = new stdClass();
                $options[$i]->value = $item->id;
                $options[$i]->text = $item->name;
            }
        }

        return array_merge(parent::getOptions(),$options);
    }
}