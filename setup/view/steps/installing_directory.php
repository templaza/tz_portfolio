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
use Joomla\CMS\Language\Text;
?>
<script type="text/javascript">
$(document).ready(function(){

	<?php if ($reinstall) { ?>
		tpp.ajaxUrl = "<?php echo Uri::root();?>administrator/index.php?option=com_tz_portfolio&ajax=1&reinstall=1";
	<?php } ?>

	<?php if ($update) { ?>
		tpp.ajaxUrl = "<?php echo Uri::root();?>administrator/index.php?option=com_tz_portfolio&ajax=1&update=1";
	<?php } ?>

	// Immediately proceed with installation
    <?php if($input -> get('license')){ ?>
	tpp.installation.activePro();
	<?php }else{ ?>
    tpp.installation.extract();
	<?php } ?>
});

</script>
<form name="installation" method="post" data-installation-form>
	<p class="section-desc">
		<?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_INSTALLING_DESC');?>
	</p>

	<div class="alert alert-primary" data-installation-message style="display: none;">
		<?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_INSTALLING_COMPLETED'); ?>
	</div>

	<div data-install-progress>

		<ol class="install-logs list-reset" data-progress-logs="">
            <?php if($license = $input -> get('license')){ ?>
			<li class="active" data-progress-active-pro>
				<div class="progress-icon">
					<i class="icon-checkbox-unchecked"></i>
				</div>
				<div class="split__title"><?php echo Text::_('COM_TZ_PORTFOLIO_ACTIVING_PRO_VERSION');?></div>
				<span class="progress-state"><?php echo Text::_('COM_TZ_PORTFOLIO_ACTIVING');?>...</span>
			</li>
            <?php } ?>
			<li class="active" data-progress-extract>
				<div class="progress-icon">
					<i class="icon-checkbox-unchecked"></i>
				</div>
				<div class="split__title"><?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_INSTALLING_EXTRACTING_FILES');?></div>
				<span class="progress-state"><?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_INSTALLING_EXTRACTING');?>...</span>
			</li>

			<?php include(dirname(__FILE__) . '/installing_steps.php'); ?>
		</ol>
	</div>

	<input type="hidden" name="option" value="com_tz_portfolio" />
	<input type="hidden" name="active" value="<?php echo $active; ?>" />
	<input type="hidden" name="source" data-source />
	<input type="hidden" name="license" value="<?php echo $input -> get('license'); ?>" data-license />
	<input type="hidden" name="sample_data" value="<?php
    echo $input -> getInt('sample_data', 0);?>" data-sample-data />

	<?php if ($reinstall) { ?>
	<input type="hidden" name="reinstall" value="1" />
	<?php } ?>

	<?php if ($update) { ?>
	<input type="hidden" name="update" value="1" />
	<?php } ?>

</form>