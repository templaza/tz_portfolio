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

// No direct access
defined('_JEXEC') or die('Restricted access');

?>
<div aria-hidden="true" aria-labelledby="myRemovePresetTitle" role="dialog" tabindex="-1" id="removePreset" class="modal<?php
echo !COM_TZ_PORTFOLIO_JVERSION_4_COMPARE?' tz-modal-sm':''; ?> fade">
    <div class="modal-dialog">
        <div class="modal-content modal-sm">
            <div class="modal-header">
                <h3 id="myRemovePresetTitle" class="modal-title"><?php echo JText::_('COM_TZ_PORTFOLIO_REMOVE_PRESET');?></h3>
                <button aria-hidden="true" data-dismiss="modal" data-bs-dismiss="modal" class="btn-close close order-2" type="button"><?php
                    echo !COM_TZ_PORTFOLIO_JVERSION_4_COMPARE?'×':'';?></button>
            </div>
            <div class="modal-body p-3">
                <p>
                    <?php echo JText::sprintf('COM_TZ_PORTFOLIO_CLICK_TO_REMOVE_PRESET','<font color="red">"<strong>'
                        .JText::_('JTOOLBAR_REMOVE').'</strong>"</font>')?>
                </p>
                <p><em class="text-warning"><?php echo JText::_('COM_TZ_PORTFOLIO_REMOVE_PRESET_BOX_DESC');?></em></p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" data-bs-dismiss="modal" class="btn btn-default" type="button"><?php echo JText::_('JTOOLBAR_CLOSE');?></button>
                <button id="removePresetAccept" class="btn btn-danger" type="button"><?php echo JText::_('JTOOLBAR_REMOVE');?></button>
            </div>
        </div>
    </div>
</div>
