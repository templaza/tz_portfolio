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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Event\Installer\AfterInstallerEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactory;
use Joomla\CMS\Table\Table;
use Joomla\DI\ContainerAwareInterface;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Installer\Installer;
use Joomla\Database\DatabaseInterface;
use TemPlaza\Component\TZ_Portfolio\Administrator\Table\ExtensionsTable;

class TZ_PortfolioInstaller extends Installer
{
    protected static $instances;

    protected array $accept_types = array();

    public function __construct($basepath = __DIR__, $classprefix = 'TemPlaza\Component\TZ_Portfolio\Administrator\Library\Adapter',
                                $adapterfolder = 'adapter')
    {
        parent::__construct($basepath, $classprefix, $adapterfolder);

        // Get a generic TZ_Portfolio_PlusTableExtension instance for use if not already loaded
        if (!($this->extension instanceof ExtensionsTable)) {
            /* @var MVCFactory $mvc */
            $this->extension = Table::getInstance('ExtensionsTable',
                'TemPlaza\Component\TZ_Portfolio\Administrator\Table\\');
        }

        if(is_object($this -> extension) && isset($this -> extension -> id)) {
            $this->extension->extension_id = $this->extension->id;
        }

        $this -> accept_types   = array(
            'tz_portfolio-plugin',
            'tz_portfolio-addon',
            'tz_portfolio-style',
            'module');
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

    public function install($path = null): false
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
        $app = Factory::getApplication();
        $input = $app->input;
        if($manifest = $this ->getManifest()){
            $attrib = $manifest -> attributes();

            /** Check add-on supported with tz portfolio
             * @var \SimpleXMLElement $targetPlatForm
             */
            $targetPlatForm = $manifest ->xpath('tpTargetPlatforms/tpTargetPlatform[@name="com_tz_portfolio"]');
            $hasSupported   = !empty($targetPlatForm);

            $component = ComponentHelper::getComponent('com_tz_portfolio');
            $extension = Table::getInstance('extension');

            $extension -> load($component->id);

            $compManifest   = new Registry($extension->manifest_cache);
            $compVersion    = $compManifest->get('version');

            if(!$hasSupported){
                $app -> enqueueMessage(sprintf(Text::_('This add-on not supported for this component version %s'),
                    $compVersion), 'error');
                return false;
            }

            $platFormAttrib     = $targetPlatForm[0] -> attributes();
            $platFormVersion    = (string)$platFormAttrib -> version;

            if(!empty($platFormVersion)) {

                if(!preg_match('/^' . $platFormVersion . '/', $compVersion)){
                    $app -> enqueueMessage(sprintf(Text::_('This add-on not supported for this component version %s'),
                        $compVersion), 'error');
                    return false;
                }
            }

            $name   = (string) $manifest -> name;
            $type   = (string) $attrib -> type;

            if(!in_array($type, $this -> accept_types)){
                $app -> enqueueMessage(Text::_('COM_TZ_PORTFOLIO_UNABLE_TO_FIND_INSTALL_PACKAGE'), 'error');
                return false;
            }

            $_type  = explode('-',$type);
            $_type  = end($_type);

            $_type  = $_type == 'plugin'?'addon':$_type;
            $_type  = $_type == 'template'?'style':$_type;

            // Install for add-ons to update version
            $class  = 'TemPlaza\Component\TZ_Portfolio\Administrator\Library\Adapter\\'.ucfirst($_type).'Adapter';

            if(!class_exists($class)){
                \JLoader::registerPrefix(ucfirst($_type),COM_TZ_PORTFOLIO_ADMIN_PATH.'/src/Library/Adapter/'
                    .ucfirst($_type).'Adapter.php');
            }

            $tzinstaller    = new $class($this,Factory::getContainer()->get(DatabaseInterface::class));
            $tzinstaller -> setMVCFactory($app -> bootComponent('tz_portfolio') -> getMVCFactory());
            $tzinstaller -> setRoute('install');
            $tzinstaller -> setManifest($this -> getManifest());

            if(!$tzinstaller -> install()){
                // There was an error installing the package.
                $msg = Text::sprintf('COM_TZ_PORTFOLIO_INSTALL_ERROR', $input -> getCmd('view'));
                $result = false;
                $this -> setError($msg);
            }
        }

//        if (!$adapter = $this->setupInstall('install', true))
//        {
//            $this->abort(Text::_('JLIB_INSTALLER_ABORT_DETECTMANIFEST'));
//
//            return false;
//        }
//
//        if (!is_object($adapter))
//        {
//            return false;
//        }
//
//        // Add the languages from the package itself
//        if (method_exists($adapter, 'loadLanguage'))
//        {
//            $adapter->loadLanguage($path);
//        }
//
//        // Run the install
//        $result = $adapter->install();
//
//        if ($result !== false)
//        {
//            // Refresh versionable assets cache
//            Factory::getApplication()->flushAssets();
//
//            return true;
//        }

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

        $adapter = $this->getAdapter($adapterPrefix, $params);

        if ($returnAdapter)
        {
            return $adapter;
        }

        return true;
    }
}