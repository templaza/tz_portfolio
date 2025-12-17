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

$params = $this -> item -> params;
if($params -> get('show_category',1)){
?>

<span class="tpp-item-category">
    <?php
    $title = $this->escape($this->item->category_title);
    $url    = $title;
    $target = '';
    if(isset($tmpl) AND !empty($tmpl)):
        $target = ' target="_blank"';
    endif;
    $url = '<a href="'.$this -> item -> category_link.'"'.$target.' itemprop="genre">'.$title.'</a>';

    $lang_text  = 'COM_TZ_PORTFOLIO_CATEGORY';
    ?>
    <?php if(isset($this->item -> second_categories) && $this->item -> second_categories
        && count($this -> item -> second_categories)){
        $lang_text  = 'COM_TZ_PORTFOLIO_CATEGORIES';
        foreach($this->item -> second_categories as $j => $scategory){
            if($j <= count($this->item -> second_categories)) {
                $title  .= ', ';
                $url    .= ', ';
            }
            $url    .= '<a href="' . $scategory -> link
                . '" itemprop="genre">' . $scategory -> title . '</a>';
            $title  .= $this->escape($scategory -> title);
        }
    }?>

    <?php if ($params->get('link_category',1) and $this->item->catslug) : ?>
        <?php echo Text::sprintf($lang_text, $url); ?>
    <?php else : ?>
        <?php echo Text::sprintf($lang_text,  '<span itemprop="genre">' . $title . '</span>'); ?>
    <?php endif; ?>
</span>
<?php } ?>