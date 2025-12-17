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

namespace TemPlaza\Component\TZ_Portfolio\Module\Filter\Site\Helper;

// no direct access
defined('_JEXEC') or die;

use Akeeba\WebPush\WebPush\VAPID;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Cache\Controller\OutputController;
use Joomla\CMS\Extension\ExtensionHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Plugin\PluginHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\ExtraFieldsFrontHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\QueryHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\RouteHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\CategoriesHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\TagHelper;

class FilterHelper
{
    protected static $items = array();

    public function getAdvFilterFields($params){
        if($advfilter = ExtraFieldsFrontHelper::getAdvFilterFields($params -> get('fields'))) {
            return $advfilter;
        }
        return false;
    }

    public function getCategoriesOptions($params, SiteApplication $app){

        $leveltmp   = 1;
        $options    = array();
        $option     = new \stdClass();

        $option->text = Text::_('JOPTION_SELECT_CATEGORY');
        $option->value = '';
        $options[] = $option;

        if($parentid = $params -> get('parent_cat', 0)){
            if($categories = CategoriesHelper::getSubCategoriesByParentId((int) $parentid)){

                $leveltmp   = $categories[0] -> level - 1;

                foreach($categories as $i => $item){
                    if(!$params -> get('show_parent_root', 1) && $parentid == $item -> id){
                        if(isset($categories[$i + 1]) && $categories[$i + 1]) {
                            $leveltmp = $categories[$i + 1] -> level - 1;
                        }
                        unset($categories[$i]);
                        continue;
                    }
                    $option = new \stdClass();

                    $repeat = ($item->level - $leveltmp - 1 >= 0) ? $item->level - $leveltmp - 1 : 0;
                    $title  = str_repeat('- ', $repeat) . $item->title;
                    $option -> text     = $title;
                    $option -> value    = $item -> id;

                    $options[]  = $option;
                }
            }
        }else{
            $_options   = HTMLHelper::_('tzcategory.options', 'com_tz_portfolio_plus',array('filter.published' => 1));

            $options    = array_merge($options, $_options);
        }
        return $options;
    }

}
