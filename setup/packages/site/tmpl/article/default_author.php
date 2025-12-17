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

use Joomla\CMS\Router\Route;
use Joomla\CMS\Menu\SiteMenu;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$params = $this -> item -> params;
if($params -> get('show_author',1)) {
?>

<?php if (!empty($this->item->author )){ ?>
    <span class="tpp-item-created-by" itemprop="author" itemscope itemtype="http://schema.org/Person">
    <?php $author = $this->item->created_by_alias ? $this->item->created_by_alias : $this->item->author; ?>
    <?php $author = '<span itemprop="name">' . $author . '</span>'; ?>
    <?php if ($params->get('link_author', 1)){ ?>
        <?php
        $target = '';
        if(isset($tmpl) AND !empty($tmpl)) {
            $target = ' target="_blank"';
        }
        $needle = 'index.php?option=com_tz_portfolio&view=users&id=' . $this->item->created_by;
        $menu   = new SiteMenu();
        $item   = $menu -> getItems('link', $needle, true);
        if(!$userItemid = '&Itemid='.$this -> FindUserItemId($this->item->created_by)){
            $userItemid = null;
        }
        $cntlink = $needle.$userItemid;
        ?>
        <?php echo Text::sprintf('COM_TZ_PORTFOLIO_WRITTEN_BY', HTMLHelper::_('link',
            Route::_($cntlink), $author,$target.' itemprop="url"')); ?>
    <?php }else{ ?>
        <?php echo Text::sprintf('COM_TZ_PORTFOLIO_WRITTEN_BY', $author); ?>
    <?php } ?>
</span>
<?php }
} ?>