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

$item           = $this -> item;
$authorParams   = $this -> item -> params;
$tmpl           = JFactory::getApplication() -> input -> getString('tmpl');

if($authorParams -> get('show_about_author',1)){
    if(!empty($item) && $item -> author){
        $arrName        =   explode(' ',$item->author);
        $avaName        =   '';
        for ($i=0; $i<count($arrName); $i++){
            if ($word = trim($arrName[$i])) {
                $avaName.=$word[0];
            }
        }
        $target = '';
        if(isset($tmpl) AND !empty($tmpl)) {
            $target = ' target="_blank"';
        }
    ?>
    <div class="tpArticleAuthor">
        <h3 class="tpArticleAuthorTitle reset-heading"><?php echo JText::_('ARTICLE_AUTHOR_TITLE'); ?></h3>
        <div class="media">
            <div class="AuthorAvatar pull-left<?php echo (!$item -> author_info -> avatar)?' tp-avatar-default tpavatar--bg-'.rand(1,5):'';?>">
                <?php if($item -> author_info -> avatar){?>
                    <img src="<?php echo JUri::root().$item -> author_info -> avatar;?>" alt="<?php echo $item -> author;?>"/>
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
                        <?php if($authorParams -> get('show_gender_user', 1)){?>
                            <?php if($item -> author_info -> gender){?>
                                <span class="muted tpAuthorInfo AuthorGender">
                                <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_GENDER');?>
                                    <span><?php echo ($item -> author_info -> gender == 'm')?JText::_('COM_TZ_PORTFOLIO_PLUS_MALE'):JText::_('COM_TZ_PORTFOLIO_PLUS_FEMALE');?></span>
                            </span>
                            <?php }?>
                        <?php }?>

                        <?php if($authorParams -> get('show_email_user', 1)){?>
                            <?php if($item -> author_info -> email){?>
                                <span class="muted tpAuthorInfo AuthorEmail">
                                <i class="tp tp-envelope-o" aria-hidden="true"></i>
                                <span><?php echo $item -> author_info -> email;?></span>
                            </span>
                            <?php } ?>
                        <?php } ?>

                        <?php if($authorParams -> get('show_url_user',1) AND !empty($item -> author_info -> url)){?>
                            <span class="muted tpAuthorInfo AuthorUrl">
                            <i class="fa fa-globe" aria-hidden="true"></i>
                            <a href="<?php echo $item -> author_info -> url;?>" target="_blank">
                                <?php echo $item -> author_info -> url;?>
                            </a>
                        </span>
                        <?php } ?>
                    </div>
                    <?php if ($item -> author_info -> twitter || $item -> author_info -> facebook || $item -> author_info -> googleplus || $item -> author_info -> instagram){ ?>
                    <div class="social_link">
                        <?php if($item -> author_info -> twitter){?>
                            <span class="muted tpAuthorInfo SocialLink">
                                <a href="<?php echo $item -> author_info -> twitter; ?>" title="Twitter"><i class="tp tp-twitter" aria-hidden="true"></i></a>
                            </span>
                        <?php }?>
                        <?php if($item -> author_info -> facebook){?>
                            <span class="muted tpAuthorInfo SocialLink">
                                <a href="<?php echo $item -> author_info -> facebook; ?>" title="Facebook"><i class="tp tp-facebook-official" aria-hidden="true"></i></a>
                            </span>
                        <?php }?>
                        <?php if($item -> author_info -> googleplus){?>
                            <span class="muted tpAuthorInfo SocialLink">
                                <a href="<?php echo $item -> author_info -> googleplus; ?>" title="Google Plus"><i class="tp tp-google-plus-square" aria-hidden="true"></i></a>
                            </span>
                        <?php }?>
                        <?php if($item -> author_info -> instagram){?>
                            <span class="muted tpAuthorInfo SocialLink">
                                <a href="<?php echo $item -> author_info -> instagram; ?>" title="Instagram"><i class="tp tp-instagram" aria-hidden="true"></i></a>
                            </span>
                        <?php }?>
                    </div>
                    <?php } ?>
                </div>

            </div>
            <div class="clr"></div>
            <?php if($authorParams -> get('show_description_user', 1)  AND !empty($item -> author_info -> description)){?>
                <div class="AuthorDescription">
                    <?php echo $item -> author_info -> description; ?>
                </div>
            <?php }?>
            <div class="clr"></div>
        </div>

    </div>
<?php }
}
?>