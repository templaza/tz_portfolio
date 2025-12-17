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

class FormModel extends \TemPlaza\Component\TZ_Portfolio\Administrator\Model\ArticleModel
{
    public function __construct()
    {
        $lang   = Factory::getLanguage();
        $lang -> load('com_tz_portfolio', JPATH_ADMINISTRATOR);
        $lang -> load('com_content', JPATH_ADMINISTRATOR);
        parent::__construct();
    }

    protected function populateState()
    {
        $app = Factory::getApplication();

        // Load state from the request.
        $pk = $app->input->getInt('a_id');
        $this->setState('article.id', $pk);

        $this->setState('article.catid', $app->input->getInt('catid'));

        $return = $app->input->get('return', '', 'base64');
        $this->setState('return_page', base64_decode($return));

        // Load the parameters.
        $params = $app->getParams();
        $this->setState('params', $params);

        $this->setState('layout', $app->input->getString('layout'));
    }


    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $name     The table name. Optional.
     * @param   string  $prefix   The class prefix. Optional.
     * @param   array   $options  Configuration array for model. Optional.
     *
     * @return  Table  A Table object
     *
     * @since   4.0.0
     * @throws  \Exception
     */
    public function getTable($name = 'Article', $prefix = 'Administrator', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }

    public function getItem($itemId = null)
    {

        $itemId = (int) (!empty($itemId)) ? $itemId : $this->getState('article.id');

        // Get a row instance.
        $table = $this->getTable('Article');

        // Attempt to load the row.
        $return = $table->load($itemId);

        // Check for a table object error.
        if ($return === false && $table->getError())
        {
            $this->setError($table->getError());

            return false;
        }

        $properties = $table->getProperties(1);
        $value = ArrayHelper::toObject($properties, CMSObject::class);

        // Convert attrib field to Registry.
        $value->params = new Registry($value->attribs);

        if(isset($value -> attribs) && !empty($value -> attribs)){
            // Convert the params field to an array.
            $registry = new Registry;
            $registry->loadString($value->attribs);
            $value->attribs = $registry->toArray();
        }

        // Compute selected asset permissions.
        $user   = Factory::getUser();
        $userId = $user->get('id');
        $asset  = 'com_tz_portfolio.article.' . $value->id;

        // Check general edit permission first.
        $canApprove = ACLHelper::allowApprove($value);
        $canEdit    = $user->authorise('core.edit', $asset );
        $canEditOwn = $user->authorise('core.edit.own', $asset);

        if(!$canApprove){
            if($value -> state == 4){
                $value->params->set('access-edit', false);
            }
            if(!$canEdit && !$canEditOwn){
                $value->params->set('access-edit', false);
            }
            if($canEdit || $canEditOwn){
                // Grant if current user is owner of the record
                $value->params->set('access-edit', $userId == $value->created_by);
            }
        }else{
            if($canEdit){
                $value->params->set('access-edit', true);
            }
            if($canEditOwn){
                if($userId == $value -> created_by
                    || ($userId != $value -> created_by && ($value -> state == 3 || $value -> state == 4))){
                    $value->params->set('access-edit', true);
                }
            }
            if($value -> state == 3 || $value -> state == 4){
                $value->params->set('access-edit', true);
            }
        }

        // Check edit state permission.
        if ($itemId)
        {
            // Existing item
            $value->params->set('access-change', $user->authorise('core.edit.state', $asset));
        }
        else
        {
            // New item.
            $catId = (int) $this->getState('article.catid');

            if ($catId)
            {
                $value->params->set('access-change', $user->authorise('core.edit.state',
                    'com_tz_portfolio.category.' . $catId));
                $value->catid = $catId;
            }
            else
            {
                $value->params->set('access-change', $user->authorise('core.edit.state', 'com_tz_portfolio'));
            }
        }

        $value->articletext = $value->introtext;

        if (!empty($value->fulltext))
        {
            $value->articletext .= '<hr id="system-readmore" />' . $value->fulltext;
        }

        // Convert the metadata field to an array.
        $registry = new Registry($value->metadata);
        $value->metadata = $registry->toArray();


        if(isset($value -> media) && !empty($value -> media)){
            $media = new Registry;
            $media -> loadString($value -> media);
            $value -> media  = $media -> toArray();
        }

        if (!empty($itemId))
        {
            $value -> tags   = null;
            $tags   = TagHelper::getTagsByArticleId($itemId);
            if($tags && count($tags)) {
                $tags = ArrayHelper::getColumn($tags, 'id');
                $tags = implode(',', $tags);
                $value->tags = $tags;
            }
        }


        return $value;
    }

    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $app = Factory::getApplication();
        $data = $app->getUserState('com_tz_portfolio.edit.article.data', array());

        if (empty($data))
        {
            $data               = $this->getItem();
            if($second_categories  = CategoriesHelper::getCategoriesByArticleId($data -> id, 0)) {
                if (is_array($second_categories)) {
                    $catids = ArrayHelper::getColumn($second_categories, 'id');
                } else {
                    $catids = $second_categories->id;
                }

                $data->set('second_catid', $catids);
            }

            if($main_category      = CategoriesHelper::getCategoriesByArticleId($data -> id, 1)) {
                if (is_array($main_category)) {
                    $catid = ArrayHelper::getColumn($main_category, 'id');
                } else {
                    $catid = $main_category->id;
                }
                $data->set('catid', $catid);
            }
        }

        $this->preprocessData('com_tz_portfolio.article', $data);

        return $data;
    }


    protected function preprocessForm(Form $form, $data, $group = 'content')
    {
        $params = $this->getState()->get('params');

        if ($params && $params->get('enable_category') == 1)
        {
            $form->setFieldAttribute('catid', 'default', $params->get('catid', 1));
            $form->setFieldAttribute('catid', 'readonly', 'true');
        }

        return parent::preprocessForm($form, $data, $group);
    }

    public function getReturnPage(){
        $input  = Factory::getApplication() -> input;
        $return = $input->get('return', null, 'base64');

        return $return;
    }
}
?>