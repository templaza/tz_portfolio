<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2018 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

if($item = $this -> item):
    if(isset($item -> extrafields) && !empty($item -> extrafields)):
        $params         = $item -> params;
        ?>
        <div class="tp-extrafield__list" >
            <?php foreach($item -> extrafields as $field):?>
                <div class="tp-extrafield__item uk-grid-small" data-uk-grid>
                    <?php if($field -> hasTitle()):?>
                    <div class="tp-extrafield__label uk-width-expand" data-uk-leader>
                    <?php endif;?>
                        <?php if($params -> get('show_date_field_image', $field -> hasImage())){ ?>
                            <span class="tp-extrafield__image">
                            <img src="<?php echo $field -> getImage();?>" alt="<?php
                            echo $field -> getTitle();?>"/></span>
                        <?php }?>
                    <?php if($field -> hasTitle()):?>
                        <?php echo $field -> getTitle();?>
                    </div>
                    <?php endif;?>
                    <div class="tp-extrafield__value uk-width-auto">
                        <?php echo $field -> getListing();?>
                    </div>
                </div>
            <?php endforeach;?>
        </div>
    <?php
    endif;
endif;