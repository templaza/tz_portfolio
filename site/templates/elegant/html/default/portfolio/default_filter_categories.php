<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

//no direct access
defined('_JEXEC') or die();
?>
<?php if($this -> itemCategories):?>
    <?php foreach($this -> itemCategories as $item):?>
        <a href="#<?php echo str_replace(' ','-',$item -> title)?>"
           class="btn btn-default btn-sm"
           data-option-value=".<?php echo 'category'.$item -> id;?>" data-order="<?php echo $item -> order;?>">
            <?php echo $item -> title;?>
        </a>
    <?php endforeach;?>
<?php endif;?>
