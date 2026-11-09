<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://tzportfolio.com

# Technical Support:  Forum - https://github.com/templaza/tz_portfolio/issues/

# Family website: http://templaza.com

# Family Support: Forum - https://templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

$params = $this -> item -> params;
if($params -> get('show_title',1)) {
    $htag    = $this->params->get('show_page_heading', 1) ? 'h2' : 'h1';
    ?>
    <<?php echo $htag; ?> class="tp-item-title uk-article-title" itemprop="name">
    <?php echo $this->escape($this->item->title); ?>
    </<?php echo $htag; ?>>
    <?php
}
//Call event onContentAfterTitle on plugin
echo $this->item->event->afterDisplayTitle;
?>