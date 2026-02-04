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

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

extract($displayData);

$allowEdit  = false;

if(isset($field) && $field) {
    if($edit = $field -> getAttribute('edit')) {
        $allowEdit  = $edit;
    }
}

$doc    = Factory::getDocument();

/* @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa     = $doc->getWebAssetManager();
?>

<?php
$function   = 'tppSelectArticle_'.$id;
$modalId    = 'tppModalArticle_' . $id;
// Render the modal
echo HTMLHelper::_(
    'bootstrap.renderModal',
    $modalId,
    array(
        'url'        => $link.'&function='.$function,
        'title'      => Text::_('COM_TZ_PORTFOLIO_CHANGE_ARTICLE'),
        'width'      => '400px',
        'height'     => '800px',
        'modalWidth' => '70',
        'bodyHeight' => '70',
        'closeButton' => true,
        'footer'      => '<a class="btn" data-dismiss="modal" data-bs-dismiss="modal" aria-hidden="true">' . Text::_('JCANCEL') . '</a>',
    )
);
if(strpos('form-control', $class) == false){
    $class  .= ' form-control';
}
if(strpos('class=', $class) == false){
    $class  = 'class="'.$class.'"';
}
?>
<div class="input-append input-group">
    <input type="text" <?php echo $required; ?> readonly="readonly" id="<?php echo $id; ?>_name" value="<?php
            echo $title; ?>" <?php echo (!empty($size)?$size:''). $class; ?>  placeholder="<?php echo Text::_('COM_TZ_PORTFOLIO_SELECT_AN_ARTICLE');?>" />
    <a id="<?php echo $id; ?>_select" class="btn btn-primary hasTooltip" title="<?php echo Text::_('COM_TZ_PORTFOLIO_CHANGE_ARTICLE');
        ?>" data-toggle="modal" data-bs-toggle="modal" href="#<?php echo $modalId;?>"><i class="icon-file me-1"></i><?php
        echo Text::_('JSELECT');?></a>
    <?php if($allowEdit){?>
        <a id="<?php echo $id; ?>_edit" class="btn<?php echo $value ? '' : ' hidden';?>" target="_blank"
           href="index.php?option=com_tz_portfolio&task=article.edit&id=<?php
           echo $value; ?>"><span class="icon-edit"></span><?php echo Text::_('JACTION_EDIT'); ?></a>
    <?php } ?>
    <a href="javascript:" id="<?php echo $id; ?>_clear" class="btn btn-danger<?php echo $value ? '' : ' hidden';?>" onclick="return tppClearArticle('<?php
    echo $id; ?>')"><span class="icon-remove"></span> <?php echo Text::_('JCLEAR'); ?></a>
</div>

    <input class="input-small" id="<?php echo $id; ?>" type="hidden" name="<?php echo $name; ?>" value="<?php
    echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8') ?>"/>
<?php
//$doc    = Factory::getApplication() -> getDocument();
$wa -> addInlineScript('
    (function($, window){
        "use strict";
        window.tppClearArticle = function(id) {
            $("#" + id + "_name").val("");
            $("#" + id ).val("");
            $("#" + id + "_clear").addClass("hidden");
            $("#" + id + "_edit").addClass("hidden");
            $("#" + id + "_select").removeClass("hidden");
                '.($submitform?'$("#'.$id.'").parents("form").first().submit()':'').'
            return false;
        };
        window.'.$function.' = function(id, title, category){
            if(id.length){
                var fieldId = "'.$id.'";
                $("#" + fieldId).val(id);
                $("#" + fieldId + "_name").val(title);
                $("#'.$modalId.'").modal("hide");
                $("#" + fieldId + "_clear").removeClass("hidden");
                '.($allowEdit?'
                $("#" + fieldId + "_edit").removeClass("hidden")
                    .attr("href",function(index, href){
                        return "index.php?option=com_tz_portfolio&task=article.edit&id="+id;
                    });
                $("#" + fieldId + "_select").addClass("hidden");':'')
                .($submitform?'$("#'.$id.'").parents("form").first().submit()':'').'
            }
        };
    })(jQuery, window);', ['position' => 'after'], [], ['jQuery']);