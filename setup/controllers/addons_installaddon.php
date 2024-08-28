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
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioInstaller;

class TZ_PortfolioSetupControllerAddons_InstallAddon extends TZ_PortfolioSetupControllerLegacy
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

        AddonHelper::loadLanguage($element, $group);

		$this->setInfo(Text::sprintf('Add-on %1$s installed on the site',Text::_('PLG_'.$group.'_'.$element)), true);
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

//	    // Require TZ Portfolio Plus installer library
//        JLoader::import('com_tz_portfolio_plus.libraries.installer',JPATH_ADMINISTRATOR
//            .DIRECTORY_SEPARATOR.'components');

		// Get TZ Portfolio Plus's installer instance
        $installer    = TZ_PortfolioInstaller::getInstance();

		// Prevent any output from the installer
		ob_start();

		// Install the plugin now
		$state = $installer->install($absolutePath);

		ob_end_clean();

		return $state;
	}
}
