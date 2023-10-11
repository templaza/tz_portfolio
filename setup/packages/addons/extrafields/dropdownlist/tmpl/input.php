<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

//no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$this -> setAttribute('class', 'custom-select', 'input');

$html = JHtml::_('select.genericlist', $options, $this->getName(), $this->getAttribute(null, null, "input"), 'value', 'text', $value, $this->getId());

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