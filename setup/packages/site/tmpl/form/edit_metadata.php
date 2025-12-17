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
?>
<div class="control-group">
	<div class="control-label"><?php echo $this->form->getLabel('metadesc'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('metadesc'); ?></div>
</div>

<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('metakey'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('metakey'); ?></div>
</div>


<?php foreach($this->form->getGroup('metadata') as $field): ?>
	<div class="control-group">
        <div class="control-label">
            <?php if (!$field->hidden): ?>
                <?php echo $field->label; ?>
            <?php endif; ?>
        </div>
        <div class="controls"><?php echo $field->input; ?></div>
	</div>
<?php endforeach; ?>
