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

//no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

//\Joomla\CMS\HTML\Helpers\UiTab::startTabSet();
//\Joomla\CMS\HTML\Helpers\UiTab::addTab()
//var_dump(__FILE__);
?>
<!-- Column setting popbox -->
<div id="columnsettingbox" style="display: none;">

<?php echo HTMLHelper::_('uitab.startTabSet', 'column-setting-tab', array('active' => 'column-layout', 'recall')); ?>
    <?php echo HTMLHelper::_('uitab.addTab', 'column-setting-tab', 'column-basic', Text::_('COM_TZ_PORTFOLIO_BASIC', true)); ?>

    <div class="row gx-3 form-vertical">
        <div class="col">
            <div id="includetypes" class="control-group mb-2">
                <div class="control-label">
                    <label><?php echo Text::_('COM_TZ_PORTFOLIO_TYPE');?>: </label>
                </div>
                <?php if($this -> includeTypes && count($this -> includeTypes)){?>
                <div class="controls">
                    <select class="includetypes form-select form-select-sm w-100">
                        <?php foreach($this -> includeTypes as $type){
                            if(is_array($type)){
                                foreach($type as $t){
                                    ?>
                                    <option value="<?php echo $t -> value;?>"><?php echo $t -> text;?></option>
                                <?php }
                            }else{
                                ?>
                                <option value="<?php echo $type -> value;?>"><?php echo $type -> text;?></option>
                                <?php
                            }
                        }
                        ?>

                    </select>
                </div>
                <?php }?>
            </div>
        </div>

        <div class="col">
            <div id="spanwidth" class="control-group mb-2">
                <div class="control-label">
                    <label><?php echo Text::_('COM_TZ_PORTFOLIO_WIDTH_LABEL');?>: </label>
                </div>
                <div class="controls">
                    <select class="possiblewidths form-select form-select-sm w-100">
                        <option value=""><?php echo Text::_('JNONE')?></option>
                        <option value="1">span1</option>
                        <option value="2">span2</option>
                        <option value="3">span3</option>
                        <option value="4">span4</option>
                        <option value="5">span5</option>
                        <option value="6">span6</option>
                        <option value="7">span7</option>
                        <option value="8">span8</option>
                        <option value="9">span9</option>
                        <option value="10">span10</option>
                        <option value="11">span11</option>
                        <option value="12">span12</option>
<!--                        <optgroup label="--><?php //echo Text::_('COM_TZ_PORTFOLIO_FRACTION_WIDTH');?><!--">-->
<!--                            <option value="1-1">1/1</option>-->
<!--                            <option value="1-2">1/2</option>-->
<!--                            <option value="1-3">1/3</option>-->
<!--                            <option value="2-3">2/3</option>-->
<!--                            <option value="1-4">1/4</option>-->
<!--                            <option value="3-4">3/4</option>-->
<!--                            <option value="1-5">1/5</option>-->
<!--                            <option value="2-5">2/5</option>-->
<!--                            <option value="3-5">3/5</option>-->
<!--                            <option value="4-5">4/5</option>-->
<!--                            <option value="1-6">1/6</option>-->
<!--                            <option value="5-6">5/6</option>-->
<!--                        </optgroup>-->
<!--                        <optgroup label="--><?php //echo Text::_('COM_TZ_PORTFOLIO_FIXED_WIDTH');?><!--">-->
<!--                            <option value="expand">--><?php //echo Text::_('COM_TZ_PORTFOLIO_EXPAND');?><!--</option>-->
<!--                            <option value="auto">--><?php //echo Text::_('COM_TZ_PORTFOLIO_AUTO');?><!--</option>-->
<!--                            <option value="small">--><?php //echo Text::_('COM_TZ_PORTFOLIO_SMALL');?><!--</option>-->
<!--                            <option value="medium">--><?php //echo Text::_('COM_TZ_PORTFOLIO_MEDIUM');?><!--</option>-->
<!--                            <option value="large">--><?php //echo Text::_('COM_TZ_PORTFOLIO_LARGE');?><!--</option>-->
<!--                            <option value="xlarge">--><?php //echo Text::_('COM_TZ_PORTFOLIO_X_LARGE');?><!--</option>-->
<!--                            <option value="2xlarge">--><?php //echo Text::_('COM_TZ_PORTFOLIO_2X_LARGE');?><!--</option>-->
<!--                        </optgroup>-->
                    </select>
                </div>
            </div>
        </div>

