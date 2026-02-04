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
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Versioning\VersionableControllerTrait;

/**
 * The Category Controller.
 */
class CategoryController extends FormController
{

    use VersionableControllerTrait;

    /**
     * The extension for which the categories apply.
     *
     * @var    string
     * @since  1.6
     */
    protected $extension	= 'com_tz_portfolio';

    /**
     * Constructor.
     *
     * @param  array  $config  An optional associative array of configuration settings.
     *
     * @since  1.6
     * @see    JController
     */
    public function __construct($config = [], MVCFactoryInterface $factory = null, CMSApplication $app = null, Input $input = null)
    {
        parent::__construct($config, $factory, $app, $input);

        Factory::getApplication() -> getLanguage() -> load('com_categories');

        // Guess the JText message prefix. Defaults to the option.
        if (empty($this->extension))
        {
            $this->extension = $this -> input -> getCmd('extension', 'com_tz_portfolio');
        }
    }

    /**
     * Method to check if you can add a new record.
     *
     * @param   array  $data  An array of input data.
     *
     * @return  boolean
     *
     * @since   1.6
     */
    protected function allowAdd($data = array())
    {
//        $user = TZ_Portfolio_PlusUser::getUser();
        $user = Factory::getUser();
        return ($user->authorise('core.create', $this->extension) || count($user->getAuthorisedCategories($this->extension, 'core.create')));
    }

    /**
     * Method to check if you can edit a record.
     *
     * @param   array   $data  An array of input data.
     * @param   string  $key   The name of the key for the primary key.
     *
     * @return  boolean
     *
     * @since   1.6
     */
    protected function allowEdit($data = array(), $key = 'parent_id')
    {
        // Initialise variables.
        $recordId = (int) isset($data[$key]) ? $data[$key] : 0;
        $user = Factory::getUser();
        $userId = $user->get('id');

        // Check general edit permission first.
        if ($user->authorise('core.edit', $this->extension))
        {
            return true;
        }

        // Check specific edit permission.
        if ($user->authorise('core.edit', $this->extension . '.category.' . $recordId))
        {
            return true;
        }

        // Fallback on edit.own.
        // First test if the permission is available.
        if ($user->authorise('core.edit.own', $this->extension . '.category.' . $recordId) || $user->authorise('core.edit.own', $this->extension))
        {
            // Now test the owner is the user.
            $ownerId = (int) isset($data['created_user_id']) ? $data['created_user_id'] : 0;
            if (empty($ownerId) && $recordId)
            {
                // Need to do a lookup from the model.
                $record = $this->getModel()->getItem($recordId);

                if (empty($record))
                {
                    return false;
                }

                $ownerId = $record->created_user_id;
            }

            // If the owner matches 'me' then do the test.
            if ($ownerId == $userId)
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Method to run batch operations.
     *
     * @param   object  $model  The model.
     *
     * @return  boolean	 True if successful, false otherwise and internal error is set.
     *
     */
    public function batch($model = null)
    {
        $this -> checkToken();

        /** @var \TemPlaza\Component\TZ_Portfolio\Administrator\Model\CategoryModel $model */
        $model = $this->getModel('Category');

        // Preset the redirect
        $this->setRedirect('index.php?option='.$this -> extension.'&view=categories&extension=' . $this->extension);

        return parent::batch($model);
    }

    /**
     * Gets the URL arguments to append to an item redirect.
     *
     * @param   integer  $recordId  The primary key id for the item.
     * @param   string   $urlVar    The name of the URL variable for the id.
     *
     * @return  string  The arguments to append to the redirect URL.
     *
     * @since   1.6
     */
    protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
    {
        $append = parent::getRedirectToItemAppend($recordId);
//		$append .= '&extension=' . $this->extension;

        return $append;
    }

    /**
     * Gets the URL arguments to append to a list redirect.
     *
     * @return  string  The arguments to append to the redirect URL.
     *
     * @since   1.6
     */
    protected function getRedirectToListAppend()
    {
        $append = parent::getRedirectToListAppend();

        return $append;
    }

    public function extrafields(){
        $model  = $this -> getModel('Category');
        $data   = $model -> extrafields();
        echo $data;
        die();
    }
}
