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

use Joomla\CMS\Language\Text;

$params     = $this -> item -> params;
if($params -> get('show_cat_tags',0) && $this -> item && isset($this -> item -> tags)){
    ?>
    <div class="tpp-item-tags uk-margin-bottom">
        <span data-uk-icon="icon: tag; ratio: 0.75"></span>
        <?php echo Text::sprintf('COM_TZ_PORTFOLIO_TAGS',''); ?>
        <?php foreach($this -> item -> tags as $i => $item): ?>
            <?php if($params -> get('cat_link_tag', 1)){ ?>
                <a href="<?php echo $item ->link; ?>" class="uk-link-text">#<?php echo $item -> title;?></a>
            <?php }else{ ?>
                <span><?php echo $item -> title;?></span>
            <?php }?>
            <?php if($i != count($this -> item -> tags) - 1):?><span><?php echo ','?></span><?php endif;?>
        <?php endforeach;?>
    </div>
<?php } ?>