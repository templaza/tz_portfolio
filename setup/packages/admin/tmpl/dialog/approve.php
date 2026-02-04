<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    Sonny

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$modalId    = 'tp-myarticle__approve';
$cids       = $this -> state -> get('article.id', array());

echo HTMLHelper::_(
    'bootstrap.renderModal',
    $modalId,
    array(
        'title'      => Text::_('COM_TZ_PORTFOLIO_APPROVE_ARTICLE'),
        'width'      => '100%',
        'height'     => '500px',
        'modalWidth' => '40',
        'closeButton' => true,
        'class'       => 'tpp-dialog-modal',
        'footer'      => '<a class="btn btn-default" data-dismiss="modal" data-bs-dismiss="modal" href="javascript:void(0);">'
            . Text::_('JCANCEL') . '</a><a class="btn btn-primary" href="javascript:void(0);" data-submit-button>'
            . Text::_('COM_TZ_PORTFOLIO_APPROVE') . '</a>',
    ),
    Text::_('COM_TZ_PORTFOLIO_DIALOG_CONFIRM_APRROVE_CONTENT')
);
?>