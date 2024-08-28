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
use Joomla\CMS\Installer\Installer;

class TZ_PortfolioSetupControllerAddons_InstallPlugin extends TZ_PortfolioSetupControllerLegacy
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
		$state = $this->installPlugin($element, $group, $absolutePath);

		$this->setInfo(Text::sprintf('Plugin %1$s installed on the site',Text::_('plg_'.$group.'_'.$element)), true);

		return $this->output();
	}

	/**
	 * Installation of plugins on the site
	 *
	 * @since   2.2.7
	 * @access  public
	 */
	public function installPlugin($element, $group, $absolutePath)
	{
	    // Before install check the plugin exists
        $db = JFactory::getDbo();

        $query  = $db -> getQuery(true);
        $query -> select('COUNT(*)');
        $query -> from('#__extensions');
        $query -> where('type='.$db -> quote('plugin'));
        $query -> where('element = '.$db -> quote($element));
        $query -> where('folder = '.$db -> quote($group));
        $db -> setQuery($query);
        $hasPlugin  = $db -> loadResult();

		// Get Joomla's installer instance
		$installer = Installer::getInstance();

//		// Allow overwriting of existing plugins
//		$installer->setOverwrite(true);

		// Prevent any output from the installer
		ob_start();

		// Install the plugin now
		$state = $installer->install($absolutePath);

		ob_end_clean();

		if(!$hasPlugin) {
            // Enable the plugin
            $query->clear('select');
            $query->clear('from');
            $query->update('#__extensions');
            $query->set('enabled = 1');
            $query->set('params = ' . $db->quote(''));
            $db->setQuery($query);
            $db->execute();
        }

		return $state;
	}
}
