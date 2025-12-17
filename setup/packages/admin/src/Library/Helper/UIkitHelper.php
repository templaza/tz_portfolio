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

class UIkitHelper{

    protected static $mapCol= array(
        1 =>  '1-6',
        2 => '1-5',
        3 => '1-4',
        4 => '1-3',
        5 => '2-5',
        6 => '1-2',
        7 => '3-5',
        8 => '2-3',
        9 => '3-4',
        10=> '4-5',
        11=> '5-6',
        12=> '1-1'
    );

    public static function syncColumnLayoutToUIkit(&$layout){
        if(!$layout){
            return $layout;
        }

        $layout = is_string($layout)?json_decode($layout):$layout;

        $oldWidthKeys   = array('col-xs', 'col-sm', 'col-md', 'col-lg');

        foreach ($layout as &$element){
            $elKeys = array_keys((array)$element);

            foreach ($elKeys as $eVal){
                if(!in_array($eVal, $oldWidthKeys)){
                    continue;
                }

                $element -> {$eVal} = self::mapBootstrapColumnToUIkit($element -> {$eVal});
            }

            if(isset($element -> children)){
                self::syncColumnLayoutToUIkit($element -> children);
            }
        }
    }

    /**
     * Map Bootstrap column width to UIkit
     * Bootstrap : UIkit
     * 1 : 1-6, 2 : 1-5, 3 : 1-4, 4 : 1-3, 5 : 2-5, 6 : 1-2, 7 : 3-5, 8 : 2-3, 9 : 3-4, 10: 4-5, 11: 5-6, 12: 1-1
     * */
    public static function mapBootstrapColumnToUIkit($bootCol){
        $map    = self::$mapCol;

        if(isset($map[$bootCol])){
            return $map[$bootCol];
        }
        return $bootCol;
    }

    /**
     * Map UIkit column width to Bootstrap
     * UIkit : Bootstrap
     * 1-1: 12, 1-2: 6, 1-3: 4, 2-3: 8, 1-4: 3, 3-4: 9, 1-5: 2, 2-5: 5, 3-5: 7, 4-5: 10, 1-6: 1, 5-6: 11
     * */
    public static function mapUIkitColumnToBootstrap($UIkitCol){
        $map    = array_flip(self::$mapCol);
        if(isset($map[$UIkitCol])){
            return $map[$UIkitCol];
        }
        return $UIkitCol;
    }

    /**
     * Map Bootstrap container with to UIkit
     * */
    public static function mapBootstrapContainerWidth($bootContainer){
        $UIkitContainer = '';

        switch($bootContainer){
            case 'container-fluid':
                $UIkitContainer = 'uk-container uk-container-expand';
                break;
            case 'container':
                $UIkitContainer = 'uk-container';
                break;
        }

        return $UIkitContainer;
    }
}