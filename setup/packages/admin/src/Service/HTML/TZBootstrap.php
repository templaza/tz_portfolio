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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Service\HTML;

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;

class TZBootstrap{

    protected static $loaded = array();

    public static function addRow($options = array()){
        $options['gridrow'] = 'row';

        if(isset($options['attribute']) && is_array($options['attribute'])){
            $options['attribute']   = implode(' ', $options['attribute']);
        }

        return LayoutHelper::render('html.bootstrap.addrow', $options);
    }

    public static function endRow(){
        return LayoutHelper::render('html.bootstrap.endrow', null);
    }

    public static function startContainer($gridColumn, $sidebar = false, $options = array()){
        $opt['sidebar']     = $sidebar;
        $opt['gridColumn']  = $gridColumn;

        if(isset($options['attribute']) && is_array($options['attribute'])){
            $opt['attribute']   = implode(' ', $options['attribute']);
        }

        if(isset($options['responsive'])){
            $opt['responsive']   = $options['responsive'];
        }

        if(isset($options['containerclass'])){
            $opt['containerclass']   = $options['containerclass'];
        }

        return LayoutHelper::render('html.bootstrap.startcontainer', $opt);
    }

    public static function endContainer(){
        return LayoutHelper::render('html.bootstrap.endcontainer', null);
    }

}