<!--        <div class="col-md-6">-->
<!--            <div id="spanoffset" class="control-group mb-2">-->
<!--                <div class="control-label">-->
<!--                    <label>--><?php //echo Text::_('COM_TZ_PORTFOLIO_OFFSET');?><!-- </label>-->
<!--                </div>-->
<!--                <div class="controls">-->
<!--                    <select class="possibleoffsets form-select form-select-sm w-100">-->
<!--                        <option value="">--><?php //echo Text::_('JNONE');?><!--</option>-->
<!--                        <option value="1">offset1</option>-->
<!--                        <option value="2">offset2</option>-->
<!--                        <option value="3">offset3</option>-->
<!--                        <option value="4">offset4</option>-->
<!--                        <option value="5">offset5</option>-->
<!--                        <option value="6">offset6</option>-->
<!--                        <option value="7">offset7</option>-->
<!--                        <option value="8">offset8</option>-->
<!--                        <option value="9">offset9</option>-->
<!--                        <option value="10">offset10</option>-->
<!--                    </select>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
        <div class="col">
            <div id="customclass" class="d-block control-group mb-2">
                <div class="control-label">
                    <label><?php echo Text::_('COM_TZ_PORTFOLIO_CUSTOM_CLASS');?> </label>
                </div>
                <div class="controls">
                    <input type="text" class="form-control form-control-sm customclass" id="inputcustomclass">
                </div>
            </div>
        </div>
    </div>

    <?php echo HTMLHelper::_('uitab.endTab'); ?>
    <?php echo HTMLHelper::_('uitab.addTab', 'column-setting-tab', 'column-basic', Text::_('COM_TZ_PORTFOLIO_RESPONSIVE', true)); ?>
        <div class="row" id="responsive">
            <div class="col-md-6">
                <label class="checkbox"> <input type="checkbox" value="visible-lg"><?php echo Text::_('COM_TZ_PORTFOLIO_VISIBLE_LARGE');?></label>
                <label class="checkbox"> <input type="checkbox" value="visible-md"><?php echo Text::_('COM_TZ_PORTFOLIO_VISIBLE_MEDIUM');?></label>
                <label class="checkbox"> <input type="checkbox" value="visible-sm"><?php echo Text::_('COM_TZ_PORTFOLIO_VISIBLE_SMALL');?></label>
                <label class="checkbox"> <input type="checkbox" value="visible-xs"><?php echo Text::_('COM_TZ_PORTFOLIO_VISIBLE_EXTRA_SMALL');?></label>
            </div>
            <div class="col-md-6">
                <label class="checkbox"> <input type="checkbox" value="hidden-lg"><?php echo Text::_('COM_TZ_PORTFOLIO_HIDDEN_LARGE');?></label>
                <label class="checkbox"> <input type="checkbox" value="hidden-md"><?php echo Text::_('COM_TZ_PORTFOLIO_HIDDEN_MEDIUM');?></label>
                <label class="checkbox"> <input type="checkbox" value="hidden-sm"><?php echo Text::_('COM_TZ_PORTFOLIO_HIDDEN_SMALL');?></label>
                <label class="checkbox"> <input type="checkbox" value="hidden-xs"><?php echo Text::_('COM_TZ_PORTFOLIO_HIDDEN_EXTRA_SMALL');?></label>
            </div>
        </div>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>
<?php echo HTMLHelper::_('uitab.endTabSet'); ?>


