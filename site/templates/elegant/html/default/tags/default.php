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
defined('_JEXEC') or die('Restricted access');

$items  = $this -> items;
$params = &$this -> tagsParams;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
//JHtml::_('behavior.tooltip');
?>

<?php if($items):?>
    <div class="tpTag <?php echo $this -> pageclass_sfx;?>" itemscope itemtype="http://schema.org/Blog">
        <?php if ($params->get('show_page_heading', 1)) : ?>
            <h1 class="page-heading">
                <?php echo $this->escape($params->get('page_heading')); ?>
            </h1>
        <?php endif; ?>

        <?php if($params -> get('show_tag_title_heading',1)):?>
            <h2 class="TzTagHeading">
                <i class="tp tp-hashtag" aria-hidden="true"></i>
                <?php echo $this -> tag -> title;?>
            </h2>
        <?php endif;?>

        <?php if($params -> get('use_filter_first_letter',0)):?>
            <div class="TzLetters">
                <?php echo $this -> loadTemplate('letters');?>
            </div>
        <?php endif;?>

        <?php if($params -> get('show_limit_box',0)):?>
            <form action="<?php echo JRoute::_('index.php?option=com_tz_portfolio_plus&view=tags&id='.JFactory::getApplication() -> input -> getInt('id').'&Itemid='.JFactory::getApplication() -> input -> getInt('Itemid'));?>"
                  id="adminForm"
                  name="adminForm"
                  method="post">
                <div class="display-limit">
                    <fieldset class="filters">
                        <?php echo  JText::_('JGLOBAL_DISPLAY_NUM');?>
                        <?php echo $this -> pagination -> getLimitBox();?>
                    </fieldset>
                </div>
            </form>
        <?php endif;?>

        <div class="TzItemsRow">
            <?php
            foreach($items as $i => &$row):
                $this -> item = &$row;
                ?>
                <div class="TzItem"
                     itemprop="blogPost" itemscope itemtype="http://schema.org/BlogPosting">
                    <?php echo $this -> loadTemplate('item');?>
                </div>
            <?php endforeach;?>
        </div>

        <?php if (($params->def('show_pagination', 1) == 1  || ($params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
            <div class="pagination">
                <?php echo $this->pagination->getPagesLinks(); ?>

                <?php  if ($params->def('show_pagination_results', 1)) : ?>
                    <p class="TzCounter">
                        <?php echo $this->pagination->getPagesCounter(); ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif;?>
    </div>
<?php endif;?>
