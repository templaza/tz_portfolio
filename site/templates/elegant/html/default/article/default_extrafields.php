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
<div class="tpArticleExtraField">
    <ul class="list-striped tpExtraFields">
    <?php foreach($item -> extrafields as $field):?>
        <li class="tp_extrafield-item">
            <?php if($field -> hasTitle()):?>
            <div class="tp_extrafield-label"><?php echo $field -> getTitle();?></div>
            <?php endif;?>
            <div class="tp_extrafield-value pull-right">
                <?php echo $field -> getOutput();?>
            </div>
        </li>
    <?php endforeach;?>
    </ul>
</div>
<?php
    endif;
endif;