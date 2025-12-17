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

//no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$this -> setAttribute('class', 'custom-select', 'input');

$html = HTMLHelper::_('select.genericlist', $options, $this->getName(), $this->getAttribute(null, null, "input"), 'value', 'text', $value, $this->getId());

if(version_compare(JVERSION, '4.0', '<')){
    echo $html;
}else{

    $attr2  = '';
    $attr2 .= ' placeholder="' . Text::_('JGLOBAL_TYPE_OR_SELECT_SOME_OPTIONS') . '" ';

    if ($this -> isRequired()) {
        $attr  .= ' required class="required"';
        $attr2 .= ' required';
    }

    Text::script('JGLOBAL_SELECT_NO_RESULTS_MATCH');
    Text::script('JGLOBAL_SELECT_PRESS_TO_SELECT');

    Factory::getApplication()->getDocument()->getWebAssetManager()
        ->usePreset('choicesjs')
        ->useScript('webcomponent.field-fancy-select');

    ?>
    <joomla-field-fancy-select <?php echo $attr2; ?>><?php echo $html; ?></joomla-field-fancy-select>
    <?php
}