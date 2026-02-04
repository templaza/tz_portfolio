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

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Form\Field\ListField;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;

class MediaTypesField extends ListField
{

    protected $type     = 'MediaTypes';

    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        $setup  = parent::setup($element, $value, $group);

        $layout = $this -> layout;

        if($this -> multiple){
            $this -> layout = 'joomla.form.field.list-fancy-select';
        }

//        if($this -> multiple && $layout != 'joomla.form.field.list-fancy-select') {
//            HTMLHelper::_('formbehavior.chosen', '#' . $this->id);
//        }

        return $setup;
    }

    protected function getOptions(){
        $element        = $this -> element;
        $options        = array();
        $_plugin_group  = $element['plugin_group']?$element['plugin_group']:'mediatype';

        if($plugins = AddonHelper::getAddOn($_plugin_group)){
            $lang   = Factory::getApplication() -> getLanguage();
            foreach($plugins as $plugin){
                $std    = new \stdClass();
                $std -> value   = $plugin -> name;

                AddonHelper::loadLanguage($plugin -> name, $plugin -> type);
                if($lang -> hasKey('PLG_'.$plugin -> type.'_'.$plugin -> name.'_TITLE')) {
                    $std -> text    = Text::_('PLG_'.$plugin -> type.'_'.$plugin -> name.'_TITLE');
                }else{
                    $std -> text    = $plugin -> name;
                }
                $options[]  = $std;
            }
        }

        return array_merge(parent::getOptions(),$options);
    }
}