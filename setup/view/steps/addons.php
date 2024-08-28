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

$source = $input->get('source', '', 'default');

$_last_step	= end($steps);
$_next_step	= $_last_step -> template == $activeStep -> template?'complete':$activeStep -> index;
?>
<script type="text/javascript">
    $(document).ready(function(){
        tpp.ajaxUrl	= "<?php echo Uri::root();?>administrator/index.php?option=com_tz_portfolio&ajax=1";

        // Immediately proceed with synchronization
        tpp.options.path = '<?php echo addslashes($source);?>';
        tpp.addons.retrieveList();
    });
</script>
<form name="installation" method="post" data-installation-form>

    <p class="section-desc"><?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_ADDONS_DESC'); ?></p>

    <div data-addons-retrieving><?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_ADDONS_RETRIEVING_LIST');?></div>

    <div class="hide" data-addons-progress>
        <div class="install-progress">
            <div class="d-flex">
                <div class="message">
                    <div class="hide" data-progress-complete-message><?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_EXTENSIONS_COMPLETED');?></div>
                    <div data-progress-active-message=""><?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_INSTALLING_MODULES');?></div>
                </div>
                <div class="result text-right">
                    <div class="progress-result">
                        <span data-progress-bar-result="">0%</span>
                        <span><?php echo Text::_('COM_TZ_PORTFOLIO_COMPLETED'); ?></span>
                    </div>
                </div>
            </div>

            <div class="progress mb-3">
                <div class="progress-bar progress-bar-info progress-bar-striped" data-progress-bar="" style="width: 0%;"></div>
            </div>
        </div>
    </div>

    <div data-addons-container></div>

    <input type="hidden" name="option" value="com_tz_portfolio" />
    <input type="hidden" name="active" value="<?php echo $_next_step;?>" />
</form>
