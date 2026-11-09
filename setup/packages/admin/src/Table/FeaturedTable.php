<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    Sonny

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://tzportfolio.com

# Technical Support:  Forum - https://github.com/templaza/tz_portfolio/issues/

# Family website: http://templaza.com

# Family Support: Forum - https://templaza.com/Forums.html

-------------------------------------------------------------------------*/

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Table;

use Joomla\CMS\Table\Table;

// no direct access
defined('_JEXEC') or die;

class FeaturedTable extends Table
{
	/**
	 * @param	Database	A database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__tz_portfolio_plus_content_featured_map', 'content_id', $db);
	}
}
