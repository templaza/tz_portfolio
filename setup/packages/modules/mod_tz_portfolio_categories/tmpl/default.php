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

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Uri\Uri;

if ($list):

    $moduleclass_sfx    = htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_COMPAT, 'UTF-8');
    ?>
    <div class="category-menu<?php echo $moduleclass_sfx;?>">
        <?php $i = 0; ?>
        <?php $str = null; ?>
        <?php ob_start(); ?>
        <?php foreach ($list as $item): ?>

            <?php
            if (count($list) > 1 && isset($list[$i + 1]->level)) {
                $subLevel = (int)$list[$i + 1]->level - (int)$list[$i]->level;
            } else {
                $subLevel = 0;
            }

            if (($i == count($list) - 1) && $item->level > 1) {
                $subLevel = -($item->level - 1);
            }
            ?>
            <?php if ($subLevel == 0): ?>
                <div class="item level<?php echo $item->level - 1; ?>">
                    <?php if ($params->get('show_image', 1) && $item->images) {
                        echo '<a href="' . $item->link . '"><img src="' . Uri::base() . $item->images . '" alt="' . $item->title . '"/></a>';
                    } ?>
                    <?php if ($params->get('show_title', 1)): ?>
                        <a href="<?php echo $item->link ?>" class="uk-link"><?php echo $item->title; ?>
                            <?php if (isset($item->total)): ?>
                                <span>(<?php echo $item->total ?>)</span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($params->get('show_desc', 1) && $item->description) {
                        echo '<div class="tz_desc">' . $item->description . '</div>';
                    } ?>
                </div>
            <?php elseif ($subLevel > 0): ?>
                <div class="item level<?php echo $item->level - 1; ?> haschild" data-uk-accordion>
                    <div class="uk-position-relative">
                        <a id="category-btn-<?php echo $item->id; ?>" href="#category-<?php echo $item->id; ?>"
                           class="uk-accordion-title uk-position-right uk-position-absolute"></a>

                        <?php if ($params->get('show_image', 1) && $item->images) {
                            echo '<a href="' . $item->link . '"><img src="' . Uri::base() . $item->images . '" alt="' . $item->title . '"/></a>';
                        } ?>
                        <?php if ($params->get('show_title', 1)): ?>
                            <a href="<?php echo $item->link ?>" class="uk-link"><?php echo $item->title; ?>
                                <?php if (isset($item->total)): ?>
                                    <span>(<?php echo $item->total ?>)</span>
                                <?php endif; ?>
                            </a>
                        <?php endif; ?>

                        <?php if ($params->get('show_desc', 1) && $item->description) {
                            echo '<div class="tz_desc">' . $item->description . '</div>';
                        } ?>

                        <div class="sub-menu-category sub-menu-category-<?php echo $subLevel; ?> uk-accordion-content uk-padding-small uk-padding-remove-vertical uk-padding-remove-right uk-margin-remove-top" id="category-<?php echo $item->id; ?>">
            <?php elseif ($subLevel < 0): ?>
                <div class="item level<?php echo $item->level - 1; ?>">
                    <?php if ($params->get('show_image', 1) && $item->images) {
                        echo '<a href="' . $item->link . '"><img src="' . Uri::base() . $item->images . '" alt="' . $item->title . '"/></a>';
                    } ?>
                    <?php if ($params->get('show_title', 1)): ?>
                        <a href="<?php echo $item->link ?>" class="uk-link"><?php echo $item->title; ?>
                            <?php if (isset($item->total)): ?>
                                <span>(<?php echo $item->total ?>)</span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($params->get('show_desc', 1) && $item->description) {
                        echo '<div class="tz_desc">' . $item->description . '</div>';
                    } ?>
                </div>

                <?php for ($k = 0; $k > $subLevel; $k--): ?>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            <?php endif; ?>

            <?php $i++; ?>
        <?php endforeach; ?>
        <?php $str = ob_get_contents() ?>
        <?php ob_end_clean(); ?>
        <?php echo $str; ?>
    </div>
<?php endif; ?>

