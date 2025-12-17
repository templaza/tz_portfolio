<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2018 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$modalId    = 'tp-myarticle__approve';
$cids       = $this -> state -> get('article.id', array());

?>
    <div id="<?php echo $modalId; ?>" class="tp-dialog-modal" data-uk-modal><div class="uk-modal-dialog">
            <button class="uk-modal-close-default" type="button" data-uk-close></button>
            <div class="uk-modal-header">
                <h4 class="uk-modal-title"><?php echo Text::_('COM_TZ_PORTFOLIO_APPROVE_ARTICLE'); ?></h4>
            </div>
            <div class="uk-modal-body">
                <?php echo Text::_('COM_TZ_PORTFOLIO_DIALOG_CONFIRM_APRROVE_CONTENT'); ?>
            </div>
            <div class="uk-modal-footer uk-text-right">
                <a class="uk-button uk-button-default uk-modal-close"><?php echo Text::_('JCANCEL');?></a>
                <a class="uk-button uk-button-primary" data-submit-button><?php echo Text::_('COM_TZ_PORTFOLIO_APPROVE');?></a>
            </div>
        </div>
    </div>
<?php

//echo JHtml::_(
//    'bootstrap.renderModal',
//    $modalId,
//    array(
//        'title'      => Text::_('COM_TZ_PORTFOLIO_APPROVE_ARTICLE'),
//        'width'      => '100%',
//        'height'     => '500px',
//        'modalWidth' => '40',
//        'closeButton' => true,
//        'class'       => 'tpp-dialog-modal',
//        'footer'      => '<a class="btn btn-default" data-dismiss="modal" href="javascript:void(0);">'
//            . JText::_('JCANCEL') . '</a><a class="btn btn-primary" href="javascript:void(0);" data-submit-button>'
//            . JText::_('COM_TZ_PORTFOLIO_APPROVE') . '</a>',
//    ),
//    JText::_('COM_TZ_PORTFOLIO_DIALOG_CONFIRM_APRROVE_CONTENT')
//);
?>