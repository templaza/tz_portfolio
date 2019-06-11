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

class TZ_Portfolio_PlusSetupControllerAddons_InstallAddon extends TZ_Portfolio_PlusSetupControllerLegacy
{
	public function install()
	{
		// Get a list of folders in the module and plugins.
		$path = $this->input->get('path', '', 'default');

		// Get the plugin group and element
		$element = $this->input->get('element', '', 'cmd');
		$group = $this->input->get('group', '', 'cmd');

		// Construct the absolute path
		$absolutePath = $path . '/' . $group . '/' . $element;

		// Try to install the plugin now
		$state = $this->installAddon($element, $group, $absolutePath);

        JLoader::import('com_tz_portfolio_plus.libraries.plugin.helper',JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components');
        TZ_Portfolio_PlusPluginHelper::loadLanguage($element, $group);

		$this->setInfo(JText::sprintf('Add-on %1$s installed on the site',JText::_('PLG_'.$group.'_'.$element)), true);
		return $this->output();
	}

	/**
	 * Installation of plugins on the site
	 *
	 * @since   2.2.7
	 * @access  public
	 */
	public function installAddon($element, $group, $absolutePath)
	{

	    // Require TZ Portfolio Plus installer library
        JLoader::import('com_tz_portfolio_plus.libraries.installer',JPATH_ADMINISTRATOR
            .DIRECTORY_SEPARATOR.'components');

		// Get TZ Portfolio Plus's installer instance
        $installer    = TZ_Portfolio_PlusInstaller::getInstance();

		// Prevent any output from the installer
		ob_start();

		// Install the plugin now
		$state = $installer->install($absolutePath);

		ob_end_clean();

		return $state;
	}
}
