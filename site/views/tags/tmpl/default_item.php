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

// no direct access
defined('_JEXEC') or die;

// Create a shortcut for params.
$params = &$this->item->params;

$images = json_decode($this->item->images);
$canEdit	= $this->item->params->get('access-edit');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
//JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework');

$blogLink   = $this -> item ->link;
$item   = $this -> item;
?>

<?php if ($this->item->state == 0) : ?>
<div class="system-unpublished">
<?php endif; ?>

<?php
// Display media from plugin of group tz_portfolio_plus_mediatype
echo $this -> loadTemplate('media');

// Start Description and some info
if(!isset($item -> mediatypes) || (isset($item -> mediatypes) && !in_array($item -> type,$item -> mediatypes))):
?>

<?php if ($params->get('show_tag_print_icon', 0) || $params->get('show_tag_email_icon', 0) || $canEdit) : ?>
<div class="TzIcon">
    <div class="btn-group pull-right">
        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="javascript: void(0)">
            <i class="icon-cog"></i> <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
            <?php if ($params->get('show_tag_print_icon', 0)) : ?>
            <li class="print-icon"> <?php echo JHtml::_('icon.print_popup', $this->item, $params); ?> </li>
            <?php endif; ?>
            <?php if ($params->get('show_tag_email_icon', 0)) : ?>
            <li class="email-icon"> <?php echo JHtml::_('icon.email', $this->item, $params); ?> </li>
            <?php endif; ?>
            <?php if ($canEdit) : ?>
            <li class="edit-icon"> <?php echo JHtml::_('icon.edit', $this->item, $params); ?> </li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<?php if ($params->get('show_tag_title',1)) : ?>
<h3 class="TzBlogTitle" itemprop="name">
    <?php if ($params->get('cat_link_titles',1) && $params->get('access-view')) : ?>
        <a<?php if($params -> get('tz_use_lightbox') == 1) echo ' class="fancybox fancybox.iframe"';?>
            href="<?php echo $blogLink; ?>" itemprop="url">
        <?php echo $this->escape($this->item->title); ?></a>
    <?php else : ?>
        <?php echo $this->escape($this->item->title); ?>
    <?php endif; ?>
    <?php if($this -> item -> featured == 1):?>
    <span class="label label-important TzFeature"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_FEATURE');?></span>
    <?php endif;?>
</h3>
<?php endif; ?>


    <?php
    //Show vote
    echo $item -> event -> contentDisplayVote;
    ?>

<?php if (!$params->get('show_tag_intro',1)) : ?>
    <?php
        //Call event onContentAfterTitle on plugin
        echo $this->item->event->afterDisplayTitle;
    ?>
<?php endif; ?>

<?php
if(!isset($item -> mediatypes) || (isset($item -> mediatypes) && !in_array($item -> type,$item -> mediatypes))):
 if ($params->get('show_tag_author',1) or $params->get('show_tag_category',1)
        or $params->get('show_tag_create_date',1) or $params->get('show_tag_modify_date',0)
        or $params->get('show_tag_publish_date',0) or $params->get('show_tag_parent_category',0)
        or $params->get('show_tag_hits',1) or $params->get('show_tag_tags',1)
        or !empty($item -> event -> beforeDisplayAdditionInfo)
        or !empty($item -> event -> afterDisplayAdditionInfo)) :
