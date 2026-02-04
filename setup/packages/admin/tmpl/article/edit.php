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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;

$doc    = Factory::getApplication() -> getDocument();

/**
 * Create shortcut to parameters.
 * @var \Joomla\Registry\Registry $params
 */
$params = $this->state->get('params');

/* @var \Joomla\CMS\Form\Form $form */
$form   = $this -> form;

if($params) {
    $params = $params->toArray();
}

// This checks if the config options have ever been saved. If they haven't they will fall back to the original settings.
$editoroptions = isset($params['show_publishing_options']);

if (!$editoroptions):
    $params['show_publishing_options'] = '1';
    $params['show_article_options'] = '1';
    $params['show_urls_images_backend'] = '0';
    $params['show_urls_images_frontend'] = '0';
endif;

// Check if the article uses configuration settings besides global. If so, use them.
if (!empty($this->item->attribs['show_publishing_options'])):
    $params['show_publishing_options'] = $this->item->attribs['show_publishing_options'];
endif;
if (!empty($this->item->attribs['show_article_options'])):
    $params['show_article_options'] = $this->item->attribs['show_article_options'];
endif;
if (!empty($this->item->attribs['show_urls_images_backend'])):
    $params['show_urls_images_backend'] = $this->item->attribs['show_urls_images_backend'];
endif;

$mediavalue = '';
$media      = array();

$pluginsTab = $this -> pluginsTab;

$assoc = Associations::isEnabled();

// Are associations implemented for this extension?
$extensionassoc = array_key_exists('item_associations', $this->form->getFieldsets());

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

