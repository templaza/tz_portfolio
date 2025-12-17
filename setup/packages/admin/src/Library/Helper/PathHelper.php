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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactory;
use Joomla\Event\DispatcherAwareInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Filesystem\Path;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Plugin\PluginHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\AddOn\AddOnController;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\AddOn\AddOnTrait;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioTemplate;

class PathHelper extends Path
{
    /**
     * Get add-on path
     * @param string $group Group of add-on: content, mediatype, extrafields...
     * @param string $addon Add-on name.
     * @return string|boolean
     * */
    public static function getAddOnPath($group, $addon){
        $path   = COM_TZ_PORTFOLIO_ADDON_PATH.'/'.$group.'/'.$addon;
        $path   = Path::clean($path);

//        if(is_dir($path)){
            return $path;
//        }

//        return false;
    }
    /**
     * Get add-on path
     * @param string $group Group of add-on: content, mediatype, extrafields...
     * @param string $addon Add-on name.
     * @return string|boolean
     * */
    public static function getAddOnTmplPath($group, $addon){
        $path   = self::getAddOnPath($group, $addon);
        $path  .= '/tmpl';
        $path   = Path::clean($path);

//        if(is_dir($path)){
            return $path;
//        }

//        return false;
    }

    /**
     * Get module tmpl path in add-on
     * @param string $module Module name which add-on supported
     * @param string $group Group of add-on: content, mediatype, extrafields...
     * @param string $addon Add-on name.
     * @return string|boolean|array
     * */
    public static function getModuleTmplPathOfAddOn($module, $group, $addon, $stylePath = false){
        $path   = self::getAddOnTmplPath($group, $addon);
        $path  .= '/'.$module;
        $path   = Path::clean($path);

        if($stylePath) {

            $paths  = [];
            $style  = TZ_PortfolioTemplate::getTemplate(true);

            $paths  = $style -> paths;

            foreach($paths as $key => &$_path){
                $_path  .= '/'.$module;
            }

//            array_unshift($paths, $path);

            $paths  = array_reverse($paths);
            $paths['mbasePath'] = $path;

            return $paths;
        }elseif(is_dir($path)){
            return $path;
        }
        return false;
    }

    public static function getModuleFormPathOfAddOn(){

    }
}