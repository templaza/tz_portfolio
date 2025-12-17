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

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

$params     = $this -> item -> params;
if($params -> get('show_date_tags', 1) && $this -> item && isset($this -> item -> tags)):
    echo Text::sprintf('COM_TZ_PORTFOLIO_TAGS','');
    ?>
    <?php foreach($this -> item -> tags as $i => $item): ?>
    <a href="<?php echo $item ->link; ?>"><?php echo $item -> title;?></a><?php if($i != count($this -> item -> tags) - 1):?><span><?php echo ','?></span><?php endif;?>
<?php endforeach;?>
<?php endif;?>