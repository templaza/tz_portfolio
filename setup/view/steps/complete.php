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

$input -> set('active', 0);
$xml    = simplexml_load_file(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.'/tz_portfolio_plus.xml');
?>
<script>
    $(document).ready(function(){
       $.ajax({
           type: "POST",
           url: "<?php echo JURI::root();?>administrator/index.php?option=com_tz_portfolio_plus&ajax=1",
           data: {
               task: "completed"
           }
       })
    });
</script>
<div class="tpp-installation__completed">
	<div class="tpp-complete-icon">
		<i class="icon-checkmark mr-0"></i>
	</div>

	<p><?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_SETUP_COMPLETED_DESC', $xml -> forumUrl, $xml -> guideUrl);?>

    <br><br>

	<p><b><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_CHECK_US_SOCIAL_DESC');?></b></p>

	<a class="btn btn-social hasTooltip" href="<?php echo (string) $xml -> facebookUrl;?>" target="_blank" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_FANPAGE');?>"><i class="tpb tp-facebook-f"></i></a>
	<a class="btn btn-social hasTooltip" href="<?php echo (string) $xml -> youtubeUrl;?>" target="_blank" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_VIDEO_TUTORIALS');?>"><i class="tpb tp-youtube"></i></a>
	<a class="btn btn-social hasTooltip" href="<?php echo (string) $xml -> jedUrl;?>" target="_blank" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_RATE_ON_JED');?>"><i class="tpb tp-joomla"></i></a>
	<a class="btn btn-social hasTooltip" href="<?php echo (string) $xml -> transifexUrl;?>" target="_blank" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_FIND_HELP_TRANSLATE');?>"><i class="tps tp-globe"></i></a>

</div>