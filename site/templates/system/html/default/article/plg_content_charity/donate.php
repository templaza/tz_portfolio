<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2015 templaza.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

if($form = $this -> formDonate){
    $doc    = JFactory::getDocument();
    $doc -> addStyleSheet(TZ_Portfolio_PlusUri::root().'/addons/content/charity/css/charity.css');
?>
    <form action="<?php echo $this -> item -> link;?>"
          method="post"
          class="form-validate form-charity">
        <div class="form-horizontal">
            <div class="form-group">
                <div class="col-sm-4 control-label"><?php echo $form -> getLabel('firstname');?></div>
                <div class="col-sm-8 controls"><?php echo $form -> getInput('firstname');?></div>
            </div>
            <div class="form-group">
                <div class="col-sm-4 control-label"><?php echo $form -> getLabel('lastname');?></div>
                <div class="col-sm-8 controls"><?php echo $form -> getInput('lastname');?></div>
            </div>
            <div class="form-group">
                <div class="col-sm-4 control-label"><?php echo $form -> getLabel('email');?></div>
                <div class="col-sm-8 controls"><?php echo $form -> getInput('email');?></div>
            </div>
            <div class="form-group">
                <div class="col-sm-4 control-label"><?php echo $form -> getLabel('address');?></div>
                <div class="col-sm-8 controls"><?php echo $form -> getInput('address');?></div>
            </div>
            <div class="form-group">
                <div class="col-sm-4 control-label"><?php echo $form -> getLabel('comment');?></div>
                <div class="col-sm-8 controls"><?php echo $form -> getInput('comment');?></div>
            </div>
            <div class="center">
                <button class="btn btn-primary radius-small" name="ok" type="submit">DONATE VIA</button>
            </div>
        </div>
        <input type="hidden" name="option" value="com_tz_portfolio_plus"/>
        <input type="hidden" name="view" value="article"/>
        <input type="hidden" name="id" value="<?php echo $this -> item -> id;?>"/>
        <input type="hidden" name="return" value="<?php echo $this -> item -> link;?>" />
        <input type="hidden" name="addon_view" value="donate"/>
        <input type="hidden" name="addon_task" value="donate.process_donation"/>
        <?php if($addon = $this -> state -> get($this -> getName().'.addon')){?>
        <input type="hidden" name="addon_id" value="<?php echo $addon -> id;?>"/>
        <?php }?>
        <?php echo JHtml::_( 'form.token' ); ?>
    </form>
<?php
}