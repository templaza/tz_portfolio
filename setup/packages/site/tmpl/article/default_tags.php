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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$params = $this -> item -> params;
$tmpl           = Factory::getApplication() -> input -> getString('tmpl');

if($params -> get('show_tags',1)){
    if($this -> listTags){
?>
<div class="tpp-item-tags mb-3">
    <span class="title"><?php echo Text::_('COM_TZ_PORTFOLIO_TAG_TITLE');?></span>
    <?php foreach($this -> listTags as $i => $item){ ?>
        <span class="tag-list<?php echo $i;  echo !$params -> get('link_tag', 0)?' label label-default badge badge-secondary':'';
        ?>" itemprop="keywords">
            <?php if($params -> get('link_tag', 1)){ ?>
              <a href="<?php echo $item -> link; ?>"<?php
              if(isset($tmpl) AND !empty($tmpl)): echo ' target="_blank"'; endif;?>><?php } ?>#<?php
                  echo $item -> title;?><?php if($params -> get('link_tag', 1)){ ?></a><?php } ?>
        </span>
    <?php } ?>
</div>
<?php }
}
?>
