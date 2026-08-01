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

namespace TemPlaza\Component\TZ_Portfolio\Site\Model;

// no direct access
defined('_JEXEC') or die();

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Menu\SiteMenu;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\ListModel;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\ACLHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\CategoriesHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\ExtraFieldsFrontHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\QueryHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\RouteHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\TagHelper;

class MyarticlesModel extends \TemPlaza\Component\TZ_Portfolio\Administrator\Model\ArticlesModel
{
    protected $pagNav         = null;
    protected $rowsTag        = null;
    protected $categories     = null;
    protected $filterFormName = 'filter_myarticles';

    protected function populateState($ordering = null, $direction = null){
        parent::populateState($ordering,$direction);

        $app    = Factory::getApplication('site');
        $params = $app -> getParams('com_tz_portfolio');
        $this -> setState('params',$params);

        $filters = $app->input->get('filter', array(), 'array');

        $published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
        if($params -> get('filter', '') && $params -> get('filter', '') != ''){
            $published  = $params -> get('filter', '');
        }
        $this -> setState('filter.published', $published);

        $this -> setState('catid',$app -> input -> get('catid'));
    }


    public function getFilterForm($data = array(), $loadData = true)
    {
//        // Get the form.
//        Form::addFormPath(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH. '/models/forms');
//        Form::addFieldPath(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH . '/models/fields');

        $form   = parent::getFilterForm();

        if($form) {
            $params = $this->getState('params');
            $filter = $params->get('filter', '');

            $published = $this->getState('filter.published');
            if ($params->get('filter', '') && $params->get('filter', '') != '') {
                $published = $filter;
            }

            if ($filter && $filter != '*') {
                $form->removeField('published', 'filter');
            } else {
                $form->setValue('published', 'filter', $published);
            }
        }

        return $form;
    }


    protected function getListQuery()
    {
        $user   = Factory::getApplication()->getIdentity();

        $query  = parent::getListQuery();

        $canApprove = $user -> authorise('core.approve', 'com_tz_portfolio');

        if(!$canApprove) {
            $query->where('a.created_by =' . $user->get('id'));
        }

        return $query;
    }
}
?>