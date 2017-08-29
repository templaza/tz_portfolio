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

// No direct access.
defined('_JEXEC') or die;

$item   = $this -> item;
$image  = $this -> image;
$params = $this -> params;

if($item && $image && isset($image -> url) && !empty($image -> url)):
?>
    <?php
    $href   = null;
    $class  = null;
    $rel    = null;
    if($params -> get('mt_image_use_cloud',1)) {

        $effect = null;
        if ($params->get('mt_image_cloud_width'))
            $effect[] = 'zoomWidth:' . $params->get('mt_image_cloud_width');
        if ($params->get('mt_image_cloud_height'))
            $effect[] = 'zoomHeight:' . $params->get('mt_image_cloud_height');
        if ($params->get('mt_image_cloud_position', 'inside'))
            $effect[] = 'position:\'' . $params->get('mt_image_cloud_position', 'inside') . '\'';
        if ($params->get('mt_image_cloud_adjustX'))
            $effect[] = 'adjustX:' . $params->get('mt_image_cloud_adjustX');
        if ($params->get('mt_image_cloud_adjustY'))
            $effect[] = 'adjustY:' . $params->get('mt_image_cloud_adjustY');
        if ($params->get('mt_image_cloud_tint'))
            $effect[] = 'tint:\'' . $params->get('mt_image_cloud_tint') . '\'';
        if ($params->get('mt_image_cloud_tint_opacity'))
            $effect[] = 'tintOpacity:' . $params->get('mt_image_cloud_tint_opacity');
        if ($params->get('mt_image_cloud_len_opacity'))
            $effect[] = 'lensOpacity:' . $params->get('mt_image_cloud_len_opacity');
        if ($params->get('mt_image_cloud_softfocus', 0))
            $effect[] = 'softFocus: true';
        else
            $effect[] = 'softFocus: false';
        if ($params->get('mt_image_cloud_smoothmove', 3))
            $effect[] = 'smoothMove:' . $params->get('mt_image_cloud_smoothmove', 3);
        if ($params->get('mt_image_cloud_show_title', 1))
            $effect[] = 'showTitle:true';
        else
            $effect[] = 'showTitle:false';
        if ($params->get('mt_image_cloud_title_opacity'))
            $effect[] = 'titleOpacity:' . $params->get('mt_image_cloud_title_opacity');
        $effect = implode(',', $effect);
        $class = 'cloud-zoom';
        $rel = ' rel="' . $effect . '"';
    }
    ?>
    <div class="tpImage" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
        <?php if($params -> get('mt_image_use_cloud',1)):?>
        <a<?php if($class) echo ' class="'.$class.'"'?> href="<?php echo $image -> url_cloud_zoom;?>"<?php if($rel) echo $rel?>>
        <?php endif;?>
            <?php if(isset($image -> url_detail) && trim($image -> url_detail)): ?>

                <img src="<?php echo $image -> url_detail;?>"
                     alt="<?php echo ($image -> caption)?($image -> caption):$item -> title;?>"
                     title="<?php echo ($image -> caption)?($image -> caption):$item -> title;?>" itemprop="image"/>
            <?php  else : ?>
                <img src="<?php echo $image -> url;?>" alt="<?php if(isset($image -> caption)) echo $image -> caption;?>"
                     title="<?php if(isset($image -> caption)) echo $image -> caption;?>"
                     itemprop="image">
            <?php endif; ?>
        <?php if($params -> get('mt_image_use_cloud',1)):?>
        </a>
    <?php endif;?>
    </div>
<?php endif;