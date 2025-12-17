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

namespace TemPlaza\Component\TZ_Portfolio\Site\View\Portfolio;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Document\Feed\FeedItem;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Menu\SiteMenu;
use Joomla\CMS\MVC\View\CategoryFeedView;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Button\DropdownButton;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\WebAsset\WebAssetManager;
use TemPlaza\Component\TZ_Portfolio\Administrator\Extension\TZ_PortfolioComponent;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\ToolbarHelper as TZ_PortfolioToolbarHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\CategoriesHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\RouteHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\TagHelper;

/**
 * HTML Portfolio View class for the TZ Portfolio component.
 */
class FeedView extends CategoryFeedView
{
    function display($tpl = null)
    {
        $app = Factory::getApplication();

        /* @var Document $doc */
        $doc        = $this -> document;
        $params 	= $app->getParams();
        $feedEmail	= (@$app->get('feed_email')) ? $app->get('feed_email') : 'author';
        $siteEmail	= $app->get('mailfrom');
        // Get some data from the model
        $app->input->set('limit', $app->get('feed_limit'));
        $rows		= $this->get('Items');

        $uri    = Uri::getInstance();
        $doc->setLink( $uri -> getPath());

//        JPluginHelper::importPlugin('tz_portfolio_plus_mediatype');

        foreach ($rows as $row)
        {

            // Compute the article slug
            $row->slug 			= $row->alias ? ($row->id . ':' . $row->alias) : $row->id;
            $row -> description	= ($params->get('feed_summary', 0) ? $row->introtext.$row->fulltext : $row->introtext);

            $results    = $app -> triggerEvent('onContentDisplayMediaType',array('com_tz_portfolio_plus.portfolio',
                &$row, &$params, 0));

            $media	= implode("\n",$results);

            // strip html from feed item title
            $title = $this->escape($row->title);
            $title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');

            $link 	= $row -> link;

            // strip html from feed item description text
            // TODO: Only pull fulltext if necessary (actually, just get the necessary fields).
            $description	= $row -> description;
            $author			= $row->created_by_alias ? $row->created_by_alias : $row->author;
            @$date			= ($row->created ? date('r', strtotime($row->created)) : '');

            // load individual item creator class
            $feedItem = new FeedItem();

            $feedItem->title		= $title;
            $feedItem->link			= $link;

            $feedItem->description	= $media.$description;
            $feedItem->date			= $date;
            $feedItem->category		= $row->category_title;

            $feedItem->author		= $author;
            if ($feedEmail == 'site') {
                $feedItem->authorEmail = $siteEmail;
            }
            else {
                $feedItem->authorEmail = $row->author_email;
            }

            // loads item info into rss array
            $doc->addItem($feedItem);
        }
    }
}
