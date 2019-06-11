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

use TZ_Portfolio_Plus\Installer\TZ_Portfolio_PlusInstaller;

class TZ_Portfolio_PlusSetupControllerAddons_InstallStyle extends TZ_Portfolio_PlusSetupControllerLegacy
{
	public function install()
	{
		// Get a list of folders in the module and plugins.
		$path = $this->input->get('path', '', 'default');

		// Determines which module to install on the site
		$style = $this->input->get('style', '', 'cmd');
			
		// Construct the absolute path to the module
		$absolutePath = $path . '/' . $style;

		// Try to install the module now.
		$state = $this->installStyle($style, $absolutePath);

        JLoader::import('com_tz_portfolio_plus.libraries.template',JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components');
		TZ_Portfolio_PlusTemplate::loadLanguage($style);

		$this->setInfo(JText::sprintf('Style %1$s installed on the site', JText::_('TZ_PORTFOLIO_PLUS_TPL_'.$style)), true);
		return $this->output();
	}

	public function installStyle($element, $path)
	{

        // Require TZ Portfolio Plus installer library
        JLoader::import('com_tz_portfolio_plus.libraries.installer',JPATH_ADMINISTRATOR
            .DIRECTORY_SEPARATOR.'components');

        // Get TZ Portfolio Plus's installer instance
        $installer    = TZ_Portfolio_PlusInstaller::getInstance();

        // Prevent any output from the installer
        ob_start();

        // Install the plugin now
        $state = $installer->install($path);

        ob_end_clean();

        return $state;
	}
	
}
