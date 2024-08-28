<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

?>
<script type="text/javascript">
jQuery(document).ready(function(){

	$ = jQuery;

	$('[data-installation-submit]').on('click', function() {
		$('[data-requirements-error]').show();
	});

	// Retry button
	$('[data-installation-reload]').on('click', function() {
		window.location.href = window.location;
	});

	$('[data-installation-submit]')
		.addClass('hide');

	$('[data-installation-steps]')
		.addClass('error');

	$('[data-installation-retry]')
		.removeClass('hide');

	$('[data-installation-retry]').on('click', function(){

		// Hide the retry button
		$(this).addClass('hide');

		// Show the loading button
		$('[data-installation-loading]')
			.removeClass('hide');

		$('[data-installation-form-nav-active]').val('');
		$('[data-installation-form-nav]').submit();
	});
});
</script>
<div class="installation-error">
	<form name="installation" method="post" data-installation-form>

	<p class="alert alert-warning"><?php echo JText::_('COM_TZ_PORTFOLIO_INSTALLATION_TECHNICAL_REQUIREMENTS_DESC');?></p>

	<div class="alert alert-danger" data-requirements-error style="display: none;">
		<?php echo JText::_('COM_TZ_PORTFOLIO_INSTALLATION_TECHNICAL_REQUIREMENTS_SETTINGS');?>
	</div>

	<div class="requirements-table" data-system-requirements>

		<?php if ($showSettingsSection) { ?>
		<table class="table">
			<thead>
				<tr>
					<td width="75%">
						<?php echo JText::_('COM_TZ_PORTFOLIO_INSTALLATION_TECHNICAL_REQUIREMENTS_SETTINGS');?>
					</td>
					<td class="text-right" width="25%"></td>
				</tr>
			</thead>

			<tbody>

				<?php if (version_compare($phpVersion, '5.3.10') == -1) { ?>
				<tr class="error">
					<td>
						<div class="clearfix">
							<img class="hasTooltip" src="<?php echo COM_TZ_PORTFOLIO_SETUP_URL;?>/assets/images/alert.svg" width="12" height="12" data-original-title="<?php echo JText::_('COM_EB_INSTALLATION_PHP_VERSION_TIPS');?>" data-toggle="tooltip" data-placement="bottom">
							PHP Version
						</div>
					</td>
					<td class="text-right text-error">
						<?php echo $phpVersion;?>
					</td>
				</tr>
				<?php } ?>

				<?php if (!$gd) { ?>
				<tr class="error">
					<td>
						<div class="clearfix">
							<img class="hasTooltip" src="<?php echo COM_TZ_PORTFOLIO_SETUP_URL;?>/assets/images/alert.svg" width="12" height="12" data-original-title="<?php echo JText::_('COM_EB_INSTALLATION_PHP_GD_TIPS');?>" data-toggle="tooltip" data-placement="bottom">
							GD Library
						</div>
					</td>
					<td class="text-right text-error">
						Disabled
					</td>
				</tr>
				<?php } ?>

				<?php if (!$curl) { ?>
					<tr class="error">
						<td>
							<div class="clearfix">
								<img class="hasTooltip" src="<?php echo COM_TZ_PORTFOLIO_SETUP_URL;?>/assets/images/alert.svg" width="12" height="12" data-original-title="<?php echo JText::_('COM_EB_INSTALLATION_PHP_CURL_TIPS');?>" data-toggle="tooltip" data-placement="bottom">
			
								CURL Library
							</div>
						</td>

						<td class="text-right text-error">
							Disabled
						</td>
					</tr>
				<?php } ?>

				<?php if (isset($magicQuotes) && $magicQuotes) { ?>
				<tr class="error">
					<td>
						<div class="clearfix">
							<img class="hasTooltip" src="<?php echo COM_TZ_PORTFOLIO_SETUP_URL;?>/assets/images/alert.svg" width="12" height="12" data-original-title="<?php echo JText::_('COM_EB_INSTALLATION_PHP_MAGICQUOTES_TIPS');?>" data-toggle="tooltip" data-placement="bottom">
							Magic Quotes GPC
						</div>
					</td>
					<td class="text-right text-error">
						Enabled
					</td>
				</tr>
				<?php } ?>

				<?php if ($memoryLimit < 64) { ?>
				<tr class="error">
					<td>
						<img class="hasTooltip" src="<?php echo COM_TZ_PORTFOLIO_SETUP_URL;?>/assets/images/alert.svg" width="12" height="12" data-original-title="<?php echo JText::_('COM_EB_INSTALLATION_PHP_MEMORYLIMIT_TIPS');?>" data-toggle="tooltip" data-placement="bottom">
						memory_limit
					</td>
					<td class="text-right text-error">
						<?php echo $memoryLimit; ?>M
					</td>
				</tr>
				<?php } ?>

				<?php if (!$mysqlVersion || version_compare($mysqlVersion , '5.0.4') == -1) { ?>
				<tr class="error">
					<td>
						<img class="hasTooltip" src="<?php echo COM_TZ_PORTFOLIO_SETUP_URL;?>/assets/images/alert.svg" width="12" height="12" data-original-title="<?php echo JText::_('COM_EB_INSTALLATION_MYSQL_VERSION_TIPS');?>" data-toggle="tooltip" data-placement="bottom">
						MySQL Version
					</td>
					<td class="text-right text-error">
						<?php echo $mysqlVersion; ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php } ?>

		<?php if ($showDirectorySection) { ?>
		<table class="table table-striped mt-20 stats">
			<thead>
				<tr>
					<td width="75%">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_DIRECTORY'); ?>
					</td>
					<td class="text-right" width="25%"></td>
				</tr>
			</thead>

			<tbody>
				<?php foreach ($files as $file) { ?>
					<?php if (!$file->writable) { ?>
						<tr class="error">
							<td>
								<div class="clearfix">
									<img src="<?php echo COM_TZ_PORTFOLIO_SETUP_URL;?>/assets/images/alert.svg" width="12" height="12">
									<span><?php echo $file->path;?></span>
								</div>
							</td>

							<td class="text-right text-error">
								<?php echo JText::_('COM_EASYBLOG_INSTALLATION_PERMISSIONS_UNWRITABLE');?>
							</td>
						</tr>
					<?php } ?>
				<?php } ?>

			</tbody>
		</table>
		<?php } ?>
	</div>

	<input type="hidden" name="option" value="com_easyblog" />
	<input type="hidden" name="active" value="<?php echo $active; ?>" />

	</form>
</div>
