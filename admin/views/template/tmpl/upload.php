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

//no direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>

<?php echo JHtml::_('tzbootstrap.addrow');?>
<?php if(!empty($this -> sidebar)){?>
    <div id="j-sidebar-container" class="span2 col-md-2">
        <?php echo $this -> sidebar; ?>
    </div>
<?php } ?>

    <?php echo JHtml::_('tzbootstrap.startcontainer', '10', !empty($this -> sidebar));?>
    <form name="adminForm" method="post" id="adminForm" class="tpp-extension__upload"
          enctype="multipart/form-data"
          action="index.php?option=com_tz_portfolio_plus&view=template&layout=upload">

        <?php echo $this -> loadTemplate('list'); ?>

        <input type="hidden" value="" name="task">
        <?php echo JHTML::_('form.token');?>
    </form>

    <?php echo JHtml::_('tzbootstrap.endcontainer');?>
<?php echo JHtml::_('tzbootstrap.endrow');?>