<!--    <ul class="nav nav-tab nav-tabs" id="columnsettings">-->
<!--        <li class="nav-item active"><a href="#basic" data-toggle="tab" data-bs-toggle="tab" class="active">--><?php //echo Text::_('COM_TZ_PORTFOLIO_BASIC');?><!--</a></li>-->
<!--        <li class="nav-item"><a href="#responsive" data-toggle="tab" data-bs-toggle="tab">--><?php //echo Text::_('COM_TZ_PORTFOLIO_RESPONSIVE');?><!--</a></li>-->
<!--    </ul>-->
<!---->
<!--    <div class="tab-content border-0 p-3">-->
<!--        <div class="tab-pane active" id="basic">-->
<!--            <div id="includetypes">-->
<!--                <label>--><?php //echo Text::_('COM_TZ_PORTFOLIO_TYPE');?><!--: </label>-->
<!--                --><?php //if($this -> includeTypes && count($this -> includeTypes)){?>
<!--                <select class="includetypes form-select form-select-sm">-->
<!--                    --><?php //foreach($this -> includeTypes as $type){
//                        if(is_array($type)){
//                            foreach($type as $t){
//                    ?>
<!--                        <option value="--><?php //echo $t -> value;?><!--">--><?php //echo $t -> text;?><!--</option>-->
<!--                    --><?php //}
//                        }else{
//                    ?>
<!--                        <option value="--><?php //echo $type -> value;?><!--">--><?php //echo $type -> text;?><!--</option>-->
<!--                    --><?php
//                        }
//                    }
//                    ?>
<!---->
<!--                </select>-->
<!--                --><?php //}?>
<!--            </div>-->
<!---->
<!--            <div id="spanwidth">-->
<!--                <label>--><?php //echo Text::_('COM_TZ_PORTFOLIO_WIDTH_LABEL');?><!--: </label>-->
<!--                <select class="possiblewidths form-select form-select-sm">-->
<!--                    <optgroup label="--><?php //echo Text::_('COM_TZ_PORTFOLIO_FRACTION_WIDTH');?><!--">-->
<!--                        <option value="1-1">1/1</option>-->
<!--                        <option value="1-2">1/2</option>-->
<!--                        <option value="1-3">1/3</option>-->
<!--                        <option value="2-3">2/3</option>-->
<!--                        <option value="1-4">1/4</option>-->
<!--                        <option value="3-4">3/4</option>-->
<!--                        <option value="1-5">1/5</option>-->
<!--                        <option value="2-5">2/5</option>-->
<!--                        <option value="3-5">3/5</option>-->
<!--                        <option value="4-5">4/5</option>-->
<!--                        <option value="1-6">1/6</option>-->
<!--                        <option value="5-6">5/6</option>-->
<!--                    </optgroup>-->
<!--                    <optgroup label="--><?php //echo Text::_('COM_TZ_PORTFOLIO_FIXED_WIDTH');?><!--">-->
<!--                        <option value="expand">--><?php //echo Text::_('COM_TZ_PORTFOLIO_EXPAND');?><!--</option>-->
<!--                        <option value="auto">--><?php //echo Text::_('COM_TZ_PORTFOLIO_AUTO');?><!--</option>-->
<!--                        <option value="small">--><?php //echo Text::_('COM_TZ_PORTFOLIO_SMALL');?><!--</option>-->
<!--                        <option value="medium">--><?php //echo Text::_('COM_TZ_PORTFOLIO_MEDIUM');?><!--</option>-->
<!--                        <option value="large">--><?php //echo Text::_('COM_TZ_PORTFOLIO_LARGE');?><!--</option>-->
<!--                        <option value="xlarge">--><?php //echo Text::_('COM_TZ_PORTFOLIO_X_LARGE');?><!--</option>-->
<!--                        <option value="2xlarge">--><?php //echo Text::_('COM_TZ_PORTFOLIO_2X_LARGE');?><!--</option>-->
<!--                    </optgroup>-->
<!--                </select>-->
<!--            </div>-->
<!---->
<!--            <div id="spanoffset">-->
<!--                <label>--><?php //echo Text::_('COM_TZ_PORTFOLIO_OFFSET');?><!-- </label>-->
<!--                <select class="possibleoffsets form-select form-select-sm">-->
<!--                    <option value="">--><?php //echo Text::_('JNONE');?><!--</option>-->
<!--                    <option value="1">offset1</option>-->
<!--                    <option value="2">offset2</option>-->
<!--                    <option value="3">offset3</option>-->
<!--                    <option value="4">offset4</option>-->
<!--                    <option value="5">offset5</option>-->
<!--                    <option value="6">offset6</option>-->
<!--                    <option value="7">offset7</option>-->
<!--                    <option value="8">offset8</option>-->
<!--                    <option value="9">offset9</option>-->
<!--                    <option value="10">offset10</option>-->
<!--                </select>-->
<!--            </div>-->
<!---->
<!--            <div id="customclass" class="d-block">-->
<!--                <label>--><?php //echo Text::_('COM_TZ_PORTFOLIO_CUSTOM_CLASS');?><!-- </label>-->
<!--                <input type="text" class="form-control form-control-sm customclass" id="inputcustomclass">-->
<!--            </div>-->
<!--        </div>-->
<!---->
<!--        <div class="tab-pane" id="responsive">-->
<!--            --><?php //echo HTMLHelper::_('tzbootstrap.addrow');?>
<!--            <div class="span6 col-md-6">-->
<!--                <label class="checkbox"> <input type="checkbox" value="visible-lg">--><?php //echo Text::_('COM_TZ_PORTFOLIO_VISIBLE_LARGE');?><!--</label>-->
<!--                <label class="checkbox"> <input type="checkbox" value="visible-md">--><?php //echo Text::_('COM_TZ_PORTFOLIO_VISIBLE_MEDIUM');?><!--</label>-->
<!--                <label class="checkbox"> <input type="checkbox" value="visible-sm">--><?php //echo Text::_('COM_TZ_PORTFOLIO_VISIBLE_SMALL');?><!--</label>-->
<!--                <label class="checkbox"> <input type="checkbox" value="visible-xs">--><?php //echo Text::_('COM_TZ_PORTFOLIO_VISIBLE_EXTRA_SMALL');?><!--</label>-->
<!--            </div>-->
<!--            <div class="span6 col-md-6">-->
<!--                <label class="checkbox"> <input type="checkbox" value="hidden-lg">--><?php //echo Text::_('COM_TZ_PORTFOLIO_HIDDEN_LARGE');?><!--</label>-->
<!--                <label class="checkbox"> <input type="checkbox" value="hidden-md">--><?php //echo Text::_('COM_TZ_PORTFOLIO_HIDDEN_MEDIUM');?><!--</label>-->
<!--                <label class="checkbox"> <input type="checkbox" value="hidden-sm">--><?php //echo Text::_('COM_TZ_PORTFOLIO_HIDDEN_SMALL');?><!--</label>-->
<!--                <label class="checkbox"> <input type="checkbox" value="hidden-xs">--><?php //echo Text::_('COM_TZ_PORTFOLIO_HIDDEN_EXTRA_SMALL');?><!--</label>-->
<!--            </div>-->
<!--            --><?php //echo HTMLHelper::_('tzbootstrap.endrow');?>
<!--        </div>-->
<!--    </div>-->
</div>

