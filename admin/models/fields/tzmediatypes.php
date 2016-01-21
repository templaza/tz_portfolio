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

class JFormFieldTZMediaTypes extends JFormFieldList
{

    protected $type     = 'TZMediaTypes';

    protected function getOptions(){
        $element        = $this -> element;
        $options        = array();
        $_plugin_group  = $element['plugin_group']?$element['plugin_group']:'mediatype';

        if($plugins = TZ_Portfolio_PlusPluginHelper::getPlugin($_plugin_group)){
            $lang   = JFactory::getLanguage();
            foreach($plugins as $plugin){
                $std    = new stdClass();
                $std -> value   = $plugin -> name;
                $lang -> load('plg_'.$plugin -> type.'_'.$plugin -> name,COM_TZ_PORTFOLIO_PLUS_ADDON_PATH
                .DIRECTORY_SEPARATOR.$plugin -> type.DIRECTORY_SEPARATOR.$plugin -> name);
                if($lang -> hasKey('PLG_'.$plugin -> type.'_'.$plugin -> name.'_TITLE')) {
                    $std -> text    = JText::_('PLG_'.$plugin -> type.'_'.$plugin -> name.'_TITLE');
                }else{
                    $std -> text    = $plugin -> name;
                }
                $options[]  = $std;
            }
        }

        return array_merge(parent::getOptions(),$options);
    }
}