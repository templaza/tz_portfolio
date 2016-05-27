<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    TemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$document->addStyleSheet(JUri::base(true).'/modules/mod_tz_portfolio_plus_tags/css/style.css');
?>
<ul class="mod_tz_tag">
<?php foreach ($list as $tag) { ?>
    <?php if ($params -> get('enable_link', 1)) { ?>
    <li class="tag_item"><a href="<?php echo $tag->link; ?>"
                            style="font-size: <?php echo $tag->size / 10; ?>px"><?php echo $tag->title; ?></a>
    </li>
    <?php } else { ?>
    <li class="tag_item"><span style="font-size: <?php echo $tag->size / 10; ?>px"><?php echo $tag->title; ?></span></li>
    <?php }
} ?>

</ul>