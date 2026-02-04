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

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$form   = $this -> form;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');
?>
<form name="adminForm" method="post" id="adminForm" class="tpArticle"
      action="index.php?option=com_tz_portfolio&view=field&layout=edit&id=<?php echo $this -> item -> id?>">

    <?php echo HTMLHelper::_('tzbootstrap.addrow');?>
        <?php // Begin Content ?>
        <div class="col-md-8 form-horizontal">
            <div class="main-card">
                <fieldset class="adminform">
                <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'details')); ?>

                    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('JDETAILS', true)); ?>

                    <?php echo HTMLHelper::_('tzbootstrap.addrow');?>
                        <div class="col-md-6">
                            <?php echo $this -> form -> renderField('title');?>
                            <?php echo $this -> form -> renderField('groupid');?>
                            <?php echo $this -> form -> renderField('published');?>
                            <?php echo $this -> form -> renderField('type');?>
                            <?php echo $this -> form -> renderField('images');?>
                            <div class="control-group">
                                <div class="control-label"><?php echo $form -> getLabel('value');?></div>
                                <div class="controls">
                                    <div id="<?php echo $form -> getField('value') -> id;?>">
                                        <?php
                                        if($fieldValue = $form->getInput('value')) {
                                            echo $fieldValue;
                                        }else{
                                            echo Text::_('COM_TZ_PORTFOLIO_NO_VALUE');
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?php echo $this -> form -> renderField('list_view');?>
                            <?php echo $this -> form -> renderField('detail_view');?>
                            <?php echo $this -> form -> renderField('advanced_search');?>
                            <?php echo $this -> form -> renderField('access');?>
                        </div>
                    <?php echo HTMLHelper::_('tzbootstrap.endrow');?>

                    <?php echo $this -> form -> renderField('description');?>

                    <?php echo HTMLHelper::_('uitab.endTab'); ?>

                    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', Text::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>

                        <?php echo $this -> form -> renderField('created');?>
                        <?php echo $this -> form -> renderField('created_by');?>

                    <?php if ($this->item && $this->item->modified_by){ ?>
                        <?php echo $this -> form -> renderField('modified_by');?>
                        <?php echo $this -> form -> renderField('modified');?>
                    <?php } ?>

                        <?php echo $this -> form -> renderField('id');?>
                    <?php echo HTMLHelper::_('uitab.endTab'); ?>

                    <?php if ($this->canDo->get('core.admin')) : ?>
                        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'permissions', Text::_('JCONFIG_PERMISSIONS_LABEL')); ?>
                        <?php echo $this->form->getInput('rules'); ?>
                        <?php echo HTMLHelper::_('uitab.endTab'); ?>
                    <?php endif; ?>

                <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
                </fieldset>
            </div>
        </div>
        <?php // End Content ?>
        <?php // Begin Sidebar ?>
        <div class="col-md-4">
            <div class="form-vertical">
            <?php echo HTMLHelper::_('bootstrap.startAccordion', 'fieldOptions', array('active' => 'collapse0'
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
                    echo HTMLHelper::_('bootstrap.addSlide', 'fieldOptions',
                        Text::_(!empty($fieldSet->label)?$fieldSet -> label:'COM_TZ_PORTFOLIO_FIELDSET_'
                            .strtoupper($name).'_LABEL'), 'collapse' . $i++);
                    ?>

                    <?php if (isset($fieldSet->description) && trim($fieldSet->description)) : ?>
                        <p class="tip"><?php echo $this->escape(Text::_($fieldSet->description));?></p>
                    <?php endif; ?>

                    <?php foreach ($fields as $field) {
                        echo $field->renderField();
                    } ?>

                    <?php echo HTMLHelper::_('bootstrap.endSlide');?>
                <?php
                    endif;
                endforeach;
                ?>

            <?php echo HTMLHelper::_('bootstrap.endAccordion');?>
            </div>

            <?php
            $previewHtml    = $this -> form -> getInput('preview');

            if(!empty($previewHtml)){
            ?>
            <div class="form-horizontal mt-3">
                <?php echo HTMLHelper::_('bootstrap.startAccordion', 'previewOptions', array('active' => 'preview_fieldset'));?>
                    <?php
                    // Start accordion preview
                    $preview    = $this->form -> getFieldset('preview_fieldset');
                    echo HTMLHelper::_('bootstrap.addSlide', 'previewOptions', Text::_('JGLOBAL_PREVIEW'), 'preview_fieldset');
                    ?>
                    <?php echo $this -> form -> renderField('preview');?>
                    <?php echo HTMLHelper::_('bootstrap.endSlide');?>
                <?php echo HTMLHelper::_('bootstrap.endAccordion');?>
            </div>
            <?php } ?>
        </div>
        <?php // End Sidebar ?>
    <?php echo HTMLHelper::_('tzbootstrap.endrow');?>
    <input type="hidden" value="com_tz_portfolio" name="option">
    <input type="hidden" value="" name="task">
    <?php echo HTMLHelper::_('form.token');?>
</form>