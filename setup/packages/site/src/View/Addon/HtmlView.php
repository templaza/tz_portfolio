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

namespace TemPlaza\Component\TZ_Portfolio\Site\View\Addon;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\WebAsset\WebAssetManager;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioUri;

/**
 * Categories view class for the Category package.
 */
class HtmlView extends BaseHtmlView
{
    public function display($tpl = null)
    {
        $html = $this -> get('RenderAddonView');
        echo $html;
    }
}
