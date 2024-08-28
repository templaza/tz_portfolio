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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioTemplate;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioInstaller;

class TZ_PortfolioSetupControllerAddons_InstallStyle extends TZ_PortfolioSetupControllerLegacy
{
    protected $extensionProtected   = array();

    public function __construct(array $config = array())
    {
        parent::__construct($config);

        $this -> extensionProtected = array(
            'system',
            'elegant'
        );
    }

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

		TZ_PortfolioTemplate::loadLanguage($style);

        $lang   = Factory::getApplication() -> getLanguage();
        $key    = 'TZ_PORTFOLIO_TPL_'.$style;
        if(!$lang -> hasKey('TZ_PORTFOLIO_TPL_'.$style)){
            $key    = 'TZ_PORTFOLIO_PLUS_TPL_'.$style;
        }

        $this->setInfo(Text::sprintf('Style %1$s installed on the site', Text::_($key)), true);
		return $this->output();
	}

	public function installStyle($element, $path)
	{

        // Get TZ Portfolio Plus's installer instance
        $installer    = TZ_PortfolioInstaller::getInstance();

        // Prevent any output from the installer
        ob_start();

        // Install the plugin now
        $state = $installer->install($path);

        ob_end_clean();

        // Set protected style
        if(in_array($element, $this -> extensionProtected)) {
            $this->setProtected($element);
        }

        // Set default style
        $this -> setDefaultStyle($element);

        return $state;
	}

	/* Set default style */
    protected function setDefaultStyle($style){

        // if($style != 'elegant'){
            // return false;
        // }

        $db     = JFactory::getDbo();
        $query  = $db -> getQuery(true);

        $query -> select('COUNT(*)');
        $query -> from('#__tz_portfolio_plus_templates');
        $query -> where('home = 1');
        $db -> setQuery($query);
        if(!$db -> loadResult()){
            $query -> clear();
            $query -> update('#__tz_portfolio_plus_templates');
            $query -> set('home = 1');
            $query -> where('template='.$db -> quote($style));
            $query -> where('title='.$db -> quote($style.' - Default'));
            $db -> setQuery($query);
            $db -> execute();
            return true;
        }
        return false;
    }

    protected function setProtected($style){

        if(!$style){
            return false;
        }

        $db     = JFactory::getDbo();
        $query  = $db -> getQuery(true);

        $query -> update('#__tz_portfolio_plus_extensions');
        $query -> set('protected = 1');
        $query -> where('element='.$db -> quote($style));
        $query -> where('type='.$db -> quote('tz_portfolio_plus-template'));
        $db -> setQuery($query);
        $db -> execute();

        $query -> clear();
        $query -> update('#__tz_portfolio_plus_templates');
        $query -> set('protected = 1');
        $query -> where('template='.$db -> quote($style));
        $db -> setQuery($query);
        $db -> execute();

        return true;
    }
	
}