<!-- Row setting popbox -->
<div id="rowsettingbox" style="display: none;">
<!--    <h3 class="row-header">--><?php //echo Text::_('COM_TZ_PORTFOLIO_ROW_SETTINGS');?><!--</h3>-->

    <div>
        <?php echo HTMLHelper::_('tzbootstrap.addrow', array('class' => 'gx-3'));?>

            <div class="col-md-6">
                <div class="control-group mb-2">
                    <div class="control-label">
                        <label><?php echo Text::_('JFIELD_NAME_LABEL');?>: </label>
                    </div>
                    <div class="controls">
                        <input type="text" class="form-control form-control-sm small rowname" id="">
                    </div>
                </div>
                <div class="control-group mb-2">
                    <div class="control-label">
                        <label class="fs-6"><?php echo Text::_('COM_TZ_PORTFOLIO_BACKGROUND');?> </label>
                    </div>
                    <div class="controls">
                        <input type="text" class="form-control form-control-sm rowbackgroundcolor" value="" id="">
                    </div>
                </div>
                <div class="control-group mb-2">
                    <div class="control-label">
                        <label class="fs-6"><?php echo Text::_('COM_TZ_PORTFOLIO_LINK');?>: </label>
                    </div>
                    <div class="controls">
                        <input type="text" class="form-control form-control-sm rowlinkcolor" id="">
                    </div>
                </div>
                <div class="control-group mb-2">
                    <div class="control-label">
                        <label><?php echo Text::_('COM_TZ_PORTFOLIO_MARGIN');?>: </label>
                    </div>
                    <div class="controls">
                        <input type="text" class="form-control form-control-sm small rowmargin" id="">
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="control-group mb-2">
                    <div class="control-label">
                        <label><?php echo Text::_('COM_TZ_PORTFOLIO_CUSTOM_CLASS');?> </label>
                    </div>
                    <div class="controls">
                        <input type="text" class="form-control form-control-sm small rowcustomclass" id="">
                    </div>
                </div>
                <div class="control-group mb-2">
                    <div class="control-label">
                        <label class="fs-6"><?php echo Text::_('COM_TZ_PORTFOLIO_TEXT');?>: </label>
                    </div>
                    <div class="controls">
                        <input type="text" class="form-control form-control-sm small rowtextcolor" id="">
                    </div>
                </div>
                <div class="control-group mb-2">
                    <div class="control-label">
                        <label class="fs-6"><?php echo Text::_('COM_TZ_PORTFOLIO_LINK_HOVER');?>: </label>
                    </div>
                    <div class="controls">
                        <input type="text" class="form-control form-control-sm small rowlinkhovercolor" id="">
                    </div>
                </div>
                <div class="control-group mb-2">
                    <div class="control-label">
                        <label><?php echo Text::_('COM_TZ_PORTFOLIO_PADDING');?>: </label>
                    </div>
                    <div class="controls">
                        <input type="text" class="form-control form-control-sm small rowpadding" id="">
                    </div>
                </div>
            </div>

<!--        --><?php //echo HTMLHelper::_('tzbootstrap.endrow');?>

