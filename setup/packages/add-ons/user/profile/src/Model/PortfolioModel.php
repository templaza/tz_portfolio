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

namespace TemPlaza\Component\TZ_Portfolio\AddOn\Content\Vote\Site\Model;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use TemPlaza\Component\TZ_Portfolio\AddOn\Content\Vote\Helper\VoteHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\AddOn\AddOnItemModel;

class PortfolioModel extends AddOnItemModel {

    protected $authorId;

    public function getAuthorAbout($authorId = null){
        $authorId   = $authorId?$authorId:$this -> authorId;

        return PlgTZ_Portfolio_PlusUserProfileHelper::getAuthorAbout($authorId);
    }
}