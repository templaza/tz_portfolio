<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    Sonny

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

$doTask = $displayData['doTask'];
$text   = $displayData['text'];
$icon   = $displayData['icon'];
if(!$icon){
    $icon   = 'question';
}

?>
<joomla-toolbar-button id="toolbar-<?php echo $displayData['id']; ?>">
    <button onclick="<?php echo $doTask; ?>" rel="help" class="btn btn-secondary btn-small btn-sm">
        <span class="fab fa-<?php echo $icon; ?>" aria-hidden="true"></span>
        <?php echo $text; ?>
    </button>
</joomla-toolbar-button>
