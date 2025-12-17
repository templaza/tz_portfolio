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

namespace TemPlaza\Component\TZ_Portfolio\AddOn\Extrafields\Checkboxes\Extension;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Language\Text as JText;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Common\ExtraFieldCommon;

defined('_JEXEC') or die;

/**
 * Field Checkboxes Add-On
 */
class Checkboxes extends ExtraFieldCommon
{
    protected $multiple_option  = true;
    protected $multiple         = true;

    public function getInput($fieldValue = null, $group = null){

        if (!$this->isPublished())
        {
            return "";
        }

        $this->setAttribute("type", "checkbox", "input");

        $value   = !is_null($fieldValue) ? (array) $fieldValue : (array) $this->value;
        $options = $this->getFieldValues();

        $this->setVariable('value', $value);
        $this->setVariable('options', $options);

        return $this->loadTmplFile('input.php', __CLASS__);
    }

    public function getSearchInput($defaultValue = ''){

        if (!$this->isPublished())
        {
            return '';
        }

        $this->setAttribute('type', 'checkbox', 'search');

        return parent::getSearchInput($defaultValue);
    }
}