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

<?php if(!empty($this -> sidebar)):?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this -> sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
<?php endif;?>
    <form name="adminForm" method="post" id="adminForm"
          enctype="multipart/form-data"
          action="index.php?option=com_tz_portfolio_plus&view=addon&layout=upload">
        <div class="container-fluid" id="plazart_layout_builder">
            <fieldset class="adminForm">
                <legend><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_UPLOAD_AND_INSTALL_ADDON');?></legend>
                <div class="form-horizontal">
                    <div class="control-group">
                        <div class="control-label"><?php echo $this -> form -> getLabel('install_package');?></div>
                        <div class="controls"><?php echo $this -> form -> getInput('install_package');?></div>
                    </div>
                    <div class="form-actions">
                        <button class="btn btn-small btn-primary" onclick="Joomla.submitbutton('addon.install')">
                            <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_UPLOAD_AND_INSTALL');?></button>
                    </div>
                </div>
            </fieldset>
        </div>

        <input type="hidden" value="com_tz_portfolio_plus" name="option">
        <input type="hidden" value="" name="task">
        <?php echo JHTML::_('form.token');?>
    </form>
<?php if(!empty($this -> sidebar)):?>
    </div>
<?php endif;?>