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
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\GroupedlistField;
use Joomla\CMS\HTML\Helpers\DraggableList;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\WebAsset\WebAssetManager;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\ExtraFieldsHelper;

class ExtrafieldsField extends GroupedlistField
{
    protected $type     = "ExtraFields";
    protected $tzscript = false;
    protected $tzgroups = array();

    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        $setup  = parent::setup($element, $value, $group);

        if($this -> multiple){
//            $this -> layout = 'joomla.form.field.list-fancy-select';
            $doc = Factory::getApplication() -> getDocument();

            /* @var WebAssetManager $wa */
            $wa = $doc -> getWebAssetManager();
            $wa ->usePreset('choicesjs')
                ->useScript('webcomponent.field-fancy-select');
        }

        return $setup;
    }

    protected function getInput()
    {

        $name   = $this -> name;
        if($this -> multiple) {
            $this->name = null;
        }
        $html   = parent::getInput();
        $sort   = isset($this -> element['sort'])?(string) $this -> element['sort']:true;
        $sort   = strtolower($sort);
        $sort   = ($sort === 'false')?false:true;

        $alt    = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname);
        $hint   = $this->translateHint ? Text::alt($this->hint, $alt) : $this->hint;

        
        if($this -> multiple && $sort) {
            if (!$this->tzscript) {

//                if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE){
//                    JHtmlDraggablelist::draggable();
//                }else {
//                    JHtml::_('jquery.ui', array('core', 'sortable'));
//                }

                $doc = Factory::getApplication() -> getDocument();

                /* @var WebAssetManager $wa */
                $wa = $doc -> getWebAssetManager();

                $wa -> usePreset('dragula')
                    -> useScript('joomla.draggable');
//                $wa -> registerAndUseStyle('mod_tz_portfolio_filter', Uri::root(true) . '/modules/mod_tz_portfolio_filter/admin/css/style.css');
//                $doc->addStyleSheet(Uri::root(true) . '/modules/mod_tz_portfolio_plus_filter/admin/css/style.css');

                $wa -> addInlineScript('
                (function($){            
                    $(document).ready(function(){                    
//                        var sfilterchosen = $("#' . $this->id . '").data("chosen");
                        var sfilterchosen = document.getElementById("' . $this->id . '").closest("joomla-field-fancy-select").choicesInstance;
                       
//                            sfchosenitems = sfilterchosen.search_choices.children().not(sfilterchosen.search_container);
//                            console.log(sfilterchosen);
//                            console.log(document.getElementById("' . $this->id . '").closest(\'joomla-field-fancy-select\').choicesInstance);
                            
//                        // Insert icon for items selected
//                        sfchosenitems.find("> span").prepend("<i class=\"icon-move s-filter-handle\"></i>");

                        let icon = document.createElement("span");
                        icon.className = "fas fa-up-down-left-right s-filter-handle";
                        
                        console.log(sfilterchosen.itemList);
                        var _schoiceDragula__'.$this -> id.' = dragula([sfilterchosen.itemList],{
                            /*ignoreInputTextSelection: false,
                            invalid: function (el, handle) {
                            console.log(el);
                                return el.classList.contains("choices__item"); // don\'t prevent any drags from initiating by default
                            },*/
//                            isContainer: function (el) {
//                            console.log("isContainer");
//                            console.log(el);
//                                return el.classList.contains(\'dragula-container\');
//                              },
//                            accepts: function (el, target) {
//                                return el.classList.contains("choices__item");
//                            },
                            moves: function (el, container, handle) {
                                console.log(container);
                                return handle.classList.contains("s-filter-handle");
                            }
                        }).on("drag", function(el){
                        console.log("Drag");
                        console.log(el);
                        }).on(\'drop\', function (el, target, source, sibling) {
                            if(sibling === null){
                                _schoiceDragula__'.$this -> id.'.cancel(el);
                            }
                        });
                        
                        sfilterchosen.passedElement.element.addEventListener("highlightItem", function(id, value, label, groupValue){

                            var items = this.closest("joomla-field-fancy-select").querySelector(".choices__inner .choices__list").children;                        
                            
                            for (let i = 0; i < items.length; i++) {
                              items[i].prepend(icon.cloneNode(true));
                            }
                            
//                            var _schoiceDragula__'.$this -> id.' = new dragula([sfilterchosen.itemList],{
//                                /*ignoreInputTextSelection: false,
//                                invalid: function (el, handle) {
//                                console.log(el);
//                                    return el.classList.contains("choices__item"); // don\'t prevent any drags from initiating by default
//                                },*/
//                                accepts: function (el, target) {
//                                    return el.classList.contains("choices__item");
//                                },
//                                moves: function (el, container, handle) {
//                                    console.log(container);
//                                    return handle.classList.contains("s-filter-handle");
//                                }
//                            }).on("drag", function(el){
//                                console.log("Drag");
//                                console.log(el);
//                            }).on(\'drop\', function (el, target, source, sibling) {
//                                if(sibling === null){
//                                    _schoiceDragula__'.$this -> id.'.cancel(el);
//                                }
//                            });
                            
                            console.log(sfilterchosen.itemList);
                        });
                        
                        sfilterchosen.passedElement.element.addEventListener("change", function(value){
                            
                            var items = sfilterchosen.itemList.element.children;                        
                            
                            for (let i = 0; i < items.length; i++) {
                              items[i].prepend(icon.cloneNode(true));
                            }
//                            console.log(_schoiceDragula__'.$this -> id.'.containers);
                        });
                        
//                        $("#' . $this->id . '").bind("change",function(evt, params){
//                        
//                            let icon = document.createElement("span");
//                            icon.className = "fas fa-up-down-left-right s-filter-handle";
//                            
//                            var items = sfilterchosen.itemList.element.children;                        
//                            
//                            for (let i = 0; i < items.length; i++) {
//                              items[i].prepend(icon.cloneNode(true));
//                            }
//                            
////                            return;
//                        });
                        
//                        if(sfchosenitems && sfchosenitems.length){
//                            var sfchosenitemstmp   = sfchosenitems.clone(true);
//                            sfchosenitemstmp.each(function(){
//                                var item   = $(this),
//                                    option = sfilterchosen.results_data[item.find("[data-option-array-index]").attr("data-option-array-index")],
//                                    index  = $("#' . $this->id . '_selected input[value=\"" + option.value + "\"]").index();                                
//                                     sfchosenitems.eq(index).replaceWith(item);
//                            });
//                        }
                    });
                })(jQuery)');
            }
            $html .= '<div id="' . $this->id . '_selected">';
            if ($values = $this->value) {
                if (is_array($values) && count($values)) {
                    foreach ($values as $value) {
                        $html .= '<input type="hidden" name="' . $name . '" value="' . $value . '">';
                    }
                }
            }
            $html .= '</div>';
        }

        if($this -> multiple) {

            $attr2  = '';
            $attr2 .= !empty($class) ? ' class="' . $class . '"' : '';
            $attr2 .= ' placeholder="' . /*$this->escape*/($hint ?: Text::_('JGLOBAL_TYPE_OR_SELECT_SOME_OPTIONS')) . '" ';

            $html = '<joomla-field-fancy-select'.$attr2.'>' . $html;
            $html .= '</joomla-field-fancy-select>';
        }

        return $html;
    }

    public function getGroups(){
        $options = array();

        $fields = $this -> _getFieldTypes();
        if(count($fields)){
            $options    = $fields;
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getGroups(), $options);

        return $options;
    }

    protected function _getFieldTypes(){
        $groups    = array();
        if($fields = ExtraFieldsHelper::getAllExtraFields()){
            foreach ($fields as $field){
                if(!isset($groups[$field -> group_title])) {
                    $groups[$field->group_title]           = array();
                }
                $option     = new \stdClass();
                $option -> text     = $field -> title;
                $option -> value    = $field -> id;
                $groups[$field->group_title][]    = HTMLHelper::_('select.option', $field -> id, $field->title);
            }
        }
        return $groups;
    }
}