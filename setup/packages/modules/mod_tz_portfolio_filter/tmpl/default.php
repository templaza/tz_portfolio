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

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

//$bootstrap4 = ($params -> get('enable_bootstrap',0) && $params -> get('bootstrapversion', 3) == 4);

//$doc    = Factory::getDocument();
//if($params -> get('enable_bootstrap',0)){
//    $doc -> addScriptDeclaration('
//        (function($){
//            $(document).off(\'click.modal.data-api\')
//            .on(\'click.modal.data-api\', \'[data-toggle="modal"]\', function (e) {
//                var $this = $(this)
//                  , href = $this.attr(\'href\')
//                  , $target = $($this.attr(\'data-target\') || (href && href.replace(/.*(?=#[^\s]+$)/, \'\'))) //strip for ie7
//                  , option = $target.data(\'modal\') ? \'toggle\' : $.extend({ remote:!/#/.test(href) && href }, $target.data(), $this.data())
//
//                e.preventDefault();
//
//                $target
//                  .modal(option)
//                  .one(\'hide\', function () {
//                    $this.focus()
//                  });
//              });
//        })(jQuery);
//    ');
//}
//$doc -> addStyleSheet(Uri::base(true).'/modules/mod_tz_portfolio_filter/css/style.css');

$lang               = Factory::getApplication() -> getLanguage();
$upper_limit        = $lang->getUpperLimitSearchWord();
$width              = (int) $params->get('width');
$maxlength          = $upper_limit;
$button             = $params->get('button', 0);
$imagebutton        = $params->get('imagebutton', 0);
$button_pos         = $params->get('button_pos', 'left');
$button_text        = htmlspecialchars($params->get('button_text',
    Text::_('MOD_TZ_PORTFOLIO_FILTER_SEARCHBUTTON_TEXT')), ENT_COMPAT, 'UTF-8');
$text               = htmlspecialchars($params->get('text', Text::_('MOD_TZ_PORTFOLIO_FILTER_SEARCHBOX_TEXT')),
    ENT_COMPAT, 'UTF-8');
$label              = htmlspecialchars($params->get('label',
    Text::_('MOD_TZ_PORTFOLIO_FILTER_LABEL_TEXT')), ENT_COMPAT, 'UTF-8');

$set_Itemid         = (int) $params->get('set_itemid', 0);
$moduleclass_sfx    = htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_COMPAT, 'UTF-8');
$mitemid            = $set_Itemid > 0 ? $set_Itemid : $app->input->get('Itemid');

if ($width)
{
    $moduleclass_sfx .= ' ' . 'mod_search' . $module->id;
    $css = 'div.mod_search' . $module->id . ' input[type="search"]{ width:auto; }';
    Factory::getDocument()->addStyleDeclaration($css);
    $width = ' size="' . $width . '"';
}
else
{
    $width = '';
}

