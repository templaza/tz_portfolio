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
defined('_JEXEC') or die('Restricted access');

$item           = $this -> item_author;
$authorParams   = $this -> params;
$tmpl           = JFactory::getApplication() -> input -> getString('tmpl');
?>

<?php if($authorParams -> get('show_cat_user',1)):
    if(!empty($item) && $item -> author):
        $target = '';
        if(isset($tmpl) AND !empty($tmpl)):
            $target = ' target="_blank"';
        endif;
        ?>
        <div class="tz_portfolio_plus_user">
            <h3 class="TzArticleAuthorTitle"><?php echo JText::_('ARTICLE_AUTHOR_TITLE'); ?></h3>
            <div class="media">
                <div class="AuthorAvatar pull-left<?php echo (!$item -> avatar)?' author-avatar-default':'';?>">
                    <?php if($item -> avatar){?>
                        <img src="<?php echo JUri::root().$item -> avatar;?>" alt="<?php echo $item -> author;?>"/>
                    <?php }else{?>
                        <span class="glyphicon glyphicon-user author-icon"></span>
                    <?php }?>
                </div>
                <div class="media-body">
                    <h4 class="media-heading"><?php echo $item -> author;?></h4>

                    <?php if($authorParams -> get('show_cat_gender_user', 1)):?>
                        <?php if($item -> gender):?>
                            <div class="muted AuthorGender">
                                <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_GENDER');?>
                                <span><?php echo ($item -> gender == 'm')?JText::_('COM_TZ_PORTFOLIO_PLUS_MALE'):JText::_('COM_TZ_PORTFOLIO_PLUS_FEMALE');?></span>
                            </div>
                        <?php endif;?>
                    <?php endif;?>

                    <?php if($authorParams -> get('show_cat_email_user', 1)):?>
                        <?php if($item -> email):?>
                            <div class="muted AuthorEmail">
                                <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_EMAIL');?>
                                <span><?php echo $item -> email;?></span>
                            </div>
                        <?php endif;?>
                    <?php endif;?>

                    <?php if($authorParams -> get('show_cat_url_user',1) AND !empty($item -> url)):?>
                        <div class="muted AuthorUrl">
                            <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_WEBSITE');?>
                            <a href="<?php echo $item -> url;?>" target="_blank">
                                <?php echo $item -> url;?>
                            </a>
                        </div>
                    <?php endif;?>

                    <?php if($authorParams -> get('show_cat_description_user', 1)  AND !empty($item -> description)):?>
                        <div class="AuthorDescription">
                            <?php echo $item -> description; ?>
                        </div>
                    <?php endif;?>

                    <?php if(!empty($item -> social_links)):?>
                        <div class="AuthorSocial">
                            <?php if(!empty($item -> social_links)):
                                $social_links   = $item -> social_links;
                                if(count($social_links)):
                                    ?>
                                    <?php foreach($social_links as $social_link):?>
                                    <a class="TzSocialLink" href="<?php echo $social_link -> url;?>"<?php echo $target?>>
                                        <?php if($social_link -> icon_class && !empty($social_link -> icon_class)){?>
                                            <span class="<?php echo $social_link -> icon_class;?>"></span>
                                        <?php }elseif($social_link -> icon && !empty($social_link -> icon)){?>
                                            <img src="<?php echo JUri::root().$social_link -> icon; ?>"
                                                 alt="<?php echo $social_link -> title;?>"
                                                 title="<?php echo $social_link -> title;?>"/>
                                        <?php }else{
                                            echo $social_link -> title;
                                        }?>

                                    </a>
                                <?php endforeach;?>
                                <?php endif;
                            endif;
                            ?>
                        </div>
                    <?php endif;?>
                </div>
            </div>

        </div>
    <?php endif;?>
<?php endif; ?>