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
$params = &$this -> params;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.framework');
?>

<?php if($items):?>

    <div class="tpUser <?php echo $this -> pageclass_sfx;?>" itemscope itemtype="http://schema.org/Blog">
        <?php if ($params->get('show_page_heading', 1)) : ?>
            <h1 class="page-heading">
                <?php echo $this->escape($params->get('page_heading')); ?>
            </h1>
        <?php endif; ?>

        <?php echo  $this -> loadTemplate('author');?>

        <?php if($params -> get('show_limit_box',0)):?>
            <form action="<?php echo JRoute::_('index.php?option=com_tz_portfolio_plus&amp;view=users&amp;created_by='.JFactory::getApplication() -> input -> getCmd('created_by').'&amp;Itemid='.JFactory::getApplication() -> input -> getInt('Itemid'));?>"
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
            $col        = $params -> get('article_columns', 1);
            $cols       = TZ_Portfolio_PlusContentHelper::getBootstrapColumns($col);
            $colCounter = 0;

            foreach($items as $i => &$item):
                $this -> item   = &$item;
                ?>
                <div class="TzItem"
                     itemprop="blogPost" itemscope itemtype="http://schema.org/BlogPosting">
                    <?php echo $this -> loadTemplate('item');?>
                </div>
                <?php
                $colCounter++;
                if($i % $col == 0){
                    $colCounter = 0;
                }
            endforeach;?>
        </div>

        <?php if (($this->params->def('show_pagination', 1) == 1
                || ($this->params->get('show_pagination') == 2))
            && ($this->pagination->get('pages.total') > 1)) : ?>
            <div class="pagination">
                <?php echo $this->pagination->getPagesLinks(); ?>

                <?php  if ($this->params->def('show_pagination_results', 1)) : ?>
                    <p class="TzCounter">
                        <?php echo $this->pagination->getPagesCounter(); ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif;?>
    </div>
<?php endif;?>

