<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2015 templaza.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

if(isset($this -> item) && $this -> item){
    $params = $this -> params;

    if($params -> get('show_vote', 1)){
        $count  = 0;
        $rating_sum = $this -> item -> rating_sum;
        $rating_count   = $this -> item -> rating_count;
        if($rating_sum != 0){
            $count  = $rating_sum / $rating_count;
        }

        $this -> document -> addScriptDeclaration('
        (function($){
            $(document).ready(function(){
                $(".js-tpp-addon-vote__article").tzPortfolioPlusAddOnVote({
                    mainSelector: ".js-tpp-addon-vote__article",
                    itemSelector: ".rating > a",
                    counterSelector: ".js-tpp-counter",
                    votedTemplate: "<span class=\"tp tp-star\"></span>",
                    notification:{
                        layout: "'.$params -> get('ct_vote_notice_layout', 'growl').'",
                        effect: "'.$params -> get('ct_vote_notice_effect', 'scale').'",
                        ttl: "'.$params -> get('ct_vote_notice_ttl', 3000).'"
                    },
                    ajaxComplete: function(result, el, ratingCount, ratingPoint){                    
                        if (result.success == true && !$.isEmptyObject(result.data)) {
                            var rItem  = el.find(this.itemSelector),
                            rVotedIcon = $(this.votedTemplate?this.votedTemplate:"<span></span>");
                            
                            el.find(this.itemSelector)
                            .not(":lt("+(5 - Math.ceil(ratingPoint))+")")
                            .removeClass("tp-star-o").addClass("tp-star");
                            
                            if((ratingPoint - parseInt(ratingPoint)) > 0) {                            
                                rVotedIcon.css("width", ((100 - Math.round((ratingPoint - parseInt(ratingPoint)) * 100))) + "%");
                                rItem.eq(5 - Math.ceil(ratingPoint)).html(rVotedIcon)
                                .removeClass("tp-star").addClass("tp-star-o");
                            }
                        }
                    }
                });
            });
        })(jQuery);
        ');
?>

<div class="tpVote">
    <span><?php echo JText::_('PLG_CONTENT_VOTE_RATING');?></span>
    <span class="content_rating js-tpp-addon-vote__article"  itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
        <span class="rating"><?php
            $class  = ' tp-star-o';
            if($count > 4){
                if($count <=4 || $count >= 5){
                    $class = ' tp-star';
                }
                $class  .= ' voted';
            }
            ?>
            <a href="javascript:;"
               data-rating-article-id="<?php echo $this -> item -> id; ?>"
               data-rating-point="5"
               data-rating-total="<?php echo $rating_sum; ?>"
               data-rating-count="<?php echo $rating_count; ?>"
               title="<?php echo JText::_('PLG_CONTENT_VOTE_VERY_GOOD');?>"
                class="rating-item tp<?php echo $class; ?>">
                <?php if($count > 4 && $count < 5){?>
                <span class="tp tp-star" style="width: <?php echo round(($count - 4)*100);?>%"></span>
                <?php }?>
            </a><?php
            $class  = ' tp-star-o';
            if($count > 3){
                if($count <=3 || $count >= 4){
                    $class = ' tp-star';
                }
                $class  .= ' voted';
            }
            ?><a href="javascript:;"
                   data-rating-article-id="<?php echo $this -> item -> id; ?>"
                   data-rating-point="4"
                   data-rating-total="<?php echo $rating_sum; ?>"
                   data-rating-count="<?php echo $rating_count; ?>"
                   title="<?php echo JText::_('PLG_CONTENT_VOTE_GOOD');?>"
                class="rating-item tp<?php echo $class; ?>">
                <?php if( $count > 3 && $count < 4){?>
                    <span class="tp tp-star" style="width: <?php echo round(($count - 3)*100);?>%"></span>
                <?php }?>
            </a><?php
            $class  = ' tp-star-o';
            if($count > 2){
                if($count <=2 || $count >= 3){
                    $class = ' tp-star';
                }
                $class  .= ' voted';
            }
            ?><a href="javascript:;"
                   data-rating-article-id="<?php echo $this -> item -> id; ?>"
                   data-rating-point="3"
                   data-rating-total="<?php echo $rating_sum; ?>"
                   data-rating-count="<?php echo $rating_count; ?>"
                   title="<?php echo JText::_('PLG_CONTENT_VOTE_REGULAR');?>"
                class="rating-item tp<?php echo $class; ?>">
                <?php if( $count > 2 && $count < 3){?>
                <span class="tp tp-star" style="width: <?php echo round(($count - 2)*100);?>%"></span>
                <?php }?>
            </a><?php
            $class  = ' tp-star-o';
            if($count > 1){
                if($count <=1 || $count >= 2){
                    $class = ' tp-star';
                }
                $class  .= ' voted';
            }
            ?><a href="javascript:;"
                   data-rating-article-id="<?php echo $this -> item -> id; ?>"
                   data-rating-point="2"
                   data-rating-total="<?php echo $rating_sum; ?>"
                   data-rating-count="<?php echo $rating_count; ?>"
                   title="<?php echo JText::_('PLG_CONTENT_VOTE_POOR');?>"
                class="rating-item tp<?php echo $class; ?>">
                <?php if( $count > 1 && $count < 2){?>
                <span class="tp tp-star" style="width: <?php echo round(($count - 1)*100);?>%"></span>
                <?php }?>
            </a><?php
            $class  = ' tp-star-o';
            if($count > 0){
                if($count <=0 || $count >= 1){
                    $class = ' tp-star';
                }
                $class  .= ' voted';
            }
            ?><a href="javascript:;"
                   data-rating-article-id="<?php echo $this -> item -> id; ?>"
                   data-rating-point="1"
                   data-rating-total="<?php echo $rating_sum; ?>"
                   data-rating-count="<?php echo $rating_count; ?>"
                   title="<?php echo JText::_('PLG_CONTENT_VOTE_VERY_POOR');?>"
                class="rating-item tp<?php echo $class; ?>">
                <?php if( $count > 0 && $count < 1){?>
                <span class="tp tp-star" style="width: <?php echo round(($count - 0)*100);?>%"></span>
                <?php }?>
            </a>
        </span>

        <?php if($params -> get('show_counter', 1)) {?>
        <span id="TzVote_<?php echo $this -> item -> id;?>" class="js-tpp-counter" itemprop="ratingCount">
            <small><?php
                if(($params -> get('unrated', 1) && $rating_count == 0) || $rating_count) {
                    echo JText::plural('PLG_CONTENT_VOTE_VOTES', $rating_count);
                }
                ?></small>
        </span>
        <?php } ?>

        <meta itemprop="worstRating" content="0" />
        <meta itemprop="bestRating" content="5" />
        <meta itemprop="ratingValue" content="<?php echo $rating_sum;?>"/>
    </span>
</div>
<?php }
}