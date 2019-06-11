<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2019 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

class TZ_Portfolio_PlusSetupControllerAddons_InstallModule extends TZ_Portfolio_PlusSetupControllerLegacy
{
	public function install()
	{
		// Get a list of folders in the module and plugins.
		$path = $this->input->get('path', '', 'default');

		// Determines which module to install on the site
		$module = $this->input->get('module', '', 'cmd');
			
		// Construct the absolute path to the module
		$absolutePath = $path . '/' . $module;

		// Try to install the module now.
		$state = $this->installModule($module, $absolutePath);

		$this->setInfo(JText::sprintf('Module %1$s installed on the site', JText::_($module)), true);
		return $this->output();
	}

	public function installModule($element, $path)
	{
		
		// Get Joomla's installer instance
		$installer = new JInstaller();

		// Allow overwriting existing modules
		$installer->setOverwrite(true);

		// Prevent any output from the installer
		ob_start();
		// Install the module
		$state = $installer->install($path);
		ob_end_clean();

		if (!$state) {
			return false;
		}

		return true;
	}
	
}
