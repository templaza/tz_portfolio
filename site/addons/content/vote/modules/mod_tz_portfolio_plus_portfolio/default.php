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

        if($module && !$this -> head[$module -> module.$module -> id]) {
            $doc = JFactory::getDocument();
            $doc->addScriptDeclaration('(function($){
                $(document).ready(function(){
                    $(".js-tpp-addon-vote__module'.$module -> id.'").tzPortfolioPlusAddOnVote({
                        mainSelector: ".js-tpp-addon-vote__module'.$module -> id.'",
                        itemSelector: ".rating > a",
                        votedTemplate: "<span class=\"icon-star\"></span>",
                        notification:{
                            layout: "' . $params->get('ct_vote_notice_layout', 'growl') . '",
                            effect: "'.$params->get('ct_vote_notice_effect', 'scale').'",
                            ttl: "' . $params->get('ct_vote_notice_ttl', 3000) . '"
                        }
                    });
                });
            })(jQuery);');
        }
        ?>

<div class="muted TzVote">
    <span><?php echo JText::_('PLG_CONTENT_VOTE_RATING');?></span>
    <span class="content_rating js-tpp-addon-vote__module<?php echo $module?$module -> id:'';
    ?>"  itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
        <span class="rating">
            <a href="javascript:;"
               data-rating-article-id="<?php echo $item -> id; ?>"
               data-rating-point="5"
               data-rating-total="<?php echo $rating_sum; ?>"
               data-rating-count="<?php echo $rating_count; ?>"
               title="<?php echo JText::_('PLG_CONTENT_VOTE_VERY_GOOD');?>"
               class="rating-item muted icon-star<?php echo (($count > 4)?' voted':'')?>">
                <?php if($count > 4 && $count < 5){?>
                    <span class="icon-star" style="width: <?php echo round(100 - ($count - 4)*100);?>%"></span>
                <?php }?>
            </a><a href="javascript:;"
                   data-rating-article-id="<?php echo $item -> id; ?>"
                   data-rating-point="4"
                   data-rating-total="<?php echo $rating_sum; ?>"
                   data-rating-count="<?php echo $rating_count; ?>"
                   title="<?php echo JText::_('PLG_CONTENT_VOTE_GOOD');?>"
               class="rating-item muted icon-star<?php echo (($count > 3)?' voted':'')?>">
                <?php if( $count > 3 && $count < 4){?>
                    <span class="icon-star" style="width: <?php echo round(100 - ($count - 3)*100);?>%"></span>
                <?php }?>
            </a><a href="javascript:;"
                   data-rating-article-id="<?php echo $item -> id; ?>"
                   data-rating-point="3"
                   data-rating-total="<?php echo $rating_sum; ?>"
                   data-rating-count="<?php echo $rating_count; ?>"
               title="<?php echo JText::_('PLG_CONTENT_VOTE_REGULAR');?>"
               class="rating-item muted icon-star<?php echo (($count > 2)?' voted':'')?>">
                <?php if( $count > 2 && $count < 3){?>
                    <span class="icon-star" style="width: <?php echo round(100 - ($count - 2)*100);?>%"></span>
                <?php }?>
            </a><a href="javascript:;"
                   data-rating-article-id="<?php echo $item -> id; ?>"
                   data-rating-point="2"
                   data-rating-total="<?php echo $rating_sum; ?>"
                   data-rating-count="<?php echo $rating_count; ?>"
                   title="<?php echo JText::_('PLG_CONTENT_VOTE_POOR');?>"
               class="rating-item muted icon-star<?php echo (($count > 1)?' voted':'')?>">
                <?php if( $count > 1 && $count < 2){?>
                    <span class="icon-star" style="width: <?php echo round(100 - ($count - 1)*100);?>%"></span>
                <?php }?>
            </a><a href="javascript:;"
                   data-rating-article-id="<?php echo $item -> id; ?>"
                   data-rating-point="1"
                   data-rating-total="<?php echo $rating_sum; ?>"
                   data-rating-count="<?php echo $rating_count; ?>"
                   title="<?php echo JText::_('PLG_CONTENT_VOTE_VERY_POOR');?>"
               class="rating-item muted icon-star<?php echo (($count > 0)?' voted':'')?>">
                <?php if( $count > 0 && $count < 1){?>
                    <span class="icon-star" style="width: <?php echo round(100 - ($count - 0)*100);?>%"></span>
                <?php }?>
            </a>
        </span>
        <?php if($params -> get('show_counter', 1)) {?>
        <span class="tpp-counter js-tpp-counter" itemprop="ratingCount">
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