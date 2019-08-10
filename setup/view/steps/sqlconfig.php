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

?>
<script>
    $(document).ready(function(){
        submit.on('click', function() {
            form.submit();
        });
    });
</script>

<form action="index.php?option=com_tz_portfolio_plus" method="post" name="installation" data-installation-form>
    <p class="section-desc">Are you a newbie with TZ Portfolio+? If yes, don't worry! We have prepared sample articles for you after you install the component successfully in the first time. So you can follow these samples and then create new ones by your own.</p>
    
    <div class="installation-inner">
        <div class="control-group">
            <h4><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_INSTALL_SAMPLE_DATA');?></h4>
            <fieldset id="field_sample_data" class="switcher btn-group radio">
                <input type="radio" id="field_sample_data0" name="sample_data" value="0" checked="checked">
                <label for="field_sample_data0" class="btn active btn-danger"><?php echo JText::_('JNO'); ?></label>
                <input type="radio" id="field_sample_data1" name="sample_data" value="1">
                <label for="field_sample_data1" class="btn"><?php echo JText::_('JYES'); ?></label>
            </fieldset>
        </div>
        <input type="hidden" name="method" value="directory" />

    </div>

	<input type="hidden" name="option" value="com_tz_portfolio_plus" />
	<input type="hidden" name="license" value="<?php echo $input -> get('license'); ?>" />
	<input type="hidden" name="active" value="<?php echo $active; ?>" />
	<input type="hidden" name="update" value="<?php echo $update;?>" />
</form>
