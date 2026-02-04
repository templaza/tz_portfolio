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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Input\Input;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\ACLHelper;

/**
 * The Category Controller.
 */
class ArticleController extends FormController
{
    protected $view_list = 'articles';

    /**
     * Class constructor.
     *
     * @param   array  $config  A named array of configuration variables.
     *
     * @since	1.6
     */
    function __construct($config = array(),
                         ?MVCFactoryInterface $factory = null,
                         ?CMSApplication $app = null,
                         ?Input $input = null,
                         ?FormFactoryInterface $formFactory = null)
    {

        parent::__construct($config, $factory, $app, $input, $formFactory);

        // An article edit form can come from the articles or featured view.
        // Adjust the redirect view on the value of 'return' in the request.
        if ($this -> input -> getCmd('return') == 'featured')
        {
            $this->view_list = 'featured';
            $this->view_item = 'article&return=featured';
        }

        // Map draft task to save.
        $this->registerTask('draft', 'save');

        $this->registerTask('reject', 'save');
    }

    /**
     * Method override to check if you can add a new record.
     *
     * @param   array  $data  An array of input data.
     *
     * @return  boolean
     *
     * @since   2.2.7
     */
    protected function allowAdd($data = array())
    {
        // Initialise variables.
        $user = Factory::getUser();
        $categoryId = ArrayHelper::getValue($data, 'catid', $this -> input -> getInt('filter_category_id'), 'int');
        $allow = null;

        // If the category has been passed in the data or URL check it.
        $allow = $user->authorise('core.create', 'com_tz_portfolio.category');

        if ($categoryId)
        {
            // If the category has been passed in the data or URL check it.
            $allow = $user->authorise('core.create', 'com_tz_portfolio.category.' . $categoryId);
        }

        if ($allow === null)
        {
            // In the absense of better information, revert to the component permissions.
            $allow  = parent::allowAdd($data);
        }

        if(COM_TZ_PORTFOLIO_EDITION == 'free'){
            // Check total articles is less 50 (only use for free version)
            $model = $this -> getModel('articles');
            if(!$model){
                $allow  = false;
            }

            $maxArticles    = 50;
            $total          = $model->getTotal();

            if($total >= $maxArticles){
                Factory::getApplication() -> enqueueMessage(Text::sprintf('COM_TZ_PORTFOLIO_ARTICLE_LIMIT_ERROR',
                    $maxArticles), 'error');
                $allow  = false;
            }
        }

        return $allow;
    }


    /**
     * Method override to check if you can edit an existing record.
     *
     * @param   array   $data  An array of input data.
     * @param   string  $key   The name of the key for the primary key.
     *
     * @return  boolean
     *
     * @since   1.6
     */
    protected function allowEdit($data = array(), $key = 'id')
    {
        $recordId = (int) isset($data[$key]) ? $data[$key] : 0;
        $user = Factory::getUser();

        // Zero record (id:0), return component edit permission by calling parent controller method
        if (!$recordId)
        {
            return parent::allowEdit($data, $key);
        }

        // Existing record already has an owner, get it
        $record     = $this->getModel()->getItem($recordId);

        if (empty($record))
        {
            return false;
        }

        $canApprove = $this -> allowApprove($data, $key);
        $canEdit    = $user->authorise('core.edit', 'com_tz_portfolio.article.' . $recordId);
        $canEditOwn = $user->authorise('core.edit.own', 'com_tz_portfolio.article.' . $recordId);

        if(!$canApprove){
            if($record -> state == 4){
                return false;
            }
            if(!$canEdit && !$canEditOwn){
                return false;
            }
            if($canEdit || $canEditOwn){
                // Grant if current user is owner of the record
                return $user->id == $record->created_by;
            }
        }else{
            if($canEdit){
                return true;
            }
            if($canEditOwn){
                if($user -> id == $record -> created_by
                    || ($user -> id != $record -> created_by && ($record -> state == 3 || $record -> state == 4))){
                    return true;
                }
            }
            if($record -> state == 3 || $record -> state == 4){
                return true;
            }
        }

        return false;
    }

    protected function allowSave($data, $key = 'id')
    {
        if($this -> task == 'reject'){
            $canApprove = $this -> allowApprove($data, $key);
            if(!$canApprove){
                return false;
            }
        }

        $allowSave  = parent::allowSave($data, $key);
        return $allowSave;
    }

    protected function allowApprove($data = array(), $key = 'id')
    {
        $recordId   = (int) isset($data[$key]) ? $data[$key] : 0;
        $record     = $this -> getModel() -> getItem($recordId);

        return ACLHelper::allowApprove($record);
    }

    public function edit($key = null, $urlVar = null)
    {
        $model = $this->getModel();
        $table = $model->getTable();
        $cid   = $this->input->post->get('cid', array(), 'array');
        $context = "$this->option.edit.$this->context";

        // Determine the name of the primary key for the data.
        if (empty($key))
        {
            $key = $table->getKeyName();
        }

        // To avoid data collisions the urlVar may be different from the primary key.
        if (empty($urlVar))
        {
            $urlVar = $key;
        }

        // Get the previous record id (if any) and the current record id.
        $recordId = (int) (count($cid) ? $cid[0] : $this->input->getInt($urlVar));

        $edit   = parent::edit($key, $urlVar);

        if($edit && $this -> allowApprove()){
            // Set state is under review (state = 4) if the article is pending (state = 3).
            if($table -> load($recordId) && $table -> state == 3){
                $db     = Factory::getDbo();
                $query  = $db -> getQuery(true);
                $query -> update($table -> getTableName());
                $query -> set('state = 4');
                $query -> where('id = '.$recordId);
                $db -> setQuery($query);
                $db -> execute();
            }
        }
        return $edit;
    }

    /**
     * Method to run batch operations.
     *
     * @param   object  $model  The model.
     *
     * @return  boolean	 True if successful, false otherwise and internal error is set.
     *
     * @since   1.6
     */
    public function batch($model = null)
    {
        $this -> checkToken();

        /** @var \TemPlaza\Component\TZ_Portfolio\Administrator\Model\ArticlesModel $model */
        $model = $this->getModel('Article');

        // Preset the redirect
        $this->setRedirect(Route::_('index.php?option=com_tz_portfolio&view=articles' . $this->getRedirectToListAppend(), false));

        return parent::batch($model);
    }

    public function tags(){
        $model      = $this -> getModel('Tags','Administrator',array('ignore_request' => true));
        $model -> setState('term',$this -> input -> getString('term',null));
        echo json_encode($model -> getTags());
        die();
    }
}
