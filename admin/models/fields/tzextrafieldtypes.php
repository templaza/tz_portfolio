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

use Joomla\Filesystem\File;

jimport('joomla.filesytem.file');
JFormHelper::loadFieldClass('list');

class JFormFieldTZExtraFieldTypes extends JFormFieldList
{
    protected $type = "TZExtraFieldTypes";

    public function setup(SimpleXMLElement $element, $value, $group = null)
    {
        $return = parent::setup($element, $value, $group);

        if(!$this -> onchange){
            $this -> onchange   = 'tppTypeHasChanged(this);';
            JFactory::getDocument()->addScriptDeclaration('
                (function($, window){
                    "use strict";
                    $( document ).ready(function() {
                        Joomla.loadingLayer("load");
                    });
                    window.tppTypeHasChanged = function(element){
                        Joomla.loadingLayer("show");
                        var cat = $(element);
                        $("input[name=task]").val("field.reload");
                        element.form.submit();
                    }
                })(jQuery, window);
            '
            );
        }
        if($this -> multiple) {
            JHtml::_('formbehavior.chosen', '#' . $this->id);
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
        $core_path  = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.DIRECTORY_SEPARATOR.'extrafields';
        if($plg_ex     = TZ_Portfolio_PlusPluginHelper::getPlugin('extrafields')){
            $lang   = JFactory::getLanguage();

            foreach($plg_ex as $i => $plg){
                $folder             = $plg -> name;
                $core_f_xml_path    = $core_path.DIRECTORY_SEPARATOR.$folder
                    .DIRECTORY_SEPARATOR.$folder.'.xml';
                if(\JFile::exists($core_f_xml_path)){
                    $core_class         = 'TZ_Portfolio_PlusExtraField'.$folder;
                    if(!class_exists($core_class)){
                        JLoader::import('com_tz_portfolio_plus.addons.extrafields.'.$folder.'.'.$folder,
                            JPATH_SITE.DIRECTORY_SEPARATOR.'components');
                    }
                    $core_class         = new $core_class();

                    $data[$i]           = new stdClass();
                    $data[$i] -> value  = $folder;
                    $core_class -> loadLanguage($folder);
                    $key_lang           = 'PLG_EXTRAFIELDS_'.strtoupper($folder).'_TITLE';
                    if($lang ->hasKey($key_lang)) {
                        $data[$i]->text = JText::_($key_lang);
                    }else{
                        $data[$i]->text = (string)$folder;
                    }
                }
            }
        }
        return $data;
    }
}