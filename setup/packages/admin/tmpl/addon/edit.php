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

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\HTML\Helpers\StringHelper;

$this->fieldsets = $this->form->getFieldsets('params');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

$this->ignore_fieldsets = array('basic', 'description', 'permissions');
?>

<form action="<?php echo Route::_('index.php?option=com_tz_portfolio&view=addon&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate tpArticle">
    <div class="form-horizontal main-card">

        <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'general')); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'general', Text::_('COM_TZ_PORTFOLIO_ADDON', true)); ?>

            <?php echo HTMLHelper::_('tzbootstrap.addrow');?>
            <div class="col-md-9">
                <?php if ($this->item->xml){ ?>
                    <h3>
                    <?php
                    if ($this->item->xml)
                    {
                        echo ($text = (string) $this->item->xml->name) ? Text::_($text) : $this->item->name;
                    }
                    else
                    {
                        echo Text::_('COM_TZ_PORTFOLIO_ADDON_XML_ERR');
                    }
                    ?>
                    </h3>
                    <div class="info-labels mb-1">
                        <span class="badge bg-secondary" aria-describedby="tip-folder">
                            <?php echo $this -> item -> folder; ?>
                        </span>
                        <div role="tooltip" id="tip-folder">
                            <?php echo HTMLHelper::tooltipText('COM_TZ_PORTFOLIO_ADDON_FIELD_FOLDER_LABEL',
                                'COM_TZ_PORTFOLIO_ADDON_FIELD_FOLDER_DESC', true, false); ?>
                        </div> /
                        <span class="badge bg-secondary" aria-describedby="tip-element">
                            <?php echo $this -> item -> element; ?>
                        </span>
                        <div role="tooltip" id="tip-element">
                            <?php echo HTMLHelper::tooltipText('COM_TZ_PORTFOLIO_ADDON_FIELD_ELEMENT_LABEL',
                                'COM_TZ_PORTFOLIO_ADDON_FIELD_ELEMENT_DESC', true, false); ?>
                        </div>
                    </div>
                    <?php if ($this->item->xml->description){ ?>

                        <div>
                            <?php
                            $short_description = Text::_($this->item->xml->description);
                            $this->fieldset = 'description';
                            $long_description = LayoutHelper::render('joomla.edit.fieldset', $this);
                            if(!$long_description) {
                                $truncated = StringHelper::truncate($short_description, 550, true, false);
                                if(strlen($truncated) > 500) {
                                    $long_description = $short_description;
                                    $short_description = StringHelper::truncate($truncated, 250);
                                    if($short_description == $long_description) {
                                        $long_description = '';
                                    }
                                }
                            }
                            ?>
                            <p><?php echo $short_description; ?></p>
                            <?php if ($long_description){ ?>
                                <p class="readmore">
                                    <a href="#" onclick="jQuery('.nav-tabs a[href=#description]').tab('show');">
                                        <?php echo Text::_('JGLOBAL_SHOW_FULL_DESCRIPTION'); ?>
                                    </a>
                                </p>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php }else{ ?>
                    <div class="alert alert-error"><?php echo Text::_('COM_TZ_PORTFOLIO_ADDON_XML_ERR'); ?></div>
                <?php } ?>

                <?php
//                $this->fieldset = 'basic';
//                $html = LayoutHelper::render('joomla.edit.fieldset', $this);
//                echo $html ? '<hr />' . $html : '';
                ?>

                <?php
                /* @var Joomla\CMS\Form\Form $form */
                $form       = $this -> form;
                $fieldSets  = $form -> getFieldsets('params'); ?>
                <?php
                if(!empty($fieldSets)){
                    $i          = 1;
                    $opentab    = 0;

                    $xml = $form -> getXml();
                    foreach ($fieldSets as $name => $fieldSet):
                        $hasChildren    = $xml->xpath('//fieldset[@name="' . $name . '"]/fieldset');
                        $hasParent      = $xml->xpath('//fieldset/fieldset[@name="' . $name . '"]');
                        $isGrandchild   = $xml->xpath('//fieldset/fieldset/fieldset[@name="' . $name . '"]');

                        if(!in_array($name, $this -> ignore_fieldsets)){
                            $this -> ignore_fieldsets[]   = $name;
                        }
                ?>

                    <?php if (isset($fieldSet->description) && trim($fieldSet->description)){ ?>
                    <p class="tip"><?php echo $this->escape(Text::_($fieldSet->description));?></p>
                    <?php } ?>
                    <fieldset<?php echo (!$isGrandchild && $hasParent)?' class="options-form"':''?> >
                        <?php if (!$isGrandchild && $hasParent){
                            ?>
                            <legend><?php echo Text::_($fieldSet -> label); ?></legend>
                        <?php }?>
                        <?php if (!$hasChildren) : ?>
                            <?php echo $form->renderFieldset($name); ?>
                            <?php $opentab = 2; ?>
                        <?php endif; ?>
                    </fieldset>
                <?php
                    endforeach;
                } ?>
            </div>
            <div class="col-md-3">
                <fieldset class="form-vertical">
                    <?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
                    <div class="form-vertical form-no-margin">
                        <?php echo $this -> form -> renderField('folder');?>
                        <?php echo $this -> form -> renderField('element');?>
                    </div>
                </fieldset>
            </div>
            <?php echo HTMLHelper::_('tzbootstrap.endrow');?>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php if (isset($long_description) && $long_description != '') : ?>
            <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'description', Text::_('JGLOBAL_FIELDSET_DESCRIPTION', true)); ?>
            <?php echo $long_description; ?>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>
        <?php endif; ?>

        <?php
        $this->fieldsets = array();
        echo LayoutHelper::render('joomla.edit.params', $this);
        ?>

        <?php if ($this->canDo->get('core.admin')){
            $rules  = $this -> form -> getInput('rules');
            $rules  = trim($rules);
            if($rules && !empty($rules)){
        ?>
            <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'permissions', Text::_('JCONFIG_PERMISSIONS_LABEL')); ?>
            <?php echo $this->form->getInput('rules'); ?>
            <?php echo $this->form->getInput('title'); ?>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>
        <?php }
        } ?>

        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
    </div>

    <input type="hidden" name="task" value="" />
    <?php if($this -> return_link){?>
    <input type="hidden" name="return" value="<?php echo $this -> return_link;?>" />
    <?php }?>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