<!--        --><?php //echo HTMLHelper::_('tzbootstrap.addrow');?>
<!--            <div class="col-md-6">-->
<!--                <div class="control-group">-->
<!--                    <div class="control-label">-->
<!--                        <label class="fs-6">--><?php //echo Text::_('COM_TZ_PORTFOLIO_BACKGROUND');?><!-- </label>-->
<!--                    </div>-->
<!--                    <div class="controls">-->
<!--                        <input type="text" class="form-control form-control-sm small rowbackgroundcolor" id="">-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!---->
<!--            <div class="col-md-6 rowcolorOuter">-->
<!--                <div class="control-group">-->
<!--                    <div class="control-label">-->
<!--                        <label class="fs-6">--><?php //echo Text::_('COM_TZ_PORTFOLIO_TEXT');?><!--: </label>-->
<!--                    </div>-->
<!--                    <div class="controls">-->
<!--                        <input type="text" class="form-control form-control-sm small rowtextcolor" id="">-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        --><?php //echo HTMLHelper::_('tzbootstrap.endrow');?>

<!--        --><?php //echo HTMLHelper::_('tzbootstrap.addrow');?>
<!--            <div class="span6 col-md-6 rowcolorOuter">-->
<!--                <label class="fs-6">--><?php //echo Text::_('COM_TZ_PORTFOLIO_LINK');?><!--: </label>-->
<!--                <input type="text" class="form-control form-control-sm small rowlinkcolor" id="">-->
<!--            </div>-->
<!---->
<!--            <div class="span6 col-md-6 rowcolorOuter">-->
<!--                <label class="fs-6">--><?php //echo Text::_('COM_TZ_PORTFOLIO_LINK_HOVER');?><!--: </label>-->
<!--                <input type="text" class="form-control form-control-sm small rowlinkhovercolor" id="">-->
<!--            </div>-->
<!--        --><?php //echo HTMLHelper::_('tzbootstrap.endrow');?>

<!--        --><?php //echo HTMLHelper::_('tzbootstrap.addrow');?>
<!--            <div class="span6 col-md-6 rownameOuter mt-2">-->
<!--                <label>--><?php //echo Text::_('COM_TZ_PORTFOLIO_MARGIN');?><!--: </label>-->
<!--                <input type="text" class="form-control form-control-sm small rowmargin" id="">-->
<!--            </div>-->
<!---->
<!--            <div class="span6 col-md-6 rowclassOuter mt-2">-->
<!--                <label>--><?php //echo Text::_('COM_TZ_PORTFOLIO_PADDING');?><!--: </label>-->
<!--                <input type="text" class="form-control form-control-sm small rowpadding" id="">-->
<!--            </div>-->
        <?php echo HTMLHelper::_('tzbootstrap.endrow');?>

        <?php echo HTMLHelper::_('tzbootstrap.addrow', array("attribute" => 'id="rowresponsiveinputs"'));?>
            <div class="col-md-6">
                <label class="checkbox"> <input type="checkbox" value="visible-xs"><?php echo Text::_('COM_TZ_PORTFOLIO_VISIBLE_EXTRA_SMALL');?></label>
                <label class="checkbox"> <input type="checkbox" value="visible-sm"><?php echo Text::_('COM_TZ_PORTFOLIO_VISIBLE_SMALL');?></label>
                <label class="checkbox"> <input type="checkbox" value="visible-md"><?php echo Text::_('COM_TZ_PORTFOLIO_VISIBLE_MEDIUM');?></label>
                <label class="checkbox"> <input type="checkbox" value="visible-lg"><?php echo Text::_('COM_TZ_PORTFOLIO_VISIBLE_LARGE');?></label>
            </div>
            <div class="col-md-6">
                <label class="checkbox"> <input type="checkbox" value="hidden-xs"><?php echo Text::_('COM_TZ_PORTFOLIO_HIDDEN_EXTRA_SMALL');?></label>
                <label class="checkbox"> <input type="checkbox" value="hidden-sm"><?php echo Text::_('COM_TZ_PORTFOLIO_HIDDEN_SMALL');?></label>
                <label class="checkbox"> <input type="checkbox" value="hidden-md"><?php echo Text::_('COM_TZ_PORTFOLIO_HIDDEN_MEDIUM');?></label>
                <label class="checkbox"> <input type="checkbox" value="hidden-lg"><?php echo Text::_('COM_TZ_PORTFOLIO_HIDDEN_LARGE');?></label>
            </div>
        <?php echo HTMLHelper::_('tzbootstrap.endrow');?>

    </div>
</div>