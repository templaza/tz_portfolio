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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Library\Controller;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\Utilities\ArrayHelper;

class TPAdminController extends AdminController {

    protected $option;
    protected $view_list;
    protected $text_prefix;
    protected $core_view_list;

    public function __construct($config = array())
    {
        parent::__construct($config);

        // Define standard task mappings.

        // Value = 0
        $this->registerTask('unpublish', 'publish');

        // Value = 2
        $this->registerTask('archive', 'publish');

        // Value = -2
        $this->registerTask('trash', 'publish');

        // Value = -3
        $this->registerTask('report', 'publish');
        $this->registerTask('orderup', 'reorder');
        $this->registerTask('orderdown', 'reorder');

        // Guess the option as com_NameOfController.
        if (empty($this->option))
        {
            $this->option = 'com_tz_portfolio_plus';
        }

        // Guess the Text message prefix. Defaults to the option.
        if (empty($this->text_prefix))
        {
            $this->text_prefix = strtoupper($this->option);
        }

        // Guess the list view as the suffix, eg: OptionControllerSuffix.
        if (empty($this->view_list))
        {
            $r = null;

            if (!preg_match('/(.*)Controller(.*)/i', get_class($this), $r))
            {
                throw new \Exception(Text::_('JLIB_APPLICATION_ERROR_CONTROLLER_GET_NAME'), 500);
            }

            $this->view_list = strtolower($r[2]);
        }
    }

    public function delete()
    {
        // Check for request forgeries
        $this -> checkToken();

        // Get items to remove from the request.
        $cid = Factory::getApplication()->input->get('cid', array(), 'array');

        if (!is_array($cid) || count($cid) < 1)
        {
            Log::add(Text::_($this->text_prefix . '_NO_ITEM_SELECTED'), Log::WARNING, 'jerror');
        }
        else
        {
            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            $cid    = ArrayHelper::toInteger($cid);

            // Remove the items.
            if ($model->delete($cid))
            {
                $this->setMessage(Text::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
            }
            else
            {
                $this->setMessage($model->getError(), 'error');
            }

            // Invoke the postDelete method to allow for the child class to access the model.
            $this->postDeleteHook($model, $cid);
        }

        $this->setRedirect(Route::_($this ->getAddonRedirect(), false));
    }

    public function display($cachable = false, $urlparams = array())
    {
        return $this;
    }

    public function publish()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

        // Get items to publish from the request.
        $cid = Factory::getApplication()->input->get('cid', array(), 'array');
        $data = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
        $task = $this->getTask();
        $value = ArrayHelper::getValue($data, $task, 0, 'int');

        if (empty($cid))
        {
            Log::add(Text::_($this->text_prefix . '_NO_ITEM_SELECTED'), Log::WARNING, 'jerror');
        }
        else
        {
            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            $cid    = ArrayHelper::toInteger($cid);

            // Publish the items.
            try
            {
                $model->publish($cid, $value);

                if ($value == 1)
                {
                    $ntext = $this->text_prefix . '_N_ITEMS_PUBLISHED';
                }
                elseif ($value == 0)
                {
                    $ntext = $this->text_prefix . '_N_ITEMS_UNPUBLISHED';
                }
                elseif ($value == 2)
                {
                    $ntext = $this->text_prefix . '_N_ITEMS_ARCHIVED';
                }
                else
                {
                    $ntext = $this->text_prefix . '_N_ITEMS_TRASHED';
                }

                $this->setMessage(Text::plural($ntext, count($cid)));
            }
            catch (Exception $e)
            {
                $this->setMessage($e->getMessage(), 'error');
            }
        }

        $extension = $this->input->get('extension');
        $extensionURL = ($extension) ? '&extension=' . $extension : '';
        $this->setRedirect(Route::_($this -> getAddonRedirect() . $extensionURL, false));
    }


    public function reorder()
    {
        // Check for request forgeries.
        $this -> checkToken();

        $ids = Factory::getApplication()->input->post->get('cid', array(), 'array');
        $inc = ($this->getTask() == 'orderup') ? -1 : 1;

        $model = $this->getModel();
        $return = $model->reorder($ids, $inc);

        if ($return === false)
        {
            // Reorder failed.
            $message = Text::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
            $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message, 'error');

            return false;
        }
        else
        {
            // Reorder succeeded.
            $message = Text::_('JLIB_APPLICATION_SUCCESS_ITEM_REORDERED');
            $this->setRedirect(Route::_($this -> getAddonRedirect(), false), $message);

            return true;
        }
    }


    public function saveorder()
    {
        // Check for request forgeries.
        $this -> checkToken();

        // Get the input
        $pks = $this->input->post->get('cid', array(), 'array');
        $order = $this->input->post->get('order', array(), 'array');

        // Sanitize the input
        $pks    = ArrayHelper::toInteger($pks);
        $order  = ArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel();

        // Save the ordering
        $return = $model->saveorder($pks, $order);

        if ($return === false)
        {
            // Reorder failed
            $message = Text::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
            $this->setRedirect(Route::_($this -> getAddonRedirect(), false), $message, 'error');

            return false;
        }
        else
        {
            // Reorder succeeded.
            $this->setMessage(Text::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));
            $this->setRedirect(Route::_($this -> getAddonRedirect(), false));

            return true;
        }
    }

    /**
     * Check in of one or more records.
     *
     * @return  boolean  True on success
     *
     * @since   12.2
     */
    public function checkin()
    {
        // Check for request forgeries.
        $this -> checkToken();

        $ids = Factory::getApplication()->input->post->get('cid', array(), 'array');

        $model = $this->getModel();
        $return = $model->checkin($ids);

        if ($return === false)
        {
            // Checkin failed.
            $message = Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError());
            $this->setRedirect(Route::_($this -> getAddonRedirect(), false), $message, 'error');

            return false;
        }
        else
        {
            // Checkin succeeded.
            $message = Text::plural($this->text_prefix . '_N_ITEMS_CHECKED_IN', count($ids));
            $this->setRedirect(Route::_($this -> getAddonRedirect(), false), $message);

            return true;
        }
    }

    /**
     * Method to save the submitted ordering values for records via AJAX.
     *
     * @return  void
     *
     * @since   3.0
     */
    public function saveOrderAjax()
    {
        // Get the input
        $pks = $this->input->post->get('cid', array(), 'array');
        $order = $this->input->post->get('order', array(), 'array');

        // Sanitize the input
        $pks    = ArrayHelper::toInteger($pks);
        $order  = ArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel();

        // Save the ordering
        $return = $model->saveorder($pks, $order);

        if ($return)
        {
            echo "1";
        }

        // Close the application
        Factory::getApplication()->close();
    }

    protected function postDeleteHook(BaseDatabaseModel $model, $id = null)
    {
    }

    protected function getAddonRedirect($addon_view = null){
        $addon_view = $addon_view?$addon_view:$this -> view_list;
        return parent::getAddonRedirect($addon_view);
    }
}