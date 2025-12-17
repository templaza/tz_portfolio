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

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
?>
<div class="tpButton">
    <a href="<?php echo $this->button['link']; ?>">
        <?php echo HTMLHelper::_('image',
            'com_tz_portfolio/' . $this->button['image'], $this->button['text'],
            ['title' => $this->button['text']], true); ?>
        <div>
            <?php echo $this->button['text']; ?>
        </div>
    </a>
</div>


