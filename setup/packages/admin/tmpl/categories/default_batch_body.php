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

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Multilanguage;

$options = array(
	HTMLHelper::_('select.option', 'c', Text::_('JLIB_HTML_BATCH_COPY')),
	HTMLHelper::_('select.option', 'm', Text::_('JLIB_HTML_BATCH_MOVE'))
);
$published  = (int) $this->state->get('filter.published');
$extension  = $this->escape($this->state->get('filter.extension'));
$addRoot    = true;

?>

<div class="p-3">

    <?php echo HTMLHelper::_('tzbootstrap.addrow');?>
        <?php if (Multilanguage::isEnabled()){ ?>
            <div class="form-group col-md-6">
                <div class="controls">
                    <?php echo LayoutHelper::render('joomla.html.batch.language', []); ?>
                </div>
            </div>
        <?php } ?>
		<div class="form-group col-md-6">
			<div class="controls">
                <?php echo LayoutHelper::render('joomla.html.batch.access', array()); ?>
			</div>
		</div>
    <?php echo HTMLHelper::_('tzbootstrap.endrow');?>
    <?php echo HTMLHelper::_('tzbootstrap.addrow');?>
		<?php if ($published >= 0){ ?>
            <label id="batch-choose-action-lbl" for="batch-category-id">
                <?php echo Text::_('JLIB_HTML_BATCH_MENU_LABEL'); ?>
            </label>
            <div id="batch-choose-action" class="control-group">
                <select name="batch[category_id]" class="form-select" id="batch-category-id">
                    <option value=""><?php echo Text::_('JLIB_HTML_BATCH_NO_CATEGORY'); ?></option>
                    <?php if (isset($addRoot) && $addRoot) : ?>
                        <?php echo HTMLHelper::_('select.options', HTMLHelper::_('tzcategory.categories', $extension)); ?>
                    <?php else : ?>
                        <?php echo HTMLHelper::_('select.options', HTMLHelper::_('tzcategory.options', $extension)); ?>
                    <?php endif; ?>
                </select>
            </div>
            <div id="batch-copy-move" class="control-group radio">
                <fieldset id="batch-copy-move-id">
                    <legend>
                        <?php echo Text::_('JLIB_HTML_BATCH_MOVE_QUESTION'); ?>
                    </legend>
                    <?php echo HTMLHelper::_('select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm'); ?>
                </fieldset>
            </div>
<!--        <div class="span12 col-md-12">-->
<!--            <div class="form-group">-->
<!--                <label id="batch-choose-action-lbl" for="batch-category-id" class="control-label">-->
<!--                    --><?php //echo Text::_('COM_TZ_PORTFOLIO_CATEGORIES_BATCH_CATEGORY_LABEL'); ?>
<!--                </label>-->
<!--                <div id="batch-choose-action" class="combo controls">-->
<!--                    <select name="batch[category_id]" class="inputbox" id="batch-category-id">-->
<!--                        <option value="">--><?php //echo Text::_('JSELECT') ?><!--</option>-->
<!--                        --><?php //echo HTMLHelper::_('select.options', HTMLHelper::_('tzcategory.categories',
//                            $extension, array('filter.published' => $published)));?>
<!--                    </select>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div id="batch-copy-move" class="form-group radio">-->
<!--                --><?php //echo Text::_('JLIB_HTML_BATCH_MOVE_QUESTION'); ?>
<!--                --><?php //echo HTMLHelper::_('select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm'); ?>
<!--            </div>-->
<!--        </div>-->
		<?php } ?>
    <?php echo HTMLHelper::_('tzbootstrap.endrow');?>
</div>
<div class="btn-toolbar p-3">
    <joomla-toolbar-button task="category.batch" class="ms-auto">
        <button type="button" class="btn btn-success"><?php echo Text::_('JGLOBAL_BATCH_PROCESS'); ?></button>
    </joomla-toolbar-button>
</div>

