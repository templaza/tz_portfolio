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

// No direct access
use Joomla\CMS\Form\Field\ListField;
use Joomla\Registry\Registry;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;

defined('_JEXEC') or die;

class TzimagesizelistField extends ListField
{

    protected $type     = 'TZImageSizeList';

    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        $setup  = parent::setup($element, $value, $group);

        if($this -> multiple){
            $this -> layout = 'joomla.form.field.list-fancy-select';
        }

        return $setup;
    }

    protected function getOptions(){
        $element        = $this -> element;
        $options        = array();
        $_plugin        = $element['addon']?(string)$element['addon']:null;
        $_plugin_group  = $element['addon_group']?(string)$element['addon_group']:'mediatype';
        $param_filter   = $element['param_name']?(string)$element['param_name']:null;
        if($_plugin && $param_filter) {
            if ($plugin = AddonHelper::getPlugin($_plugin_group, $_plugin, false)) {
                if(!empty($plugin -> params)) {
                    $plg_params = new Registry();
                    $plg_params -> loadString($plugin->params);
                    if($image_size = $plg_params -> get($param_filter)){
                        if(!is_array($image_size) && preg_match_all('/(\{.*?\})/',$image_size,$match)) {
                            $image_size = $match[1];
                        }

                        foreach($image_size as $i => $size){
                            $_size  = json_decode($size);
                            $options[$i]            = new \stdClass();
                            $options[$i] -> text    = $_size -> {$element['param_text']};
                            $options[$i] -> value   = $_size -> {$element['param_value']};
                        }
                    }
                }
            }
        }
        return array_merge(parent::getOptions(),$options);
    }
}