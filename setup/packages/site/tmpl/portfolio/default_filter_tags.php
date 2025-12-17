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

?>
<?php if($this -> itemTags):?>
    <?php foreach($this -> itemTags as $item):?>
    <li data-uk-filter-control="[data-filter-tag*='<?php echo $item -> id;
    ?>']">
        <a href="#<?php echo $item -> alias; ?>" data-tp-filter-tag-id="<?php echo $item -> id; ?>">
            <?php echo $item -> title;?>
        </a>
    </li>
    <?php endforeach;?>
<?php endif;?>
