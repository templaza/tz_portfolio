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

namespace TemPlaza\Component\TZ_Portfolio\Site\Controller;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Menu\SiteMenu;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use TemPlaza\Component\TZ_Portfolio\Administrator\Controller\ArticlesController;
use TemPlaza\Component\TZ_Portfolio\Site\Model\PortfolioModel;

// no direct access
defined('_JEXEC') or die;

class MyarticlesController extends ArticlesController
{
    public function search(){
        // Slashes cause errors, <> get stripped anyway later on. # causes problems.
        $badchars = array('#', '>', '<', '\\');
        if($searchword = trim(str_replace($badchars, '', $this->input->getString('searchword', null, 'post')))){
            // If searchword enclosed in double quotes, strip quotes and do exact match
            if (substr($searchword, 0, 1) == '"' && substr($searchword, -1) == '"')
            {
                $post['searchword'] = substr($searchword, 1, -1);
                $this->input->set('searchphrase', 'exact');
            }
            else
            {
                $post['searchword'] = $searchword;
            }
        }

        $data	= $this -> input -> getArray();
        if(isset($data['id'])){
            $id			= $this -> input -> getInt('id');
            $post['id']	= $id;
        }

        // The Itemid from the request, we will use this if it's a search page or if there is no search page available
        $itemId         = $this -> input -> getInt('Itemid');
        $post['Itemid'] = $itemId;

        // Set Itemid id for links from menu

        $uri    = Uri::getInstance();
        $app    = Factory::getApplication();
        $menu   = $app->getMenu();
        $item   = $menu->getItem($post['Itemid']);

        $uri->setQuery($post);
        $uri->setVar('option', 'com_tz_portfolio');

        if($item -> query['view'] == 'portfolio'){
            $uri -> setVar('view', 'portfolio');
        }else{
            $uri -> setVar('view', 'search');
        }

        // The requested Item is not a search page so we need to find one
        if ($item->component != 'com_tz_portfolio' || ($item -> component == 'com_tz_portfolio'
                && $item->query['view'] != 'search' && $item->query['view'] != 'portfolio'))
        {
            // Get item based on component, not link. link is not reliable.
            $item = $menu->getItems('component', 'com_tz_portfolio', true);

            // If we found a search page, use that.
            if (!empty($item))
            {
                $post['Itemid'] = $item->id;
            }
        }

        if($fields = $this->input -> get('fields', array(), 'array')){
            if(count($fields)){
                $fields			= array_filter($fields);
                $post['fields']	= $fields;
            }
        }

        $post['limit']	= $this->input->getUInt('limit', null, 'post');

        unset($post['task']);
        unset($post['submit']);

        $uri = Uri::getInstance();
        $uri->setQuery($post);
        $uri->setVar('option', 'com_tz_portfolio');
        if($item->query['view'] == 'portfolio') {
            $uri->setVar('view', 'portfolio');
        }else{
            $uri->setVar('view', 'search');
        }

        $this->setRedirect(Route::_('index.php' . $uri->toString(array('query', 'fragment')), false));
    }
}