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

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

// Create shortcut to parameters.
$params = $this->state->get('params');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate')
    ->useScript('com_tz_portfolio.jquery-ui')
    /*->useStyle('com_tz_portfolio.jquery-ui')*/;

//$doc    = Factory::getDocument();
//$doc -> addScript(TZ_Portfolio_PlusUri::root(true, null, true).'/js/jquery-ui.min.js', array('version' => 'v=1.11.4'));
//$doc -> addStyleSheet(TZ_Portfolio_PlusUri::root(true, null, true).'/css/jquery-ui.min.css', array('version' => 'v=1.11.4'));
//$doc -> addStyleSheet(TZ_Portfolio_PlusUri::root(true, null, true).'/css/tz_portfolio_plus.min.css', array('version' => 'auto'));

if(!$this -> tagsSuggest){
    $this -> tagsSuggest    = 'null';
}

$wa -> addInlineScript('
(function($){
    "use strict";
    $(document).ready(function(){
        $("#jform_catid").on("change",function(){
            $("#jform_second_catid option[value="+ this.value +"]:selected").removeAttr("selected");
            $("#jform_second_catid option:disabled").removeAttr("disabled");
            $("#jform_second_catid option[value="+this.value+"]").attr("disabled","disabled");
            $("#jform_second_catid").trigger("liszt:updated");
            
            var __second_fancy  = $("#jform_second_catid").closest("joomla-field-fancy-select")[0];
            if(typeof __second_fancy !== "undefined" && typeof __second_fancy.choicesInstance !== "undefined"){
                __second_fancy.enableAllOptions();
                __second_fancy.choicesInstance.removeActiveItemsByValue(this.value);
                __second_fancy.disableByValue(this.value);
            }
        });
        $("#jform_catid").trigger("change");
        
        $(document).off("click.bs.tab.data-api")
					.on("click.bs.tab.data-api", "[data-toggle=tab]", function (e) {
            e.preventDefault();
              $(this).tab("show");
        });
                
        $(document).ready(function(){
            $("[data-toggle=dropdown]").parent().on("hidden.bs.dropdown", function(){ $(this).show();});
            $("[data-toggle=popover],.hasPopover").on("mouseleave", function () {
                if(!$(this).is(":visible")){
                    $(this).show();
                }
            });
        });
    });
})(jQuery);
');

//$bootstrapClass = '';
//if($params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 4){
//    $bootstrapClass = 'tpp-bootstrap ';
//}elseif($params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 3){
//    $bootstrapClass = 'tzpp_bootstrap3 ';
//}

$menu   = Factory::getApplication() -> getMenu();
$active = $menu -> getActive();
$url    = 'index.php?option=com_tz_portfolio&view='.$this -> getName()
    .((isset($active -> id) && $active -> id)?'&Itemid='.$active -> id:'')
    .'&a_id=' . (int) $this->item->id;
?>

<div class="tp-edit-page<?php echo $this->pageclass_sfx; ?>">
    <?php if ($params->get('show_page_heading')){ ?>
        <div class="page-header">
            <h1>
                <?php echo $this->escape($params->get('page_heading')); ?>
            </h1>
        </div>
    <?php } ?>

    <form action="<?php echo Route::_($url);
    ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-vertical"
          enctype="multipart/form-data">

        <?php echo HTMLHelper::_('uitab.startTabSet', 'tp-tab-edit', array('active' => 'tp-tab-edit__general')); ?>

            <?php
            // Start tab general
            echo HTMLHelper::_('uitab.addTab', 'tp-tab-edit', 'tp-tab-edit__general', Text::_('JDETAILS')); ?>
                <div class="row">
                    <div class="col-md-6">
                        <?php echo $this->form->renderField('title'); ?>
                        <?php echo $this->form->renderField('alias'); ?>

                        <div class="control-group">
                            <div class="control-label">
                                <label><?php echo $this->form->getLabel('tags');?></label>
                            </div>
                            <div class="controls">
                                <?php echo $this -> form -> getInput('tags'); ?>
                                <div><?php echo Text::_('COM_TZ_PORTFOLIO_FORM_TAGS_DESC');?></div>
                            </div>
                        </div>
                        <?php echo $this->form->renderField('state'); ?>
                        <?php echo $this->form->renderField('access'); ?>
                        <?php echo $this->form->renderField('id'); ?>
                    </div>
                    <div class="col-md-6">
                        <?php echo $this->form->renderField('catid'); ?>
                        <?php echo $this->form->renderField('second_catid'); ?>
                        <?php echo $this->form->renderField('groupid'); ?>
                        <?php echo $this->form->renderField('type'); ?>
                        <?php echo $this->form->renderField('featured'); ?>
                        <?php echo $this->form->renderField('language'); ?>
                        <?php echo $this->form->renderField('template_id'); ?>
                        <?php echo $this -> form -> renderField('priority');?>
                    </div>
                </div>

                <?php
                // Before description position
                echo $this -> loadTemplate('addon_before_description');
                ?>

                <?php echo HTMLHelper::_('uitab.startTabSet', 'tp-tab-add-on', array('active' => 'tp-tab-add-on__content')); ?>
                    <?php echo HTMLHelper::_('uitab.addTab', 'tp-tab-add-on', 'tp-tab-add-on__content', Text::_('COM_TZ_PORTFOLIO_TAB_CONTENT')); ?>
                    <?php echo $this->form->getInput('articletext'); ?>
                    <?php echo HTMLHelper::_('uitab.endTab'); ?>

                    <?php
                    if($this -> plgTabs && count($this -> plgTabs)) {
                        foreach ($this->plgTabs as $media) {
                            echo HTMLHelper::_('uitab.addTab', 'tp-tab-add-on', 'tp-tab-add-on__mediatype-'.$media -> type -> value, $media -> type -> text);
                            echo $media -> html;
                            echo HTMLHelper::_('uitab.endTab');
                        }
                    }
                    ?>

                    <?php
                    // Create extra fields tabs
                    echo HTMLHelper::_('uitab.addTab', 'tp-tab-add-on', 'tp-tab-add-on__fields', Text::_('COM_TZ_PORTFOLIO_TAB_FIELDS'));
                    echo $this-> loadTemplate('extrafields');
                    echo HTMLHelper::_('uitab.endTab');
                    ?>


                    <?php
                    // Create advanced tabs from add-ons
                    if(isset($this -> advancedDesc) && count($this -> advancedDesc)){
                        foreach($this -> advancedDesc as $i => $advance){
                            $id              = 'tztabsaddonsplg_'.$advance -> group.'_'
                                .$advance -> addon;

                            echo HTMLHelper::_('uitab.addTab', 'tp-tab-add-on',
                                'tp-tab-add-on__'.$advance -> group.'-'.$advance -> addon, $advance -> title);
                            echo $advance -> html;
                            echo HTMLHelper::_('uitab.endTab');
                        }
                    }
                    ?>

                <?php echo HTMLHelper::_('uitab.endTabSet'); ?>

            <?php echo HTMLHelper::_('uitab.endTab'); // End tab general ?>

            <?php // Start tab publishing
            echo HTMLHelper::_('uitab.addTab', 'tp-tab-edit', 'tp-tab-edit__publishing', Text::_('COM_TZ_PORTFOLIO_PUBLISHING')); ?>
                <?php echo $this->form->renderField('publish_up'); ?>
                <?php echo $this->form->renderField('publish_down'); ?>
            <?php echo HTMLHelper::_('uitab.endTab'); // End tab publishing ?>

            <?php // Start tab options
            echo HTMLHelper::_('uitab.addTab', 'tp-tab-edit', 'tp-tab-edit__options',
                Text::_('JOPTIONS')); ?>

                <?php  $fieldSets = $this->form->getFieldsets('attribs');
                if($fieldSets && count($fieldSets)) {
                    ?>
                    <?php echo HTMLHelper::_('bootstrap.startAccordion', 'tp-ac-edit__article-options', array('active' => 'tp-ac-edit__collapse0'
                    , 'parent' => true)); // Start accordion ?>
                    <?php $i = 0; ?>
                    <?php foreach ($fieldSets as $name => $fieldSet) { ?>
                        <?php echo HTMLHelper::_('bootstrap.addSlide', 'tp-ac-edit__article-options', Text::_($fieldSet->label), 'tp-ac-edit__collapse' . $i++); ?>
                        <?php if (isset($fieldSet->description) && trim($fieldSet->description)) { ?>
                            <p class="tip"><?php echo $this->escape(Text::_($fieldSet->description)); ?></p>
                        <?php } ?>
                        <fieldset>
                            <?php foreach ($this->form->getFieldset($name) as $field) {
                                echo $field->renderField();
                            } ?>
                        </fieldset>
                        <?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
                    <?php } ?>

                    <?php echo HTMLHelper::_('bootstrap.endAccordion');
                } // End accordion?>
            <?php echo HTMLHelper::_('uitab.endTab'); // End tab options ?>

            <?php // Start tab metadata
            echo HTMLHelper::_('uitab.addTab', 'tp-tab-edit', 'tp-tab-edit__metadata',
                Text::_('COM_TZ_PORTFOLIO_METADATA')); ?>
                <?php echo $this->loadTemplate('metadata'); ?>
            <?php echo HTMLHelper::_('uitab.endTab'); // End tab metadata ?>

        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>

        <?php
        // Before description position
        echo $this -> loadTemplate('addon_after_description');
        ?>

        <?php
        $user       = Factory::getApplication()->getIdentity();
        $canApprove = $user -> authorise('core.approve', 'com_tz_portfolio');
        $saveText   = Text::_('JSAVE');
        if(!$canApprove){
            $saveText   = Text::_('COM_TZ_PORTFOLIO_SUBMIT_APPROVE');
        }
        if($canApprove && ($this -> item -> state == 3 || $this -> item -> state == 4)){
            $saveText   = Text::_('COM_TZ_PORTFOLIO_APPROVE_AND_PUBLISH');
        }
        ?>
        <div class="uk-margin-top" data-uk-margin>
                <button type="button" class="uk-button uk-button-primary" onclick="Joomla.submitbutton('article.save')">
                    <i class="fas fa-check"></i> <?php echo $saveText; ?>
                </button>
            <?php if(!$canApprove){ ?>
                <button type="button" class="uk-button uk-button-primary" onclick="Joomla.submitbutton('article.draft')">
                    <i class="fas fa-check"></i> <?php echo Text::_('COM_TZ_PORTFOLIO_SAVE_DRAFT') ?>
                </button>
            <?php } ?>
            <?php if($canApprove && ($this -> item -> state == 3 || $this -> item -> state == 4)){ ?>
                <button type="button" class="uk-button uk-button-primary" onclick="Joomla.submitbutton('article.reject')">
                    <i class="fas fa-check"></i> <?php echo Text::_('COM_TZ_PORTFOLIO_REJECT') ?>
                </button>
            <?php } ?>
                <button type="button" class="uk-button uk-button-danger" onclick="Joomla.submitbutton('article.cancel')">
                    <i class="fas fa-times-circle"></i> <?php echo Text::_('JCANCEL') ?>
                </button>
            <?php if ($params->get('save_history', 0) && $this->item->id) : ?>
                <?php echo $this->form->getInput('contenthistory'); ?>
            <?php endif; ?>
        </div>

        <input type="hidden" name="task" value="" />
        <input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />

        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>
