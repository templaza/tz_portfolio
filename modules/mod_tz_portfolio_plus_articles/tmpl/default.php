<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author   TuanNATemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

if ($list):
    if($params -> get('load_style', 0)) {
        $doc    = JFactory::getDocument();
        $doc->addStyleSheet(JUri::base(true) . '/modules/mod_tz_portfolio_plus_articles/css/basic.css');
    }
?>
<ul class="tz_portfolio_plus_articles<?php echo $moduleclass_sfx; ?>">
    <?php foreach ($list as $i => $item) : ?>
    <li<?php if ($i == 0) echo ' class="first"'; if ($i == count($list) - 1) echo ' class="last"' ?>>
    <?php if(!isset($item -> mediatypes) || (isset($item -> mediatypes) && !in_array($item -> type,$item -> mediatypes))){?>
        <?php if ($params->get('show_title',1)) {?>
        <h3 class="title"><a href="<?php echo $item->link?>"><?php echo $item->title?></a></h3>
        <?php } ?>
    <?php }
    if(isset($item->event->onContentDisplayMediaType)){
    ?>

        <div class="tzpp_media"><?php echo $item->event->onContentDisplayMediaType;?></div>
    <?php }

        if(!isset($item -> mediatypes) || (isset($item -> mediatypes) && !in_array($item -> type,$item -> mediatypes))){

            //Call event onContentBeforeDisplay on plugin
            if(isset($item -> event -> beforeDisplayContent)) {
                echo $item->event->beforeDisplayContent;
            }

            if ($params->get('show_introtext',1)) {
            ?>
            <div class="description"><?php echo $item->introtext;?></div>
            <?php }
            if($params -> get('show_author', 1) or $params->get('show_created_date', 1)
                or $params->get('show_hit', 1) or $params->get('show_tag', 1)
                or $params->get('show_category', 1)
                or !empty($item -> event -> beforeDisplayAdditionInfo)
                or !empty($item -> event -> afterDisplayAdditionInfo)) {
                ?>
                <div class="muted item-meta">

                    <?php
                    if (isset($item->event->beforeDisplayAdditionInfo)) {
                        echo $item->event->beforeDisplayAdditionInfo;
                    }
                    if ($params->get('show_author', 1)) { ?>
                        <div class="tz_created_by"><span
                                class="text"><?php echo JText::_('MOT_TZ_PORTFOLIO_PLUS_ARTICLE_TZ_CREATED_BY'); ?>
                    </span><a href="<?php echo $item->author_link; ?>"><?php echo $item->user_name; ?></a></div>
                    <?php } ?>

                    <?php if ($params->get('show_created_date', 1)) { ?>
                        <div class="tz_date">
                    <span class="text"><?php echo JText::_('MOT_TZ_PORTFOLIO_PLUS_ARTICLE_TZ_DATE'); ?>
                    </span><?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC1')); ?></div>
                    <?php } ?>

                    <?php if ($params->get('show_hit', 1)) { ?>
                        <div class="tz_hit">
                            <span
                                class="text"><?php echo JText::_('MOT_TZ_PORTFOLIO_PLUS_ARTICLE_TZ_HIT'); ?></span><?php echo $item->hits; ?>
                        </div>
                    <?php }

                    if ($params->get('show_tag', 1)) {
                        if (isset($tags[$item->content_id])) {
                            ?>
                            <div class="tz_tag">
                                <span
                                    class="text"><?php echo JText::_('MOT_TZ_PORTFOLIO_PLUS_ARTICLE_TZ_TAGS'); ?></span>
                                <?php foreach ($tags[$item->content_id] as $t => $tag) { ?>
                                    <a href="<?php echo $tag->link; ?>"><?php echo $tag->title; ?></a>
                                    <?php if ($t != count($tags[$item->content_id]) - 1) {
                                        echo ', ';
                                    }
                                } ?>
                            </div>
                        <?php }
                    }

                    if ($params->get('show_category', 1)) {
                        if (isset($categories[$item->content_id]) && $categories[$item->content_id]) {
                            ?>
                            <div class="tz_categories">
                                <span
                                    class="text"><?php echo JText::_('MOT_TZ_PORTFOLIO_PLUS_ARTICLE_TZ_CATEGORIES'); ?></span>
                                <?php foreach ($categories[$item->content_id] as $c => $category) { ?>
                                    <a href="<?php echo $category->link; ?>"><?php echo $category->title; ?></a>
                                    <?php if ($c != count($categories[$item->content_id]) - 1) {
                                        echo ', ';
                                    }
                                } ?>
                            </div>
                        <?php }
                    }

                    if (isset($item->event->afterDisplayAdditionInfo)) {
                        echo $item->event->afterDisplayAdditionInfo;
                    } ?>
                </div>

                <?php
            }
            if(isset($item -> event -> contentDisplayListView)) {
                echo $item->event->contentDisplayListView;
            }

            if($params -> get('show_readmore',1)){
        ?>
        <a href="<?php echo $item->link?>"
           class="btn btn-primary readmore"><?php echo $params -> get('readmore_text','Read More');?></a>
        <?php }
        }
        ?>
    </li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>