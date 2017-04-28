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

JHtmlBootstrap::startTabSet()

?>
<form name="adminForm" method="post" id="template-form"
      action="index.php?option=com_tz_portfolio_plus&view=template_style&layout=edit&id=<?php echo $this -> item -> id?>">
    <div class="container-fluid" id="plazart_layout_builder">

        <div class="form-horizontal">
            <div class="row-fluid">
                <div class="span8 form-horizontal">
                    <fieldset class="adminForm">
                        <legend><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_DETAILS');?></legend>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this -> form -> getLabel('title');?></div>
                            <div class="controls"><?php echo $this -> form -> getInput('title');?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('id'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('id'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('home'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('home'); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('template'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('template'); ?>
                            </div>
                        </div>

                        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'layout')); ?>

                        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'layout', JText::_('COM_TZ_PORTFOLIO_PLUS_LAYOUT', true)); ?>
                        <div id="layout_params">
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
                <div class="span4">
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
            </div>

        </div>
    </div>

    <input type="hidden" value="com_tz_portfolio_plus" name="option">
    <input type="hidden" value="" name="task">
    <?php echo JHTML::_('form.token');?>
</form>