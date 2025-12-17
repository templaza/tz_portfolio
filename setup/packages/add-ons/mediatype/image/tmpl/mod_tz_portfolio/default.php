<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

if($item && $image && isset($image -> url) && !empty($image -> url)):
    $image_uikit  =   $params->get('mt_image_uikit',0);
    if($params -> get('mt_show_image',1)):
        ?>
        <div class="tz_portfolio_plus_image">
            <a href="<?php echo $item -> link;?>">
                <?php
                $imagesrc   =   $image -> url;
                if ($image_uikit) :
                    ?>
                    <img data-src="<?php echo $imagesrc; ?>" alt="<?php echo isset($image -> caption) && $image -> caption ? $image -> caption : $item -> title; ?>"
                         title="<?php echo isset($image -> caption) && $image -> caption ? $image -> caption : $item -> title; ?>"<?php
                        if ($image_properties && is_array($image_properties)) echo ' data-width="'.$image_properties[0].'" data-height="'.$image_properties[1].'" ';
                        ?> itemprop="image" data-uk-img />
                <?php else: ?>
                    <img src="<?php echo $imagesrc; ?>" alt="<?php echo isset($image -> caption) && $image -> caption ? $image -> caption : $item -> title; ?>"
                         title="<?php echo isset($image -> caption) && $image -> caption ? $image -> caption : $item -> title; ?>"
                         itemprop="image" />
                <?php endif; ?>
            </a>
        </div>
    <?php endif;?>
<?php endif;?>
