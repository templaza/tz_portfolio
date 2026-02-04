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
use Joomla\CMS\Log\Log;
use Joomla\Input\Input;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Utilities\ArrayHelper;

/**
 * The Categories List Controller.
 */
class FieldsController extends AdminController
{
    protected $input        = null;
    protected $text_prefix  = 'COM_TZ_PORTFOLIO_FIELDS';

    public function __construct($config = array(), MVCFactoryInterface $factory = null,
                                ?CMSApplication $app = null, ?Input $input = null){
        $this -> input  = Factory::getApplication() -> input;
        parent::__construct($config, $factory, $app, $input);

        $this -> registerTask('listview', 'updatestate');
        $this -> registerTask('unlistview', 'updatestate');
        $this -> registerTask('detailview', 'updatestate');
        $this -> registerTask('undetailview', 'updatestate');
        $this -> registerTask('advsearch', 'updatestate');
        $this -> registerTask('unadvsearch', 'updatestate');
    }
	/**
	 * Proxy for getModel
	 *
	 * @param	string	$name	The model name. Optional.
	 * @param	string	$prefix	The class prefix. Optional.
	 *
	 * @return	object	The model.
	 * @since	1.6
	 */
	function getModel($name = 'Field', $prefix = 'Administrator', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

    public function delete()
    {
        // Check for request forgeries
        $this -> checkToken();

        // Get items to remove from the request.
        $cid = $this -> input -> get('cid', array(), 'array');

        if (!is_array($cid) || count($cid) < 1)
        {
            Factory::getApplication() -> enqueueMessage(Text::_($this->text_prefix . '_NO_ITEM_SELECTED'), 'error');

        }
        else
        {
            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            jimport('joomla.utilities.arrayhelper');
            $cid    = ArrayHelper::toInteger($cid);

            // Remove the items.
            if ($model->delete($cid))
            {
                $this->setMessage(Text::plural('COM_TZ_PORTFOLIO_FIELDS_COUNT_DELETED', count($cid)));
            }
            else
            {
                $this->setMessage($model->getError());
            }
        }

        $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
    }

    public function updateState(){
        // Check for request forgeries
        $this -> checkToken();

        $cid    = Factory::getApplication()->input->get('cid', array(), 'array');
        $data = array('listview' => 1, 'unlistview' => 0,
            'detailview' => 1, 'undetailview' => 0,
            'advsearch' => 1, 'unadvsearch' => 0);
        $task   = $this->getTask();
        $value  = ArrayHelper::getValue($data, $task, 0, 'int');

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
                $model->updateState($cid, $value, $task);
                $errors = $model->getErrors();

                if ($value == 1)
                {
                    if ($errors)
                    {
                        $app = Factory::getApplication();
                        $app->enqueueMessage(Text::plural($this->text_prefix . '_N_ITEMS_FAILED_PUBLISHING', count($cid)), 'error');
                    }
                    else
                    {
                        $ntext = $this->text_prefix . '_N_ITEMS_UPDATESTATE';
                    }
                }
                elseif ($value == 0)
                {
                    $ntext = $this->text_prefix . '_N_ITEMS_UPDATESTATE';
                }

                $this->setMessage(Text::plural($ntext, count($cid)));
            }
            catch (\Exception $e)
            {
                $this->setMessage($e->getMessage(), 'error');
            }
        }

        $extension = $this->input->get('extension');
        $extensionURL = ($extension) ? '&extension=' . $extension : '';
        $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $extensionURL, false));

    }

    public function saveOrderAjax()
    {
        // Get the input
        $pks    = $this->input->post->get('cid', array(), 'array');
        $order  = $this->input->post->get('order', array(), 'array');
        $group  = $this->input->post->get('filter_group');

        // Sanitize the input
        $pks    = ArrayHelper::toInteger($pks);
        $order  = ArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel();

        // Save the ordering
        $return = $model->saveOrderAjax($pks, $order, $group);

        if ($return)
        {
            echo "1";
        }

        // Close the application
        Factory::getApplication()->close();
    }

}
