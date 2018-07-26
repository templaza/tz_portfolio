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

// no direct access
defined('_JEXEC') or die;

if($item = $this -> item):
    if(isset($item -> extrafields) && !empty($item -> extrafields)):
?>
<ul class="tpp-extrafield__list">
<?php foreach($item -> extrafields as $field):?>
    <li class="tpp-extrafield__item">
        <?php if($field -> hasTitle()):?>
        <div class="tpp-extrafield__label"><?php echo $field -> getTitle();?></div>
        <?php endif;?>
        <div class="tpp-extrafield__value pull-left">
            <?php echo $field -> getListing();?>
        </div>
    </li>
<?php endforeach;?>
</ul>
<?php
    endif;
endif;