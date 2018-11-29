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

// No direct access.
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tabstate');

if(!COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
    JHtml::_('formbehavior.chosen', 'select');
}else{
    JHtml::_('formbehavior.chosen', 'select[multiple]');
}

$doc    = JFactory::getDocument();
$doc -> addScript(TZ_Portfolio_PlusUri::base(true, true).'/js/jquery-ui.min.js');
$doc -> addStyleSheet(TZ_Portfolio_PlusUri::base(true, true).'/css/jquery-ui.min.css');
$doc -> addStyleSheet(TZ_Portfolio_PlusUri::base(true, true).'/css/tz_portfolio_plus.min.css');

// Create shortcut to parameters.
$params = $this->state->get('params');
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

$assoc = JLanguageAssociations::isEnabled();

// Are associations implemented for this extension?
$extensionassoc = array_key_exists('item_associations', $this->form->getFieldsets());
?>

    <script type="text/javascript">
    (function($){
        "use strict";
        Joomla.submitbutton = function(task) {
            if (task == 'article.cancel' || document.formvalidator.isValid(document.getElementById('item-form'))) {
                <?php echo $this->form->getField('articletext')->save(); ?>
                Joomla.submitform(task, document.getElementById('item-form'));
            }
        }
        $(document).ready(function(){
            $('#jform_second_catid option[value="'+$('#jform_catid').val()+'"]').attr('disabled','disabled');
            $('#jform_second_catid').trigger('liszt:updated');
            $('#jform_catid').on('change',function(){
                $('#jform_second_catid option:selected').removeAttr('selected');
                $('#jform_second_catid option:disabled').removeAttr('disabled');
                $('#jform_second_catid option[value="'+this.value+'"]').attr('disabled','disabled');
                $('#jform_second_catid').trigger('liszt:updated');
            });
        });
    })(jQuery);
    </script>


    <form action="<?php echo JRoute::_('index.php?option=com_tz_portfolio_plus&view=article&layout=edit&id='.(int) $this->item->id); ?>"
          method="post"
          name="adminForm"
          id="item-form"
          class="form-validate tpArticle"
          enctype="multipart/form-data">
        <?php echo JHtml::_('tzbootstrap.addrow');?>
            <div class="span8 col-md-8 form-horizontal">
                <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

                    <?php
                    // Tab general
                    echo JHtml::_('bootstrap.addTab', 'myTab', 'general',
                        JText::_('JDETAILS', true)); ?>
                        <?php echo JHtml::_('tzbootstrap.addrow');?>
                            <div class="span6 col-md-6">
                                <?php echo $this -> form -> renderField('title');?>
                                <?php echo $this -> form -> renderField('alias');?>
                                <div class="control-group">
                                    <div class="control-label">
                                        <label><?php echo $this->form->getLabel('tags');?></label>
                                    </div>
                                    <div class="controls">
                                        <?php echo $this -> form -> getInput('tags');?>
                                        <div><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_FORM_TAGS_DESC');?></div>
                                    </div>
                                </div>
                                <?php echo $this -> form -> renderField('state');?>
                                <?php echo $this -> form -> renderField('access');?>
                                <?php echo $this -> form -> renderField('priority');?>
                                <?php echo $this -> form -> renderField('id');?>
                            </div>
                            <div class="span6 col-md-6">
                                <?php echo $this -> form -> renderField('catid');?>
                                <?php echo $this -> form -> renderField('second_catid');?>
                                <?php echo $this -> form -> renderField('groupid');?>
                                <?php echo $this -> form -> renderField('type');?>
                                <?php echo $this -> form -> renderField('featured');?>
                                <?php echo $this -> form -> renderField('language');?>
                                <?php echo $this -> form -> renderField('template_id');?>
                            </div>
                        <?php echo JHtml::_('tzbootstrap.endrow');?>

                        <ul class="nav nav-tabs">
                            <li class="nav-item active"><a class="nav-link" href="#tz_content" data-toggle="tab"><?php
                                    echo JText::_('COM_TZ_PORTFOLIO_PLUS_TAB_CONTENT');?></a></li>
                            <?php echo $this -> loadTemplate('plugin_title_tab');?>
                            <li class="nav-item"><a class="nav-link" href="#tztabsFields" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_TAB_FIELDS');?></a></li>
                        </ul>
                        <?php //-- Begin Content --// ?>
                        <div class="tab-content">
                            <?php //-- Begin Tabs --// ?>
                            <div class="tab-pane active" id="tz_content">
                                <?php echo $this->form->getInput('articletext'); ?>
                            </div>

                            <?php echo $this -> loadTemplate('plugin_content_tab');?>
                            <div class="tab-pane" id="tztabsFields">
                                <?php echo $this -> loadTemplate('extrafields');?>
                            </div>
                            <?php //-- End Tabs --// ?>
                        </div>
                        <?php //-- End Content --// ?>


                    <?php echo JHtml::_('bootstrap.endTab');
                    // End tab general
                    ?>

                    <?php if($assoc && $extensionassoc){ ?>
                    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'associations',
                            JText::_('JGLOBAL_FIELDSET_ASSOCIATIONS', true)); ?>
                        <?php echo $this->loadTemplate('associations'); ?>
                    <?php echo JHtml::_('bootstrap.endTab'); ?>
                    <?php } ?>

                    <?php if ($this->canDo->get('core.admin')){ ?>
                    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions',
                        JText::_('JCONFIG_PERMISSIONS_LABEL', true)); ?>
                        <?php echo $this->form->getInput('rules'); ?>
                    <?php echo JHtml::_('bootstrap.endTab'); ?>
                    <?php } ?>
                <?php echo JHtml::_('bootstrap.endTabSet'); ?>

            </div>
            <div class="span4 col-md-4 form-vertical">
                <?php echo JHtml::_('bootstrap.startAccordion', 'articleOptions', array('active' => 'collapse0'
                , 'parent' => true));?>

                <?php // Do not show the publishing options if the edit form is configured not to. ?>
                <?php  if ($params['show_publishing_options'] || ( $params['show_publishing_options'] = '' && !empty($editoroptions)) ): ?>
                    <?php echo JHtml::_('bootstrap.addSlide', 'articleOptions', JText::_('JGLOBAL_FIELDSET_PUBLISHING'), 'collapse0'); ?>
                    <fieldset>
                        <?php echo $this -> form -> renderField('created_by');?>
                        <?php echo $this -> form -> renderField('created_by_alias');?>
                        <?php echo $this -> form -> renderField('created');?>
                        <?php echo $this -> form -> renderField('publish_up');?>
                        <?php echo $this -> form -> renderField('publish_down');?>

                        <?php if ($this->item && $this->item->modified_by) : ?>
                            <?php echo $this -> form -> renderField('modified_by');?>
                            <?php echo $this -> form -> renderField('modified');?>
                        <?php endif; ?>

                        <?php if ($this->item->version) : ?>
                            <?php echo $this -> form -> renderField('version');?>
                        <?php endif; ?>

                        <?php if ($this->item->hits) : ?>
                            <?php echo $this -> form -> renderField('hits');?>
                        <?php endif; ?>
                    </fieldset>
                    <?php echo JHtml::_('bootstrap.endSlide');?>
                <?php  endif; ?>

                <?php  $fieldSets = $this->form->getFieldsets('attribs'); ?>
                <?php $i = 1;?>
                <?php foreach ($fieldSets as $name => $fieldSet) : ?>
                    <?php // If the parameter says to show the article options or if the parameters have never been set, we will
                    // show the article options. ?>

                    <?php if ($params['show_article_options'] || (( $params['show_article_options'] == '' && !empty($editoroptions) ))): ?>
                        <?php // Go through all the fieldsets except the configuration and basic-limited, which are
                        // handled separately below. ?>


                        <?php if ($name != 'editorConfig' && $name != 'basic-limited') :?>
                            <?php //echo JHtml::_('sliders.panel', JText::_($fieldSet->label), $name.'-options'); ?>
                            <?php echo JHtml::_('bootstrap.addSlide', 'articleOptions', JText::_($fieldSet->label), 'collapse' . $i++); ?>
                            <?php if (isset($fieldSet->description) && trim($fieldSet->description)) : ?>
                                <p class="tip"><?php echo $this->escape(JText::_($fieldSet->description));?></p>
                            <?php endif; ?>
                            <fieldset>
                                <?php foreach ($this->form->getFieldset($name) as $field){
                                    echo $field -> renderField();
                                } ?>
                            </fieldset>
                            <?php echo JHtml::_('bootstrap.endSlide');?>
                        <?php endif ?>
                        <?php // If we are not showing the options we need to use the hidden fields so the values are not lost.  ?>
                    <?php  elseif ($name == 'basic-limited'): ?>
                        <?php foreach ($this->form->getFieldset('basic-limited') as $field) : ?>
                            <?php  echo $field->input; ?>
                        <?php endforeach; ?>

                    <?php endif; ?>
                <?php endforeach; ?>

                <?php // The url and images fields only show if the configuration is set to allow them.  ?>
                <?php // This is for legacy reasons. ?>

                <?php echo JHtml::_('bootstrap.addSlide', 'articleOptions', JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'), 'meta-options' ); ?>
                <fieldset class="panelform">
                    <?php echo $this->loadTemplate('metadata'); ?>
                </fieldset>
                <?php echo JHtml::_('bootstrap.endSlide');?>
                <?php echo JHtml::_('bootstrap.endAccordion');?>

            </div>
        <?php echo JHtml::_('tzbootstrap.endrow');?>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="return" value="<?php echo JFactory::getApplication() -> input -> getCmd('return');?>" />
        <input type="hidden" name="contentid" id="contentid" value="<?php echo JFactory::getApplication() -> input -> getCmd('id');?>">
        <?php echo JHtml::_('form.token'); ?>
    </form>