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

// No direct access
defined('_JEXEC') or die('Restricted access');

$group  = 'presets';

$doc    = JFactory::getDocument();
$doc -> addScriptDeclaration('
    (function($){
        $(document).ready(function(){
            $(".load-preset").click(function(e){
                e.stopPropagation();
                e.preventDefault();
                $("#loadPreset").modal("toggle");
                var $thisPreset = $(this);
                $("#loadPresetAccept").click(function(e){
                    $("#jform_preset").val($thisPreset.attr("data-preset"));
                    Joomla.submitbutton("template_style.loadpreset");
                });
            });
            $(".removepreset").click(function(e){
                e.stopPropagation();
                e.preventDefault();
                $("#removePreset").modal("toggle");
                var $thisPreset = $(this);
                $("#removePresetAccept").click(function(e){
                    $("#jform_preset").val($thisPreset.attr("data-preset"));
                    Joomla.submitbutton("template_style.removepreset");
                });
            });
        });
    })(jQuery);
');
?>
<div class="row-fluid">
    <div class="span6">
        <div class="control-group">
            <div class="control-label"><?php echo $this -> form -> getLabel('name',$group);?></div>
            <div class="controls"><?php echo $this -> form -> getInput('name',$group);?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this -> form -> getLabel('image',$group);?></div>
            <div class="controls"><?php echo $this -> form -> getInput('image',$group);?></div>
        </div>
    </div>
    <div class="span6">
        <div class="control-group">
            <div class="control-label"><?php echo $this -> form -> getLabel('demo_link',$group);?></div>
            <div class="controls"><?php echo $this -> form -> getInput('demo_link',$group);?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this -> form -> getLabel('doc_link',$group);?></div>
            <div class="controls"><?php echo $this -> form -> getInput('doc_link',$group);?></div>
        </div>
    </div>
</div>
<?php
if($presets = $this -> presets):
?>
<div class="presets">
    <div class="alert alert-warning">
        <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LOAD_PRESET_DESCRIPTION');?>
    </div>
    <?php foreach($presets as $preset){?>
        <div class="preset<?php echo (isset($this -> item -> preset)
            && ($this -> item -> preset == $preset -> name))?' active':'';?>">
            <div class="preset-screenshot<?php echo (!isset($preset -> image) || (isset($preset -> image) && !$preset -> image))?' background':''; ?>">
                <?php if(isset($preset -> image) && $preset -> image){?>
                <img src="<?php echo TZ_Portfolio_PlusUri::root().'/'.$preset -> image;?>" alt="<?php echo $preset -> name;?>">
                <?php }else{?>
                    <span><?php echo '287 x 220';?></span>
                <?php }?>
                <div class="preset-bar">
                    <div class="preset-bar-table">
                        <div class="preset-bar-table-cell">
                            <span data-preset="<?php echo $preset -> name;?>" data-target="#loadPreset" data-toggle="modal"
                                  class="load-preset btn btn-warning btn-small"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LOAD_PRESET');?></span>
                            <?php if(isset($preset -> doc_link) && $preset -> doc_link){ ?>
                                <a target="_blank" class="btn btn-primary btn-small"
                                   href="<?php echo $preset -> doc_link;?>"><?php echo JText::_('JTOOLBAR_HELP');?></a>
                            <?php }
                            if(isset($preset -> demo_link) && $preset -> demo_link){
                                ?>
                                <a target="_blank" class="btn btn-success btn-small"
                                   href="<?php echo $preset -> demo_link;?>"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LIVE_PREVIEW');?></a>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>
            <h3 class="preset-name hasTooltip" data-placement="bottom" title="<?php echo $preset -> name;?>"><?php echo $preset -> name;?></h3>
            <i data-preset="<?php echo $preset -> name;?>" data-target="#removePreset" data-toggle="modal"
               title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_REMOVE_PRESET_DESCRIPTION');?>"
               class="fa fa-times removepreset hasTooltip"></i>
        </div>
    <?php }?>
</div>
<?php

echo $this -> form -> getInput('preset');

echo $this -> loadTemplate('presets_load_modal');
echo $this -> loadTemplate('presets_remove_modal');
?>
<input type="hidden" name="return" value="<?php echo base64_encode(JUri::current());?>"/>

<?php
endif;
