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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Helper;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\Filesystem\File;
use Joomla\CMS\Table\Table;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;

class AddonDatasHelper{
    public static function getRootURL($addon_id,$root_view = 'addon_datas'){
        if($addon_id){
            return 'index.php?option=com_tz_portfolio&view='.$root_view.'&addon_id='.$addon_id;
        }
        return false;
    }

    public static function getActions($id, $section = 'addon',$parent_section = '')
    {
        $component  = 'com_tz_portfolio';
        $user	    = Factory::getApplication()->getIdentity();
        $result	    = new \Joomla\Registry\Registry();

        $path       = JPATH_ADMINISTRATOR . '/components/com_tz_portfolio/access.xml';

        if($addon  = AddonHelper::getPluginById($id)){
            $_path   = COM_TZ_PORTFOLIO_ADDON_PATH.'/'.$addon -> type.'/'. $addon -> name.'/access.xml';
            if(file_exists($_path)){
                $path   = $_path;
            }
        }

        $assetName = $component;

        if ($section && $id)
        {
            $assetName = $component . '.' . $section . '.' . (int) $id;

            $tblAsset   = Table::getInstance('Asset', 'Table');
            if(!$tblAsset -> loadByName($assetName)){
                $assetName  = $component . '.' . $parent_section;
            }
        }elseif (empty($id))
        {
            $assetName = $component . '.' . $section;
        }

        $actions = Access::getActionsFromFile($path, "/access/section[@name='addon']/");

        foreach ($actions as $action)
        {
            $result->set($action->name, $user->authorise($action->name, $assetName));
        }

        return $result;
    }
}