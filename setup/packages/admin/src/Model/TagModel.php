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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Model;

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\CMS\Object\CMSObject;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\MVC\Model\AdminModel;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TagsHelper;

class TagModel extends AdminModel
{
    function getForm($data = array(), $loadData = true){
        $form = $this->loadForm('com_tz_portfolio.tag', 'tag', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        if (empty($data)) {
            $data = $this->getItem();
            if(isset($data -> params) && $data -> params){
                $params         = new Registry($data -> params);
                $data -> params = $params -> toArray();
            }
            $data -> articles_assignment = $this -> getArticlesAssignment();
        }

        return $data;
    }

    function getItem($pk = null){
        // Initialise variables.
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
        $table  = $this -> getTable();

        if ($pk > 0)
        {
            // Attempt to load the row.
            $return = $table->load($pk);

            // Check for a table object error.
            if ($return === false && $table->getError())
            {
                $this->setError($table->getError());
                return false;
            }
        }

        // Convert to the JObject before adding other data.
        $properties = $table->getProperties(1);
        $item = ArrayHelper::toObject($properties, CMSObject::class);

        if (property_exists($item, 'params'))
        {
            $registry = new Registry();
            if($item -> params) {
                $registry->loadString($item->params);
            }
            $item->params = $registry->toArray();
        }

        return $item;
    }

    public function getArticlesAssignment($pk = null){
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
        if($pk > 0){
            $db     = $this -> getDatabase();
            $query  = $db -> getQuery(true);
            $query -> select('contentid');
            $query -> from('#__tz_portfolio_plus_tag_content_map');
            $query -> where('tagsid = '.$pk);
            $db -> setQuery($query);
            if($rows = $db -> loadColumn()){
                return implode(',',$rows);
            }
        }
        return null;
    }

    public function save($data){
        $app    = Factory::getApplication();
        $input = $app->input;
        $articlesAssignment = null;

        if(isset($data['articles_assignment']) && count($data['articles_assignment'])){
            $articlesAssignment  = $data['articles_assignment'];
            unset($data['articles_assignment']);
        }
        // Automatic handling of alias for empty fields
        if (in_array($input->get('task'), array('apply', 'save', 'save2new')))
        {
            if((!isset($data['id']) || (int) $data['id'] == 0)){
                if ($data['alias'] == null)
                {
                    if (Factory::getConfig()->get('unicodeslugs') == 1)
                    {
                        $data['alias'] = OutputFilter::stringURLUnicodeSlug($data['title']);
                    }
                    else
                    {
                        $data['alias'] = OutputFilter::stringURLSafe($data['title']);
                    }


                    $table = $this -> getTable();
                    if ($table->load(array('alias' => $data['alias'])))
                    {
                        $msg = Text::sprintf('COM_TZ_PORTFOLIO_ALIAS_SAVE_WARNING', $input -> get('view'));
                    }

                    list($title, $alias) = $this->generateNewTitle(0, $data['alias'], $data['title']);
                    $data['alias']  = $alias;

                    if (isset($msg))
                    {
                        $app->enqueueMessage($msg, 'warning');
                    }
                }
            }

            // Check tag's alias
            $alias_check    = TagsHelper::getTagByKey(array('alias' => $data['alias'], 'id' => (int) $data['id']),
                array('id' => true));
            if($alias_check && count($alias_check)){
                $msg    = Text::sprintf('COM_TZ_PORTFOLIO_ALIAS_SAVE_WARNING', $input -> get('view'));
                $this -> setError($msg);
                return false;
            }
            // Check tag's title
            $title_check    = TagsHelper::getTagByKey(array('title' => $data['title'], 'id' => (int) $data['id']),
                array('id' => true));

            if($title_check && count($title_check)){
                $msg    = Text::sprintf('COM_TZ_PORTFOLIO_TITLE_SAVE_WARNING', $input -> get('view'));
                $this -> setError($msg);
                return false;
            }
        }

        if(parent::save($data)){
            $db     = $this -> getDatabase();
            $query  = $db->getQuery(true);
            $id     = $this->getState($this->getName() . '.id');
            if(!empty($articlesAssignment)) {
                $articlesAssignment = array_filter($articlesAssignment);
            }

            // Assign articles with this tag;
            if(!empty($articlesAssignment) && count($articlesAssignment)){

                $query -> select('DISTINCT contentid');
                $query -> from($db -> quoteName('#__tz_portfolio_plus_tag_content_map'));
                $query->where('tagsid = ' . (int) $id);
                $db -> setQuery($query);

                if(!$updateIds = $db -> loadColumn()){
                    $updateIds  = array();
                }

                // Insert article items with this tag if they were created in
                if($insertIds  = array_diff($articlesAssignment,$updateIds)){
                    $insertIds  = array_filter($insertIds);

                    if(!empty($insertIds)) {
                        $query->clear();
                        $query->insert($db->quoteName('#__tz_portfolio_plus_tag_content_map'));
                        $query->columns('contentid,tagsid');
                        foreach ($insertIds as $cid) {
                            $query->values($cid . ',' . $id);
                        }
                        $db->setQuery($query);
                        $db->execute();
                    }
                }
            }

            // Remove tags mappings for article items this tag is NOT assigned to.
            // If unassigned then all existing maps will be removed.
            if (!empty($articlesAssignment) && count($articlesAssignment))
            {
                $query -> clear();
                $query -> delete('#__tz_portfolio_plus_tag_content_map');
                $query->where('contentid NOT IN (' . implode(',', $articlesAssignment) . ')');
                $query->where('tagsid = ' . (int) $id);

                $db->setQuery($query);
                $db->execute();
            }
            return true;
        }
        return true;
    }

    public function delete(&$pks)
    {
        $_pks = (array)$pks;
        $result = parent::delete($pks);
        if($result){
            if ($_pks && count($_pks)) {
                $db     = $this->getDatabase();
                $query  = $db->getQuery(true);

                // Remove tag map to content
                $query -> clear();
                $query -> delete('#__tz_portfolio_plus_tag_content_map');
                $query -> where('tagsid IN(' . implode(',', $_pks) . ')');

                $db -> setQuery($query);
                $db -> execute();
            }
        }
        return $result;
    }

    protected function prepareTable($table){
        if(isset($table -> title) && $table -> title){
//            $table -> title   = str_replace(array(',',';','\'','"','.','?'
//            ,'/','\\','<','>','(',')','*','&','^','%','$','#','@','!','-','+','|','`','~'),' ',$table -> title);
            $table -> title  = trim($table -> title);
        }
        if(is_array($table -> params)){
            $attribs            = new Registry($table -> params);
            $table -> params    = $attribs -> toString();
        }
    }

    protected function generateNewTitle($category_id, $alias, $title)
    {
        // Alter the title & alias
        $table = $this->getTable();

        while ($table->load(array('alias' => $alias)))
        {
            $title = StringHelper::increment($title);
            $alias = StringHelper::increment($alias, 'dash');
        }

        return array($title, $alias);
    }

    protected function canDelete($record)
    {
        if (!empty($record->id))
        {
            $user = Factory::getApplication()->getIdentity();

            if(isset($record -> asset_id) && !empty($record -> asset_id)) {
                $state = $user->authorise('core.delete', $this->option . '.tag.' . (int)$record->id);
            }else{
                $state = $user->authorise('core.delete', $this->option . '.tag');
            }
            return $state;
        }

        return parent::canDelete($record);
    }

    protected function canEditState($record)
    {
        $user = Factory::getApplication()->getIdentity();

        // Check for existing tag.
        if (!empty($record->id))
        {
            if(isset($record -> asset_id) && !empty($record -> asset_id)) {
                return $user->authorise('core.edit.state', $this->option . '.tag.' . (int)$record->id);
            }else{
                return $user->authorise('core.edit.state', $this->option.'.tag');
            }
        }
        return parent::canEditState($record);
    }
}