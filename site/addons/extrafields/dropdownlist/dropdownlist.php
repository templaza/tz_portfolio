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

class TZ_Portfolio_PlusExtraFieldDropDownList extends TZ_Portfolio_PlusExtraField{

    protected $multiple_option = true;

    public function getInput($fieldValue = null, $group = null){

        if(!$this -> isPublished()){
            return "";
        }

        $selectOptions  = array();
        $value          = !is_null($fieldValue) ? $fieldValue : $this->value;

        $options        = $this -> getFieldValues();
        if ($options)
        {
            $optGroupState = "close";
            foreach ($options AS $option)
            {
                if ($option->text == strtoupper($option->text))
                {
                    $text = JText::_($option->text);
                }
                else
                {
                    $text = $option->text;
                }

                $selectOptionItem['text']  = htmlspecialchars($text, ENT_COMPAT, 'UTF-8');
                $selectOptionItem['value'] = htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8');

                if (strtoupper($option->value) == "<OPTGROUP>")
                {
                    if ($optGroupState == "open")
                    {
                        $selectOptions[] = JHtml::_('select.option', '</OPTGROUP>');
                        $optGroupState   = "close";
                    }
                    $selectOptions[] = JHtml::_('select.option', '<OPTGROUP>', $selectOptionItem['text']);
                    $optGroupState   = "open";
                }
                elseif (strtoupper($option->value) == "</OPTGROUP>")
                {
                    $selectOptions[] = JHtml::_('select.option', '</OPTGROUP>');
                    $optGroupState   = "close";
                }
                else
                {
                    if (isset($option->disabled) && $option->disabled)
                    {
                        $selectOptions[] = JHtml::_('select.option', $selectOptionItem['value'], $selectOptionItem['text'], "value", "text", true);
                    }
                    else
                    {
                        $selectOptions[] = JHtml::_('select.option', $selectOptionItem['value'], $selectOptionItem['text']);
                    }
                }
            }
        }

        $this->setAttribute("class", $this->getInputClass(), "input");
        if ((int) $this->params->get("size", 5))
        {
            $this->setAttribute("size", (int) $this->params->get("size", 5), "input");
        }

        $this->setVariable('value', $value);
        $this->setVariable('options', $selectOptions);

        return $this -> loadTmplFile('input', __CLASS__);
    }
}