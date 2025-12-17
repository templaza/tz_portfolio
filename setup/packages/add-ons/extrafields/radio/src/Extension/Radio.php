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

namespace TemPlaza\Component\TZ_Portfolio\AddOn\Extrafields\Radio\Extension;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Language\Text as JText;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Common\ExtraFieldCommon;

defined('_JEXEC') or die;

/**
 * Field Radio Add-On
 */
class Radio extends ExtraFieldCommon
{
    protected $multiple_option  = true;

    public function getInput($fieldValue = null, $group = null){

        if(!$this -> isPublished()){
            return "";
        }

        $options = $this->getFieldValues();
        $value   = !is_null($fieldValue) ? $fieldValue : $this->value;

        $this->setAttribute("type", "radio", "input");
        $this->setAttribute("class", $this->getInputClass(), "input");

        $this->setVariable('options', $options);
        $this->setVariable('value', $value);

        return $this->loadTmplFile('input.php', __CLASS__);
    }

    public function getInputClass()
    {
        $isAdmin    = Factory::getApplication() -> isClient('administrator');
        $orgClass   = parent::getInputClass();
        $switcher   = $this -> params -> get('switcher', 0) && count($this -> getFieldValues()) <= 2;

        $class = array();

        if($isAdmin){
            if($switcher){
                $class[] = 'switcher btn-group';
            }else {
                $class[] = 'form-check-input';
            }
        }elseif(!in_array('uk-radio', $class)) {
            $class[] = 'uk-radio';
        }

        if ($class) {
            return $orgClass.implode(' ', $class);
        }

        return $orgClass;
    }

    protected function getParentAttribute(){

        if($this -> getAttribute('disabled', null, 'input')){
            return ' disabled=""';
        }
    }

    public function getSearchName(){
        $params = $this -> params;
        if($params -> get('search_type', 'dropdownlist') == 'checkbox'
            || $params -> get('search_type', 'dropdownlist') == 'multiselect') {
            return 'fields[' . $this->id . '][]';
        }
        return 'fields['.$this -> id.']';
    }

    public function getSearchInput($defaultValue = '')
    {
        if (!$this->isPublished())
        {
            return '';
        }
        $params = $this -> params;

        if ($this->getAttribute('type', '', 'search') == '')
        {
            $this->setAttribute('type', 'text', 'search');
        }

        if ((int) $this->params->get('size', 32))
        {
            $this->setAttribute('size', (int) $this->params->get('size', 32), 'search');
        }

        if(isset($this -> dataSearch[$this -> id])){
            $defaultValue  = $this -> dataSearch[$this -> id];
        }

        $this->setVariable('defaultValue', $defaultValue);

        if($this -> multiple_option) {
            $options    = $this->getFieldValues();

            $value      = !is_null($defaultValue) ? $defaultValue : $this->value;
            if($this -> multiple){
                $value  = (array) $value;
            }

            if($params -> get('search_type', 'dropdownlist') == 'dropdownlist'
                || $params -> get('search_type', 'dropdownlist') == 'multiselect'){
                $options    = $this -> removeDefaultOption($options);

                $firstOption = new \stdClass();

                $lang   = Factory::getLanguage();
                $lang -> load('com_tz_portfolio', JPATH_SITE);

                $firstOption->text      = Text::sprintf('COM_TZ_PORTFOLIO_OPTION_SELECT', $this->getTitle());
                $firstOption->value     = '';
                $firstOption->default   = 1;

                array_unshift($options, $firstOption);

                if($params -> get('search_type', 'dropdownlist') == 'multiselect'){
                    $this -> setAttribute('multiple', 'multiple', 'search');
                }
            }

            $this->setVariable('options', $options);
            $this->setVariable('value', $value);
        }

        if($params -> get('search_type', 'dropdownlist') == 'checkbox') {
            $this->setAttribute('type', 'checkbox', 'search');
        }elseif($params -> get('search_type', 'dropdownlist') == 'radio'){
            $this->setAttribute('type', 'radio', 'search');
        }

        if($html = $this -> loadTmplFile('searchinput')){
            return $html;
        }

        $html   = '<label class="group-label">'.$this -> getTitle().'</label>';

        $html  .= '<input name="'.$this -> getSearchName().'" id="'.$this -> getSearchId().'" '
            .($this -> isRequired()?' required=""':''). $this->getAttribute(null, null, 'search') .'/>';

        return $html;
    }


    public function prepareForm(&$form, $data){
        parent::prepareForm($form, $data);
        $name   = $form -> getName();
        if($name == 'com_tz_portfolio.addon' || $name == 'com_tz_portfolio.field'){

            /* Remove switcher param field if Option field values are more 2 */
            if($name == 'com_tz_portfolio.field'){
                $fieldValues    = isset($data -> value)?$data -> value:array();
                $reg    = new Registry($fieldValues);

                if($reg -> count() > 2){
                    $form -> removeField('switcher', 'params');
                }
            }

//            $form -> removeField('bootstrap_style', 'params');
        }
    }
}