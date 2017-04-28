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
//$fields = $this -> item -> defvalue;

$form   = $this -> form;

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');

?>

<script type="text/javascript ">
Joomla.submitbutton = function(task) {
    if (task == 'field.cancel' || document.formvalidator.isValid(document.id('field-form'))) {
        <?php echo $this->form->getField('description')->save(); ?>
        Joomla.submitform(task, document.getElementById('field-form'));
    }
}
</script>
<form name="adminForm" method="post" id="field-form"
      action="index.php?option=com_tz_portfolio_plus&view=field&layout=edit&id=<?php echo $this -> item -> id?>">

    <!-- Begin Content -->
    <div class="span7 form-horizontal">
        <fieldset class="adminform">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#details" data-toggle="tab"><?php echo JText::_('JDETAILS');?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="details">
                    <div class="control-group">
                        <div class="control-label"><?php echo $form -> getLabel('title');?></div>
                        <div class="controls"><?php echo $form -> getInput('title');?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $form -> getLabel('groupid');?></div>
                        <div class="controls"><?php echo $form -> getInput('groupid');?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $form -> getLabel('type');?></div>
                        <div class="controls"><?php echo $form -> getInput('type');?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $form -> getLabel('value');?></div>
                        <div class="controls">
                            <div id="<?php echo $form -> getField('value') -> id;?>" class="pull-left">
                                <?php
                                if($this -> item && $this -> item -> id) {
                                    echo $form->getInput('value');
                                }else{
                                    echo JText::_('COM_TZ_PORTFOLIO_PLUS_NO_VALUE');
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $form -> getLabel('published');?></div>
                        <div class="controls"><?php echo $form -> getInput('published');?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $form -> getLabel('list_view');?></div>
                        <div class="controls"><?php echo $form -> getInput('list_view');?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $form -> getLabel('detail_view');?></div>
                        <div class="controls"><?php echo $form -> getInput('detail_view');?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $form -> getLabel('advanced_search');?></div>
                        <div class="controls"><?php echo $form -> getInput('advanced_search');?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $form -> getLabel('id');?></div>
                        <div class="controls"><?php echo $form -> getInput('id');?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $form -> getLabel('description');?></div>
                        <div class="controls"><?php echo $form -> getInput('description');?></div>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
    <!-- End Content -->
    <!-- Begin Sidebar -->
    <div class="span5">
        <div class="form-vertical">
        <?php echo JHtml::_('bootstrap.startAccordion', 'articleOptions', array('active' => 'collapse0'
        , 'parent' => true));?>
            <?php
            // Display parameter's params from xml file
            $fieldSets = $this->form->getFieldsets('params');
            $i = 0;
            ?>
            <?php foreach ($fieldSets as $name => $fieldSet) :
                $fields = $this->form->getFieldset($name);
                if(count($fields)):
            ?>

                <?php
                // Start accordion parameters
                echo JHtml::_('bootstrap.addSlide', 'articleOptions', JText::_(!empty($fieldSet->label)?$fieldSet -> label:'COM_TZ_PORTFOLIO_PLUS_FIELDSET_'.strtoupper($name).'_LABEL'), 'collapse' . $i++);
                ?>

                <?php if (isset($fieldSet->description) && trim($fieldSet->description)) : ?>
                    <p class="tip"><?php echo $this->escape(JText::_($fieldSet->description));?></p>
                <?php endif; ?>

                <?php foreach ($fields as $field) {
                    echo $field->renderField();
                } ?>

                <?php echo JHtml::_('bootstrap.endSlide');?>
            <?php
                endif;
            endforeach;
            ?>

        <?php echo JHtml::_('bootstrap.endAccordion');?>
        </div>

        <div class="form-horizontal">
            <?php echo JHtml::_('bootstrap.startAccordion', 'previewOptions', array('active' => 'preview_fieldset'));?>
                <?php
                // Start accordion preview
                $preview    = $this->form -> getFieldset('preview_fieldset');
                echo JHtml::_('bootstrap.addSlide', 'previewOptions', JText::_('JGLOBAL_PREVIEW'), 'preview_fieldset');
                ?>
                <div class="control-group">
                    <div class="control-label"><?php echo $this -> form -> getLabel('preview'); ?></div>
                    <div class="controls"><?php echo $this -> form -> getInput('preview'); ?></div>
                </div>
                <?php echo JHtml::_('bootstrap.endSlide');?>
            <?php echo JHtml::_('bootstrap.endAccordion');?>
        </div>
    </div>
    <!-- End Sidebar -->
    <input type="hidden" value="com_tz_portfolio_plus" name="option">
    <input type="hidden" value="" name="task">
    <?php echo JHTML::_('form.token');?>
</form>