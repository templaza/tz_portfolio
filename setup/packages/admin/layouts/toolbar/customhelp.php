<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2017 tzportfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - http://tzportfolio.com/forum

# Family website: http://www.templaza.com

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

<button onclick="<?php echo $doTask; ?>" rel="help" class="btn btn-secondary btn-small btn-sm"<?php
echo COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE?' id="toolbar-'.$displayData['id'].'"':'';?>>
    <span class="tpb tp-<?php echo $icon; ?>" aria-hidden="true"></span>
    <?php echo $text; ?>
</button>
