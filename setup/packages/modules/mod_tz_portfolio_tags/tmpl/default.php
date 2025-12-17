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

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

if($list){

    $moduleclass_sfx    = htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_COMPAT, 'UTF-8');

    $bootstrap4 = ($params -> get('enable_bootstrap',0) && $params -> get('bootstrapversion', 3) == 4);
    $input      = Factory::getApplication() -> input;
    $menuActive = $params -> get('menu_active', 'auto');
    $tid        = $input -> getInt('tid');
    $option     = $input -> getInt('option');
    $itemid     = $input -> getInt('Itemid');
    $tidActive  = ($option == 'com_tz_portfolio' && $tid && $itemid == $menuActive)?$tid:0;
?>
<ul class="mod_tz_tag<?php echo $moduleclass_sfx;?> uk-grid-small" data-uk-grid>
    <?php if($params -> get('show_tag_all', 0)){ ?>
    <li class="tag_item<?php echo ($option == 'com_tz_portfolio' && !$tid && $itemid == $menuActive)?' active':''; ?>">
        <?php if ($params -> get('enable_link', 1)) { ?>
        <a href="<?php echo $tagAllLink; ?>">
        <?php }else{ ?>
            <span>
        <?php } ?>
            <?php echo $params -> get('tag_all_text', 'All'); ?><span class="count">(<?php echo $articleCount;?>)</span>
    <?php if (!$params -> get('enable_link', 1)) { ?>
        </span>
    <?php }else{ ?>
        </a>
    <?php } ?>
    </li>
    <?php } ?>
<?php foreach ($list as $tag) { ?>
    <li class="tag_item<?php echo $tidActive == $tag -> id?' active':''; ?>">
        <?php
        $fontSize   = null;
        if($params -> get('enable_min_max_font_size', 1)){
            $fontSize   = 'font-size: '.($tag -> size / 10).'px';
        }
        if ($params -> get('enable_link', 1)) { ?>
        <a href="<?php echo $tag->link; ?>" class="uk-link"<?php
        echo $fontSize?' style="'.$fontSize.'"':'';?>><?php
            echo $tag->title; ?><?php
            if($params -> get('show_article_counter', 1)){
                ?><span class="count">(<?php echo $tag -> article_count;?>)</span>
            <?php } ?>
        </a>
        <?php } else { ?>
        <span<?php echo $fontSize?' style="'.$fontSize.'"':'';?>><?php echo $tag->title;
            ?><?php if($params -> get('show_article_counter', 1)){
                ?><span class="count">(<?php echo $tag -> article_count;?>)</span>
            <?php } ?>
        </span>
        <?php } ?>
    </li>
<?php } ?>

</ul>
<?php } ?>