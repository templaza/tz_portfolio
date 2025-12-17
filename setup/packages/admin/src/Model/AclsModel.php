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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Model;

// no direct access
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die;

class AclsModel extends ListModel {
    public function getItems(){
        $sections   = json_decode(COM_TZ_PORTFOLIO_ACL_SECTIONS);
        $items      = array();
        foreach($sections as $i => $section){
            $item               = new \stdClass();
            $item -> section    = $section;
            switch ($section){
                default:
                    $item -> title  = Text::_('COM_TZ_PORTFOLIO_'.strtoupper($section).'S');
                    break;
                case 'category':
                    $item -> title  = Text::_('COM_TZ_PORTFOLIO_CATEGORIES');
                    break;
                case 'group':
                    $item -> title  = Text::_('COM_TZ_PORTFOLIO_FIELD_GROUPS');
                    break;
                case 'style':
                    $item -> title  = Text::_('COM_TZ_PORTFOLIO_TEMPLATE_STYLES');
                    break;

            }
            $items[]        = $item;
        }
        return $items;
    }
}