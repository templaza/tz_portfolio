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
JHtml::_('formbehavior.chosen', 'select');
$doc    = JFactory::getDocument();
$doc -> addscript(TZ_Portfolio_PlusUri::base(true, true).'/js/tz-chosen.min.js');
$doc -> addScript(TZ_Portfolio_PlusUri::base(true, true).'/js/jquery-ui.min.js');
$doc -> addStyleSheet(TZ_Portfolio_PlusUri::base(true, true).'/css/jquery-ui.min.css');
$doc -> addStyleSheet(TZ_Portfolio_PlusUri::base(true, true).'/css/tz_portfolio_plus.min.css');

if(!$this -> tagsSuggest){
    $this -> tagsSuggest    = 'null';
}
$doc -> addScriptDeclaration('
    jQuery(document).ready(function(){
        jQuery(".suggest").tzChosen({ source: '.$this -> tagsSuggest.', sourceEdit: '.$this -> listsTags.',keys: ["\,","/"]});
    })
');


// Create shortcut to parameters.
$params = $this->state->get('params');

//
$params = $params->toArray();

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

$type       = '';
$mediavalue = '';
$media      = array();
$list       = $this -> listEdit;

if($list){
    $type   = $list -> type;
}

$pluginsTab = $this -> pluginsTab;

$assoc  = false;
$assoc = JLanguageAssociations::isEnabled();
?>

    <script type="text/javascript">
    (function($){
        Joomla.submitbutton = function(task) {
            if (task == 'article.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
                <?php echo $this->form->getField('articletext')->save(); ?>
                Joomla.submitform(task, document.getElementById('item-form'));
            }
        }
    })(jQuery);
    </script>


    <form action="<?php echo JRoute::_('index.php?option=com_tz_portfolio_plus&view=article&layout=edit&id='.(int) $this->item->id); ?>"
          method="post"
          name="adminForm"
          id="item-form"
          class="form-validate"
          enctype="multipart/form-data">
        <div class="span8 form-horizontal">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#general" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ARTICLE_DETAILS');?></a></li>
                <?php if($assoc):?>
                    <li><a href="#associations" data-toggle="tab"><?php echo JText::_('Associations');?></a></li>
                <?php endif;?>
                <li><a href="#permissions" data-toggle="tab"><?php echo JText::_('JCONFIG_PERMISSIONS_LABEL');?></a></li>

            </ul>
            <div class="tab-content">
                <!-- Begin Tabs -->
                <div class="tab-pane active" id="general">
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('title'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('title'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('alias'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('alias'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <label><?php echo $this->form->getLabel('tags');?></label>
                                </div>
                                <div class="controls">
                                    <?php echo $this -> form -> getInput('tags');?>
                                    <div><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_FORM_TAGS_DESC');?></div>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('state'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('state'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('access'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('access'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('id'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('id'); ?>
                                </div>
                            </div>

                        </div>
                        <div class="span6">
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('catid'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('catid'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('second_catid'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('second_catid'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('groupid'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('groupid'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('type'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('type'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('featured'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('featured'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('language'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('language'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('template_id'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('template_id'); ?>
                                </div>
                            </div>
                        </div>


                    </div>
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tz_content" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_TAB_CONTENT');?></a></li>
                        <?php echo $this -> loadTemplate('plugin_title_tab');?>
                        <li><a href="#tztabsFields" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_TAB_FIELDS');?></a></li>
                    </ul>
                    <!-- Begin Content -->
                    <div class="tab-content">
                        <!-- Begin Tabs -->
                        <div class="tab-pane active" id="tz_content">
                            <?php echo $this->form->getInput('articletext'); ?>
                        </div>

                        <?php echo $this -> loadTemplate('plugin_content_tab');?>
                        <div class="tab-pane" id="tztabsFields">
                            <?php echo $this -> loadTemplate('extrafields');?>
                        </div>
                        <!-- End Tabs -->
                    </div>
                    <!-- End Content -->
                </div>
                <?php if ($assoc) : ?>
                    <div class="tab-pane" id="associations">
                        <?php echo $this->loadTemplate('associations'); ?>

                    </div>
                <?php endif; ?>
                <div class="tab-pane" id="permissions">
                    <?php echo $this->form->getInput('rules'); ?>
                </div>
                <!-- End Tabs -->
            </div>

        </div>
        <div class="span4 form-vertical">
            <?php echo JHtml::_('bootstrap.startAccordion', 'articleOptions', array('active' => 'collapse0'
            , 'parent' => true));?>

            <?php // Do not show the publishing options if the edit form is configured not to. ?>
            <?php  if ($params['show_publishing_options'] || ( $params['show_publishing_options'] = '' && !empty($editoroptions)) ): ?>
                <?php echo JHtml::_('bootstrap.addSlide', 'articleOptions', JText::_('JGLOBAL_FIELDSET_PUBLISHING'), 'collapse0'); ?>
                <fieldset>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
                    </div>

                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('created_by_alias'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('created_by_alias'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('created'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('created'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('publish_up'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('publish_up'); ?></div>
                    </div>

                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('publish_down'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('publish_down'); ?></div>
                    </div>

                    <?php if ($this->item->modified_by) : ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('modified_by'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('modified_by'); ?></div>
                        </div>

                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('modified'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('modified'); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->item->version) : ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('version'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('version'); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->item->hits) : ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('hits'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('hits'); ?></div>
                        </div>
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
        <div>
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="return" value="<?php echo JFactory::getApplication() -> input -> getCmd('return');?>" />
            <input type="hidden" name="contentid" id="contentid" value="<?php echo JFactory::getApplication() -> input -> getCmd('id');?>">
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </form>