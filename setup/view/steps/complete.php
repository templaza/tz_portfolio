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
		<i class="icon-checkmark"></i>
	</div>

	<p>TZ Portfolio+ has been installed on your site. Should you require any assistance please head to our <a href="https://www.tzportfolio.com/help/forum.html">forums</a> and <a href="https://www.tzportfolio.com/document.html">documentations</a>.

    <br><br>

	<p><b>Check us out on our social media for upates and news:</b></p>

	<a class="btn btn-social hasTooltip" href="https://www.facebook.com/groups/tzportfolio" target="_blank" title="Our Fanpage"><i class="tpb tp-facebook-f"></i></a>
	<a class="btn btn-social hasTooltip" href="https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos" target="_blank" title="Video Tutorials"><i class="tpb tp-youtube"></i></a>
	<a class="btn btn-social hasTooltip" href="https://extensions.joomla.org/extension/tz-portfolio" target="_blank" title="Rate on JED"><i class="tpb tp-joomla"></i></a>
	<a class="btn btn-social hasTooltip" href="https://www.transifex.com/templaza-com/tz-portfolio-plus" target="_blank" title="Find & Help Translate"><i class="tps tp-globe"></i></a>

</div>