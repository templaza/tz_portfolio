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

if($params -> get('show_modify_date',1)){
    if(isset($this -> item -> modified)) {
    ?>
    <div class="tpModified">
        <i class="tp tp-pencil-square-o"></i>
        <time itemprop="dateModified" datetime="<?php echo JHtml::_('date', $this->item->modified, JText::_('DATE_FORMAT_FILTER_DATE')); ?>">
        <?php echo JHtml::_('date', $this->item->modified, JText::_('DATE_FORMAT_LC')); ?>
        </time>
    </div>
    <?php
    }
}