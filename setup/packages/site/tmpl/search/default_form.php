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

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$lang           = Factory::getLanguage();
$upper_limit    = $lang->getUpperLimitSearchWord();

$params = $this -> params;
//HTMLHelper::_('formbehavior.chosen', 'select');

$menu       = Factory::getApplication() -> getMenu();
$mnuActive  = $menu -> getActive()
?>

<form id="searchForm" class="uk-form-stacked" data-uk-margin action="<?php
echo Route::_('index.php?option=com_tz_portfolio&Itemid='.$mnuActive -> id);?>" method="post">

    <div class="uk-search uk-search-default uk-width-expand">
        <input type="search" name="searchword" placeholder="<?php echo Text::_('COM_TZ_PORTFOLIO_SEARCH_KEYWORD'); ?>"
               id="search-searchword" size="30" class="uk-search-input uk-width-expand" maxlength="<?php echo $upper_limit; ?>"
               value="<?php echo $this->escape($this->state -> get('filter.searchword')); ?>" />
        <button name="search" class="uk-search-icon-flip" data-uk-search-icon></button>
<!--        <button name="search" onclick="this.form.submit()" class="uk-button uk-button-primary"-->
<!--                data-uk-tooltip="title: --><?php //echo HTMLHelper::tooltipText('JSEARCH_FILTER_SUBMIT');
//                ?><!--"><i class="fas fa-search"></i> --><?php //echo Text::_('JSEARCH_FILTER_SUBMIT'); ?><!--</button>-->
    </div>

    <?php if($params -> get('show_search_category',0)) { ?>
        <div >
            <label class="uk-form-label"
                   for="catid"><?php echo Text::_('COM_TZ_PORTFOLIO_FILTER_CATEGORY'); ?></label>
            <div class="uk-form-controls">
                <select name="id" class="uk-select" id="catid">
                    <?php echo HTMLHelper::_('select.options', $this->catOptions, 'value', 'text',
                        $this->state->get('filter.category_id')); ?>
                </select>
            </div>
        </div>
        <?php
    }
    if($advFilterFields = $this -> advFilterFields){
        if($params -> get('show_s_gfield_title', 1)) {
            $adv    = $advFilterFields;
            $first  = array_shift($adv);
            echo HTMLHelper::_('bootstrap.startTabSet', 'tz-search-', array('active' => 'tz-search-group-'.$first -> id));
        }
        foreach($advFilterFields as $i => $group) {
            if(isset($group -> fields) && $group -> fields){
                if($params -> get('show_s_gfield_title', 1)){
                    echo HTMLHelper::_('bootstrap.addTab', 'tz-search-', 'tz-search-group-'.$group -> id, $group -> name);
                }
                foreach($group -> fields as $field){
                    if($searchinput = $field -> getSearchInput()){
                        ?>
                        <div>
                            <?php echo $searchinput;?>
                        </div>
                        <?php
                    }
                }
                if($params -> get('show_s_gfield_title', 1)){
                    echo HTMLHelper::_('bootstrap.endTab');
                }
            }
        }

        if($params -> get('show_s_gfield_title', 1)) {
            echo HTMLHelper::_('bootstrap.endTabSet');
        }
    }
    ?>
    <input type="hidden" name="task" value="search.search" />
</form>
