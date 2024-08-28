<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2019 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

if(isset($steps) && $steps){
?>
    <?php foreach ($steps as $i => $step) { ?>
        <li class="<?php echo $step -> className?>">
            <div class="circle">
                <span class="number"><?php echo $i + 1; ?></span>
                <span class="icon-checkmark mr-0 complete"></span>
            </div>
            <div class="title"><?php echo Text::_($step -> title); ?></div>
        </li>
    <?php } ?>
<?php } ?>
<li class="<?php echo $active == 'complete' ? ' current' : '';?>">
    <div class="circle">
        <span class="number"><?php echo count($steps) + 1; ?></span>
        <span class="icon-checkmark mr-0 complete"></span>
    </div>
    <div class="title"><?php echo Text::_('COM_TZ_PORTFOLIO_COMPLETED'); ?></div>
</li>
