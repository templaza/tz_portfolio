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

//no direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('bootstrap.tooltip','.hasTooltip,[data-toggle=tooltip]');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tabstate');
JHtml::_('formbehavior.chosen', '#menuOptions select');
JHtmlBootstrap::startTabSet();
?>
<script>
    jQuery(function($) {
        "use strict";
        $('input[type=radio][name="jform[params][use_single_layout_builder]"]').change(function() {
            if (this.value == '1') {
                $('#layout_params').css('display', 'block');
                $('#layout_disable').css('display', 'none');
            }
            else {
                $('#layout_params').css('display', 'none');
                $('#layout_disable').css('display', 'block');
            }
        });
    });
</script>
<form name="adminForm" method="post" id="template-form" class="tpArticle"
      action="index.php?option=com_tz_portfolio_plus&view=template_style&layout=edit&id=<?php echo $this -> item -> id?>">
    <div class="container-fluid" id="plazart_layout_builder">
        <div class="form-horizontal">
            <?php echo JHtml::_('tzbootstrap.addrow');?>
                <div class="span8 col-md-8 form-horizontal">
                    <fieldset class="adminForm">
                        <legend><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_DETAILS');?></legend>

                        <?php echo JHtml::_('tzbootstrap.addrow');?>
                        <div class="span6 col-md-6">
                            <?php echo $this -> form -> renderField('title'); ?>
                            <?php echo $this -> form -> renderField('home'); ?>
                        </div>
                        <div class="span6 col-md-6">
                            <?php echo $this -> form -> renderField('template'); ?>
                            <?php echo $this -> form -> renderField('id'); ?>
                        </div>
                        <?php echo JHtml::_('tzbootstrap.endrow');?>

                        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'layout')); ?>

                        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'layout', JText::_('COM_TZ_PORTFOLIO_PLUS_LAYOUT', true)); ?>
                        <div id="layout_params" style="<?php echo intval($this->item->params->use_single_layout_builder) ? 'display: block;' : 'display: none;'; ?>">
                            <div id="plazart-admin-device">
                                <div class="pull-left plazart-admin-layout-header"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LAYOUTBUIDER_HEADER')?></div>
                                <div class="pull-right">
                                    <button type="button" class="btn tz-admin-dv-lg active" data-device="lg">
                                        <i class="fa fa-desktop"></i><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LARGE');?>
                                    </button>
                                    <button type="button" class="btn tz-admin-dv-md" data-device="md" data-toggle="tooltip"
                                            title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ONLY_BOOTSTRAP_3');?>">
                                        <i class="fa fa-laptop"></i><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_MEDIUM');?>
                                    </button>
                                    <button type="button" class="btn tz-admin-dv-sm" data-device="sm" data-toggle="tooltip"
                                            title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ONLY_BOOTSTRAP_3');?>">
                                        <i class="fa fa-tablet"></i><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_SMALL');?>
                                    </button>
                                    <button type="button" class="btn tz-admin-dv-xs" data-device="xs" data-toggle="tooltip"
                                            title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ONLY_BOOTSTRAP_3');?>">
                                        <i class="fa fa-mobile"></i><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_EXTRA_SMALL');?>
                                    </button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <?php echo $this -> loadTemplate('column_settings');?>
                            <?php echo $this -> loadTemplate('generator');?>
                        </div>
                        <div id="layout_disable" style="<?php echo intval($this->item->params->use_single_layout_builder) ? 'display: none;' : 'display: block;'; ?>">
                            <h3 style="text-align: center;"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LAYOUT_DISABLED');?></h3>
                        </div>
                        <?php echo JHtml::_('bootstrap.endTab'); ?>

                        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'menus_assignment', JText::_('COM_TZ_PORTFOLIO_PLUS_MENUS_ASSIGNMENT', true)); ?>
                        <?php echo $this -> loadTemplate('menu_assignment'); ?>
                        <?php echo JHtml::_('bootstrap.endTab'); ?>

                        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'categories_assignment', JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES_ASSIGNMENT', true)); ?>
                        <?php echo $this->form->getInput('categories_assignment'); ?>
                        <?php echo JHtml::_('bootstrap.endTab'); ?>

                        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'articles_assignment', JText::_('COM_TZ_PORTFOLIO_PLUS_ARTICLES_ASSIGNMENT', true)); ?>
                        <?php echo $this->form->getInput('articles_assignment'); ?>
                        <?php echo JHtml::_('bootstrap.endTab'); ?>

                        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'presets', JText::_('Preset', true)); ?>
                        <?php echo $this -> loadTemplate('presets');?>
                        <?php echo JHtml::_('bootstrap.endTab'); ?>

                        <?php echo JHtml::_('bootstrap.endTabSet'); ?>

                    </fieldset>
                </div>
                <div class="span4 col-md-4">
                    <?php echo JHtml::_('bootstrap.startAccordion', 'menuOptions', array('active' => 'collapse0'));?>
                    <?php  $fieldSets = $this->form->getFieldsets('params'); ?>
                    <?php $i = 0;?>
                    <?php foreach ($fieldSets as $name => $fieldSet) :?>
                        <?php // If the parameter says to show the article options or if the parameters have never been set, we will
                        // show the article options. ?>
                        <?php
                        $fields = $this->form->getFieldset($name);
                        if($fields && count($fields)):?>
                            <?php echo JHtml::_('bootstrap.addSlide', 'menuOptions', JText::_($fieldSet->label), 'collapse' . $i++); ?>
                            <?php if (isset($fieldSet->description) && trim($fieldSet->description)) : ?>
                                <p class="tip"><?php echo $this->escape(JText::_($fieldSet->description));?></p>
                            <?php endif; ?>
                            <fieldset>
                                <?php foreach ($fields as $field){
                                    echo $field -> renderField();
                                } ?>
                            </fieldset>
                            <?php echo JHtml::_('bootstrap.endSlide');?>
                        <?php endif;?>
                    <?php endforeach; ?>
                    <?php echo JHtml::_('bootstrap.endAccordion');?>
                </div>
            <?php echo JHtml::_('tzbootstrap.endrow');?>

        </div>
    </div>

    <input type="hidden" value="com_tz_portfolio_plus" name="option">
    <input type="hidden" value="" name="task">
    <?php echo JHTML::_('form.token');?>
</form>