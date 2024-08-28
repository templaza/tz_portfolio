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
<script type="text/javascript">
(function($){
    $(document).ready(function() {
        if(typeof tooltip !== "undefined" || typeof $.fn.tooltip !== "undefined") {
            $('[data-toggle="tooltip"]').tooltip({trigger: 'manual'}).tooltip('show');
        }
        $(".progress.progress__tooltip .progress-bar").each(function(){
            var each_bar_width = $(this).attr('aria-valuenow');
            $(this).width(each_bar_width + '%');
        });

        var loading = $('[data-checking]');
        var form = $('[data-installation-form]');

        // // Hide submit button
        // submit.addClass('hide');

        // Change the behavior of form submission
        submit.on('click', function() {
            form.submit();
        });

        // Validate api key
        var licenceVerify   = function(){
            $.ajax({
                type: 'POST',
                url: '<?php echo Uri::root();?>administrator/index.php?option=com_tz_portfolio&ajax=1&task=license.verify',
                data: {
                    task: "license.verify",
                    "token_key": $('[data-api-key]').val()
                }
            }).done(function(result) {

                // Hide the loading
                if (result.state != 200 || (result.state == 200 && result.licenses.length > 1)) {
                    loading.addClass('hide');
                }

                if(typeof result.type !== typeof undefined){
                    switch (result.type) {
                        default:
                        case "message":
                            $("[data-api-errors]").removeClass("alert-danger alert-success alert-warning").addClass("alert-primary");
                            break;
                        case "error":
                            $("[data-api-errors]").removeClass("alert-primary alert-success alert-warning").addClass("alert-danger");
                            break;
                        case "notice":
                        case "warning":
                            $("[data-api-errors]").removeClass("alert-primary alert-danger alert-success").addClass("alert-warning");
                            break;

                    }
                }

                var submit = $('[data-installation-submit]');

                submit.find("> span:first").html("<?php echo Text::_('COM_TZ_PORTFOLIO_SKIP_THIS_STEP'); ?>");

                // User is not allowed to install
                if (result.state == 400) {

                    // Set the error message
                    // $('[data-api-errors]').removeClass("alert-success").addClass("alert-danger").removeClass('hide');
                    $('[data-api-errors]').removeClass('hide');
                    $('[data-error-message]').html(result.message);
                    $('[data-source-method]').addClass('hide');
                    $('[data-api-key]').removeClass("hidden hide");
                    // submit.addClass("hide");
                    $('[data-installation-check-licences]').removeClass("hide");

                    if(!$('[data-api-key]').val().length){
                        $('[data-token-key-group]').removeClass("hide");
                    }else{
                        $('.installation-inner').addClass("hide");
                    }

                    return false;
                }

                // Valid licenses
                if (result.state == 200) {

                    var licenses = $('[data-licenses]');
                    var licensePlaceholder = $('[data-licenses-placeholder]');

                    submit.removeClass('hide');

                    $('[data-installation-check-licences]').addClass("hide disabled");

                    if(typeof result.message !== typeof undefined && result.message.length) {
                        $('[data-error-message]').html(result.message);
                    }

                    // If there are multiple licenses, we need to request them to submit
                    if (result.licenses.length > 1) {

                        submit.find("> span:first").html("<?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_NEXT_STEP'); ?>");

                        licenses.removeClass('hide');
                        $('[data-token-key-group]').addClass("hide");

                        licensePlaceholder.append(result.html);

                        $('[data-api-errors]').removeClass("alert-success").addClass("alert-danger").removeClass('hide');

                        // Change the behavior of form submission
                        submit.on('click', function() {
                            form.submit();
                        });
                        return;
                    }

                    // If the user only has 1 license, just submit this immediately.
                    licensePlaceholder.append(result.html);
                    form.submit();
                }
            });
        };
        licenceVerify();
        $('[data-installation-check-licences]').on("click", function () {
            $('[data-api-errors]').addClass("hide");
            $('.installation-inner').removeClass("hide");
            loading.removeClass("hide");
            $('[data-token-key-group]').addClass("hide");
           licenceVerify();
        });
    });
})(jQuery);
</script>

<form action="index.php?option=com_tz_portfolio" method="post" name="installation" data-installation-form><?php
    $fileExists = JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/controller.php';

    if(file_exists($fileExists)){ ?>
        <?php
        $class      = ' alert-danger';
        $errorMsg   = Text::_('COM_TZ_PORTFOLIO_SETUP_METHOD_TOKEN_KEY_INVALID');
        ?>
    <?php }else{ ?>
        <?php
        $class      = ' alert-warning';
        $errorMsg = Text::_('COM_TZ_PORTFOLIO_NEW_INSTALLATION_METHOD_TOKEN_KEY_INVALID'); ?>
    <?php }
    ?>
	<div class="hide alert<?php echo $class; ?>" data-source-errors data-api-errors>
		<div data-error-message><?php echo $errorMsg; ?></div>
	</div>

        <div class="installation-inner">

            <div class="form-inline hide" data-licenses>
                <p><?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_METHOD_SELECT_LICENSE_INFO');?></p>
                <div data-licenses-placeholder></div>
            </div>

            <div class="text-center" data-checking>
                <div class="progress progress-loading"></div>
                <div><?php echo Text::_('COM_TZ_PORTFOLIO_CHECKING_LICENSES');?></div>
            </div>
            <div class="control-group hide" data-token-key-group>
                <h4><?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_ENTER_TOKEN_KEY');?></h4>
                <input type="text" value="<?php echo COM_TZ_PORTFOLIO_SETUP_TOKEN_KEY; ?>" name="token_key" class="hide" data-api-key />
            </div>
            <input type="hidden" name="method" value="directory" />

        </div>

	<input type="hidden" name="option" value="com_tz_portfolio" />
	<input type="hidden" name="active" value="<?php echo $active; ?>" />
	<input type="hidden" name="update" value="<?php echo $update;?>" />
</form>