$wa -> addInlineScript('(function($){
    "use strict";
    $(document).ready(function(){
        $(\'#jform_catid\').on(\'change\',function(){
            $("#jform_second_catid option[value="+ $(this).val() +"]:selected").removeAttr("selected");
            $(\'#jform_second_catid option:disabled\').removeAttr(\'disabled\');
            $(\'#jform_second_catid option[value="\'+this.value+\'"]\').attr(\'disabled\',\'disabled\');
            $(\'#jform_second_catid\').trigger(\'liszt:updated\');

            var __second_fancy  = $("#jform_second_catid").closest("joomla-field-fancy-select")[0];
            if(typeof __second_fancy !== "undefined" && typeof __second_fancy.choicesInstance !== "undefined"){
                __second_fancy.enableAllOptions();
                __second_fancy.choicesInstance.removeActiveItemsByValue($(this).val());
                __second_fancy.disableByValue($(this).val());
            }
        });
        $("#jform_catid").trigger("change");
    });
})(jQuery);
');

$jTab   = 'uitab';

?>
<form action="<?php echo Route::_('index.php?option=com_tz_portfolio&view=article&layout=edit&id='
    .(int) $this->item->id); ?>"
      method="post"
      name="adminForm"
      id="adminForm"
      class="form-validate tpArticle"
      enctype="multipart/form-data">
    <?php echo HTMLHelper::_('tzbootstrap.addrow');?>
    <div class="col-md-8 form-horizontal">
        <div class="main-card">
            <?php echo HTMLHelper::_($jTab.'.startTabSet', 'myTab', array('active' => 'general')); ?>

            <?php
            // Tab general
            echo HTMLHelper::_($jTab.'.addTab', 'myTab', 'general',
                Text::_('JDETAILS', true)); ?>
            <?php echo HTMLHelper::_('tzbootstrap.addrow');?>
            <div class="col-md-6">
                <?php echo $form -> renderField('title');?>
                <?php echo $form -> renderField('alias');?>
                <div class="control-group">
                    <div class="control-label">
                        <label><?php echo $this->form->getLabel('tags');?></label>
                    </div>
                    <div class="controls">
                        <?php echo $form -> getInput('tags');?>
                        <div><?php echo Text::_('COM_TZ_PORTFOLIO_FORM_TAGS_DESC');?></div>
                    </div>
                </div>
                <?php echo $form -> renderField('state');?>
                <?php echo $form -> renderField('access');?>
                <?php echo $form -> renderField('priority');?>
                <?php echo $form -> renderField('id');?>
            </div>
            <div class="col-md-6">
                <?php echo $form -> renderField('catid');?>
                <?php echo $form -> renderField('second_catid');?>
                <?php echo $form -> renderField('groupid');?>
                <?php echo $form -> renderField('type');?>
                <?php echo $form -> renderField('featured');?>
                <?php echo $form -> renderField('language');?>
                <?php echo $form -> renderField('template_id');?>
            </div>
            <?php echo HTMLHelper::_('tzbootstrap.endrow');?>

            <?php
            // Before description position
            echo $this -> loadTemplate('addon_before_description');

            ?>


            <?php echo HTMLHelper::_($jTab.'.startTabSet', 'tpArticleTab', ['active' => 'tz_content',
                'recall' => true, 'breakpoint' => 768]); ?>
            <?php echo HTMLHelper::_($jTab.'.addTab', 'tpArticleTab', 'tz_content', Text::_('COM_TZ_PORTFOLIO_TAB_CONTENT')); ?>
            <?php echo $this->form->getInput('articletext'); ?>
            <?php echo HTMLHelper::_($jTab.'.endTab'); ?>
            <?php
            if(!empty($this -> pluginsMediaTypeTab) && count($this -> pluginsMediaTypeTab)){
                foreach($this -> pluginsMediaTypeTab as $media){
                    echo HTMLHelper::_($jTab.'.addTab', 'tpArticleTab', 'tztabsaddonsplg_mediatype'
                        . $media->type->value, $media -> type -> text);
                    echo $media -> html;
                    echo HTMLHelper::_($jTab.'.endTab');
                }
            }
            ?>
            <?php echo HTMLHelper::_($jTab.'.addTab', 'tpArticleTab', 'tztabsFields', Text::_('COM_TZ_PORTFOLIO_TAB_FIELDS')); ?>
            <?php echo $this -> loadTemplate('extrafields');?>
            <?php echo HTMLHelper::_($jTab.'.endTab'); ?>

            <?php
            if(!empty($this -> advancedDesc) && count($this -> advancedDesc)){
                foreach($this -> advancedDesc as $advance){
                    echo HTMLHelper::_($jTab.'.addTab', 'tpArticleTab', 'tztabsaddonsplg_'
                        .$advance -> group.'_'.$advance -> addon, $advance -> title);
                    echo $advance -> html;
                    echo HTMLHelper::_($jTab.'.endTab');
                }
            }
            ?>
            <?php echo HTMLHelper::_($jTab.'.endTabSet'); ?>

            <?php
            // After description position
            echo $this->loadTemplate('addon_after_description');
            ?>


            <?php echo HTMLHelper::_($jTab.'.endTab');
            // End tab general

            ?>

            <?php if($assoc && $extensionassoc){ ?>
                <?php echo HTMLHelper::_($jTab.'.addTab', 'myTab', 'associations',
                    Text::_('JGLOBAL_FIELDSET_ASSOCIATIONS', true)); ?>
                <?php echo $this->loadTemplate('associations'); ?>
                <?php echo HTMLHelper::_($jTab.'.endTab'); ?>
            <?php } ?>

            <?php if ($this->canDo->get('core.admin')){ ?>
                <?php echo HTMLHelper::_($jTab.'.addTab', 'myTab', 'permissions',
                    Text::_('JCONFIG_PERMISSIONS_LABEL', true)); ?>
                <?php echo $this->form->getInput('rules'); ?>
                <?php echo HTMLHelper::_($jTab.'.endTab'); ?>
            <?php } ?>
            <?php echo HTMLHelper::_($jTab.'.endTabSet'); ?>

        </div>
    </div>
    <div class="col-md-4 form-vertical">
        <?php echo HTMLHelper::_('bootstrap.startAccordion', 'articleOptions', array('active' => 'collapse0'
        , 'parent' => true));?>

        <?php // Do not show the publishing options if the edit form is configured not to. ?>
        <?php  if ($params['show_publishing_options'] || ( $params['show_publishing_options'] = '' && !empty($editoroptions)) ): ?>
            <?php echo HTMLHelper::_('bootstrap.addSlide', 'articleOptions', Text::_('JGLOBAL_FIELDSET_PUBLISHING'), 'collapse0'); ?>
            <fieldset>
                <?php echo $form -> renderField('created_by');?>
                <?php echo $form -> renderField('created_by_alias');?>
                <?php echo $form -> renderField('created');?>
                <?php echo $form -> renderField('publish_up');?>
                <?php echo $form -> renderField('publish_down');?>

                <?php if ($this->item && $this->item->modified_by) : ?>
                    <?php echo $form -> renderField('modified_by');?>
                    <?php echo $form -> renderField('modified');?>
                <?php endif; ?>

                <?php if ($this->item->version) : ?>
                    <?php echo $form -> renderField('version');?>
                <?php endif; ?>

                <?php if ($this->item->hits) : ?>
                    <?php echo $form -> renderField('hits');?>
                <?php endif; ?>
            </fieldset>
            <?php echo HTMLHelper::_('bootstrap.endSlide');?>
        <?php  endif; ?>

        <?php
        $fieldSets = $form -> getFieldsets('attribs'); ?>
        <?php $i = 1;?>
        <?php $opentab = 0; ?>
        <?php

        $xml = $form -> getXml();

        $fielsetLoaded  = array();

        foreach ($fieldSets as $name => $fieldSet):
            if(empty($xml -> xpath('/form/fields/fieldset[@name="'.$name.'"]'))){
                continue;
            }
            $hasChildren    = $xml->xpath('//fieldset[@name="' . $name . '"]/fieldset');
            $hasParent      = $xml->xpath('//fieldset/fieldset[@name="' . $name . '"]');
            $isGrandchild   = $xml->xpath('//fieldset/fieldset/fieldset[@name="' . $name . '"]');
            ?>

            <?php // If the parameter says to show the article options or if the parameters have never been set, we will
            // show the article options. ?>

            <?php if ($params['show_article_options'] || (( $params['show_article_options'] == ''
                && !empty($editoroptions) ))){ ?>
                <?php // Go through all the fieldsets except the configuration and basic-limited, which are
                // handled separately below. ?>

                <?php if ($name != 'editorConfig' && $name != 'basic-limited'){ ?>
                    <?php if(!$hasParent){ ?>
                        <?php if ($opentab){ ?>
                            <?php
                            echo HTMLHelper::_('bootstrap.endSlide');?>
                        <?php } ?>

                        <?php echo HTMLHelper::_('bootstrap.addSlide', 'articleOptions',
                            Text::_($fieldSet->label), 'collapse' . $i++);?>

                        <?php $opentab = 1; ?>
                    <?php } ?>
                    <?php if (isset($fieldSet->description) && trim($fieldSet->description)){ ?>
                        <p class="tip"><?php echo $this->escape(Text::_($fieldSet->description));?></p>
                    <?php } ?>
                    <fieldset<?php echo (!$isGrandchild && $hasParent)?' class="options-form"':''?> >
                        <?php if (!$isGrandchild && $hasParent){ ?>
                            <legend><?php echo Text::_($fieldSet->label); ?></legend>
                        <?php }?>
                        <?php if($ownerFields = $xml->xpath('//fieldset[@name="' . $name . '"]/field')):
                            foreach ($ownerFields as $field){
                                $fAttrib    = $field -> attributes();
                                echo $form -> renderField($fAttrib -> name, 'attribs');
                            }
                            ?>
                        <?php endif; ?>
<!--                        --><?php //if (!$hasChildren) : ?>
<!--                            --><?php
//                            echo $form->renderFieldset($name); ?>
<!--                            --><?php //$opentab = 2; ?>
<!--                        --><?php //elseif($ownerFields = $xml->xpath('//fieldset[@name="' . $name . '"]/field')):
//                            foreach ($ownerFields as $field){
//                                $fAttrib    = $field -> attributes();
//                                echo $form -> renderField($fAttrib -> name, 'attribs');
//                            }
//                            ?>
<!--                        --><?php //endif; ?>

                        <?php
                        if($hasChildren){
                            foreach($hasChildren as $childField){
                                $childAttrib    = $childField -> attributes();
                                $childName      = (string) $childAttrib -> name;

                                if(!in_array($childName, $fielsetLoaded)){
                                ?>

                                <fieldset class="options-form">
                                    <legend><?php echo Text::_($childAttrib -> label); ?></legend>
                                    <?php echo $form -> renderFieldset($childAttrib -> name); ?>
                                </fieldset>
                            <?php
                                    $fielsetLoaded[]    = (string) $childAttrib -> name;
                                }
                            }
                        }
                        ?>
                    </fieldset>
                <?php }elseif ($name == 'basic-limited'){
                    // If we are not showing the options we need to use the hidden fields so the values are not lost.
                ?>
                    <?php foreach ($this->form->getFieldset('basic-limited') as $field):
                        echo $field->input;
                    endforeach; ?>
                <?php } ?>
            <?php } ?>
        <?php endforeach; ?>

        <?php
        if($opentab) {
            echo HTMLHelper::_('bootstrap.endSlide');
        } ?>

        <?php // The url and images fields only show if the configuration is set to allow them.  ?>
        <?php // This is for legacy reasons. ?>

        <?php echo HTMLHelper::_('bootstrap.addSlide', 'articleOptions', Text::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'),
            'meta-options' ); ?>
        <fieldset class="panelform">
            <?php echo $this->loadTemplate('metadata'); ?>
        </fieldset>
        <?php echo HTMLHelper::_('bootstrap.endSlide');?>
        <?php echo HTMLHelper::_('bootstrap.endAccordion');?>

    </div>
    <?php echo HTMLHelper::_('tzbootstrap.endrow');?>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="return" value="<?php echo Factory::getApplication() -> input -> getCmd('return');?>" />
    <input type="hidden" name="contentid" id="contentid" value="<?php echo Factory::getApplication() -> input -> getCmd('id');?>">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>