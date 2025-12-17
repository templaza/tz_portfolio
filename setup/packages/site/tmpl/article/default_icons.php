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

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

$params     = $this -> item -> params;
$canEdit	= $this->item->params->get('access-edit');
?>

<?php if (!$this->print) : ?>
    <?php if ($canEdit) : ?>
        <div class="uk-flex uk-flex-right tp-item-icon" data-uk-dropnav="mode: click;">
            <a class="uk-button uk-button-default uk-button-small" href="#">
                <i class="fas fa-cog"></i><span data-uk-drop-parent-icon></span>
            </a>
            <?php // Note the actions class is deprecated. Use dropdown-menu instead. ?>
            <div class="uk-dropdown">
                <ul class="uk-nav uk-dropdown-nav actions">
<!--                    --><?php //if ($params->get('show_print_icon', 1)) : ?>
<!--                        <li class="print-icon"> --><?php //echo HTMLHelper::_('icon.print_popup',  $this->item, $params); ?><!-- </li>-->
<!--                    --><?php //endif; ?>
<!--                    --><?php //if ($params->get('show_email_icon', 1)) : ?>
<!--                        <li class="email-icon"> --><?php //echo HTMLHelper::_('icon.email',  $this->item, $params); ?><!-- </li>-->
<!--                    --><?php //endif; ?>
                    <?php if ($canEdit) : ?>
                        <li class="edit-icon"> <?php echo HTMLHelper::_('icon.edit', $this->item, $params); ?> </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
<?php else : ?>
    <div class="pull-right float-right">
        <?php echo HTMLHelper::_('icon.print_screen',  $this->item, $params); ?>
    </div>
<?php endif; ?>