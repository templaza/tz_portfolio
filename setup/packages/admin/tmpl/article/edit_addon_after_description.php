<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    Sonny

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

// After description position
if(isset($this -> afterDescription) && count($this -> afterDescription)) {
    $pt_after   = $this -> afterDescription;

?>
    <div class="mt-px-18">
        <?php
        echo HTMLHelper::_('bootstrap.startAccordion', 'afterDescriptionOptions',
            array('active' => 'afterDescriptionCollapse0', 'parent' => true));
        ?>
            <?php foreach ($pt_after as $i => $pt) {
                echo HTMLHelper::_('bootstrap.addSlide', 'afterDescriptionOptions',
                    $pt -> title, 'afterDescriptionCollapse'.$i);
                echo isset($pt -> html)?$pt -> html:'';
                echo HTMLHelper::_('bootstrap.endSlide');
            } ?>
        <?php echo HTMLHelper::_('bootstrap.endAccordion'); ?>
    </div>
<?php
}
?>