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

$form   = $this -> form;
$group  = 'media.'.$this -> _name;
$image  = null;

if($this -> item && isset($this -> item -> media)){
    $image  = $this -> item -> media;
    if(isset($image[$this -> _name])) {
        $image = $image[$this -> _name];
    }
}
die(__FILE__);
?>
<div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text"><?php echo $form -> getLabel('url',$group);?></label>
    <div class="uk-form-controls">
        <?php echo $form -> getInput('url',$group);?>
        <?php
        if($image && isset($image['url']) && !empty($image['url'])){
            ?>
            <div class="uk-margin">
                <?php
                echo $form -> getInput('url_remove',$group,$image['url']);
                ?>
            </div>
            <?php
        }
        ?>
    </div>
</div>
<div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text"><?php echo $form -> getLabel('url_detail',$group);?></label>
    <div class="uk-form-controls"><?php echo $form -> getInput('url_detail',$group);?>
        <?php
        if($image && isset($image['url_detail']) && !empty($image['url_detail'])){
            ?>
            <div class="uk-margin">
                <?php
                echo $form -> getInput('url_detail_remove',$group);
                ?>
            </div>
            <?php
        }
        ?>
    </div>
</div>
<div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text"><?php echo $form -> getLabel('caption',$group);?></label>
    <div class="uk-form-controls">
        <?php echo $form -> getInput('caption',$group);?>
    </div>
</div>