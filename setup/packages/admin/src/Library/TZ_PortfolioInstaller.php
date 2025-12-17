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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Library;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactory;
use Joomla\CMS\Table\Table;
use Joomla\DI\ContainerAwareInterface;
use Joomla\String\StringHelper;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Installer\Installer;
use TemPlaza\Component\TZ_Portfolio\Administrator\Table\ExtensionsTable;

class TZ_PortfolioInstaller extends Installer
{
    protected static $instances;

    public function __construct($basepath = __DIR__, $classprefix = 'TemPlaza\Component\TZ_Portfolio\Administrator\Library\Adapter',
                                $adapterfolder = 'adapter')
    {
        parent::__construct($basepath, $classprefix, $adapterfolder);

        // Get a generic TZ_Portfolio_PlusTableExtension instance for use if not already loaded
        if (!($this->extension instanceof ExtensionsTable)) {
            /* @var MVCFactory $mvc */
//            $mvc    = Factory::getApplication() -> bootComponent('com_tz_portfolio') -> getMVCFactory();
//            $this->extension = $mvc -> createTable('Extensions', 'Administrator');
            $this->extension = Table::getInstance('ExtensionsTable',
                'TemPlaza\Component\TZ_Portfolio\Administrator\Table\\');

        }

        if(is_object($this -> extension) && isset($this -> extension -> id)) {
            $this->extension->extension_id = $this->extension->id;
        }
    }

    public static function getInstance($basepath = __DIR__,
                                       $classprefix = 'TemPlaza\Component\TZ_Portfolio\Administrator\Library\Adapter',
                                       $adapterfolder = 'Adapter')
    {
        if (!isset(self::$instances[$basepath]))
        {
            self::$instances[$basepath] = new static($basepath, $classprefix, $adapterfolder);

            // For B/C, we load the first instance into the static $instance container, remove at 4.0
            if(!version_compare(JVERSION, '4.0', 'ge')){

                if (!isset(self::$instance))
                {
                    self::$instance = self::$instances[$basepath];
                }
            }
        }

        return self::$instances[$basepath];
    }

    public function install($path = null)
    {
        if ($path && is_dir($path))
        {
            $this->setPath('source', $path);
        }
        else
        {
            $this->abort(Text::_('JLIB_INSTALLER_ABORT_NOINSTALLPATH'));

            return false;
        }

        if (!$adapter = $this->setupInstall('install', true))
        {
            $this->abort(Text::_('JLIB_INSTALLER_ABORT_DETECTMANIFEST'));

            return false;
        }

        if (!is_object($adapter))
        {
            return false;
        }

        // Add the languages from the package itself
        if (method_exists($adapter, 'loadLanguage'))
        {
            $adapter->loadLanguage($path);
        }

//        // Fire the onExtensionBeforeInstall event.
//        JPluginHelper::importPlugin('extension');
//        $dispatcher = JEventDispatcher::getInstance();
//        $dispatcher->trigger(
//            'onExtensionBeforeInstall',
//            array(
//                'method' => 'install',
//                'type' => $this->manifest->attributes()->type,
//                'manifest' => $this->manifest,
//                'extension' => 0
//            )
//        );

        // Run the install
        $result = $adapter->install();

//        // Fire the onExtensionAfterInstall
//        $dispatcher->trigger(
//            'onExtensionAfterInstall',
//            array('installer' => clone $this, 'eid' => $result)
//        );

        if ($result !== false)
        {
            // Refresh versionable assets cache
            Factory::getApplication()->flushAssets();

            return true;
        }

        return false;
    }

    public function getAdapter($name, $options = array())
    {
        $this->getAdapters($options);

        if (!$this->setAdapter($name, $this->_adapters[$name]))
        {
            return false;
        }

        return $this->_adapters[$name];
    }

    public function setupInstall($route = 'install', $returnAdapter = false)
    {
        // We need to find the installation manifest file
        if (!$this->findManifest())
        {
            return false;
        }

        // Load the adapter(s) for the install manifest
        $type   = (string) $this->manifest->attributes()->type;
        $type   = StringHelper::str_ireplace('tz_portfolio_plus-','',$type);
        $type   = StringHelper::str_ireplace('tz_portfolio-','',$type);
        $params = array('route' => $route, 'manifest' => $this->getManifest());

        // Include adapter folder
        $path = $this->_basepath . '/' . $this->_adapterfolder . '/' . $type . '.php';

        switch($type){
            case 'plugin':
                $type   = 'addon';
                break;
            case 'template':
                $type   = 'style';
                break;
        }

        $adapterPrefix  = ucfirst($type);

        $adapter = $this->loadAdapter($adapterPrefix, $params);

        if ($returnAdapter)
        {
            return $adapter;
        }

        return true;
    }
}