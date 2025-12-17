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

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

$params = $this -> item -> params;
if($params -> get('show_parent_category',1)) {
    ?>
    <?php if ($this->item->parent_slug != '1:root'){ ?>
        <span class="tpp-item-parent-category">
    <?php
    $title = $this->escape($this->item->parent_title);
    $url = $title;
    $target = '';
    if (isset($tmpl) AND !empty($tmpl)) {
        $target = ' target="_blank"';
    }
    $url = '<a href="' . $this->item->parent_link . '"' . $target . ' itemprop="genre">' . $title . '</a>';
    ?>
    <?php if ($params->get('link_parent_category', 1) and $this->item->parent_slug){ ?>
        <?php echo Text::sprintf('COM_TZ_PORTFOLIO_PARENT', $url); ?>
    <?php }else{ ?>
        <?php echo Text::sprintf('COM_TZ_PORTFOLIO_PARENT', '<span itemprop="genre">' . $title . '</span>'); ?>
    <?php } ?>
</span>
    <?php }
}