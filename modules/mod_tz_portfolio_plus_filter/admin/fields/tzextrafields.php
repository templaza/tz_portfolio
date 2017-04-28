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

JFormHelper::loadFieldClass('groupedlist');
JLoader::import('com_tz_portfolio_plus.libraries.fields.extrafield', JPATH_ADMINISTRATOR.'/components');
JLoader::import('com_tz_portfolio_plus.helpers.extrafields', JPATH_ADMINISTRATOR.'/components');

class JFormFieldTZExtraFields extends JFormFieldGroupedList
{
    protected $type     = "TZExtraFields";
    protected $tzscript = false;
    protected $tzgroups = array();

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
        
        if($this -> multiple && $sort) {
            if (!$this->tzscript) {

                JHtml::_('jquery.ui', array('core', 'sortable'));

                $doc = JFactory::getDocument();
                $doc->addStyleSheet(JUri::root(true) . '/modules/mod_tz_portfolio_plus_filter/admin/css/style.css');
                $doc->addScriptDeclaration('
                (function($){            
                    $(document).ready(function(){
                        var sfilterchosen = $("#' . $this->id . '").data("chosen"),
                            sfchosenitems = sfilterchosen.search_choices.children().not(sfilterchosen.search_container);
                            
                            // Insert icon for items selected
                            sfchosenitems.find("> span").prepend("<i class=\"icon-move s-filter-handle\"></i>");
                            
                        sfilterchosen.search_choices.sortable({
                            cursor: "move",
                            handle: ".s-filter-handle",
                            items: ".search-choice",
                            placeholder: "s-filter-placeholder",
                            update: function( event, ui ){
                                var currentItem = ui.item.find("[data-option-array-index]"),
                                    option = sfilterchosen.results_data[currentItem.attr("data-option-array-index")],
                                    nextItem = ui.item.next().find("[data-option-array-index]"),
                                    nextOption = sfilterchosen.results_data[nextItem.attr("data-option-array-index")];
                                    
                                    $("#' . $this->id . '_selected input[value=\"" + option.value + "\"]").insertBefore($("#' . $this->id . '_selected input[value=\"" + nextOption.value + "\"]"));                               
    
                            }
                        });
                        
                        $("#' . $this->id . '").bind("change",function(evt, params){
                            if(params.selected !=  undefined){
                                sfilterchosen.search_choices.children().not(sfilterchosen.search_container)
                                    .last().find("> span").prepend("<i class=\"icon-move s-filter-handle\"></i>");
                                $("#' . $this->id . '_selected").append("<input type=\"hidden\" name=\"' . $name . '\" value=\"" + params.selected +"\"/>");
                            }
                            
                            if(params.deselected !=  undefined){
                                $("#' . $this->id . '_selected input[value=\"" + params.deselected + "\"]").remove();
                            }
                        });
                        
                        if(sfchosenitems && sfchosenitems.length){
                            var sfchosenitemstmp   = sfchosenitems.clone(true);
                            sfchosenitemstmp.each(function(){
                                var item   = $(this),
                                    option = sfilterchosen.results_data[item.find("[data-option-array-index]").attr("data-option-array-index")],
                                    index  = $("#' . $this->id . '_selected input[value=\"" + option.value + "\"]").index();                                
                                     sfchosenitems.eq(index).replaceWith(item);
                            });
                        }
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
        if($fields = TZ_Portfolio_PlusHelperExtraFields::getAllExtraFields()){
            foreach ($fields as $field){
                if(!isset($groups[$field -> group_title])) {
                    $groups[$field->group_title]           = array();
                }
                $option     = new stdClass();
                $option -> text     = $field -> title;
                $option -> value    = $field -> id;
                $groups[$field->group_title][]    = JHtml::_('select.option', $field -> id, $field->title);
            }
        }
        return $groups;
    }
}