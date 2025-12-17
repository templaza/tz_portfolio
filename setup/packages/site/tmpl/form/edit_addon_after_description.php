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

use Joomla\CMS\HTML\HTMLHelper;

// After description position
if(isset($this -> afterDescription) && count($this -> afterDescription)) {
    $pt_after   = $this -> afterDescription;

?>
    <div class="mt-px-18">
        <?php
        echo HTMLHelper::_('bootstrap.startAccordion', 'tp-ac-edit-add_ons__after-desc',
            array('active' => 'tp-ac-edit-add_ons__after-desc__'.$pt_after[0] -> group
                .'-'.$pt_after[0] -> addon, 'parent' => true, 'onHidden' => 'function(){
                    alert("test");
                }'));
        ?>
            <?php foreach ($pt_after as $i => $pt) {
                echo HTMLHelper::_('bootstrap.addSlide', 'tp-ac-edit-add_ons__after-desc',
                    $pt -> title, 'tp-ac-edit-add_ons__after-desc__'.$pt -> group.'-'.$pt -> addon);
                echo isset($pt -> html)?$pt -> html:'';
                echo HTMLHelper::_('bootstrap.endSlide');
            } ?>
        <?php echo HTMLHelper::_('bootstrap.endAccordion'); ?>
    </div>
<?php
}
?>