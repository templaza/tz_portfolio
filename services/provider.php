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

defined('_JEXEC') or die;

use Joomla\DI\Container;
use Joomla\CMS\HTML\Registry;
use Joomla\DI\ServiceProviderInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use TemPlaza\Component\TZ_Portfolio\Administrator\Extension\TZ_PortfolioComponent;

if(file_exists(dirname(dirname(__FILE__)).'/setup/index.php')){
    return require_once dirname(dirname(__FILE__)).'/setup/index.php';
}

return new class implements ServiceProviderInterface {

    public function register(Container $container)
    {
        $container->registerServiceProvider(new MVCFactory('\\TemPlaza\\Component\\TZ_Portfolio'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\TemPlaza\\Component\\TZ_Portfolio'));
        $container->registerServiceProvider(new RouterFactory('\\TemPlaza\\Component\\TZ_Portfolio'));
        $container->registerServiceProvider(new CategoryFactory('\\TemPlaza\\Component\\TZ_Portfolio'));

        $container->set(
            ComponentInterface::class,
            function (Container $container) {

                $component = new TZ_PortfolioComponent($container->get(ComponentDispatcherFactoryInterface::class));

                $component->setMVCFactory($container->get(MVCFactoryInterface::class));
                $component->setRegistry($container->get(Registry::class));
                $component->setRouterFactory($container->get(RouterFactoryInterface::class));
                $component->setCategoryFactory($container->get(CategoryFactoryInterface::class));

                return $component;
            }
        );
    }
};