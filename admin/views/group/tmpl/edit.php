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

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.tabstate');
JHtml::_('formbehavior.chosen', 'select');

$form   = $this -> form;
JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "group.cancel" || document.formvalidator.isValid(document.getElementById("group-form")))
		{
			' . $form->getField("description")->save() . '
			Joomla.submitform(task, document.getElementById("group-form"));
		}
	};
');
?>
<form name="adminForm" method="post" class="form-validate" id="group-form"
      action="index.php?option=com_tz_portfolio_plus&view=group&layout=edit&id=<?php echo (int) $this -> item -> id?>">
    <div class="form-horizontal">
        <fieldset class="adminform">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('JDETAILS', true)); ?>
            <div class="control-group">
                <div class="control-label"><?php echo $form -> getLabel('name');?></div>
                <div class="controls"><?php echo $form -> getInput('name');?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $form -> getLabel('published');?></div>
                <div class="controls"><?php echo $form -> getInput('published');?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $form -> getLabel('field_ordering_type');?></div>
                <div class="controls"><?php echo $form -> getInput('field_ordering_type');?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $form -> getLabel('id');?></div>
                <div class="controls"><?php echo $form -> getInput('id');?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $form -> getLabel('description');?></div>
                <div class="controls"><?php echo $form -> getInput('description');?></div>
            </div>
            <?php echo JHtml::_('bootstrap.endTab'); ?>

            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'categories_assignment', JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES_ASSIGNMENT', true)); ?>
            <?php echo $form->getInput('categories_assignment'); ?>
            <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php echo JHtml::_('bootstrap.endTabSet'); ?>
        </fieldset>

    </div>
    <div class="clr"></div>

    <input type="hidden" value="" name="task">
    <input type="hidden" name="return" value="<?php echo JFactory::getApplication() -> input -> getCmd('return');?>" />
    <?php echo JHTML::_('form.token');?>

</form>