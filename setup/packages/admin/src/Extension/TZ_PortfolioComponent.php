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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Extension;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryServiceInterface;
use Joomla\CMS\Categories\CategoryServiceTrait;
use Joomla\CMS\Uri\Uri;
use Psr\Container\ContainerInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use TemPlaza\Component\TZ_Portfolio\Administrator\Service\HTML\Icon;
use TemPlaza\Component\TZ_Portfolio\Administrator\Service\HTML\TpGrid;
use TemPlaza\Component\TZ_Portfolio\Administrator\Service\HTML\TPJGrid;
use TemPlaza\Component\TZ_Portfolio\Administrator\Service\HTML\TZCategory;
use TemPlaza\Component\TZ_Portfolio\Administrator\Service\HTML\TZBootstrap;
use TemPlaza\Component\TZ_Portfolio\Administrator\Service\HTML\TZTemplates;

class TZ_PortfolioComponent extends MVCComponent implements
    BootableExtensionInterface, RouterServiceInterface, CategoryServiceInterface
{
    use RouterServiceTrait;
    use HTMLRegistryAwareTrait;
    use CategoryServiceTrait;

    /**
     * The archived condition
     */
    public const CONDITION_ARCHIVED = 2;

    /**
     * The published condition
     */
    public const CONDITION_PUBLISHED = 1;

    /**
     * The unpublished condition
     */
    public const CONDITION_UNPUBLISHED = 0;

    /**
     * The trashed condition
     */
    public const CONDITION_TRASHED = -2;

    /**
     * Booting the extension. This is the function to set up the environment of the extension like
     * registering new class loaders, etc.
     *
     * If required, some initial set up can be done from services of the container, eg.
     * registering HTML services.
     *
     * @param   ContainerInterface  $container  The container
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function boot(ContainerInterface $container)
    {

        $this -> loadFramework();

        $this -> registerStyle();
        $this -> registerService($container);
    }

    private function loadFramework() {
        require_once JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/includes/defines.php';
    }

    private function registerService(ContainerInterface $container){
        \JLoader::registerNamespace('TemPlaza\Component\TZ_Portfolio\Site', JPATH_SITE.'/components/com_tz_portfolio/src');
        $this->getRegistry()->register('tpicon', new Icon());
        $this->getRegistry()->register('tpjgrid', new TPJGrid());
        $this->getRegistry()->register('tpgrid', new TpGrid());
        $this->getRegistry()->register('tzcategory', new TZCategory($container -> get(SiteApplication::class)));
        $this->getRegistry()->register('tzbootstrap', new TZBootstrap());
        $this->getRegistry()->register('tztemplates', new TZTemplates());

    }

    public function registerStyle(){

        if(Factory::getApplication() -> getDocument()){
            /* @var WebAssetManager $wa */
            $wa = Factory::getApplication() -> getDocument() -> getWebAssetManager();

            $params = ComponentHelper::getParams('com_tz_portfolio');

            if($params -> get('enable_uikit', 1)
                && $params -> get('uikit_loading', 'cdn') == 'cdn') {
                if($wa -> assetExists('script', 'com_tz_portfolio.uikit')){
                    $waUIkitScript = $wa->getAsset('script', 'com_tz_portfolio.uikit');
                    if($cdn = $waUIkitScript -> getAttribute('cdn')){
                        $wa -> registerScript('com_tz_portfolio.uikit', $cdn);
                    }
                }
                if($wa -> assetExists('script', 'com_tz_portfolio.uikiticon')){
                    $waUIkitIconScript = $wa->getAsset('script', 'com_tz_portfolio.uikiticon');
                    if($cdn = $waUIkitIconScript -> getAttribute('cdn')) {
                        $wa -> registerScript('com_tz_portfolio.uikiticon', $cdn);
                    }
                }

                if($wa -> assetExists('style', 'com_tz_portfolio.uikit')){
                    $waUIkitStyle = $wa->getAsset('style', 'com_tz_portfolio.uikit');
                    if($cdn = $waUIkitStyle -> getAttribute('cdn')) {
                        $wa->registerStyle('com_tz_portfolio.uikit', $cdn);
                    }
                }
            }
        }
    }
}