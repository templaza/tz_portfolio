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

class TZ_Portfolio_PlusTableGroups extends JTable
{
    function __construct(&$db) {
        parent::__construct('#__tz_portfolio_plus_fieldgroups','id',$db);

    }
}
