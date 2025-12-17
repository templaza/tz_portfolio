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

//no direct access
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

$params = &$this -> params;
?>
<div class="tz-search<?php echo $this->pageclass_sfx;?>" data-uk-margin="margin: uk-margin-top">
    <?php if ($params->get('show_page_heading', 1)){ ?>
    <h1 class="page-heading">
        <?php echo $this->escape($params->get('page_heading')); ?>
    </h1>
    <?php } ?>

    <?php
    echo $this->loadTemplate('form');
    ?>

    <div class="tp-search-total">
        <?php echo Text::plural('COM_TZ_PORTFOLIO_SEARCH_KEYWORD_N_RESULTS', '<span class="uk-badge">'
            . $this -> total. '</span>');?>
    </div>

    <?php if($this -> items){
        echo $this -> loadTemplate('results');
    }else{
        echo $this->loadTemplate('error');
    } ?>
</div>

