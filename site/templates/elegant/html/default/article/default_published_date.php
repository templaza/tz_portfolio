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
defined('_JEXEC') or die('Restricted access');

$params = $this -> item -> params;

?>
<div class="tpDate">
    <i class="tp tp-calendar"></i>
    <time itemprop="datePublished" datetime="<?php echo JHtml::_('date', $this->item->publish_up, JText::_('DATE_FORMAT_FILTER_DATE')); ?>">
        <?php echo JHtml::_('date', $this->item->publish_up, JText::_('DATE_FORMAT_LC')); ?>
    </time>
</div>