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
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.tabstate');
JHtml::_('formbehavior.chosen', 'select');
$this->fieldsets = $this->form->getFieldsets('params');

JFactory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(task)
	{
		if (task == 'addon.cancel' || document.formvalidator.isValid(document.getElementById('addon-form'))) {
			Joomla.submitform(task, document.getElementById('plugin-form'));
		}
	};
");
?>

<form action="<?php echo JRoute::_('index.php?option=com_tz_portfolio_plus&view=addon&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate">
    <div class="form-horizontal">

        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_TZ_PORTFOLIO_PLUS_ADDON', true)); ?>

        <div class="row-fluid">
            <div class="span9">
                <?php if ($this->item->xml) : ?>
                    <?php if ($this->item->xml->description) : ?>
                        <h3>
                            <?php
                            if ($this->item->xml)
                            {
                                echo ($text = (string) $this->item->xml->name) ? JText::_($text) : $this->item->name;
                            }
                            else
                            {
                                echo JText::_('COM_TZ_PORTFOLIO_PLUS_ADDON_XML_ERR');
                            }
                            ?>
                        </h3>
                        <div class="info-labels">
							<span class="label hasTooltip" title="<?php echo JHtml::tooltipText('COM_TZ_PORTFOLIO_PLUS_ADDON_FIELD_FOLDER_LABEL', 'COM_TZ_PORTFOLIO_PLUS_ADDON_FIELD_FOLDER_DESC'); ?>">
								<?php echo $this->form->getValue('folder'); ?>
							</span> /
							<span class="label hasTooltip" title="<?php echo JHtml::tooltipText('COM_TZ_PORTFOLIO_PLUS_ADDON_FIELD_ELEMENT_LABEL', 'COM_TZ_PORTFOLIO_PLUS_ADDON_FIELD_ELEMENT_DESC'); ?>">
								<?php echo $this->form->getValue('element'); ?>
							</span>
                        </div>
                        <div>
                            <?php
                            $short_description = JText::_($this->item->xml->description);
                            $this->fieldset = 'description';
                            $long_description = JLayoutHelper::render('joomla.edit.fieldset', $this);
                            if(!$long_description) {
                                $truncated = JHtmlString::truncate($short_description, 550, true, false);
                                if(strlen($truncated) > 500) {
                                    $long_description = $short_description;
                                    $short_description = JHtmlString::truncate($truncated, 250);
                                    if($short_description == $long_description) {
                                        $long_description = '';
                                    }
                                }
                            }
                            ?>
                            <p><?php echo $short_description; ?></p>
                            <?php if ($long_description) : ?>
                                <p class="readmore">
                                    <a href="#" onclick="jQuery('.nav-tabs a[href=#description]').tab('show');">
                                        <?php echo JText::_('JGLOBAL_SHOW_FULL_DESCRIPTION'); ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="alert alert-error"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ADDON_XML_ERR'); ?></div>
                <?php endif; ?>

                <?php
                $this->fieldset = 'basic';
                $html = JLayoutHelper::render('joomla.edit.fieldset', $this);
                echo $html ? '<hr />' . $html : '';
                ?>
            </div>
            <div class="span3">
                <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
                <div class="form-vertical">
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('folder'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('folder'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel('element'); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput('element'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php if (isset($long_description) && $long_description != '') : ?>
            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'description', JText::_('JGLOBAL_FIELDSET_DESCRIPTION', true)); ?>
            <?php echo $long_description; ?>
            <?php echo JHtml::_('bootstrap.endTab'); ?>
        <?php endif; ?>

        <?php
        $this->fieldsets = array();
        $this->ignore_fieldsets = array('basic', 'description');
        echo JLayoutHelper::render('joomla.edit.params', $this);
        ?>

        <?php echo JHtml::_('bootstrap.endTabSet'); ?>
    </div>

    <input type="hidden" name="task" value="" />
    <?php if($this -> return_link){?>
    <input type="hidden" name="return" value="<?php echo $this -> return_link;?>" />
    <?php }?>
    <?php echo JHtml::_('form.token'); ?>
</form>
