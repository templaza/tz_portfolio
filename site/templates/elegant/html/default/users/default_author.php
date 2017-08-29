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
        $arrName        =   explode(' ',$item->author);
        $avaName        =   '';
        for ($i=0; $i<count($arrName); $i++){
            if ($word = trim($arrName[$i])) {
                $avaName.=$word[0];
            }
        }
        $target = '';
        if(isset($tmpl) AND !empty($tmpl)):
            $target = ' target="_blank"';
        endif;
        ?>
        <div class="tpArticleAuthor">
            <h3 class="tpArticleAuthorTitle reset-heading"><?php echo JText::_('ARTICLE_AUTHOR_TITLE'); ?></h3>
            <div class="media">
                <div class="AuthorAvatar pull-left<?php echo (!$item -> avatar)?' tp-avatar-default tpavatar--bg-'.rand(1,5):'';?>">
                    <?php if($item -> avatar){?>
                        <img src="<?php echo JUri::root().$item -> avatar;?>" alt="<?php echo $item -> author;?>"/>
                    <?php }else{?>
                        <span class="tpSymbol"><?php echo $avaName; ?></span>
                    <?php }?>
                </div>
                <div class="tpAuthorContainer">
                    <div class="cell-col">
                        <h4 class="media-heading reset-heading" itemprop="name">
                            <a href="<?php echo $item -> author_link;?>" rel="author"<?php echo $target;?>>
                                <?php echo $item -> author;?>
                            </a>
                        </h4>
                        <div class="general_info">
                            <?php if($authorParams -> get('show_gender_user', 1)):?>
                                <?php if($item -> gender):?>
                                    <span class="muted tpAuthorInfo AuthorGender">
                                <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_GENDER');?>
                                        <span><?php echo ($item -> gender == 'm')?JText::_('COM_TZ_PORTFOLIO_PLUS_MALE'):JText::_('COM_TZ_PORTFOLIO_PLUS_FEMALE');?></span>
                            </span>
                                <?php endif;?>
                            <?php endif;?>

                            <?php if($authorParams -> get('show_email_user', 1)):?>
                                <?php if($item -> email):?>
                                    <span class="muted tpAuthorInfo AuthorEmail">
                                <i class="tp tp-envelope-o" aria-hidden="true"></i>
                                <span><?php echo $item -> email;?></span>
                            </span>
                                <?php endif;?>
                            <?php endif;?>

                            <?php if($authorParams -> get('show_url_user',1) AND !empty($item -> url)):?>
                                <span class="muted tpAuthorInfo AuthorUrl">
                            <i class="fa fa-globe" aria-hidden="true"></i>
                            <a href="<?php echo $item -> url;?>" target="_blank">
                                <?php echo $item -> url;?>
                            </a>
                            </span>
                            <?php endif;?>
                        </div>
                        <?php if ($item  -> twitter || $item  -> facebook || $item  -> googleplus || $item  -> instagram) : ?>
                            <div class="social_link">
                                <?php if($item  -> twitter):?>
                                    <span class="muted tpAuthorInfo SocialLink">
                                <a href="<?php echo $item  -> twitter; ?>" title="Twitter"><i class="tp tp-twitter" aria-hidden="true"></i></a>
                            </span>
                                <?php endif;?>
                                <?php if($item  -> facebook):?>
                                    <span class="muted tpAuthorInfo SocialLink">
                                <a href="<?php echo $item  -> facebook; ?>" title="Facebook"><i class="tp tp-facebook-official" aria-hidden="true"></i></a>
                            </span>
                                <?php endif;?>
                                <?php if($item  -> googleplus):?>
                                    <span class="muted tpAuthorInfo SocialLink">
                                <a href="<?php echo $item  -> googleplus; ?>" title="Google Plus"><i class="tp tp-google-plus-square" aria-hidden="true"></i></a>
                            </span>
                                <?php endif;?>
                                <?php if($item  -> instagram):?>
                                    <span class="muted tpAuthorInfo SocialLink">
                                <a href="<?php echo $item  -> instagram; ?>" title="Instagram"><i class="tp tp-instagram" aria-hidden="true"></i></a>
                            </span>
                                <?php endif;?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
                <div class="clr"></div>
                <?php if($authorParams -> get('show_description_user', 1)  AND !empty($item -> description)):?>
                    <div class="AuthorDescription">
                        <?php echo $item -> description; ?>
                    </div>
                <?php endif;?>

                <?php if(!empty($item  -> social_links)):?>
                    <div class="AuthorSocial">
                        <?php if(!empty($item  -> social_links)):
                            $social_links   = $item  -> social_links;
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
                <div class="clr"></div>
            </div>
        </div>
    <?php endif;?>
<?php endif; ?>