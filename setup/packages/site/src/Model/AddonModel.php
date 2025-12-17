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

namespace TemPlaza\Component\TZ_Portfolio\Site\Model;

// no direct access
defined('_JEXEC') or die();

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Menu\SiteMenu;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\ListModel;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\CategoriesHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\ExtraFieldsFrontHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\QueryHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\RouteHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\TagHelper;

class AddonModel extends BaseDatabaseModel
{
    protected function populateState(){
        $app    = Factory::getApplication();
        $params = $app -> getParams();
        $this -> setState('params',$params);

        $addon_id = $app -> input -> getInt('addon_id');
        $this -> setState('addon.addon_id', $addon_id);
    }

    public function getRenderAddonView(){
        if($addon_id = $this -> getState('addon.addon_id')){
            if($addon      = AddonHelper::getPluginById($addon_id)) {

                if($addonObj = AddonHelper::getInstance($addon->type, $addon->name)) {
                    if(method_exists($addonObj, 'onRenderAddonView')) {
                        ob_start();
                        $addonObj->onRenderAddonView();
                        $html = ob_get_contents();
                        ob_end_clean();
                        return $html;
                    }
                }
            }
        }
        return false;
    }
}
?>