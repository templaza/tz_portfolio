<?php
/*------------------------------------------------------------------------
# plg_extravote - ExtraVote Plugin
# ------------------------------------------------------------------------
# author    Joomla!Vargas
# copyright Copyright (C) 2010 joomla.vargas.co.cr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://joomla.vargas.co.cr
# Technical Support:  Forum - http://joomla.vargas.co.cr/forum
-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

if(isset($item) && $item){
    if($params -> get('show_cat_vote', 1)){
        $count  = 0;
        $rating_sum = $item -> rating_sum;
        $rating_count   = $item -> rating_count;
        if($rating_sum != 0){
            $count  = $rating_sum / $rating_count;
        }
        ?>

        <div class="muted TzVote">
            <span><?php echo JText::_('PLG_CONTENT_VOTE_RATING');?></span>
    <span class="content_rating"  itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
        <span class="rating">
            <a href="javascript:void(null)"
               onclick="javascript:JVXVote(this,<?php echo $item -> id ?>,5,<?php echo $rating_sum; ?>,
               <?php echo $rating_count; ?>,<?php echo $params -> get('show_counter', 1);?>,
                   1);"
               title="<?php echo JTEXT::_('PLG_CONTENT_VOTE_VERY_POOR');?>"
               class="rating-item muted icon-star<?php echo (($count > 4)?' voted':'')?>">
                <?php if($count > 4 && $count < 5){?>
                    <span class="icon-star" style="width: <?php echo round(100 - ($count - 4)*100);?>%"></span>
                <?php }?>
            </a><a href="javascript:void(null)"
               onclick="javascript:JVXVote(this,<?php echo $item -> id; ?>,4,<?php echo $rating_sum; ?>,
               <?php echo $rating_count; ?>,<?php echo $params -> get('show_counter', 1);?>,
                   1);"
               title="<?php echo JTEXT::_('PLG_CONTENT_VOTE_POOR');?>"
               class="rating-item muted icon-star<?php echo (($count > 3)?' voted':'')?>">
                <?php if( $count > 3 && $count < 4){?>
                    <span class="icon-star" style="width: <?php echo round(100 - ($count - 3)*100);?>%"></span>
                <?php }?>
            </a><a href="javascript:void(null)"
               onclick="javascript:JVXVote(this,<?php echo $item -> id ?>,3,<?php echo $rating_sum; ?>,
               <?php echo $rating_count; ?>,<?php echo $params -> get('show_counter', 1);?>,
                   1);"
               title="<?php echo JTEXT::_('PLG_CONTENT_VOTE_REGULAR');?>"
               class="rating-item muted icon-star<?php echo (($count > 2)?' voted':'')?>">
                <?php if( $count > 2 && $count < 3){?>
                    <span class="icon-star" style="width: <?php echo round(100 - ($count - 2)*100);?>%"></span>
                <?php }?>
            </a><a href="javascript:void(null)"
               onclick="javascript:JVXVote(this,<?php echo $item -> id ?>,2,<?php echo $rating_sum; ?>,
               <?php echo $rating_count; ?>,<?php echo $params -> get('show_counter', 1);?>,
                   1);"
               title="<?php echo JTEXT::_('PLG_CONTENT_VOTE_GOOD');?>"
               class="rating-item muted icon-star<?php echo (($count > 1)?' voted':'')?>">
                <?php if( $count > 1 && $count < 2){?>
                    <span class="icon-star" style="width: <?php echo round(100 - ($count - 1)*100);?>%"></span>
                <?php }?>
            </a><a href="javascript:void(null)"
               onclick="javascript:JVXVote(this,<?php echo $item -> id ?>,1,<?php echo $rating_sum; ?>,
               <?php echo $rating_count; ?>,<?php echo $params -> get('show_counter', 1);?>,
                   1);"
               title="<?php echo JTEXT::_('PLG_CONTENT_VOTE_VERY_GOOD');?>"
               class="rating-item muted icon-star<?php echo (($count > 0)?' voted':'')?>">
                <?php if( $count > 0 && $count < 1){?>
                    <span class="icon-star" style="width: <?php echo round(100 - ($count - 0)*100);?>%"></span>
                <?php }?>
            </a>
        </span>
        <span id="TzVote_<?php echo $item -> id;?>" class="TzVote-count" itemprop="ratingCount">
            <small>
                <?php if($params -> get('show_counter', 1)) {
                    echo '('.(($rating_count != 1) ? JTEXT::sprintf('PLG_CONTENT_VOTE_VOTES', $rating_count)
                            : JTEXT::sprintf('PLG_CONTENT_VOTE_VOTE', $rating_count)).')';
                }?></small>
        </span>
        <meta itemprop="worstRating" content="0" />
        <meta itemprop="bestRating" content="5" />
        <meta itemprop="ratingValue" content="<?php echo $rating_sum;?>"/>
    </span>
        </div>
    <?php }
}