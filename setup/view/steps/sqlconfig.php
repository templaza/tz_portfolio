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

use Joomla\CMS\Language\Text;

?>
<script>
    $(document).ready(function(){
        $("[data-installation-form] input[type=radio][name=sample_data]").on("change", function(){
            var $this   = $(this);
            if(this.value == 1){
                var result = confirm("<?php echo htmlspecialchars(Text::_('COM_TZ_PORTFOLIO_SETUP_SAMPLE_DATA_QUESTION'))?>");
                if(!result){
                    $this.prop("checked", "");
                    $("#field_sample_data0").prop("checked", true);
                }
            }
        });
        submit.on('click', function() {
            form.submit();
        });
    });
</script>

<form action="index.php?option=com_tz_portfolio" method="post" name="installation" data-installation-form>
    <p class="section-desc"><?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_DATABASE_CONFIG_DESC');?></p>
    
    <div class="installation-inner">
        <div class="control-group">
            <h4><?php echo Text::_('COM_TZ_PORTFOLIO_INSTALL_SAMPLE_DATA');?></h4>
            <?php $joomla4  = version_compare(JVERSION, 4.0, '>='); ?>
            <fieldset id="field_sample_data" class="switcher<?php echo $joomla4?' float-none m-auto has-success':' btn-group radio';?>">
                <input type="radio" id="field_sample_data0" name="sample_data" value="0" checked="checked">
                <label for="field_sample_data0" class="<?php echo !$joomla4?' btn active btn-danger':'';?>"><?php echo Text::_('JNO'); ?></label>
                <input type="radio" id="field_sample_data1" name="sample_data" value="1">
                <label for="field_sample_data1" class="<?php echo !$joomla4?' btn':'';?>"><?php echo Text::_('JYES'); ?></label>
                <?php if($joomla4){?>
                    <span class="toggle-outside text-left"><span class="toggle-inside"></span></span>
                <?php } ?>
            </fieldset>
        </div>
        <input type="hidden" name="method" value="directory" />

    </div>

	<input type="hidden" name="option" value="com_tz_portfolio" />
	<input type="hidden" name="license" value="<?php echo $input -> get('license'); ?>" />
	<input type="hidden" name="active" value="<?php echo $active; ?>" />
	<input type="hidden" name="update" value="<?php echo $update;?>" />
</form>
