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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Library\Image;

// no direct access
use PHPImageWorkshop\ImageWorkshop;

defined('_JEXEC') or die;

\JLoader::registerNamespace('PHPImageWorkshop', COM_TZ_PORTFOLIO_ADMIN_PATH.'/vendor/PHPImageWorkshop');

class ImageWaterMark extends ImageWorkshop{

    const POSITION_TOP_LEFT     = 'LT'; // Left Top
    const POSITION_TOP          = 'MT'; // Middle Top
    const POSITION_TOP_RIGHT    = 'RT'; // Right Top
    const POSITION_LEFT         = 'LM'; // Left Middle
    const POSITION_CENTER       = 'MM'; // Middle Middle
    const POSITION_RIGHT        = 'RM'; // Right middle
    const POSITION_BOTTOM_LEFT  = 'LB'; // Left Bottom
    const POSITION_BOTTOM       = 'MB'; // Middle Bottom
    const POSITION_BOTTOM_RIGHT = 'RB'; // Right Bottom
}