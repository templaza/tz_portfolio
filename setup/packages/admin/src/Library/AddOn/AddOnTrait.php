<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    Sonny

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Library\AddOn;

use Joomla\CMS\Dispatcher\ModuleDispatcherFactory;
use Joomla\CMS\Event\AbstractEvent;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\DummyPlugin;
use Joomla\CMS\Extension\ExtensionHelper;
use Joomla\CMS\Extension\LegacyComponent;
use Joomla\CMS\Extension\Module;
use Joomla\CMS\Extension\ModuleInterface;
use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\HelperFactory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;

trait AddOnTrait{
    public function bootAddon($addon, $type)/*: PluginInterface*/
    {
        // Normalize the plugin name
        $addon = strtolower($addon);
        $addon = str_starts_with($addon, 'plg_') ? substr($addon, 4) : $addon;

        // Path to look for services
        $path = COM_TZ_PORTFOLIO_ADDON_PATH . '/' . $type . '/' . $addon;

//        if($type != 'extrafields') {
            $extension = $this->loadExtension(AddOnInterFace::class, $addon . ':' . $type
                . ':com_tz_portfolio:addon', $path);
//        }

        return $extension;

    }

    /**
     * Loads the extension.
     *
     * @param   string  $type           The extension type
     * @param   string  $extensionName  The extension name
     * @param   string  $extensionPath  The path of the extension
     *
     * @return  ComponentInterface|ModuleInterface|PluginInterface|AddOnInterFace
     *
     * @since   4.0.0
     */
    private function loadExtension($type, $extensionName, $extensionPath)
    {
        if(!isset(ExtensionHelper::$extensions[$type])){
            ExtensionHelper::$extensions[$type] = [];
        }
        // Check if the extension is already loaded
        if (!empty(ExtensionHelper::$extensions[$type][$extensionName])) {
            return ExtensionHelper::$extensions[$type][$extensionName];
        }

        // The container to get the services from
        $container = Factory::getContainer() ->createChild();

        $container->get(DispatcherInterface::class)->dispatch(
            'onBeforeExtensionBoot',
            AbstractEvent::create(
                'onBeforeExtensionBoot',
                [
                    'subject'       => $this,
                    'type'          => $type,
                    'extensionName' => $extensionName,
                    'container'     => $container,
                ]
            )
        );

        list($ex, $group)   = explode(':', $extensionName);

        $namespace  = '\\TemPlaza\\Component\\TZ_Portfolio\\AddOn\\'.ucfirst($group).'\\'.ucfirst($ex);
        $adoPath    = COM_TZ_PORTFOLIO_ADDON_PATH.'/'.$group.'/'.$ex.'/src';

        if(is_dir($adoPath)) {
            \JLoader::registerNamespace($namespace, $adoPath);
        }else{
             $dummyAddon = new DummyPlugin($container->get(DispatcherInterface::class));
            return $dummyAddon;
        }
        // The path of the loader file
        $path = $extensionPath . '/services/provider.php';

        if(!is_file($path)){
            return new DummyPlugin($container->get(DispatcherInterface::class));
        }

        // Load the file
        $provider = require_once $path;

        // Check if the extension supports the service provider interface
        if ($provider instanceof ServiceProviderInterface) {
            $provider->register($container);

            $container->get(DispatcherInterface::class)->dispatch(
                'onTPAfterAddOnBoot',
                AbstractEvent::create(
                    'onTPAfterAddOnBoot',
                    [
                        'subject'       => $this,
                        'type'          => $type,
                        'extensionName' => $extensionName,
                        'container'     => $container,
                    ]
                )
            );
            $container->get(DispatcherInterface::class)->dispatch(
                'onAfterExtensionBoot',
                AbstractEvent::create(
                    'onAfterExtensionBoot',
                    [
                        'subject'       => $this,
                        'type'          => $type,
                        'extensionName' => $extensionName,
                        'container'     => $container,
                    ]
                )
            );

            $extension = $container->get($type);

            if ($extension instanceof BootableExtensionInterface) {
                $extension->boot($container);
            }
        }

        if(!isset($extension)){
            return new DummyPlugin($container->get(DispatcherInterface::class));
        }

        // Cache the extension
        ExtensionHelper::$extensions[$type][$extensionName] = $extension;

        return $extension;
    }

