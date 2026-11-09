<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    Sonny

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://tzportfolio.com

# Technical Support:  Forum - https://github.com/templaza/tz_portfolio/issues/

# Family website: http://templaza.com

# Family Support: Forum - https://templaza.com/Forums.html

-------------------------------------------------------------------------*/

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

// Before description position
if(isset($this -> beforeDescription) && count($this -> beforeDescription)) {
    $pt_before   = $this -> beforeDescription;

    echo HTMLHelper::_('bootstrap.startAccordion', 'beforeDescriptionOptions',
        array('active' => 'beforeDescriptionCollapse0', 'parent' => true));
    ?>
    <fieldset>
        <?php foreach ($pt_before as $i => $pt) {
            echo HTMLHelper::_('bootstrap.addSlide', 'beforeDescriptionOptions',
                $pt -> title, 'beforeDescriptionCollapse'.$i);
            echo isset($pt -> html)?$pt -> html:'';
            echo HTMLHelper::_('bootstrap.endSlide');
        } ?>
    </fieldset>
    <?php
    echo HTMLHelper::_('bootstrap.endAccordion');
}
?>