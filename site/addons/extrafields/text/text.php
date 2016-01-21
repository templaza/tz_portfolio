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

class TZ_Portfolio_PlusExtraFieldText extends TZ_Portfolio_PlusExtraField{

    public function getInput($fieldValue = null, $group = null){

        if(!$this -> isPublished()){
            return "";
        }

        $this->setAttribute("class", $this->getInputClass(), "input");

        if ((int) $this->params->get("size"))
        {
            $this->setAttribute("size", (int) $this->params->get("size"), "input");
        }

        if ($this->params->get("placeholder", ""))
        {
            $placeholder = htmlspecialchars($this->params->get("placeholder", ""), ENT_COMPAT, 'UTF-8');
            $this->setAttribute("placeholder", $placeholder, "input");
        }

        $values          = !is_null($fieldValue) ? $fieldValue : (string) $this -> value;

        $this -> setAttribute('value', $values, 'input');

        return parent::getInput($fieldValue);
    }

    public function getInputClass()
    {
        $class = array();

        if ($this->isRequired())
        {
            $class[] = 'required';
        }

        if ($this->getRegex())
        {
            $class[] = 'validate-' . $this->getId();
            $this->JSValidate();
        }

        if ($this->params->get('auto_suggest', 0))
        {
            $class[] = 'autosuggest';
        }

        if ($class)
        {
            return implode(' ', $class);
        }
        else
        {
            return "";
        }
    }

    protected function getRegex()
    {
        $regex = $this->params->get('regex', 'none');

        if($regex == 'none'){
            $regex  = '';
        }

        if ($regex == "custom")
        {
            $regex = trim($this->params->get('custom_regex', ''));
        }

        if (!$regex)
        {
            $regex = $this->regex;
        }

        return $regex;
    }


    protected function JSValidate()
    {
        $regex = $this->getRegex();

        if (!$regex)
        {
            return false;
        }
        $invalid_message = JText::sprintf('COM_TZ_PORTFOLIO_PLUS_EXTRAFIELDS_FIELD_VALUE_IS_INVALID', $this->getTitle());


        $invalid_message = htmlspecialchars($invalid_message, ENT_COMPAT, 'UTF-8');
        $validate_id     = $this->getId();
        $document        = JFactory::getDocument();

        $script = "jQuery(document).ready(function ($) {
			$('#" . $this->getId() . "-lbl').data(\"invalid_message\",\"" . $invalid_message . "\" );
			document.formvalidator.setHandler('" . $validate_id . "',
				function (value) {
					if(value=='') {
						return true;
					}
					var regex = " . $regex . ";
					return regex.test(value);
				});
			});";

        $document->addScriptDeclaration($script);

        return true;
    }
}