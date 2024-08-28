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

$input -> set('active', 0);
$xml    = simplexml_load_file(COM_TZ_PORTFOLIO_ADMIN_PATH.'/tz_portfolio.xml');
?>
<script>
    $(document).ready(function(){
       $.ajax({
           type: "POST",
           url: "<?php echo Uri::root();?>administrator/index.php?option=com_tz_portfolio&ajax=1",
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

	<p><?php echo Text::sprintf('COM_TZ_PORTFOLIO_SETUP_COMPLETED_DESC', $xml -> forumUrl, $xml -> guideUrl);?>

    <br><br>

	<p><b><?php echo Text::_('COM_TZ_PORTFOLIO_CHECK_US_SOCIAL_DESC');?></b></p>

    <?php
    $btn_class  = '';
    if(version_compare(JVERSION, '4.0', 'ge')){
        $btn_class  = ' d-flex justify-content-center text-center align-items-center';
        ?>
    <div class="d-flex justify-content-center text-center align-items-center">
    <?php }?>
        <a class="btn btn-social hasTooltip<?php echo $btn_class; ?>" href="<?php echo (string) $xml -> facebookUrl;?>" target="_blank" title="<?php echo Text::_('COM_TZ_PORTFOLIO_FANPAGE');?>"><i class="fab fa-facebook-f"></i></a>
        <a class="btn btn-social hasTooltip<?php echo $btn_class; ?>" href="<?php echo (string) $xml -> youtubeUrl;?>" target="_blank" title="<?php echo Text::_('COM_TZ_PORTFOLIO_VIDEO_TUTORIALS');?>"><i class="fab fa-youtube"></i></a>
        <a class="btn btn-social hasTooltip<?php echo $btn_class; ?>" href="<?php echo (string) $xml -> jedUrl;?>" target="_blank" title="<?php echo Text::_('COM_TZ_PORTFOLIO_RATE_ON_JED');?>"><i class="fab fa-joomla"></i></a>
        <a class="btn btn-social hasTooltip<?php echo $btn_class; ?>" href="<?php echo (string) $xml -> transifexUrl;?>" target="_blank" title="<?php echo Text::_('COM_TZ_PORTFOLIO_FIND_HELP_TRANSLATE');?>"><i class="fas fa-globe"></i></a>
    <?php if(version_compare(JVERSION, '4.0', 'ge')){?>
    </div>
    <?php }?>

</div>