?>
<div class="muted TzArticleBlogInfo">

    <?php echo $item -> event -> beforeDisplayAdditionInfo;?>

    <?php if ($params->get('show_tag_create_date', 1)) : ?>
    <span class="TzBlogCreate">
      <span class="date" itemprop="dateCreated"> <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_CREATED_DATE_ON', JHtml::_('date', $this->item->created, JText::_('DATE_FORMAT_LC2'))); ?></span>
    </span>
    <?php endif; ?>

    <?php if ($params->get('show_tag_author',1) && !empty($this->item->author )) : ?>
    <div class="TzBlogCreatedby" itemprop="author" itemscope itemtype="http://schema.org/Person">
        <?php $author =  $this->item->author; ?>
        <?php $author = ($this->item->created_by_alias ? $this->item->created_by_alias : $author);?>
        <?php $author = '<span itemprop="name">' . $author . '</span>'; ?>

        <?php if ($params->get('cat_link_author', 1)):?>
            <?php 	echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_WRITTEN_BY' ,
             JHtml::_('link', $this -> item -> author_link, $author, array('itemprop' => 'url'))); ?>
        <?php else :?>
            <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_WRITTEN_BY', $author); ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($params->get('show_tag_hits', 1)) : ?>
    <div class="TzBlogHits">
        <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_ARTICLE_HITS', $this -> item ->hits); ?>
        <meta itemprop="interactionCount" content="UserPageVisits:<?php echo $this -> item->hits; ?>" />
    </div>
    <?php endif; ?>

    <?php if ($params->get('show_tag_category',1)) : ?>
    <div class="TzBlogCategory">
        <?php $title = $this->escape($this->item->category_title);
                $url = '<a href="' . $this -> item -> category_link . '" itemprop="genre">' . $title . '</a>'; ?>

        <?php if(isset($this->item -> second_categories) && $this->item -> second_categories){
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

        <?php if ($params->get('cat_link_category',1)) : ?>
            <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_CATEGORY', $url); ?>
            <?php else : ?>
            <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_CATEGORY', '<span itemprop="genre">' . $title . '</span>'); ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($params->get('show_tag_parent_category', 0) && $this -> item->parent_id != 1) : ?>
    <div class="TzParentCategoryName">
        <?php $title = $this->escape($this -> item->parent_title);
        $url = '<a href="' . JRoute::_(TZ_Portfolio_PlusHelperRoute::getCategoryRoute($this -> item->parent_id)) . '" itemprop="genre">' . $title . '</a>'; ?>
        <?php if ($params->get('cat_link_parent_category', 1)) : ?>
            <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_PARENT', $url); ?>
        <?php else : ?>
            <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_PARENT', '<span itemprop="genre">' . $title . '</span>'); ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if($params -> get('show_tag_tags',1)):
        echo $this -> loadTemplate('tags');
    endif;
    ?>

    <?php if ($params->get('show_tag_modify_date',0)) : ?>
    <div class="TzBlogModified" itemprop="dateModified">
    <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_LAST_UPDATED', JHtml::_('date', $this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
    </div>
    <?php endif; ?>

    <?php if ($params->get('show_tag_publish_date',0)) : ?>
    <div class="TzBlogPublished" itemprop="datePublished">
    <?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_PUBLISHED_DATE_ON', JHtml::_('date', $this->item->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
    </div>
    <?php endif; ?>

    <?php echo $item -> event -> afterDisplayAdditionInfo; ?>

</div>
<?php endif; ?>

<?php
    //Call event onContentBeforeDisplay and onTZPluginBeforeDisplay on plugin
    echo $this->item->event->beforeDisplayContent;
?>

<?php if($this -> item -> introtext):?>
    <div class="TzDescription" itemprop="description">
    <?php echo $this->item->introtext; ?>
    </div>
<?php endif;?>

<?php if ($params->get('show_tag_readmore',1) && $this->item->readmore) {
    if ($params->get('access-view')) :
        $link = $blogLink;
    else :
        $menu = JFactory::getApplication()->getMenu();
        $active = $menu->getActive();
        $itemId = $active->id;
        $link1 = JRoute::_('index.php?option=com_users&amp;view=login&amp;Itemid=' . $itemId);

        $returnURL = $blogLink;

        $link = new JURI($link1);
        $link->setVar('return', base64_encode($returnURL));
    endif;
?>

<?php echo $item -> event -> contentDisplayListView; ?>

    <?php echo $this -> loadTemplate('extrafields');?>

    <?php if($params -> get('show_tag_readmore',1)):?>
    <a href="<?php echo $link; ?>"
     class="btn btn-default TzReadmore<?php echo $params -> get('tz_use_lightbox', 1)?' fancybox fancybox.iframe':'';?>"
     >
    <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_READ_MORE');?>
    </a>
    <?php endif;?>
<?php } ?>

        <?php
            //Call event onContentAfterDisplay on plugin
            echo $this->item->event->afterDisplayContent;
        ?>

    <?php if ($this->item->state == 0) : ?>
    </div>
    <?php endif; ?>
    
<?php endif;
endif;
// End Description and some info
?>