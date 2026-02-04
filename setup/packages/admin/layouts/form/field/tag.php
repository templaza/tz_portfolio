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

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Layout variables
 * ---------------------
 *
 * @var  string   $selector       The id of the field
 * @var  string   $minTermLength  The minimum number of characters for the tag
 * @var  boolean  $allowCustom    Can we insert custom tags?
 */

extract($displayData);

    $doc    	= Factory::getDocument();
    $wa = $doc->getWebAssetManager();
    $wa ->useScript('jquery');


    $html = [];
    $attr = '';

    // Initialize some field attributes.
    $attr .= $multiple ? ' multiple' : '';
    $attr .= $autofocus ? ' autofocus' : '';
    $attr .= $onchange ? ' onchange="' . $onchange . '"' : '';
    $attr .= $dataAttribute;

    // To avoid user's confusion, readonly="readonly" should imply disabled="disabled".
    if ($readonly || $disabled) {
        $attr .= ' disabled="disabled"';
    }

    $attr2  = '';
    $attr2 .= !empty($class) ? ' class="' . $class . '"' : '';
    $attr2 .= ' placeholder="' . $this->escape($hint ?: Text::_('JGLOBAL_TYPE_OR_SELECT_SOME_TAGS')) . '" ';
    $attr2 .= $dataAttribute;

    if ($allowCustom) {
        $attr2 .= $allowCustom ? ' allow-custom' : '';
        $attr2 .= $allowCustom ? ' new-item-prefix="#new#"' : '';
    }

    if ($remoteSearch) {
        $attr2 .= ' remote-search';
//        $attr2 .= ' url="' . Uri::root() . 'index.php?option=com_tz_portfolio&task=tags.searchAjax"';
        $attr2 .= ' url="' . Uri::base(true) . '/index.php?option=com_tz_portfolio&task=tags.searchAjax"';
        $attr2 .= ' term-key="like"';
        $attr2 .= ' min-term-length="' . $minTermLength . '"';
    }

    if ($required) {
        $attr  .= ' required class="required"';
        $attr2 .= ' required';
    }

    // Create a read-only list (no name) with hidden input(s) to store the value(s).
    if ($readonly) {
        $html[] = HTMLHelper::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $value, $id);

        // E.g. form field type tag sends $this->value as array
        if ($multiple && is_array($value)) {
            if (!count($value)) {
                $value[] = '';
            }

            foreach ($value as $val) {
                $html[] = '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($val, ENT_COMPAT, 'UTF-8') . '">';
            }
        } else {
            $html[] = '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '">';
        }
    } else // Create a regular list.
    {
        $html[] = HTMLHelper::_('select.genericlist', $options, $name, trim($attr), 'value', 'text', $value, $id);
    }

    Text::script('JGLOBAL_SELECT_NO_RESULTS_MATCH');
    Text::script('JGLOBAL_SELECT_PRESS_TO_SELECT');

    Factory::getDocument()->getWebAssetManager()
        ->usePreset('choicesjs')
        ->useScript('webcomponent.field-fancy-select');

    ?>
    <joomla-field-fancy-select <?php echo $attr2; ?>><?php echo implode($html); ?></joomla-field-fancy-select>