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

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$params = &$this -> params;
?>
<div id="portfolio" class="<?php echo $this->pageclass_sfx;?> uk-margin-medium-bottom">
    <?php if ($params->get('show_page_heading', 1)){ ?>
        <h1 class="page-heading">
            <?php echo $this->escape($params->get('page_heading')); ?>
        </h1>
    <?php } ?>

    <?php
    // Display category about when the portfolio has filter category by category id
    echo $this -> loadTemplate('category_about');
    ?>

    <?php
    // Display tag about when the portfolio has filter tag by tag id
    echo $this -> loadTemplate('tag_about');
    ?>

    <?php
    // Display author about when the portfolio has filter user by user id
    echo $this -> loadTemplate('author_about');
    ?>

    <?php if($params -> get('use_filter_first_letter',0)){?>
        <div class="tp-letters uk-text-center">
            <?php echo $this -> loadTemplate('letters');?>
        </div>
    <?php } ?>

    <div data-uk-filter="target: .js-tp-portfolio" data-uk-margin>

        <?php if($params -> get('tz_show_filter',1)){ ?>
        <div class="js-tp-filter">
            <div class="filter-title"><?php echo Text::_('COM_TZ_PORTFOLIO_FILTER');?></div>
            <div class="uk-grid-small uk-grid-divider uk-child-width-auto" data-uk-grid>
                <div>
                    <ul class="uk-subnav uk-subnav-pill js-tp-filter-list" data-uk-margin>
                        <li class="uk-active" data-uk-filter-control><a href="#"><?php echo Text::_('JALL');?></a></li>
                        <?php
                        switch($params -> get('tz_filter_type','tags')){
                            default:
                            case 'tags':
                                echo $this -> loadTemplate('filter_tags');
                                break;
                            case 'categories':
                                echo $this -> loadTemplate('filter_categories');
                                break;
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if($params -> get('show_sort',0) && ($sortfields = $params -> get('sort_fields',array('date','hits','title')))){
            $sort   = $params -> get('orderby_sec','rdate');
            ?>
        <div>
            <div class="filter-title"><?php echo Text::_('COM_TZ_PORTFOLIO_SORT_BY')?></div>
            <div class="uk-grid-small uk-grid-divider uk-child-width-auto" data-uk-grid>
                <div>
                    <ul class="uk-subnav uk-subnav-pill" data-uk-margin>
                        <?php
                        $active = ' class="uk-active"';
                        foreach($sortfields as $sortfield):
                            switch($sortfield):
                                case 'title':
                                    ?>
                                    <li data-uk-filter-control="sort: data-name<?php
                                    echo $sort == 'ralpha'?'; order: desc':'';?>"<?php
                                    echo ($sort == 'alpha' || $sort == 'ralpha')?$active:''?>>
                                        <a href="#title"><?php echo Text::_('COM_TZ_PORTFOLIO_TITLE');?></a>
                                    </li>
                                    <?php
                                    break;
                                case 'date':
                                    ?>
                                    <li data-uk-filter-control="sort: data-date<?php
                                    echo $sort == 'rdate'?'; order: desc':'';?>"<?php
                                    echo ($sort == 'date' || $sort == 'rdate')?$active:''?>>
                                        <a href="#date"><?php echo Text::_('COM_TZ_PORTFOLIO_DATE');?></a>
                                    </li>
                                    <?php
                                    break;
                                case 'hits':
                                    ?>
                                    <li data-uk-filter-control="sort: data-hits<?php
                                    echo $sort == 'rhits'?'; order: desc':'';?>"<?php
                                    echo ($sort == 'hits' || $sort == 'rhits')?$active:''?>>
                                        <a href="#hits"><?php echo Text::_('JGLOBAL_HITS');?></a>
                                    </li>
                                    <?php
                                    break;
                            endswitch;
                        endforeach;
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php echo $this -> loadTemplate('item');?>
    </div>
    
    <?php if($params -> get('tz_portfolio_layout', 'ajaxButton') == 'default'):?>
        <?php if (($params->def('show_pagination', 1) == 1  || ($params->get('show_pagination', 1) == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
            <div class="tp-pagination uk-margin-top">
                <?php if ($params->def('show_pagination_results', 1)) : ?>
                    <p class="counter mr-2 mb-0">
                        <?php echo $this->pagination->getPagesCounter(); ?>
                    </p>
                <?php endif; ?>

                <?php echo $this->pagination->getPagesLinks(); ?>
            </div>
        <?php endif;?>
    <?php endif;?>

    <?php if($params -> get('tz_portfolio_layout', 'ajaxButton') == 'ajaxButton'
        || $params -> get('tz_portfolio_layout', 'ajaxButton') == 'ajaxInfiScroll'):?>
        <?php echo $this -> loadTemplate('infinite_scroll');?>
    <?php endif;?>
</div>
