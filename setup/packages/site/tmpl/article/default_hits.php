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
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$params = $this -> item -> params;

if($params -> get('show_hits',1)){
?>

<span class="tpp-item-hit">
    <?php echo Text::sprintf('COM_TZ_PORTFOLIO_ARTICLE_HITS',$this->item->hits); ?>
    <meta itemprop="interactionCount" content="UserPageVisits:<?php echo $this->item->hits; ?>" />
</span>
<?php }?>