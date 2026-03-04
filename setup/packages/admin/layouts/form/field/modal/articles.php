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

$doc    	= Factory::getDocument();

/* @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $doc->getWebAssetManager();

$wa -> useScript('jquery');
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
        'title'      => Text::_('COM_TZ_PORTFOLIO_CHANGE_ARTICLES'),
        'width'      => '400px',
        'height'     => '800px',
        'modalWidth' => '70',
        'bodyHeight' => '70',
        'closeButton' => true,
        'footer'      => '<a class="btn" data-dismiss="modal" data-bs-dismiss="modal" aria-hidden="true">' . Text::_('JCANCEL') . '</a>',
    )
);
?>
<div class="btn-group control-group">
    <a class="btn btn-primary hasTooltip" title="<?php echo Text::_('COM_TZ_PORTFOLIO_CHANGE_ARTICLES');
        ?>" data-toggle="modal" data-bs-toggle="modal" href="#tppModalArticle_<?php echo $id;?>"><i class="icon-copy"></i> <?php
        echo Text::_('JSELECT');?></a>
    <a href="javascript:" id="<?php echo $id; ?>_clear" class="btn btn-danger<?php echo $value ? '' : ' disabled';?>" onclick="return tppClearArticles('<?php
    echo $id; ?>')"><span class="icon-remove"></span> <?php echo Text::_('JCLEAR'); ?></a>
</div>
<div style="max-height: 330px; overflow-y: auto;">
    <table id="<?php echo $id.'_table';?>" class="table table-striped">
        <thead>
        <tr>
            <th><?php echo Text::_('JGLOBAL_TITLE');?></th>
            <th><?php echo Text::_('JCATEGORY');?></th>
            <th class="w-5 text-center"><?php echo Text::_('JSTATUS');?></th>
            <th class="w-5"><?php echo Text::_('JGRID_HEADING_ID');?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(isset($items) && $items){
            foreach($items as $item) {
                ?>
                <tr>
                    <td><?php echo $item -> title; ?></td>
                    <td><?php echo $item -> category_title; ?></td>
                    <td class="center">
                        <?php if ($allowEdit) { ?>
                        <div class="btn-group">
                            <a class="btn btn-secondary btn-small btn-sm hasTooltip" target="_blank" title="<?php echo Text::_('JACTION_EDIT'); ?>"
                               href="index.php?option=com_tz_portfolio&task=article.edit&id=<?php
                               echo $item->id; ?>"><span class="icon-edit"></span></a>
                        <?php } ?>
                        <a href="javascript:" class="btn btn-danger btn-small btn-sm hasTooltip" title="<?php echo Text::_('JTOOLBAR_REMOVE'); ?>"
                           onclick="tppClearArticle(this);"><i class="icon-remove"></i></a>
                        <?php if ($allowEdit) { ?>
                        </div>
                        <?php } ?>
                    </td>
                    <td>
                        <?php echo $item->id; ?>
                        <input type="hidden" name="<?php echo $name; ?>"
                               value="<?php echo $item->id; ?>">
                    </td>
                </tr>
                <?php
                }
            }?>
        </tbody>
    </table>
</div>

<?php
$doc    = Factory::getApplication() -> getDocument();
$doc -> addScriptDeclaration('
    (function($, window){
        "use strict";
        window.tppClearArticles = function(id) {
            $("#" + id + "_table tbody").html("");
            $("#" + id + "_clear").addClass("disabled");
            return false;
        };

        window.tppClearArticle = function(obj){
            if(typeof $.fn.tooltip !== "undefined"){
                $(obj).tooltip("hide");
            }
            $(obj).parents("tr").first().remove();
        };
        window.'.$function.' = function(ids, titles, categories){
            if(ids.length){
                var fieldId = "'.$id.'",
                    html = $("<div/>");
                for(var i = 0; i < ids.length; i++){
                    var tr    = $("<tr/>");
                    tr.html("<td>" + titles[i]
                        + "</td>"
                        + "<td>" + categories[i]+ "</td>"
                        + "<td>"
                        '.($allowEdit?'
                        + "<div class=\"btn-group\">"
                        + "<a class=\"btn btn-secondary btn-small btn-sm hasTooltip\" target=\"_blank\" title=\"'
                            .Text::_('JACTION_EDIT').'\""
                         +"  href=\"index.php?option=com_tz_portfolio'
                            .'&task=article.edit&id="+ ids[i] +"\"><span"
                         +" class=\"icon-edit\"></span></a>"
                        ':'').'
                        + "<a href=\"javascript:\" class=\"btn btn-danger btn-small btn-sm hasTooltip\" title=\"'.Text::_('JTOOLBAR_REMOVE').'\""
                        + "  onclick=\"tppClearArticle(this);\"><i class=\"icon-remove\"></i></a>"
                       '.($allowEdit?'+ "</div>"':'').'
                        +"</td>"
                        + "<td>" + ids[i]
                        + "<input type=\"hidden\" name=\"'.$name.'\" value=\""+ ids[i] +"\"/>"
                        + "</td>");
                        if(!$("#" + fieldId + "_table tbody input[value=\""+ ids[i] + "\"]").length){
                            html.append(tr);
                        }
                }
                $("#'.$modalId.'").modal("hide");
                $("#" + fieldId + "_table tbody").prepend(html.html());
                
                if(typeof $.fn.tooltip !== "undefined"){
                    $("#" + fieldId + "_table tbody").find(".hasTooltip").tooltip({"html": true,"container": "body"});
                }
                $("#" + fieldId + "_clear").removeClass("disabled");
            }
        };
    })(jQuery, window);');