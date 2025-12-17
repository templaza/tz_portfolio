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

namespace TemPlaza\Component\TZ_Portfolio\AddOn\Extrafields\Multipleselect\Extension;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Language\Text as JText;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Common\ExtraFieldCommon;
use TemPlaza\Component\TZ_Portfolio\AddOn\Extrafields\Dropdownlist\Extension\Dropdownlist;

defined('_JEXEC') or die;

/**
 * Field Multipleselect Add-On
 */
class Multipleselect extends Dropdownlist
{
    protected $multiple = true;

    public function getInput($fieldValue = null, $group = null){

        if(!$this -> isPublished()){
            return "";
        }

        $this -> setAttribute('multiple', 'multiple', 'input');

        return parent::getInput($fieldValue);
    }

    public function getSearchInput($defaultValue = "")
    {
        $this->setAttribute('type', 'checkbox', 'search');

        $this -> setAttribute('multiple', 'multiple', 'search');

        return parent::getSearchInput($defaultValue);
    }
}