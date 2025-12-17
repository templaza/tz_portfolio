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

namespace TemPlaza\Component\TZ_Portfolio\AddOn\User\Profile\Site\View\Portfolio;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\WebAsset\WebAssetManager;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioUri;

/**
 * Portfolio view class.
 */
class HtmlView extends BaseHtmlView
{

    protected $params;
    protected $authorAbout;

    public function display($tpl = null){
        $state                  = $this -> get('State');
        $addon                  = $state -> get($this -> getName().'.addon');
        $this -> params         = $addon -> params;
        $this -> authorAbout    = $this -> get('AuthorAbout');
        return parent::display($tpl);
    }
}
