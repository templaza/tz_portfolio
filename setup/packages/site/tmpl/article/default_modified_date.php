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
use Joomla\CMS\HTML\HTMLHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioTemplate;

$params = $this -> item -> params;

if($params -> get('show_modify_date',1)){
    if(isset($this -> item -> modified)) {
        $tpParams   = TZ_PortfolioTemplate::getTemplate(true) -> params;
    ?>
<span class="tpp-item-modified">
    <?php echo Text::sprintf('COM_TZ_PORTFOLIO_LAST_UPDATED', HTMLHelper::_('date',
        $this->item->modified, $tpParams -> get('date_format', 'l, d F Y H:i'))); ?>
</span>
    <?php
    }
}