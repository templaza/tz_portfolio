<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

//no direct access
defined('_JEXEC') or die();

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

$params = &$this -> params;
?>
<div class="tzpp_bootstrap3 tz-search<?php echo $this->pageclass_sfx;?>">
    <?php if ($params->get('show_page_heading', 1)){ ?>
    <h1 class="page-heading">
        <?php echo $this->escape($params->get('page_heading')); ?>
    </h1>
    <?php } ?>

    <?php
    echo $this->loadTemplate('form');
    ?>

    <div class="total">
        <?php echo JText::plural('COM_TZ_PORTFOLIO_PLUS_SEARCH_KEYWORD_N_RESULTS', '<span class="badge badge-info">'
            . $this -> total . '</span>');?>
    </div>

    <?php if($this -> items){
        echo $this -> loadTemplate('results');
    }else{
        echo $this->loadTemplate('error');
    } ?>
</div>

