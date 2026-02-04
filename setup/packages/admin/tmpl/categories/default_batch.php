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

$options = array(
	HTMLHelper::_('select.option', 'c', Text::_('JLIB_HTML_BATCH_COPY')),
	HTMLHelper::_('select.option', 'm', Text::_('JLIB_HTML_BATCH_MOVE'))
);
$published	= $this->state->get('filter.published');
$extension	= $this->escape($this->state->get('filter.extension'));
?>
<div class="modal hide fade" id="collapseModal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal">x</button>
		<h3><?php echo Text::_('COM_TZ_PORTFOLIO_CATEGORIES_BATCH_OPTIONS');?></h3>
	</div>
	<div class="modal-body">
		<p><?php echo Text::_('COM_TZ_PORTFOLIO_CATEGORIES_BATCH_TIP'); ?></p>
		<div class="control-group">
			<div class="controls">
                <?php echo LayoutHelper::render('joomla.html.batch.access', array()); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
                <?php echo LayoutHelper::render('joomla.html.batch.language', array()); ?>
			</div>
		</div>
		<?php if ($published >= 0) : ?>
			<div class="control-group">
				<label id="batch-choose-action-lbl" for="batch-category-id" class="control-label">
					<?php echo Text::_('COM_TZ_PORTFOLIO_CATEGORIES_BATCH_CATEGORY_LABEL'); ?>
				</label>
				<div id="batch-choose-action" class="combo controls">
					<select name="batch[category_id]" class="inputbox" id="batch-category-id">
						<option value=""><?php echo Text::_('JSELECT') ?></option>
						<?php echo HTMLHelper::_('select.options', HTMLHelper::_('tzcategory.categories', $extension, array('filter.published' => $published)));?>
					</select>
				</div>
			</div>
			<div class="control-group radio">
				<?php echo HTMLHelper::_('select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm'); ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="modal-footer">
		<button class="btn" type="button" onclick="document.id('batch-category-id').value='';document.id('batch-access').value='';document.id('batch-language-id').value=''" data-dismiss="modal" data-bs-dismiss="modal">
			<?php echo Text::_('JCANCEL'); ?>
		</button>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('category.batch');">
			<?php echo Text::_('JGLOBAL_BATCH_PROCESS'); ?>
		</button>
	</div>
</div>
