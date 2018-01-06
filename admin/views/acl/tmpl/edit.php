<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2017 tzportfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - http://tzportfolio.com/forum

# Family website: http://www.templaza.com

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

$user		= JFactory::getUser();
?>
<script type="text/javascript">

    var orginal_sendPermissions = sendPermissions;
    sendPermissions = function(event){
        //get values and prepare GET-Parameter
        var asset = "not";
        var component = getUrlParam('component');
        var extension = getUrlParam('extension');
        var option    = getUrlParam('option');
        var section   = getUrlParam('section');
        var view      = getUrlParam('view');
        var title     = component;
        var value     = this.value;

        if(view == "acl"){

            //        // set the icon while storing the values
            var icon = document.getElementById("icon_" + this.id);
            icon.removeAttribute("class");
            icon.setAttribute("style", "background: url(../media/system/images/modal/spinner.gif); display: inline-block; width: 16px; height: 16px");

            asset   = option + "." + section;
            title = document.getElementById('jform_title').value;

            var id = this.id.replace("jform_rules_", "");
            var lastUnderscoreIndex = id.lastIndexOf("_");

            var permission_data = {
                comp   : asset,
                action : id.substring(0, lastUnderscoreIndex),
                rule   : id.substring(lastUnderscoreIndex + 1),
                value  : value,
                title  : title
            };

            // Remove js messages, if they exist.
            Joomla.removeMessages();

            // doing ajax request
            jQuery.ajax({
                method: "POST",
                url: document.getElementById("permissions-sliders").getAttribute("data-ajaxuri"),
                data: permission_data,
                datatype: "json"
            })
                .fail(function (jqXHR, textStatus, error) {
                    // Remove the spinning icon.
                    icon.removeAttribute("style");

                    Joomla.renderMessages(Joomla.ajaxErrorsMessages(jqXHR, textStatus, error));

                    window.scrollTo(0, 0);

                    icon.setAttribute("class", "icon-cancel");
                })
                .done(function (response) {
                    // Remove the spinning icon.
                    icon.removeAttribute("style");

                    if (response.data)
                    {
                        // Check if everything is OK
                        if (response.data.result == true)
                        {
                            icon.setAttribute("class", "icon-save");

                            jQuery(event.target).parents().next("td").find("span")
                                .removeClass()
                                .addClass(response["data"]["class"])
                                .html(response.data.text);
                        }
                    }

                    // Render messages, if any. There are only message in case of errors.
                    if (typeof response.messages == "object" && response.messages !== null)
                    {
                        Joomla.renderMessages(response.messages);

                        if (response.data && response.data.result == true)
                        {
                            icon.setAttribute("class", "icon-save");
                        }
                        else
                        {
                            icon.setAttribute("class", "icon-cancel");
                        }

                        window.scrollTo(0, 0);
                    }
                });
        }else{
            orginal_sendPermissions.apply(this,event);
        }
    };
</script>
<form name="adminForm" id="adminForm" method="post" action="<?php
    echo JRoute::_('index.php?option=com_tz_portfolio_plus&view=acl&layout=edit&section='
    .$this->state->get('acl.section')); ?>">
    <div class="tpContainer ">
        <div class="control-group">
            <?php echo $this->form->getInput('rules'); ?>
        </div>

        <?php echo $this->form->getInput('title'); ?>
        <?php echo $this->form->getInput('section'); ?>
        <input type="hidden" value="" name="task">
        <input type="hidden" value="com_tz_portfolio_plus" name="comp">
        <input type="hidden" value="0" name="boxchecked">
        <?php echo JHTML::_('form.token');?>
    </div>
</form>
