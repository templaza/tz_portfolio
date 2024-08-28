<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2019 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

$_last_step	= end($steps);
$_next_step	= $_last_step -> template == $activeStep -> template?'complete':$active;
?>
<script>
    $(document).ready(function(){
        submit.on('click', function() {
            form.submit();
        });
    });
</script>

<form action="index.php?option=com_tz_portfolio" method="post" name="installation" data-installation-form>
    <div class="alert alert-warning"><?php echo Text::_($activeStep -> desc);?></div>

    <div class="installation-inner">
        <div class="control-group">
            <h4><?php echo Text::_('JTOOLBAR_DISABLE');?></h4>
            <?php $joomla4  = version_compare(JVERSION, 4.0, '>='); ?>
            <fieldset id="field_disable_tz_portfolio_plus" class="switcher<?php echo $joomla4?' float-none m-auto has-success':' btn-group radio';?>">
                <input type="radio" id="field_disable_tz_portfolio_plus0" name="disable_tz_portfolio_plus" value="0">
                <label for="field_disable_tz_portfolio_plus0" class="<?php echo !$joomla4?' btn active btn-danger':'';
                ?>"><?php echo Text::_('JNO'); ?></label>
                <input type="radio" id="field_disable_tz_portfolio_plus1" name="disable_tz_portfolio_plus" value="1" checked="checked">
                <label for="field_disable_tz_portfolio_plus1" class="<?php echo !$joomla4?' btn':'';?>"><?php
                    echo Text::_('JYES'); ?></label>
                <?php if($joomla4){?>
                    <span class="toggle-outside text-left"><span class="toggle-inside"></span></span>
                <?php } ?>
            </fieldset>
        </div>
    </div>

    <input type="hidden" name="option" value="com_tz_portfolio" />
    <input type="hidden" name="license" value="<?php echo $input -> get('license'); ?>" />
    <input type="hidden" name="active" value="<?php echo $_next_step; ?>" />
    <input type="hidden" name="update" value="<?php echo $update;?>" />
</form>