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

namespace TemPlaza\Component\TZ_Portfolio\AddOn\Content\Vote\Site\View\Portfolio;

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
    protected $item     = null;
    protected $params   = null;
    protected $head     = array();

    public function display($tpl = null){

        $this -> item   = $this -> get('Item');
        $state          = $this -> get('State');
        $params         = $state -> get('params');
        $addon          = $state -> get($this -> getName().'.addon');
        $this -> params = $params;

        if(!isset($this -> head['display'])){
            $this -> head['display']    = false;
        }
        if(!isset($this -> head['layout_'.$this -> getLayout()])){
            $this -> head['layout_'.$this -> getLayout()]    = false;
        }

        if(!$this -> head['display']) {

            /* @var WebAssetManager $wa */
            $wa = $this -> document -> getWebAssetManager();

            $wa -> registerAndUseStyle('com_tz_portfolio.addon.content.vote', TZ_PortfolioUri::root()
                . '/add-ons/content/vote/css/vote.css'/*, array('version' => 'auto')*/);

            $wa -> registerAndUseScript('com_tz_portfolio.addon.content.vote', TZ_PortfolioUri::root()
                . '/add-ons/content/vote/js/vote.tz_portfolio.js',
                array('version' => 'auto', 'relative' => true));

            $this -> head['display']   = true;
        }

        parent::display($tpl);

        $this -> head['layout_'.$this -> getLayout()] = true;
    }
}
