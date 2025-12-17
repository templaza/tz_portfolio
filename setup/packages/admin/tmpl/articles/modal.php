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

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Layout\LayoutHelper;

$app    = Factory::getApplication();

if ($app->isClient('site')) {
    Session::checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
}

$function	= $app->input->getCmd('function', 'tppSelectArticle');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$isMultiple = $app -> input -> get('ismultiple', false, 'boolean');

$doc    = Factory::getApplication() -> getDocument();
$doc -> addScriptDeclaration('
function tzGetDatas(){
    if (window.parent){
        var j= 0,titles  = new Array(),ids = new Array(),categories = new Array();
        if(document.getElementsByName("cid[]").length){
            var idElems  = document.getElementsByName("cid[]"),
                titleElems  = document.getElementsByName("tztitles[]"),
                categoryElems  = document.getElementsByName("tzcategories[]");
            for(var i = 0; i<idElems.length; i++){
                if(idElems[i].checked){
                    ids[j]  = idElems[i].value;
                    titles[j]  = titleElems[i].value;
                    categories[j]  = categoryElems[i].value;
                    j++;
                }
            }
        }
        window.parent.'.$this->escape($function).'(ids,titles,categories);
    }
}');
?>

<form action="<?php echo Route::_('index.php?option=com_tz_portfolio_plus&view=articles&layout=modal&tmpl=component&function='
    .$function.($isMultiple?'&ismultiple=true':'').'&'.Session::getFormToken().'=1');?>"
      method="post" name="adminForm" id="adminForm" class="tpContainer">
    <?php if($isMultiple){?>
    <div class="btn-toolbar">
        <button type="button" class="btn btn-primary" onclick="tzGetDatas();">
            <i class="icon-checkmark"></i> <?php echo Text::_('COM_TZ_PORTFOLIO_INSERT');?></button>
        <hr class="hr-condensed" />
    </div>
    <?php } ?>

    <?php
    // Search tools bar
    echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    ?>

    <?php if (empty($this->items)){ ?>
        <div class="alert alert-no-items">
            <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
        </div>
    <?php }else{ ?>

    <table class="table table-sm">
        <caption class="visually-hidden">
            <?php echo Text::_('COM_TZ_PORTFOLIO_ARTICLES'); ?>,
            <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
            <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
        </caption>
        <thead>
        <tr>
            <?php if($isMultiple){?>
            <th class="w-1">
                <?php echo HTMLHelper::_('grid.checkall'); ?>
            </th>
            <?php } ?>
            <th class="title">
                <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
            </th>
            <th class="w-6">
                <?php echo HTMLHelper::_('searchtools.sort', 'COM_TZ_PORTFOLIO_TYPE_OF_MEDIA', 'groupname', $listDirn, $listOrder); ?>
            </th>
            <th class="w-15">
                <?php echo HTMLHelper::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
            </th>
            <th class="w-15">
                <?php echo HTMLHelper::_('searchtools.sort', 'JCATEGORY', 'a.catid', $listDirn, $listOrder); ?>
            </th>
            <th class="w-5">
                <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
            </th>
            <th class="w-5">
                <?php echo HTMLHelper::_('searchtools.sort',  'JDATE', 'a.created', $listDirn, $listOrder); ?>
            </th>
            <th class="w-1">
                <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
            </th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan="8">
                <?php echo $this->pagination->getListFooter(); ?>
            </td>
        </tr>
        </tfoot>
        <tbody>
        <?php foreach ($this->items as $i => $item) :?>
            <tr class="row<?php echo $i % 2; ?>">
                <?php if($isMultiple){?>
                <td class="center text-center">
                    <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                </td>
                <?php } ?>
                <td>
                    <a style="cursor: pointer;" class="pointer"
                       onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>(['<?php echo $item->id; ?>'], ['<?php echo $this->escape(addslashes($item->title)); ?>'],['<?php echo $this->escape(addslashes($item->category_title)); ?>']);">
                        <?php echo $this->escape($item->title); ?></a>
                    <input type="hidden" name="tztitles[]" value="<?php echo $this->escape(addslashes($item->title));?>">
                </td>
                <td class="small d-none d-md-table-cell">
                    <?php echo $item -> type;?>
                </td>
                <td class="center text-center small">
                    <?php echo $this->escape($item->access_level); ?>
                </td>
                <td class="small">
                    <?php echo $this->escape($item->category_title); ?>
                    <?php if(isset($item -> categories) && $item -> categories && count($item -> categories)):?>
                        <?php
                        echo ',';
                        foreach($item -> categories as $i => $category):
                            ?>
                            <?php echo $this->escape($category->title); ?>
                            <?php
                            if($i < count($item -> categories) - 1){
                                echo ',';
                            }
                            ?>
                        <?php endforeach;?>
                    <?php endif;?>
                    <input type="hidden" name="tzcategories[]" value="<?php echo $this->escape(addslashes($item->category_title));?>">
                </td>
                <td class="center text-center small">
                    <?php if ($item->language=='*'):?>
                        <?php echo Text::alt('JALL', 'language'); ?>
                    <?php else:?>
                        <?php echo $item->language_title ? $this->escape($item->language_title) : Text::_('JUNDEFINED'); ?>
                    <?php endif;?>
                </td>
                <td class="center text-center small nowrap">
                    <?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC4')); ?>
                </td>
                <td class="center text-center small">
                    <?php echo (int) $item->id; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php } ?>

    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo HTMLHelper::_('form.token'); ?>
    </div>
</form>