$input  = Factory::getApplication() -> input;
$column     =   [];
$column[]   =   ($params -> get('column_lg', 1) && intval($params -> get('column_lg', 1))) ? 'uk-width-1-' . $params -> get('column_lg', 1).'@xl' : '';
$column[]   =   ($params -> get('column_md', 1) && intval($params -> get('column_md', 1))) ? 'uk-width-1-' . $params -> get('column_md', 1) : 'col-md';
$column[]   =   ($params -> get('column_sm', 1) && intval($params -> get('column_sm', 1))) ? 'uk-width-1-' . $params -> get('column_sm', 1) : 'col-sm';
$column[]   =   ($params -> get('column', 1) && intval($params -> get('column', 1))) ? 'uk-width-1-' . $params -> get('column', 1) : 'col';
$gutter     =   $params -> get('gutter', '') ? ' '. $params -> get('gutter', '') : '';
//$column[]   =   ($params -> get('column_lg', 1) && intval($params -> get('column_lg', 1))) ? 'uk-width-' . 12/$params -> get('column_lg', 1).'@xl' : '';
//$column[]   =   ($params -> get('column_md', 1) && intval($params -> get('column_md', 1))) ? 'uk-width-' . 12/$params -> get('column_md', 1) : 'col-md';
//$column[]   =   ($params -> get('column_sm', 1) && intval($params -> get('column_sm', 1))) ? 'uk-width-' . 12/$params -> get('column_sm', 1) : 'col-sm';
//$column[]   =   ($params -> get('column', 1) && intval($params -> get('column', 1))) ? 'uk-width-' . 12/$params -> get('column', 1) : 'col';
//$gutter     =   $params -> get('gutter', '') ? ' '. $params -> get('gutter', '') : '';
$button_width   =   $params -> get('button_width', '') ? ' style="width:'. $params -> get('button_width', '').'px;"' : '';
?>
<div class="tz-filter<?php echo $moduleclass_sfx ?>">
    <form action="<?php echo Route::_('index.php?option=com_tz_portfolio&view=search&Itemid='.$mitemid);?>"
          method="post" class="uk-form-stacked">
        <div class="uk-grid-small" data-uk-grid>
        <?php if($params -> get('show_search_word', 1)) { ?>
            <div class="<?php echo implode(' ', $column); ?>">
                <div class="uk-margin">
                    <?php if($params -> get('show_box_label', 1)){?>
                        <label for="mod_tz_portfolio_filter-searchword" class="uk-form-label"><?php echo $label ?></label>
                    <?php }?>
                    <div class="uk-form-controls">
                        <input type="search" name="searchword" id="mod_tz_portfolio_filter-searchword"
                               maxlength="<?php echo $maxlength; ?>"
                               class="uk-input" placeholder="<?php echo $text;?>"
                               value="<?php echo ($input->get('option') == 'com_tz_portfolio') ? $input->getString('searchword') : ''; ?>"/>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
        <?php if($params -> get('show_category',0)) { ?>
            <div class="<?php echo implode(' ', $column); ?>">
                <div>
                    <?php if($params -> get('show_category_text', 1)){ ?>
                        <label class="uk-form-label" for="catid"><?php echo ($text = $params -> get('category_text'))?$text:Text::_('MOD_TZ_PORTFOLIO_FILTER_CATEGORY');?></label>
                    <?php } ?>
                    <div class="uk-form-controls">
                        <select name="id" class="uk-select" id="catid">
                            <?php echo HTMLHelper::_('select.options', $categoryOptions, 'value', 'text',
                                (($input -> get('option') == 'com_tz_portfolio')?$input -> get('id'):'')); ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php
            }
        if($advfilter && $params -> get('show_fields', 1)){
            if ($params -> get('show_group_title', 0)) echo '<div class="col-12">';
            require JModuleHelper::getLayoutPath('mod_tz_portfolio_filter', 'default_filter');
            if ($params -> get('show_group_title', 0)) echo '</div>';
        }
        ?>
            <div class="<?php echo $params -> get('search_inline', 0) ? 'uk-width-auto' : 'uk-width-expand'; ?>">
                <?php if($params -> get('button', 1)){?>
                <div<?php echo $button_width; ?>>
                    <button class="uk-button uk-width-1-1 <?php echo $params -> get('button_style', 'btn-primary'); ?>">
                        <?php
                        $btn_output = null;
                        if($imagebutton){
                            if($icon = $params -> get('icon')) {
                                $btn_output = '<img src="'.$icon.'" alt="'.$button_text.'"/>';
                            }elseif($iconClass = $params -> get('icon_class')){
                                $btn_output = '<i class="' .$iconClass.'"></i>';
                            }
                        }
                        echo $btn_output.$button_text;
                        ?>
                    </button>
                </div>
                <?php } ?>
            </div>
        </div>
        <input type="hidden" name="option" value="com_tz_portfolio"/>
        <input type="hidden" name="task" value="search.search"/>
        <input type="hidden" name="Itemid" value="<?php echo $mitemid;?>"/>
    </form>
</div>
