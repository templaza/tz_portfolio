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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Field;

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\PredefinedlistField;

class TZStatusField extends PredefinedlistField{

    public $type        = 'TZStatus';
    protected $predefinedOptions = array(
        '-3' =>	'COM_TZ_PORTFOLIO_DRAFTS',
        '3' =>	'COM_TZ_PORTFOLIO_PENDING',
        '4' =>	'COM_TZ_PORTFOLIO_UNDER_REVIEW',
        '-2' =>	'JTRASHED',
        '0'  => 'JUNPUBLISHED',
        '1'  => 'JPUBLISHED',
        '2'  => 'JARCHIVED',
        '*'  => 'JALL',
    );


}