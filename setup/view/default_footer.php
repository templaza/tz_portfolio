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

?>
<?php if ($active != 'complete') { ?>
<script type="text/javascript">
    $(document).ready( function(){

        var previous = $('[data-installation-nav-prev]'),
            active = $('[data-installation-form-nav-active]'),
            nav = $('[data-installation-form-nav]'),
            retry = $('[data-installation-retry]'),
            cancel = $('[data-installation-nav-cancel]'),
            loading = $('[data-installation-loading]');


        previous.on('click', function() {
            active.val(<?php echo $active;?> - 2);

            nav.submit();
        });

        cancel.on('click', function() {
            window.location = '<?php echo Uri::base();?>/index.php?option=com_tz_portfolio&cancelSetup=1';
        });

        retry.on('click', function() {
            var step = $(this).data('retry-step');

            $(this).addClass('hide');

            loading.removeClass('hide');

            window['tpp']['installation'][step]();
        });
    });
</script>

<form action="index.php?option=com_tz_portfolio" method="post" data-installation-form-nav class="hidden">
	<input type="hidden" name="active" value="" data-installation-form-nav-active />
	<input type="hidden" name="option" value="com_tz_portfolio" />
	<?php if ($reinstall) { ?>
	<input type="hidden" name="reinstall" value="1" />
	<?php } ?>

	<?php if ($update) { ?>
	<input type="hidden" name="update" value="1" />
	<?php } ?>

    <?php if($input -> get('token_key')){ ?>
        <input type="hidden" name="token_key" value="<?php echo $input -> get('token_key'); ?>" />
    <?php } ?>
    <?php if($input -> get('license')){ ?>
        <input type="hidden" name="license" value="<?php echo $input -> get('license'); ?>"/>
    <?php } ?>

    <?php if($active == 4 ){ ?>
        <input type="hidden" name="method" value="directory" />
    <?php } ?>
</form>


<div class="navi">
    <a href="javascript:void(0);" class="btn btn-default col-cell" <?php echo $active > 1 ? ' data-installation-nav-prev' : ' data-installation-nav-cancel';?>>
        <span>
            <?php if ($active > 1) { ?>
                <?php echo Text::_('JPREVIOUS'); ?>
            <?php } else { ?>
                <?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_EXIT'); ?>
            <?php } ?>
        </span>
    </a>

    <a href="javascript:void(0);" class="btn btn-success hide" data-installation-check-licences>
        <span><?php echo Text::_('COM_TZ_PORTFOLIO_CHECK_LICENCES'); ?></span>
    </a>
    <a href="javascript:void(0);" class="btn btn-primary col-cell primary" data-installation-submit>
        <span><?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_NEXT_STEP'); ?></span>
    </a>

    <a href="javascript:void(0);" class="btn btn-primary loading hide disabled" data-installation-loading>
        <span><?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_LOADING'); ?></span>
        <span class="progress progress-loading"></span>
    </a>

    <a href="javascript:void(0);" class="btn btn-primary hide" data-installation-install-addons>
        <span><?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_INSTALL_ADDONS'); ?></span>
    </a>

    <a href="javascript:void(0);" class="btn btn-primary hide" data-installation-retry>
        <span><?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_RETRY'); ?></span>
    </a>
</div>
<?php } ?>

<?php if ($active == 'complete') { ?>
<div class="navi">
    <?php
    $menu   = JFactory::getApplication() -> getMenu('site');
    $menuTpp    = $menu -> getItems('link', 'index.php?option=com_tz_portfolio&view=portfolio');
    $menuItemid = count($menuTpp)?$menuTpp[0] -> id:0;
    ?>
    <a class="btn btn-default" href="<?php echo Uri::root().($menuItemid?'index.php?option=com_tz_portfolio&Itemid='.$menuItemid:'');?>" target="_blank">
        <b><span><?php echo Text::_('COM_TZ_PORTFOLIO_LAUNCH_FRONTEND');?></span></b>
    </a>
    <a class="btn btn-primary" href="<?php echo Uri::root();?>administrator/index.php?option=com_tz_portfolio">
        <b><span><?php echo Text::_('COM_TZ_PORTFOLIO_CONTINUE_TO_BACKEND');?></span></b>
    </a>
</div>
<?php } ?>