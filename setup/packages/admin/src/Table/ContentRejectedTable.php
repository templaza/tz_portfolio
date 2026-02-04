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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Table;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

class ContentRejectedTable extends Table
{
    public function __construct($db)
    {
        parent::__construct('#__tz_portfolio_plus_content_rejected', 'id', $db);
    }

    public function store($updateNulls = false)
    {
        $date = Factory::getDate();
        $user = Factory::getUser();

//        if (!$this->created)
//        {
            $this->created = $date->toSql();
//        }
//        if (empty($this->created_by))
//        {
            $this->created_by = $user->get('id');
//        }

        return parent::store($updateNulls);
    }

}
