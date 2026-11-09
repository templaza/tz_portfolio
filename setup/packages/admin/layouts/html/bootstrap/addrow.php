<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    Sonny

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://tzportfolio.com

# Technical Support:  Forum - https://github.com/templaza/tz_portfolio/issues/

# Family website: http://templaza.com

# Family Support: Forum - https://templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

$gridrow    = $displayData['gridrow'];
$class      = isset($displayData['class'])?$displayData['class']:'';
$attribute  = isset($displayData['attribute'])?$displayData['attribute']:'';
?>
<div class="<?php echo $gridrow.($class?' '.$class:''); ?>"<?php echo $attribute?' '.$attribute:'';?>>
