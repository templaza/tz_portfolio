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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$html = "";
if ($options)
{
    $bootstrap_style    = $this -> params -> get('bootstrap_style',1);

    $html .= "<fieldset id=\"" . $this->getId() . "\" class=\"".($bootstrap_style?'btn-group ':'')."radio " . $this->getInputClass() . "\">";
    $number_columns = $this->params->get("number_columns", 0);
    if (!$number_columns)
    {
        foreach ($options AS $key => $option)
        {
            if ($option->text == strtoupper($option->text))
            {
                $text = JText::_($option->text);
            }
            else
            {
                $text = $option->text;
            }
            $text = htmlspecialchars($text, ENT_COMPAT, 'UTF-8');

            $this->setAttribute("value", htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8'), "input");

            if ($option->value === $value)
            {
                $this->setAttribute("checked", "checked", "input");
            }
            else
            {
                $this->setAttribute("checked", null, "input");
            }

            if ((isset($option->disabled) && $option->disabled))
            {
                $this->setAttribute("disabled", "disabled", "input");
            }
            else
            {
                $this->setAttribute("disabled", null, "input");
            }

            $input = "<input id=\"" . $this->getId() . $key . "\" name=\"" . $this->getName() . "\" " . $this->getAttribute(null, null, "input") . " />";
            $html .= "<label class=\"".($bootstrap_style?'btn':'inline')." radio\" for=\"" . $this->getId() . $key . "\">$input $text</label>";
        }
    }
    else
    {
        $html .= '<ul class="radio-box" style="">';
        $width = 100 / (int) $number_columns;
        foreach ($options AS $key => $option)
        {
            $html .= '<li style="width: ' . $width . '%; float: left; clear: none;" >';
            if ($option->text == strtoupper($option->text))
            {
                $text = JText::_($option->text);
            }
            else
            {
                $text = $option->text;
            }

            $this->setAttribute("value", htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8'), "input");

            if ($option->value === $value)
            {
                $this->setAttribute("checked", "checked", "input");
            }
            else
            {
                $this->setAttribute("checked", null, "input");
            }

            if ((isset($option->disabled) && $option->disabled))
            {
                $this->setAttribute("disabled", "disabled", "input");
            }
            else
            {
                $this->setAttribute("disabled", null, "input");
            }

            $input = "<input id=\"" . $this->getId() . $key . "\" name=\"" . $this->getName() . "\" " . $this->getAttribute(null, null, "input") . " />";
            $html .= "<label class=\"radio\" for=\"" . $this->getId() . $key . "\">$input $text</label>";
            $html .= '</li>';
        }
        $html .= '</ul>';
    }
    $html .= "</fieldset>";

    echo $html;
}