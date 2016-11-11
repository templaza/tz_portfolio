<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

JLoader::import('com_tz_portfolio_plus.includes.framework',JPATH_ADMINISTRATOR.'/components');

class TZ_Portfolio_PlusInstaller extends JInstaller
{
    protected static $instances;

    public function __construct($basepath = __DIR__, $classprefix = 'TZ_Portfolio_PlusInstallerAdapter', $adapterfolder = 'adapter')
    {
        parent::__construct($basepath, $classprefix, $adapterfolder);

        // Get a generic TZ_Portfolio_PlusTableExtension instance for use if not already loaded
        if (!($this->extension instanceof TZ_Portfolio_PlusTableExtensions)) {
            JTable::addIncludePath(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH . DIRECTORY_SEPARATOR . 'tables');
            $this->extension = JTable::getInstance('Extensions', 'TZ_Portfolio_PlusTable');
        }

        if(is_object($this -> extension) && isset($this -> extension -> id)) {
            $this->extension->extension_id = $this->extension->id;
        }
    }

    public static function getInstance($basepath = __DIR__, $classprefix = 'TZ_Portfolio_PlusInstallerAdapter', $adapterfolder = 'adapter')
    {
        if (!isset(self::$instances[$basepath]))
        {
            self::$instances[$basepath] = new TZ_Portfolio_PlusInstaller($basepath, $classprefix, $adapterfolder);

            // For B/C, we load the first instance into the static $instance container, remove at 4.0
            if (!isset(self::$instance))
            {
                self::$instance = self::$instances[$basepath];
            }
        }

        return self::$instances[$basepath];
    }

    public function install($path = null)
    {
        if ($path && JFolder::exists($path))
        {
            $this->setPath('source', $path);
        }
        else
        {
            $this->abort(JText::_('JLIB_INSTALLER_ABORT_NOINSTALLPATH'));

            return false;
        }

        if (!$adapter = $this->setupInstall('install', true))
        {
            $this->abort(JText::_('JLIB_INSTALLER_ABORT_DETECTMANIFEST'));

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
            JFactory::getApplication()->flushAssets();

            return true;
        }

        return false;
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
        $type   = JString::str_ireplace('tz_portfolio_plus-','',$type);
        $params = array('route' => $route, 'manifest' => $this->getManifest());

        // Load the adapter
        $adapter = $this->getAdapter($type, $params);

        if ($returnAdapter)
        {
            return $adapter;
        }

        return true;
    }
}