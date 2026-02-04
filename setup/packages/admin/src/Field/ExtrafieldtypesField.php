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

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Filesystem\File;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\ExtraFieldsFrontHelper;

class ExtraFieldTypesField extends ListField
{
    protected $type     = 'ExtraFieldTypes';

    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        $return = parent::setup($element, $value, $group);

        if(!$this -> onchange){
            $this -> onchange   = 'tppTypeHasChanged(this);';
            Factory::getApplication() -> getDocument()->addScriptDeclaration('
                (function($, window){
                    "use strict";
                    $( document ).ready(function() {
                        if (Joomla.loadingLayer && typeof Joomla.loadingLayer === "function") {
                            // We are in J3 so use the old method
                            Joomla.loadingLayer("load");
                        } else {
                             // We are in the future
                             let spinner = document.querySelector("joomla-core-loader");
                             if(spinner){
                                 spinner.parentNode.removeChild(spinner);
                             }
                        }
                    });
                    window.tppTypeHasChanged = function(element){
                        if (Joomla.loadingLayer && typeof Joomla.loadingLayer === "function") {
                            // We are in J3 so use the old method                            
                            Joomla.loadingLayer("show");
                        } else {
                             // We are in the future
                            var spinner = document.createElement("joomla-core-loader");
                            document.body.appendChild(spinner);
                        }
                        var cat = $(element);
                        $("input[name=task]").val("field.reload");
                        element.form.submit();
                    }
                })(jQuery, window);
            '
            );
        }

        if($this -> multiple){
            $this -> layout = 'joomla.form.field.list-fancy-select';
        }

        return $return;
    }

    public function getOptions(){
        $options = array();

        $fields = $this -> _getFieldTypes();
        if(count($fields)){
            $options    = $fields;
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

    protected function _getFieldTypes(){
        $data       = array();
        $core_path  = COM_TZ_PORTFOLIO_ADDON_PATH.DIRECTORY_SEPARATOR.'extrafields';
        if($plg_ex     = AddonHelper::getAddOn('extrafields')){
            $lang   = Factory::getApplication() -> getLanguage();

            foreach($plg_ex as $i => $plg){
                $folder             = $plg -> name;
                $core_f_xml_path    = $core_path.DIRECTORY_SEPARATOR.$folder
                    .DIRECTORY_SEPARATOR.$folder.'.xml';
                if(file_exists($core_f_xml_path)){

                    $data[$i]           = new \stdClass();
                    $data[$i] -> value  = $folder;

                    $core_class         = AddonHelper::getInstance($plg -> type, $plg -> name);

                    if($core_class){
                        $core_class -> loadLanguage($folder);
                    }

                    $key_lang           = 'PLG_EXTRAFIELDS_'.strtoupper($folder).'_TITLE';

                    if($lang ->hasKey($key_lang)) {
                        $data[$i]->text = Text::_($key_lang);
                    }else{
                        $data[$i]->text = (string)$folder;
                    }
                }
            }
        }
        return $data;
    }
}