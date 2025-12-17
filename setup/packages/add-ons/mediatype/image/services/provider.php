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

use Joomla\CMS\Factory;
use Joomla\DI\Container;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Event\DispatcherInterface;
use Joomla\DI\ServiceProviderInterface;
use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use TemPlaza\Component\TZ_Portfolio\AddOn\Mediatype\Image\Extension\Image;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\AddOn\AddOnInterFace;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;

return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   4.3.0
     */
    public function register(Container $container)
    {
        $container->registerServiceProvider(new MVCFactory('\\TemPlaza\\Component\\TZ_Portfolio\\AddOn\\Mediatype\\Image'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\TemPlaza\\Component\\TZ_Portfolio\\AddOn\\Mediatype\\Image'));

        $container->set(
            AddOnInterFace::class,
            function (Container $container) {
                $addon     = new Image(
                    $container->get(DispatcherInterface::class),
                    (array) AddonHelper::getAddOn('mediatype', 'image')
                );

                $addon->setApplication(Factory::getApplication());
                $addon->setMVCFactory($container->get(MVCFactoryInterface::class));

                return $addon;
            }
        );
    }
};