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

use Joomla\CMS\Language\Text;

?>
<li class="pending" data-progress-sql>
	<div class="progress-icon">
		<i class="icon-checkbox-unchecked"></i>
	</div>
	<div class="split__title">
		<?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_INITIALIZING_DB' );?>
	</div>
	<span class="progress-state"><?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_INITIALIZING');?>...</span>
</li>
<li class="pending" data-progress-admin>
	<div class="progress-icon">
		<i class="icon-checkbox-unchecked"></i>
	</div>
	<div class="split__title">
		<?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_INITIALIZING_ADMIN');?>
	</div>
	<span class="progress-state"><?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_INITIALIZING');?>...</span>
</li>
<li class="pending" data-progress-site>
	<div class="progress-icon">
		<i class="icon-checkbox-unchecked"></i>
	</div>
	<div class="split__title">
		<?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_INITIALIZING_SITE');?>
	</div>
	<span class="progress-state"><?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_INITIALIZING');?>...</span>
</li>
<li class="pending" data-progress-media>
	<div class="progress-icon">
		<i class="icon-checkbox-unchecked"></i>
	</div>
	<div class="split__title">
		<?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_INITIALIZING_MEDIA_FILES');?>
	</div>
	<span class="progress-state"><?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_INITIALIZING');?>...</span>
</li>
