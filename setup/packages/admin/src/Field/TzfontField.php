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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Field;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Uri\Uri;

class TzfontField extends FormField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    protected $type = 'TZFont';

    /**
     * Method to get the field input markup.
     *
     * @return  string   The field input markup.
     *
     * @since   11.1
     */
    protected function getInput()
    {
        $font_list = array(
            HTMLHelper::_('select.option', '', Text::_('JGLOBAL_USE_GLOBAL')),
            HTMLHelper::_('select.option', 'Verdana', 'Verdana'),
            HTMLHelper::_('select.option', 'Georgia', 'Georgia'),
            HTMLHelper::_('select.option', 'Arial', 'Arial'),
            HTMLHelper::_('select.option', 'Impact', 'Impact'),
            HTMLHelper::_('select.option', 'Tahoma', 'Tahoma'),
            HTMLHelper::_('select.option', 'Trebuchet MS', 'Trebuchet MS'),
            HTMLHelper::_('select.option', 'Arial Black', 'Arial Black'),
            HTMLHelper::_('select.option', 'Times New Roman', 'Times New Roman'),
            HTMLHelper::_('select.option', 'Palatino Linotype', 'Palatino Linotype'),
            HTMLHelper::_('select.option', 'Lucida Sans Unicode', 'Lucida Sans Unicode'),
            HTMLHelper::_('select.option', 'MS Serif', 'MS Serif'),
            HTMLHelper::_('select.option', 'Comic Sans MS', 'Comic Sans MS'),
            HTMLHelper::_('select.option', 'Courier New', 'Courier New'),
            HTMLHelper::_('select.option', 'Lucida Console', 'Lucida Console')
        );

        $plugin_path   = JPATH_ROOT . '/media/com_tz_portfolio/assets/vendor/webfonts.json';
        $json = @file_get_contents( $plugin_path );

        $webfonts   = json_decode($json);
        $items      = $webfonts->items;
        $value      = json_decode($this->value);

        foreach ($items as $item) {
            $font_list[]  =   HTMLHelper::_('select.option', $item->family, $item->family);
        }

        $fontWeights = array(
            '100'=>'Thin',
            '200'=>'Extra Light',
            '300'=>'Light',
            '400'=>'Normal',
            '500'=>'Medium',
            '600'=>'Semi Bold',
            '700'=>'Bold',
            '800'=>'Extra Bold',
            '900'=>'Black'
        );

        $fontSpacings = array(
            '0px'=>'0px',
            '1px'=>'1px',
            '2px'=>'2px',
            '3px'=>'3px',
            '4px'=>'4px',
            '5px'=>'5px',
            '6px'=>'6px',
            '7px'=>'7px',
            '8px'=>'8px',
            '9px'=>'9px',
            '10px'=>'10px'
        );

        $fontStyles = array(
            'normal'=>'Normal',
            'italic'=>'Italic',
            'oblique'=>'Oblique'
        );

        $textTransforms = array(
            'uppercase'=>'UPPERCASE',
            'lowercase'=>'lowercase',
            'capitalize'=>'Capitalize'
        );

        $textDecorations = array(
            'underline'=>'underline',
            'line-through'=>'line-through',
            'overline'=>'overline'
        );

        $fontFamily = isset($value->fontFamily) ? $value->fontFamily : '';
        $base_id    =   'tzfont-option';
        $html = '<div class="tzfont-container">';
        $html .= '<div class="'.(JVERSION >= 4 ? 'row' : 'row-fluid').'">';
        $html .= '<div class="span6 col-sm-6">';
        $html .= '<label><small>'. Text::_('TZPORTFOLIO_TYPO_FONT_FAMILY') .'</small></label>';
        // Font list
        $html .= HTMLHelper::_('select.genericlist', $font_list, '', 'class="' .$base_id.'_fontfamily form-select"', 'value', 'text', $fontFamily);

        $html .= '</div>';
        //Font Weight
        $html .= '<div class="span6 col-sm-6">';
        $html .= '<label><small>'. Text::_('TZPORTFOLIO_TYPO_FONT_WEIGHT') .'</small></label>';
        $html .= '<select class="'.$base_id . '_fontweight'.' form-select">';
        $html .= '<option value="">'. Text::_('JGLOBAL_USE_GLOBAL') .'</option>';

        foreach($fontWeights as $key=>$fontWeight)
        {
            if(isset($value->fontWeight) && $value->fontWeight == $key)
            {
                $html .= '<option value="'. $key .'" selected>'. $fontWeight .'</option>';
            }
            else
            {
                $html .= '<option value="'. $key .'">'. $fontWeight .'</option>';
            }
        }
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="'.(JVERSION >= 4 ? 'row' : 'row-fluid').'">';
        //Font Size
        $fontSize = (isset($value->fontSize))?$value->fontSize:'';
        $html .= '<div class="span6 col-sm-6">';
        $html .= '<label><small>'. Text::_('TZPORTFOLIO_TYPO_FONT_SIZE') .'</small></label>';
        $html .= '<input class="'.$base_id . '_fontsize'.' form-control" type="text" value="'. $fontSize .'" min="6">';
        $html .= '</div>';
        //Font Height
        $lineHeight = (isset($value->lineHeight))?$value->lineHeight:'';
        $html .= '<div class="span6 col-sm-6">';
        $html .= '<label><small>'. Text::_('TZPORTFOLIO_TYPO_FONT_HEIGHT') .'</small></label>';
        $html .= '<input class="'.$base_id . '_lineheight'.' form-control" type="text" value="'. $lineHeight .'" min="6">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="'.(JVERSION >= 4 ? 'row' : 'row-fluid').'">';
        //Font Style
        $html .= '<div class="span6 col-sm-6">';
        $html .= '<label><small>'. Text::_('TZPORTFOLIO_TYPO_FONT_STYLE') .'</small></label>';
        $html .= '<select class="'.$base_id . '_fontstyle'.' form-select">';
        $html .= '<option value="">'. Text::_('JGLOBAL_USE_GLOBAL') .'</option>';

        foreach($fontStyles as $key=>$fontStyle)
        {
            if(isset($value->fontStyle) && $value->fontStyle == $key)
            {
                $html .= '<option value="'. $key .'" selected>'. $fontStyle .'</option>';
            }
            else
            {
                $html .= '<option value="'. $key .'">'. $fontStyle .'</option>';
            }
        }
        $html .= '</select>';
        $html .= '</div>';
        //Letter Spacing
        $html .= '<div class="span6 col-sm-6">';
        $html .= '<label><small>'. Text::_('TZPORTFOLIO_TYPO_LETTER_SPACING') .'</small></label>';
        $html .= '<select class="'.$base_id . '_letterspacing'.' form-select">';
        $html .= '<option value="">'. Text::_('JGLOBAL_USE_GLOBAL') .'</option>';
        foreach($fontSpacings as $key=>$fontSpacing)
        {
            if(isset($value->letterSpacing) && $value->letterSpacing == $key)
            {
                $html .= '<option value="'. $key .'" selected>'. $fontSpacing .'</option>';
            }
            else
            {
                $html .= '<option value="'. $key .'">'. $fontSpacing .'</option>';
            }
        }
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="'.(JVERSION >= 4 ? 'row' : 'row-fluid').'">';
        //Text transform
        $html .= '<div class="span6 col-sm-6">';
        $html .= '<label><small>'. Text::_('TZPORTFOLIO_TYPO_TEXT_TRANSFORM') .'</small></label>';
        $html .= '<select class="'.$base_id . '_text_transform'.' form-select">';
        $html .= '<option value="">'. Text::_('JGLOBAL_USE_GLOBAL') .'</option>';

        foreach($textTransforms as $key=>$textTransform)
        {
            if(isset($value->textTransform) && $value->textTransform == $key)
            {
                $html .= '<option value="'. $key .'" selected>'. $textTransform .'</option>';
            }
            else
            {
                $html .= '<option value="'. $key .'">'. $textTransform .'</option>';
            }
        }
        $html .= '</select>';
        $html .= '</div>';

        //Letter Spacing
        $html .= '<div class="span6 col-sm-6">';
        $html .= '<label><small>'. Text::_('TZPORTFOLIO_TYPO_TEXT_DECORATION') .'</small></label>';
        $html .= '<select class="'.$base_id . '_text_decoration'.' form-select">';
        $html .= '<option value="">'. Text::_('JGLOBAL_USE_GLOBAL') .'</option>';
        foreach($textDecorations as $key=>$textDecoration)
        {
            if(isset($value->textDecoration) && $value->textDecoration == $key)
            {
                $html .= '<option value="'. $key .'" selected>'. $textDecoration .'</option>';
            }
            else
            {
                $html .= '<option value="'. $key .'">'. $textDecoration .'</option>';
            }
        }
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';

        // Data store
        $html .= '<input type="hidden" name="'.$this->name.'" id="'.$this->id.'" data-id="'.$base_id.'" class="typoData" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '" />';
        $html .= '</div>';


        return $html;
    }
}