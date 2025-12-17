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

namespace TemPlaza\Component\TZ_Portfolio\Module\Tags\Site\Dispatcher;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\WebAsset\WebAssetManager;
use Joomla\Input\Input;
use TemPlaza\Component\TZ_Portfolio\Administrator\Extension\TZ_PortfolioComponent;

defined('_JEXEC') or die;

/**
 * Dispatcher class for mod_tz_portfolio
 *
 */
class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
    use HelperFactoryAwareTrait;

    /**
     * Returns the layout data.
     *
     * @return  array
     *
     * @since   4.2.0
     */
    protected function getLayoutData()
    {

        $data   = parent::getLayoutData();
        $helper = $this -> getHelperFactory()-> getHelper('TagsHelper', $data);

        /**
         * Load styles and scripts of com_tz_portfolio to use this module
         * @var WebAssetManager $wa
         * */
        $wa     = $this->getApplication() -> getDocument() -> getWebAssetManager();

        $wa -> getRegistry() -> addExtensionRegistryFile('com_tz_portfolio');

        $module = $data['module'];

//        $wa -> registerScript($module -> module.'.resize', Uri::base().'/media/'.$module -> module);

//        $data['list'] = $helper -> getList($data['params'], $this->getApplication());

        $app    = $this -> getHelperFactory();
        $params = $data['params'];

        $data['list']           = $helper -> getList($params, $this -> getApplication());
        $data['articleCount']   = $helper -> getArticleTotal();

        if($params -> get('enable_uikit', 1)){
            $wa -> usePreset('com_tz_portfolio.uikit');
        }

        return $data;
    }
}
