<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

use Joomla\Registry\Registry;

JLoader::register('TZ_Portfolio_PlusHelper', COM_TZ_PORTFOLIO_PLUS_ADMIN_HELPERS_PATH
    .DIRECTORY_SEPARATOR.'tz_portfolio_plus.php');
JLoader::register('TZ_Portfolio_PlusFrontHelperExtraFields', COM_TZ_PORTFOLIO_PLUS_SITE_HELPERS_PATH
    .DIRECTORY_SEPARATOR.'extrafields.php');
JLoader::import('com_tz_portfolio_plus.helpers.tags',JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components');
JLoader::import('com_tz_portfolio_plus.helpers.association',JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components');


/**
 * Item Model for an Article.
 *
 * @since  1.6
 */
class TZ_Portfolio_PlusModelArticle extends JModelAdmin
{
    /**
     * @var        string    The prefix to use with controller messages.
     * @since   1.6
     */
    protected $text_prefix = 'COM_CONTENT';

    /**
     * The type alias for this content type (for example, 'com_content.article').
     *
     * @var      string
     * @since    3.2
     */

    public $typeAlias = 'com_tz_portfolio_plus.article';

    protected $associationsContext = 'com_tz_portfolio_plus.article.item';

    /**
     * Batch copy items to a new category or current.
     *
     * @param   integer  $value     The new category.
     * @param   array    $pks       An array of row IDs.
     * @param   array    $contexts  An array of item contexts.
     *
     * @return  mixed  An array of new IDs on success, boolean false on failure.
     *
     * @since   11.1
     */
    protected function batchCopy($value, $pks, $contexts)
    {
        $categoryId = (int) $value;

        $newIds = array();

        if (!parent::checkCategoryId($categoryId))
        {
            return false;
        }

        // Parent exists so we let's proceed
        while (!empty($pks))
        {
            // Pop the first ID off the stack
            $pk = array_shift($pks);

            $this->table->reset();

            // Check that the row actually exists
            if (!$this->table->load($pk))
            {
                if ($error = $this->table->getError())
                {
                    // Fatal error
                    $this->setError($error);

                    return false;
                }
                else
                {
                    // Not fatal error
                    $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
                    continue;
                }
            }

            // Alter the title & alias
            $data = $this->generateNewTitle($categoryId, $this->table->alias, $this->table->title);
            $this->table->title = $data['0'];
            $this->table->alias = $data['1'];

            // Reset the ID because we are making a copy
            $this->table->id = 0;

            // Reset hits because we are making a copy
            $this->table->hits = 0;

            // Unpublish because we are making a copy
            $this->table->state = 0;

            // New category ID
            $this->table->catid = $categoryId;

            // TODO: Deal with ordering?
            // $table->ordering	= 1;

            // Get the featured state
            $featured = $this->table->featured;

            // Check the row.
            if (!$this->table->check())
            {
                $this->setError($this->table->getError());
                return false;
            }

            parent::createTagsHelper($this->tagsObserver, $this->type, $pk, $this->typeAlias, $this->table);

            // Store the row.
            if (!$this->table->store())
            {
                $this->setError($this->table->getError());
                return false;
            }

            // Get the new item ID
            $newId = $this->table->get('id');

            // Add the new ID to the array
            $newIds[$pk] = $newId;

            // Check if the article was featured and update the #__tz_portfolio_plus_content_featured_map table
            if ($featured == 1)
            {
                $db = $this->getDbo();
                $query = $db->getQuery(true)
                    ->insert($db->quoteName('#__tz_portfolio_plus_content_featured_map'))
                    ->values($newId . ', 0');
                $db->setQuery($query);
                $db->execute();
            }
        }

        // Clean the cache
        $this->cleanCache();

        return $newIds;
    }

    protected function generateNewTitle($category_id, $alias, $title)
    {
        // Alter the title & alias
        $table = $this->getTable();

        while ($table->load(array('alias' => $alias, 'catid' => $category_id)))
        {
            $title = JString::increment($title);
            $alias = JString::increment($alias, 'dash');
        }

        return array($title, $alias);
    }

    /**
     * Method to test whether a record can be deleted.
     *
     * @param   object  $record  A record object.
     *
     * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
     *
     * @since   1.6
     */
    protected function canDelete($record)
    {
        if (!empty($record->id))
        {
            if ($record->state != -2)
            {
                return false;
            }
            $user = JFactory::getUser();

            return $user->authorise('core.delete', 'com_tz_portfolio_plus.article.' . (int) $record->id);
        }

        return false;
    }

    /**
     * Method to test whether a record can have its state edited.
     *
     * @param   object  $record  A record object.
     *
     * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
     *
     * @since   1.6
     */
    protected function canEditState($record)
    {
        $user = JFactory::getUser();

        // Check for existing article.
        if (!empty($record->id))
        {
            return $user->authorise('core.edit.state', 'com_tz_portfolio_plus.article.' . (int) $record->id);
        }
        // New article, so check against the category.
        elseif (!empty($record->catid))
        {
            return $user->authorise('core.edit.state', 'com_tz_portfolio_plus.category.' . (int) $record->catid);
        }
        // Default to component settings if neither article nor category known.
        else
        {
            return parent::canEditState('com_tz_portfolio_plus');
        }
    }

    /**
     * Prepare and sanitise the table data prior to saving.
     *
     * @param   JTable  $table  A JTable object.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function prepareTable($table)
    {
        // Set the publish date to now
        $db = $this->getDbo();

        if ($table->state == 1 && (int) $table->publish_up == 0)
        {
            $table->publish_up = JFactory::getDate()->toSql();
        }

        if ($table->state == 1 && intval($table->publish_down) == 0)
        {
            $table->publish_down = $db->getNullDate();
        }

        // Increment the content version number.
        $table->version++;

        // Reorder the articles within the category so the new article is first
        if (empty($table->id))
        {
            $table->reorder('m.catid = ' . (int) $table->catid . ' AND c.state >= 0');
        }
    }

    /**
     * Returns a Table object, always creating it.
     *
     * @param   string  $type    The table type to instantiate
     * @param   string  $prefix  A prefix for the table class name. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  JTable    A database object
     */
    public function getTable($type = 'Content', $prefix = 'TZ_Portfolio_PlusTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get a single record.
     *
     * @param   integer  $pk  The id of the primary key.
     *
     * @return  mixed  Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk))
        {
            // Convert the params field to an array.
            $registry = new Registry;
            $registry->loadString($item->attribs);
            $item->attribs = $registry->toArray();

            // Convert the metadata field to an array.
            $registry = new Registry;
            $registry->loadString($item->metadata);
            $item->metadata = $registry->toArray();

            // Convert the images field to an array.
            $registry = new Registry;
            $registry->loadString($item->images);
            $item->images = $registry->toArray();

            // Convert the urls field to an array.
            $registry = new Registry;
            $registry->loadString($item->urls);
            $item->urls = $registry->toArray();

            $item->articletext = trim($item->fulltext) != '' ? $item->introtext
                . "<hr id=\"system-readmore\" />" . $item->fulltext : $item->introtext;

            if(isset($item -> media) && !empty($item -> media)){
                $media = new Registry;
                $media -> loadString($item -> media);
                $item -> media  = $media -> toArray();
            }
        }

        // Load associated content items
        $app = JFactory::getApplication();
        $assoc = JLanguageAssociations::isEnabled();

        if ($assoc)
        {
            $item->associations = array();

            if ($item->id != null)
            {
//                $associations = JLanguageAssociations::getAssociations('com_tz_portfolio_plus',
//                    '#__tz_portfolio_plus_content', 'com_tz_portfolio_plus.item', $item->id);
                $associations    = TZ_Portfolio_PlusBackEndHelperAssociation::getArticleAssociations($item->id);


                foreach ($associations as $tag => $association)
                {
                    $item->associations[$tag] = $association->id;
                }
            }
        }

        return $item;
    }

    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  mixed  A JForm object on success, false on failure
     *
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_tz_portfolio_plus.article', 'article', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form))
        {
            return false;
        }
        $jinput = JFactory::getApplication()->input;

        // The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
        if ($jinput->get('a_id'))
        {
            $id = $jinput->get('a_id', 0);
        }
        // The back end uses id so we use that the rest of the time and set it to 0 by default.
        else
        {
            $id = $jinput->get('id', 0);
        }
        // Determine correct permissions to check.
        if ($this->getState('article.id'))
        {
            $id = $this->getState('article.id');

            // Existing record. Can only edit in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.edit');

            // Existing record. Can only edit own articles in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.edit.own');
        }
        else
        {
            // New record. Can only create in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.create');
        }

        $user = JFactory::getUser();

        // Check for existing article.
        // Modify the form based on Edit State access controls.
        if ($id != 0 && (!$user->authorise('core.edit.state', 'com_tz_portfolio_plus.article.' . (int) $id))
            || ($id == 0 && !$user->authorise('core.edit.state', 'com_tz_portfolio_plus')))
        {
            // Disable fields for display.
            $form->setFieldAttribute('featured', 'disabled', 'true');
            $form->setFieldAttribute('ordering', 'disabled', 'true');
            $form->setFieldAttribute('publish_up', 'disabled', 'true');
            $form->setFieldAttribute('publish_down', 'disabled', 'true');
            $form->setFieldAttribute('state', 'disabled', 'true');

            // Disable fields while saving.
            // The controller has already verified this is an article you can edit.
            $form->setFieldAttribute('featured', 'filter', 'unset');
            $form->setFieldAttribute('ordering', 'filter', 'unset');
            $form->setFieldAttribute('publish_up', 'filter', 'unset');
            $form->setFieldAttribute('publish_down', 'filter', 'unset');
            $form->setFieldAttribute('state', 'filter', 'unset');
        }

        // Prevent messing with article language and category when editing existing article with associations
        $app = JFactory::getApplication();
        $assoc = JLanguageAssociations::isEnabled();

        // Check if article is associated
        if ($this->getState('article.id') && $app->isSite() && $assoc)
        {
            $associations = TZ_Portfolio_PlusBackEndHelperAssociation::getArticleAssociations($id);

            // Make fields read only
            if (!empty($associations))
            {
                $form->setFieldAttribute('language', 'readonly', 'true');
                $form->setFieldAttribute('catid', 'readonly', 'true');
                $form->setFieldAttribute('language', 'filter', 'unset');
                $form->setFieldAttribute('catid', 'filter', 'unset');
            }
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     *
     * @since   1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $app = JFactory::getApplication();
        $data = $app->getUserState('com_tz_portfolio_plus.edit.article.data', array());

        if (empty($data))
        {
            $data               = $this->getItem();
            if($second_categories  = TZ_Portfolio_PlusHelperCategories::getCategoriesByArticleId($data -> id, 0)) {
                if (is_array($second_categories)) {
                    $catids = JArrayHelper::getColumn($second_categories, 'id');
                } else {
                    $catids = $second_categories->id;
                }

                $data->set('second_catid', $catids);
            }

            if($main_category      = TZ_Portfolio_PlusHelperCategories::getCategoriesByArticleId($data -> id, 1)) {
                if (is_array($main_category)) {
                    $catid = JArrayHelper::getColumn($main_category, 'id');
                } else {
                    $catid = $main_category->id;
                }
                $data->set('catid', $catid);
            }


            // Pre-select some filters (Status, Category, Language, Access) in edit form if those have been selected in Article Manager: Articles
            if ($this->getState($this -> getName().'.id') == 0)
            {
                $filters = (array) $app->getUserState('com_tz_portfolio_plus.articles.filter');
                $data->set('state', $app->input->getInt('state', (!empty($filters['published']) ? $filters['published'] : null)));
                $data->set('catid', $app->input->get('catid', (!empty($filters['category_id']) ? $filters['category_id'] : array())));
                $data->set('language', $app->input->getString('language', (!empty($filters['language']) ? $filters['language'] : null)));
                $data->set('access', $app->input->getInt('access', (!empty($filters['access']) ? $filters['access'] : JFactory::getConfig()->get('access'))));
            }
        }

        $this->preprocessData('com_tz_portfolio_plus.article', $data);

        return $data;
    }


    // Show tags
    public function getTags(){
        $pk = (int) $this->getState($this->getName() . '.id');
        return TZ_Portfolio_PlusHelperTags::getTagTitlesByArticleId($pk);
    }

    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success.
     *
     * @since   1.6
     */
    public function save($data)
    {
        $input      = JFactory::getApplication()->input;
        $filter     = JFilterInput::getInstance();

        if (isset($data['metadata']) && isset($data['metadata']['author']))
        {
            $data['metadata']['author'] = $filter->clean($data['metadata']['author'], 'TRIM');
        }

        if (isset($data['created_by_alias']))
        {
            $data['created_by_alias'] = $filter->clean($data['created_by_alias'], 'TRIM');
        }

        if (isset($data['urls']) && is_array($data['urls']))
        {
            foreach ($data['urls'] as $i => $url)
            {
                if ($url != false && ($i == 'urla' || $i == 'urlb' || $i == 'urlc'))
                {
                    $data['urls'][$i] = JStringPunycode::urlToPunycode($url);
                }
            }

            $registry = new Registry;
            $registry->loadArray($data['urls']);
            $data['urls'] = (string) $registry;
        }

        // Alter the title for save as copy
        if ($input->get('task') == 'save2copy')
        {
            $origTable = clone $this->getTable();
            $origTable->load($input->getInt('id'));

            if ($data['title'] == $origTable->title)
            {
                list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
                $data['title'] = $title;
                $data['alias'] = $alias;
            }
            else
            {
                if ($data['alias'] == $origTable->alias)
                {
                    $data['alias'] = '';
                }
            }

            $data['state'] = 0;
        }

        // Automatic handling of alias for empty fields
        if (in_array($input->get('task'), array('apply', 'save', 'save2new')) && (!isset($data['id']) || (int) $data['id'] == 0))
        {
            if ($data['alias'] == null)
            {
                if (JFactory::getConfig()->get('unicodeslugs') == 1)
                {
                    $data['alias'] = JFilterOutput::stringURLUnicodeSlug($data['title']);
                }
                else
                {
                    $data['alias'] = JFilterOutput::stringURLSafe($data['title']);
                }

                // Verify that the alias is unique
                if(!$this -> verifyAlias($data['id'], $data['alias'], $data['catid'])){
                    $msg    = JText::_('JLIB_DATABASE_ERROR_ARTICLE_UNIQUE_ALIAS');
                    return false;
                }

                list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
                $data['alias'] = $alias;

                if (isset($msg))
                {
                    JFactory::getApplication()->enqueueMessage($msg, 'warning');
                }
            }
        }

        // Verify that the alias is unique
        if(!$this -> verifyAlias($data['id'], $data['alias'], $data['catid'])){
            $this->setError(JText::_('JLIB_DATABASE_ERROR_ARTICLE_UNIQUE_ALIAS'));
            return false;
        }

//        unset($data['catid']);

        $tags   = null;
        if(isset($data['tags'])){
            $tags   = $data['tags'];
            unset($data['tags']);
        }

        if (parent::save($data))
        {
            // Save categories
            $this -> saveArticleCategories($data);

            $table  = $this -> getTable();
            $table -> load($this->getState($this->getName() . '.id'));

            // Save extrafields
            if(isset($data['extrafields'])) {
                $this->saveArticleFields($data['extrafields'], $table);
            }

            if (isset($data['featured']))
            {
                $this->featured($this->getState($this->getName() . '.id'), $data['featured']);
            }

            $assoc = JLanguageAssociations::isEnabled();
            if ($assoc)
            {
                $id = (int) $this->getState($this->getName() . '.id');
                $item = $this->getItem($id);

                // Adding self to the association
                $associations = $data['associations'];

                foreach ($associations as $tag => $id)
                {
                    if (empty($id))
                    {
                        unset($associations[$tag]);
                    }
                }

                // Detecting all item menus
                $all_language = $item->language == '*';

                if ($all_language && !empty($associations))
                {
                    JError::raiseNotice(403, JText::_('COM_CONTENT_ERROR_ALL_LANGUAGE_ASSOCIATED'));
                }

                $associations[$item->language] = $item->id;

                // Deleting old association for these items
                $db     = JFactory::getDbo();
                $query  = $db->getQuery(true)
                    ->delete('#__associations')
                    ->where('context=' . $db->quote('com_tz_portfolio_plus.item'))
                    ->where('id IN (' . implode(',', $associations) . ')');
                $db->setQuery($query);
                $db->execute();

                if ($error = $db->getErrorMsg())
                {
                    $this->setError($error);

                    return false;
                }

                if (!$all_language && count($associations))
                {
                    // Adding new association for these items
                    $key = md5(json_encode($associations));
                    $query->clear()
                        ->insert('#__associations');

                    foreach ($associations as $id)
                    {
                        $query->values($id . ',' . $db->quote('com_tz_portfolio_plus.item') . ',' . $db->quote($key));
                    }

                    $db->setQuery($query);
                    $db->execute();

                    if ($error = $db->getErrorMsg())
                    {
                        $this->setError($error);
                        return false;
                    }
                }
            }


            // Tags
            $articleId  = $this->getState($this->getName() . '.id');
            if (isset($articleId) && $articleId) {
                if(!TZ_Portfolio_PlusHelperTags::insertTagsByArticleId($articleId, $tags)){
                    $this -> setError(TZ_Portfolio_PlusHelperTags::getError());
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    public function saveArticleCategories($data, $table = null, $isNew = true){
        // Insert categories
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);

        //// Before insert new categories must delete old categories
        $query -> delete('#__tz_portfolio_plus_content_category_map');
        $query -> where('contentid = '.$this->getState($this->getName() . '.id'));
        $db -> setQuery($query);
        $db -> execute();

        $query -> clear();

        $query -> insert('#__tz_portfolio_plus_content_category_map');
        $query -> columns('contentid, catid, main');

        if(isset($data['catid'])){
            $query -> values($this->getState($this->getName() . '.id').', '.$data['catid'].', 1');
            unset($data['catid']);
        }

        if(isset($data['second_catid']) && count($data['second_catid'])){
            foreach($data['second_catid'] as $catid){
                $query -> values($this->getState($this->getName() . '.id').', '.$catid.', 0');
            }
            unset($data['second_catid']);
        }

        $db -> setQuery($query);
        if(!$db -> execute()){
            $this -> setError($db -> getErrorMsg());
            return false;
        }
        // End insert categories
    }

    public function saveArticleFields($fieldsData, $table, $isNew = true){
        if($fieldsData){
            if($fields = TZ_Portfolio_PlusFrontHelperExtraFields::getExtraFields($table, null, true)){
                if(count($fields) >= count($fieldsData)){
                    foreach($fields as $field){
                        $fieldObj   = TZ_Portfolio_PlusFrontHelperExtraFields::getExtraField($field, $table);
                        $defValue   = $field -> getDefaultValues();
                        $fieldValue = isset($fieldsData[$field->id]) ? $fieldsData[$field->id] : "";
                        if((!$fieldValue || empty($fieldValue)) && isset($defValue) && !empty($defValue)){
                            $fieldValue = $defValue;
                        }
                        $fieldObj -> onSaveArticleFieldValue($fieldValue);
                    }
                    return true;
                }
            }

            foreach($fieldsData as $id => $fieldValue){
                $fieldObj   = TZ_Portfolio_PlusFrontHelperExtraFields::getExtraField($id, $table);
                $fieldObj -> onSaveArticleFieldValue($fieldValue);
            }

            return true;
        }
        return false;
    }

    protected function verifyAlias($articleId, $alias, $catid){
        if(!empty($alias)){
            $db     = $this -> getDbo();
            $query  = $db -> getQuery(true);

            $query -> select('c.*');
            $query -> from('#__tz_portfolio_plus_content AS c');
            $query -> join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.contentid = c.id');
            $query -> join('LEFT', '#__tz_portfolio_plus_categories AS cc ON cc.id = m.catid');
            if(is_array($catid)){
                $query -> where('m.catid IN('.implode(',',$catid).')');
            }else{
                $query -> where('m.catid = '.$catid);
            }
            $query -> where('c.alias = '. $db -> quote($alias));
            $query -> where('c.id <> '.$articleId);
            $db -> setQuery($query);
            if($db -> loadResult()){
                return false;
            }
        }
        return true;
    }

    /**
     * Method to toggle the featured setting of articles.
     *
     * @param   array    $pks    The ids of the items to toggle.
     * @param   integer  $value  The value to toggle to.
     *
     * @return  boolean  True on success.
     */
    public function featured($pks, $value = 0)
    {
        // Sanitize the ids.
        $pks = (array) $pks;
        JArrayHelper::toInteger($pks);

        if (empty($pks))
        {
            $this->setError(JText::_('COM_CONTENT_NO_ITEM_SELECTED'));

            return false;
        }

        $table = $this->getTable('Featured', 'TZ_Portfolio_PlusTable');

        try
        {
            $db = $this->getDbo();
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__tz_portfolio_plus_content'))
                ->set('featured = ' . (int) $value)
                ->where('id IN (' . implode(',', $pks) . ')');
            $db->setQuery($query);
            $db->execute();

            if ((int) $value == 0)
            {
                // Adjust the mapping table.
                // Clear the existing features settings.
                $query = $db->getQuery(true)
                    ->delete($db->quoteName('#__tz_portfolio_plus_content_featured_map'))
                    ->where('content_id IN (' . implode(',', $pks) . ')');
                $db->setQuery($query);
                $db->execute();
            }
            else
            {
                // First, we find out which of our new featured articles are already featured.
                $query = $db->getQuery(true)
                    ->select('f.content_id')
                    ->from('#__tz_portfolio_plus_content_featured_map AS f')
                    ->where('content_id IN (' . implode(',', $pks) . ')');
                $db->setQuery($query);

                $old_featured = $db->loadColumn();

                // We diff the arrays to get a list of the articles that are newly featured
                $new_featured = array_diff($pks, $old_featured);

                // Featuring.
                $tuples = array();

                foreach ($new_featured as $pk)
                {
                    $tuples[] = $pk . ', 0';
                }

                if (count($tuples))
                {
                    $db = $this->getDbo();
                    $columns = array('content_id', 'ordering');
                    $query = $db->getQuery(true)
                        ->insert($db->quoteName('#__tz_portfolio_plus_content_featured_map'))
                        ->columns($db->quoteName($columns))
                        ->values($tuples);
                    $db->setQuery($query);
                    $db->execute();
                }
            }
        }
        catch (Exception $e)
        {
            $this->setError($e->getMessage());
            return false;
        }

        $table->reorder();

        $this->cleanCache();

        return true;
    }

    /**
     * A protected method to get a set of ordering conditions.
     *
     * @param   object  $table  A record object.
     *
     * @return  array  An array of conditions to add to add to ordering queries.
     *
     * @since   1.6
     */
    protected function getReorderConditions($table)
    {
        $condition = array();
        $condition[] = 'catid = ' . (int) $table->catid;

        return $condition;
    }

    /**
     * Auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   JForm   $form   The form object
     * @param   array   $data   The data to be merged into the form object
     * @param   string  $group  The plugin group to be executed
     *
     * @return  void
     *
     * @since    3.0
     */
    protected function preprocessForm(JForm $form, $data, $group = 'content')
    {
        // Association content items
        $app = JFactory::getApplication();
        $assoc = JLanguageAssociations::isEnabled();

        if ($assoc)
        {
            $languages = JLanguageHelper::getLanguages('lang_code');
            $addform = new SimpleXMLElement('<form />');
            $fields = $addform->addChild('fields');
            $fields->addAttribute('name', 'associations');
            $fieldset = $fields->addChild('fieldset');
            $fieldset->addAttribute('name', 'item_associations');
            $fieldset->addAttribute('description', 'COM_CONTENT_ITEM_ASSOCIATIONS_FIELDSET_DESC');
            $add = false;

            foreach ($languages as $tag => $language)
            {
                if (empty($data->language) || $tag != $data->language)
                {
                    $add = true;
                    $field = $fieldset->addChild('field');
                    $field->addAttribute('name', $tag);
                    $field->addAttribute('type', 'modal_article');
                    $field->addAttribute('language', $tag);
                    $field->addAttribute('label', $language->title);
                    $field->addAttribute('translate_label', 'false');
                    $field->addAttribute('edit', 'true');
                    $field->addAttribute('clear', 'true');
                }
            }
            if ($add)
            {
                $form->load($addform, false);
            }
        }

        // Insert Mediatype from plugins
        $dispatcher	= JDispatcher::getInstance();

        TZ_Portfolio_PlusPluginHelper::importPlugin('mediatype');
        if($mediaType  = $dispatcher ->trigger('onAddMediaType')){
            if(count($mediaType)){
                $xml        = $form -> getXml();
                $field_type = $xml -> xpath('//field[@name="type"]');
                $field_type = $field_type[0];

                foreach($mediaType as $type){
                    if(is_object($type)) {
                        $field_type->addChild('option', $type->text)->addAttribute('value', $type->value);
                    }elseif(is_array($type)){
                        $field_type->addChild('option', $type['text'])->addAttribute('value', $type['value']);
                    }
                }
            }
        }

        parent::preprocessForm($form, $data, $group);
    }


    public function getExtraFields()
    {
        $app        = JFactory::getApplication();
        $articleId  = $app->input->getInt('id', 0);
        $db         = $this -> getDbo();
        $query      = $db -> getQuery(true);

        if($fieldGroups = TZ_Portfolio_PlusFrontHelperExtraFields::getFieldGroupsByArticleId($articleId)){
            $fieldsCache    = array();
            foreach($fieldGroups as $i => $fieldGroup){
                $fieldGroup->fields = array();

                $query -> clear();
                $query->select("field.*, m.groupid");
                $query->from("#__tz_portfolio_plus_fields AS field");
                $query -> join('LEFT','#__tz_portfolio_plus_field_fieldgroup_map AS m ON field.id = m.fieldsid');
                $query -> join('INNER', '#__tz_portfolio_plus_fieldgroups AS fg ON fg.id = m.groupid');

                $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e ON e.element = field.type')
                    -> where('e.type = '.$db -> quote('tz_portfolio_plus-plugin'))
                    -> where('e.folder = '.$db -> quote('extrafields'))
                    -> where('e.published = 1');

                $query->where("field.published = 1");
                $query->where("m.groupid = " . $fieldGroup->id);

                // Ordering by default : core fields, then extra fields
                $query -> order('IF(fg.field_ordering_type = 2, '.$db -> quoteName('m.ordering')
                    .',IF(fg.field_ordering_type = 1,'.$db -> quoteName('field.ordering').',NULL))');

                $db->setQuery($query);
                $_fields = $db->loadObjectList();
                if ($_fields)
                {
                    foreach ($_fields AS $field)
                    {
                        if(!in_array($field -> id, $fieldsCache)) {
                            $fieldObj               = TZ_Portfolio_PlusFrontHelperExtraFields::getExtraField($field, $articleId);
                            $fieldGroup->fields[]   = $fieldObj;
                            $fieldsCache[]          = $field->id;
                        }
                    }
                }

                if(!count($fieldGroup -> fields)){
                    unset($fieldGroups[$i]);
                }
            }
            return $fieldGroups;
        }

        return false;
    }

    /**
     * Custom clean the cache of com_content and content modules
     *
     * @param   string   $group      The cache group
     * @param   integer  $client_id  The ID of the client
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function cleanCache($group = null, $client_id = 0)
    {
        parent::cleanCache('com_tz_portfolio_plus');
        parent::cleanCache('mod_tz_portfolio_plus_archive');
        parent::cleanCache('mod_tz_portfolio_plus_categories');
        parent::cleanCache('mod_tz_portfolio_plus_articles');
    }
}