    /**
     * Loads the extension.
     *
     * @param   string  $type           The extension type
     * @param   string  $extensionName  The extension name
     * @param   string  $extensionPath  The path of the extension
     *
     * @return  ComponentInterface|ModuleInterface|PluginInterface
     *
     * @since   4.0.0
     */
    private function loadExtrafieldAddon($type, $extensionName, $extensionPath)
    {
        // Check if the extension is already loaded
        if (!empty(ExtensionHelper::$extensions[$type][$extensionName])) {
            return ExtensionHelper::$extensions[$type][$extensionName];
        }

        // The container to get the services from
        $container = Factory::getContainer() ->createChild();

        $container->get(DispatcherInterface::class)->dispatch(
            'onBeforeExtensionBoot',
            AbstractEvent::create(
                'onBeforeExtensionBoot',
                [
                    'subject'       => $this,
                    'type'          => $type,
                    'extensionName' => $extensionName,
                    'container'     => $container,
                ]
            )
        );

        list($ex, $group)   = explode(':', $extensionName);

        $namespace  = 'TemPlaza\Component\TZ_Portfolio\AddOn\\'.ucfirst($group).'\\'.ucfirst($ex);
        $adoPath    = COM_TZ_PORTFOLIO_ADDON_PATH.'/'.$group.'/'.$ex.'/src';

        if(is_dir($adoPath)) {
            \JLoader::registerNamespace($namespace, $adoPath);
        }else{
             $dummyAddon = new DummyPlugin($container->get(DispatcherInterface::class));
            return $dummyAddon;
        }
        // The path of the loader file
        $path = $extensionPath . '/services/provider.php';

        if(!is_file($path)){
            return new DummyPlugin($container->get(DispatcherInterface::class));
        }

        // Load the file
        $provider = require_once $path;

        // Check if the extension supports the service provider interface
        if ($provider instanceof ServiceProviderInterface) {
            $provider->register($container);

            $container->get(DispatcherInterface::class)->dispatch(
                'onTPAfterAddOnBoot',
                AbstractEvent::create(
                    'onTPAfterAddOnBoot',
                    [
                        'subject'       => $this,
                        'type'          => $type,
                        'extensionName' => $extensionName,
                        'container'     => $container,
                    ]
                )
            );
            $container->get(DispatcherInterface::class)->dispatch(
                'onAfterExtensionBoot',
                AbstractEvent::create(
                    'onAfterExtensionBoot',
                    [
                        'subject'       => $this,
                        'type'          => $type,
                        'extensionName' => $extensionName,
                        'container'     => $container,
                    ]
                )
            );

            $extension = $container->get($type);

            if ($extension instanceof BootableExtensionInterface) {
                $extension->boot($container);
            }
        }

//        // Fallback to legacy
//        if (!$container->has($type)) {
//            switch ($type) {
//                case ComponentInterface::class:
//                    $container->set($type, new LegacyComponent('com_' . $extensionName));
//                    break;
//                case ModuleInterface::class:
//                    $container->set($type, new Module(new ModuleDispatcherFactory(''), new HelperFactory('')));
//                    break;
//                case PluginInterface::class:
//                    list($pluginName, $pluginType) = explode(':', $extensionName);
//                    $container->set($type, $this->loadPluginFromFilesystem($pluginName, $pluginType));
//            }
//        }
//
//        $container->get(DispatcherInterface::class)->dispatch(
//            'onAfterExtensionBoot',
//            AbstractEvent::create(
//                'onAfterExtensionBoot',
//                [
//                    'subject'       => $this,
//                    'type'          => $type,
//                    'extensionName' => $extensionName,
//                    'container'     => $container,
//                ]
//            )
//        );

//        $container->get(DispatcherInterface::class)->dispatch(
//            'onTPAfterAddOnBoot',
//            AbstractEvent::create(
//                'onTPAfterAddOnBoot',
//                [
//                    'subject'       => $this,
//                    'type'          => $type,
//                    'extensionName' => $extensionName,
//                    'container'     => $container,
//                ]
//            )
//        );
//
//        $extension = $container->get($type);
//
//        if ($extension instanceof BootableExtensionInterface) {
//            $extension->boot($container);
//        }

        if(!isset($extension)){
            return new DummyPlugin($container->get(DispatcherInterface::class));
        }

        // Cache the extension
        ExtensionHelper::$extensions[$type][$extensionName] = $extension;

        return $extension;
    }
}