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

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\Helpers\DraggableList;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\WebAsset\WebAssetManager;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioUri;

class TzmultiplefieldField extends FormField {

    protected $type     = 'TZMultipleField';
    protected $head     = false;
    protected $multiple = true;

    protected function getName($fieldName)
    {
        return parent::getName($fieldName);
    }

    protected function getInput()
    {
        if(!is_array($this -> value) && preg_match_all('/(\{.*?\})/',$this -> value,$match)) {
            $this -> setValue($match[1]);
        }
        $doc    = Factory::getApplication() -> getDocument();
        if(!$this -> head) {
//            $doc->addScript(TZ_PortfolioUri::root(true,null,true).'/js/jquery-ui.min.js', array('version' => 'v=1.11.4'));
//            $doc->addStyleSheet(TZ_PortfolioUri::root(true,null,true). '/css/jquery-ui.min.css', array('version' => 'v=1.11.4'));
//            $doc->addStyleDeclaration('.tz_pricing-table-table .ui-sortable-helper{
//                background: #fff;
//            }');

            DraggableList::draggable();
            HTMLHelper::_('draggablelist.draggable', 'tp-table__'.$this -> fieldname);
            $lang   = Factory::getApplication() -> getLanguage();
            $lang -> load('com_tz_portfolio');
            $this -> head   = true;
        }
        $id                 = $this -> id;
        $element            = $this -> element;
        $this -> __set('multiple','true');

        // Initialize some field attributes.
        $class = !empty($this->class) ? ' class="' . $this->class . '"' : '';
        $disabled = $this->disabled ? ' disabled' : '';

        // Initialize JavaScript field attributes.
        $onchange = $this->onchange ? ' onchange="' . $this->onchange . '"' : '';

        // Get children fields from xml file
        $tzfields = $element->children();
        // Get field with tzfield tags
        $xml                = array();
        $html               = array();
        $thead              = array();
        $tbody_col_require  = array();
        $tbody_row_id       = array();
        $tbody_row_html     = array();
        $tzform_control_id  = array();
        $form_control       = array();

        $tbody_row_html[]   = '<td style="text-align: center;">'
            .'<span class="icon-move sortable-handler hasTooltip" title="'.Text::_('COM_TZ_PORTFOLIO_MOVE').'"
             style="cursor: move;"></span></td>';

        ob_start();
        ?>
        <div id="<?php echo $id;?>-content">
            <div class="control-group">
                <button type="button" class="btn btn-success me-2 tz_btn-add">
                    <span class="icon-plus icon-white" title="<?php echo Text::_('COM_TZ_PORTFOLIO_UPDATE');?>"></span>
                    <?php echo Text::_('COM_TZ_PORTFOLIO_UPDATE');?>
                </button>
                <button type="button" class="btn btn-secondary tz_btn-reset">
                    <span class="icon-cancel" title="<?php echo Text::_('COM_TZ_PORTFOLIO_RESET');?>"></span>
                    <?php echo Text::_('COM_TZ_PORTFOLIO_RESET');?>
                </button>
            </div>
            <?php

            // Generate children fields from xml file
            if ($tzfields) {

                /* @var Form $form */
                $form   = $this -> form;
                $i  = 0;
                foreach ($tzfields as $xmlElement) {

                    $type = $xmlElement['type'];
                    if (!$type) {
                        $type = 'text';
                    }

                    /**
                    * Load the FormField object for the subfield.
                    * @var FormField $field
                     * */
                    $field  = FormHelper::loadFieldType($type);

                    // Check formfield class of children field
                    if(!empty($field)) {

                        // Create formfield class of children field
                        $field -> setForm($this -> form);
                        $field->formControl = 'tzform';
                        // Init children field for children class
                        $field -> setup($xmlElement, '');
                        $field -> value      = is_string($xmlElement['default']) ? $xmlElement['default'] : '';
                        $tz_name                = (string)$xmlElement['name'];
                        $tz_tbl_require         = (bool)$xmlElement['table_required'];

                        $tzform_control_id[$i]                      = array();
                        $tzform_control_id[$i]["id"]                = $field -> id;
                        $tzform_control_id[$i]["type"]              = $field -> type;
                        $tzform_control_id[$i]["fieldname"]         = $field -> fieldname;
                        $tzform_control_id[$i]["table_required"]    = 0;
                        $tzform_control_id[$i]["name"]              = $field -> name;
                        $tzform_control_id[$i]["default"]           = $field ->default;
                        $tzform_control_id[$i]["field_required"]    = (bool)$xmlElement['field_required'];
                        $tzform_control_id[$i]["value_validate"]    = (string)$xmlElement['value_validate'];
                        $tzform_control_id[$i]["label"]             = $field -> getTitle();

                        // Create table's head column (check attribute table_required of children field from xml file)
                        if ($tz_tbl_require) {
                            $tbody_row_id[]                             = $field -> id;
                            $tbody_col_require[]                        = $field -> fieldname;
                            $tzform_control_id[$i]["table_required"]    = 1;

                            ob_start();
                            ?>
                            <th><?php echo $field -> getTitle(); ?></th>
                            <?php
                            $thead[] = ob_get_clean();
                            ob_start();
                            ?>
                            <td>{<?php echo $field -> id;?>}</td>
                            <?php
                            $tbody_row_html[]   = ob_get_clean();
                        }
                        ob_start();
                        // Generate children field from xml file
                        ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $field->getLabel($tz_name) ?></div>
                            <div class="controls"><?php echo $field->getInput($tz_name); ?></div>
                        </div>
                        <?php
                        $form_control[] = ob_get_clean();
                    }
                    $i++;
                }
            }
            // Generate table
            if(count($thead)) {
                ?>
                <table class="table table-striped tz_pricing-table-table" id="tp-table__<?php echo $this -> fieldname;?>">
                    <thead>
                    <tr>
                        <th style="width: 3%; text-align: center;">#</th>
                        <?php echo implode("\n", $thead); ?>
                        <th style="width: 10%; text-align: center;"><?php echo Text::_('JSTATUS'); ?></th>
                    </tr>
                    </thead>
                    <tbody class="js-draggable">
                    <?php
                    if($values = $this -> value){
                        if(count($values)){
                            foreach($values as $value){
                                $j_value    = json_decode($value);

                                $arr_j_value    = (array) $j_value;
                                $arr_j_value    = array_keys($arr_j_value);

                                $difference_keys    = array_diff( $tbody_col_require, $arr_j_value);
                                $difference_keys    = array_keys($difference_keys);
                                ?>
                                <tr data-draggable-group="0">
                                    <td style="text-align: center;"><span class="icon-move hasTooltip" style="cursor: move;"
                                                                          title="<?php echo Text::_('COM_TZ_PORTFOLIO_MOVE')?>"></span></td>
                                    <?php
                                    if($j_value && !empty($j_value)) {
                                        $j = 0;
                                        foreach ($j_value as $key => $_j_value) {
                                            if(in_array($key,$tbody_col_require)){

                                                ?>
                                                <td><?php echo $_j_value ?></td>
                                            <?php }
                                            if(count($difference_keys) && in_array($j+1, $difference_keys)){
                                                for($k=0; $k <count($difference_keys); $k++) {
                                                    ?>
                                                    <td></td>
                                                    <?php
                                                }
                                            }
                                            $j++;
                                        }
                                    }
                                    ?>
                                    <td style="text-align: center;">
                                        <div class="btn-group">
                                            <button class="btn btn-secondary btn-sm tz_btn-edit hasTooltip"
                                                    type="button" title="<?php echo Text::_('JACTION_EDIT');?>"><i class="icon-edit"></i></button>
                                            <button class="btn btn-danger btn-sm tz_btn-remove hasTooltip"
                                                    type="button" title="<?php echo Text::_('COM_TZ_PORTFOLIO_REMOVE');?>"><i class="icon-trash"></i></button>
                                        </div>
                                        <input type="hidden" name="<?php echo $this -> getName($this -> fieldname);?>"
                                               value="<?php echo !empty($value)?htmlspecialchars( $value):$value;?>" <?php echo $class . $disabled . $onchange?>/>
                                        <?php ?>
                                    </td>
                                </tr>
                            <?php } } }?>
                    </tbody>
                </table>
                <?php
            }

            echo implode("\n",$form_control);

            $tbody_row_html[]   = '<td style="text-align: center;">'
                .'<div class="btn-group">'
                .'<button type="button" class="btn btn-secondary btn-sm tz_btn-edit hasTooltip" title="'
                .Text::_('JACTION_EDIT').'"><i class="icon-edit"></i></button>'
                .'<button type="button" class="btn btn-danger btn-sm tz_btn-remove hasTooltip" title="'
                .Text::_('COM_TZ_PORTFOLIO_REMOVE').'">'
                .'<i class="icon-trash"></i></button>'
                .'</div>'
                .'<input type="hidden" name="' . $this -> getName($this -> fieldname) . '" value="{'.
                $this -> id.'}"' . $class . $disabled . $onchange . ' />'
                .'</td>';

            $config = Factory::getConfig();

            $addEditor      = '';
            $editEditor     = '';
            $resetEditor    = '';

            if($config -> get('editor') == 'jce'){
                $addEditor  = '$content[value["fieldname"]]    =  WFEditor.getContent(value["id"]);';
            }elseif($config -> get('editor') == 'tinymce'){
                $addEditor  = '$content[value["fieldname"]]    =  tinyMCE.activeEditor.getContent();';
            }elseif($config -> get('editor') == 'codemirror'){
                $addEditor  = '$content[value["fieldname"]]    =  Joomla.editors.instances[value["id"]].getValue();';
            }

            if($config -> get('editor') == 'jce'){
                $editEditor = 'WFEditor.setContent(value["id"], $hidden_obj_value[value["fieldname"]]);';
            }elseif($config -> get('editor') == 'tinymce'){
                $editEditor = 'tinyMCE.activeEditor.setContent($hidden_obj_value[value["fieldname"]]);';
            }elseif($config -> get('editor') == 'codemirror'){
                $editEditor = 'Joomla.editors.instances[value["id"]].setValue($hidden_obj_value[value["fieldname"]]);';
            }

            if($config -> get('editor') == 'jce'){
                $resetEditor    = 'WFEditor.setContent(value["id"], value["default"]);';
            }elseif($config -> get('editor') == 'tinymce'){
                $resetEditor    = 'tinyMCE.activeEditor.setContent(value["default"]);';
            }elseif($config -> get('editor') == 'codemirror'){
                $resetEditor    = 'Joomla.editors.instances[value["id"]].setValue(value["default"]);';
            }

            $tbody_row_html = '<tr>'.implode('',$tbody_row_html).'</tr>';

            /* @var WebAssetManager $wa */
            $wa = $doc -> getWebAssetManager();
            $wa -> addInlineScript('function htmlspecialchars(str) {
                    if (typeof(str) == "string") {
                        str = str.replace(/&/g, "&amp;"); /* must do &amp; first */
                        str = str.replace(/"/g, "&quot;");
                        str = str.replace(/\'/g, "&#039;");
                        str = str.replace(/</g, "&lt;");
                        str = str.replace(/>/g, "&gt;");
                    }
                    return str;
                }


                (function($){
                    $(document).ready(function(){

                        var $tbody_row_html     = "'.TZ_PortfolioHelper::jsAddSlashes( trim($tbody_row_html)).'";
                        var $tzpricing_table_id = "'.$this -> id.'";
                        var $tbody_control_id   = '.json_encode($tzform_control_id ).';
                        var $hidden_name        = "'.TZ_PortfolioHelper::jsAddSlashes($this -> getName($this -> fieldname)).'";
                        var $tzpricing_position = -1;

                        // Add new data row
                        $("#'.$id.'-content .tz_btn-add").bind("click",function(e){

                            // Create input hidden with data were put
                            var $tbody_row_html_clone   = $tbody_row_html;
                            var $tbody_bool             = true;
                            var $content                = {};

                            $.each($tbody_control_id,function(key,value){
                                var input_name  = value["name"].replace(/\\[/,"\\\[")
                                    .replace(/\\]/,"\\\]");

                                if(value["field_required"]){
                                    $tbody_bool = false;
                                    if(!$("#" + value["id"]).val().length){
                                        alert("'.htmlspecialchars(Text::sprintf('JLIB_FORM_VALIDATE_FIELD_INVALID','')).'"
                                            + value["label"]);
                                        $("#" + value["id"]).focus();
                                        return false;
                                    }
                                }

                                if(value["value_validate"]){
                                    if($("#" + value["id"]).val() == value["value_validate"]){
                                        alert("'.htmlspecialchars(Text::sprintf('COM_TZ_PORTFOLIO_FAILED_TO_VALUE','')).'"
                                            + value["value_validate"]
                                            + " '.htmlspecialchars(Text::sprintf('COM_TZ_PORTFOLIO_FAILED_OF_FIELD','')).'"
                                            + value["label"]);
                                        return false;
                                    }
                                }

                                // Check required and create row for table
                                if(value["table_required"]){
                                    var pattern = "\\\{"+value["id"]+"\\\}";
                                    var regex   = new RegExp(pattern,"gi");
                                    $tbody_row_html_clone   = $tbody_row_html_clone.replace(regex,$("#" + value["id"]).val());
                                }

                                $tbody_bool = true;

                                if(value["type"].toLowerCase() == "editor"){
                                    // tinyMCE.activeEditor.getContent();
                                    //WFEditor.getContent(id)
                                    '.$addEditor.'
                                    $content[value["fieldname"]] = $("#" + value["id"]).val();
                                }else {
                                    if($("[name=" + input_name + "]").prop("tagName").toLowerCase() == "input"
                                        && $("[name=" + input_name + "]").prop("type") == "radio") {
                                        $content[value["fieldname"]] = $("[name="+ value["name"].replace(/\\[/,"\\\[")
                                            .replace(/\\]/,"\\\]")+"]:checked").val();
                                    }else {
                                        $content[value["fieldname"]] = $("#" + value["id"]).val();
                                    }
                                }
                            });

                            if($tbody_bool && Object.keys($content).length){
                                var pattern2 = "\\\{"+$tzpricing_table_id+"\\\}";
                                var regex2   = new RegExp(pattern2,"gi");
                                $tbody_row_html_clone   = $tbody_row_html_clone.replace(regex2,htmlspecialchars(JSON.stringify($content)));
                                if($tzpricing_position > -1 ) {
                                    $("#" + $tzpricing_table_id + "-content .tz_pricing-table-table tbody tr")
                                        .eq( $tzpricing_position).after($tbody_row_html_clone).remove();
                                    $tzpricing_position = -1;
                                }else {
                                    $("#" + $tzpricing_table_id + "-content .tz_pricing-table-table tbody").prepend($tbody_row_html_clone);
                                }

                                // Call trigger reset form
                                $("#'.$id.'-content .tz_btn-reset").trigger("click");

                                tzPricingTableAction();
                            }

                        });
                        // Reset form
                        $("#'.$id.'-content .tz_btn-reset").bind("click",function(){
                            if($tbody_control_id.length) {
                                $.each($tbody_control_id, function (key, value) {
                                    var input_name  = value["name"].replace(/\\[/,"\\\[")
                                        .replace(/\\]/,"\\\]");
                                    if (value["type"].toLowerCase() == "editor") {
                                        // tinyMCE.activeEditor.getContent();
                                        //WFEditor.getContent(id)
                                        '.$resetEditor.'
                                        $("#" + value["id"]).val("");
                                    } else {
                                        if($("[name=" + input_name + "]").prop("tagName").toLowerCase() == "select") {
                                            $("#" + value["id"]).val(value["default"])
                                                .trigger("liszt:updated");
                                        }else{
                                            if($("[name=" + input_name + "]").prop("tagName").toLowerCase() == "input"
                                                && $("[name=" + input_name + "]").prop("type") == "radio") {
                                                $("[name=" + input_name + "]").removeAttr("checked");
                                                $("#" + value["id"]+" label[for=" + $("[name=" + input_name + "][value="
                                                        + value["default"] +"]").attr("id")
                                                    +"]").trigger("click");
                                            }else {
                                                $("#" + value["id"]).val(value["default"]);
                                            }
                                        }
                                    }
                                });
                                $tzpricing_position = -1;
                            }
                        });

                        function tzPricingTableAction() {
                            // Edit data
                            $("#'.$id.'-content .tz_btn-edit").unbind("click").bind("click", function () {
                                var $hidden_value = $(this).parents("td").first()
                                    .find("input[name=\\"" + $hidden_name + "\\"]").val();
                                if ($hidden_value.length) {
                                    var $hidden_obj_value = $.parseJSON($hidden_value);
                                    if ($tbody_control_id.length) {
                                        $.each($tbody_control_id, function (key, value) {
                                            var input_name  = value["name"].replace(/\\[/,"\\\[")
                                                .replace(/\\]/,"\\\]");
                                            if (value["type"].toLowerCase() == "editor") {
                                                '.$editEditor.'
                                                $("#" + value["id"]).val($hidden_obj_value[value["fieldname"]]);
                                            } else{
                                                if($("[name=" + input_name + "]").prop("tagName").toLowerCase() == "select") {
                                                    $("#" + value["id"]).val($hidden_obj_value[value["fieldname"]])
                                                        .trigger("liszt:updated");
                                                }else{
                                                    if($("[name=" + input_name + "]").prop("tagName").toLowerCase() == "input"
                                                    && $("[name=" + input_name + "]").prop("type") == "radio") {
                                                        $("[name=" + input_name + "]").removeAttr("checked");
                                                        $("#" + value["id"]+" label[for=" + $("[name=" + input_name + "][value="
                                                            + $hidden_obj_value[value["fieldname"]] +"]").attr("id")
                                                            +"]").trigger("click");
                                                    }else {
                                                        $("#" + value["id"]).val($hidden_obj_value[value["fieldname"]]);
                                                    }
                                                }
                                            }
                                        });
                                        $tzpricing_position = $("#'.$id.'-content .tz_pricing-table-table tbody tr")
                                            .index($(this).parents("tr").first());
                                    }
                                }
                            });

                            // Remove data row
                            $("#'.$id.'-content .tz_btn-remove").unbind("click").bind("click", function () {
                                var message = confirm("'.htmlspecialchars(Text::_('COM_TZ_PORTFOLIO_REMOVE_THIS_ITEM')).'");
                                if (message) {
                                    $(this).parents("tr").first().remove();
                                }
                            });
                        }
                        tzPricingTableAction();

//                        // Sortable row
//                        $("#" + $tzpricing_table_id + "-content .tz_pricing-table-table tbody").sortable({
//                            cursor: "move",
//                            items: "> tr",
//                            revert: true,
//                            handle: ".icon-move",
//                            forceHelperSize: true,
//                            placeholder: "ui-state-highlight"
//                        });
                    });
                })(jQuery);');
            ?>

        </div>
        <?php
        $html[] = ob_get_contents();
        ob_end_clean();

        return implode("\n",$html);
    }

}