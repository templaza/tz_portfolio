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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

if (!$this->print) :
    $doc    = Factory::getDocument();

    $lists  = $this -> itemsRelated;
    // Create shortcuts to some parameters.
    $params		= $this->item->params;
    $tmpl       = null;
    if($lists):
        if($params -> get('show_related_article',1)):
?>
<div class="tp-item-related">
    <?php if($params -> get('show_related_heading',1)):?>
        <?php
            $title    = Text::_('COM_TZ_PORTFOLIO_RELATED_ARTICLE');
            if($params -> get('related_heading')){
                $title  = $params -> get('related_heading');
            }
        ?>
        <h3 class="title"><?php echo $title;?></h3>
    <?php endif;?>
    <ul class="">

    <?php foreach($lists as $i => $itemR){
        ?>
    <li class="tp-item-related__item<?php if($i == 0) echo ' first'; if($i == count($lists) - 1) echo ' last';?> mb-2">
        <?php
        if($itemR->event->onContentDisplayMediaType && !empty($itemR->event->onContentDisplayMediaType)) {
            echo $itemR->event->onContentDisplayMediaType;
        }

        if(!isset($itemR -> mediatypes) || (isset($itemR -> mediatypes) && !in_array($itemR -> type,$itemR -> mediatypes))){
            if($params -> get('show_related_title',1)){
        ?>
        <a href="<?php echo $itemR -> link;?>"
           class="TzTitle">
            <?php echo $itemR -> title;?>
        </a>
        <?php }
        }?>
    </li>

    <?php }?>
    </ul>
</div>
 
        <?php endif;?>
    <?php endif;?>
<?php endif;?>