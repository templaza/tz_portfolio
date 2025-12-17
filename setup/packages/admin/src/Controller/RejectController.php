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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\Filesystem\File;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\AddonsHelper;

class RejectController extends BaseController
{
    protected $view_list    = 'articles';

    protected function allowReject($data = array())
    {
        $user = Factory::getUser();

        return $user->authorise('core.approve', $this->option);
    }

//    public function getModel($name = 'Reject', $prefix = 'TZ_Portfolio_PlusModel', $config = array('ignore_request' => true))
//    {
//        $model = parent::getModel($name, $prefix, $config);
//        return $model;
//    }

    protected function getUrlRedirect(){

        $url = 'index.php?option=' . $this->option . '&view='.$this -> view_list
            . $this->getRedirectToListAppend();

        // Check if there is a return value
        $return = $this->input->get('return', null, 'base64');

        if (!is_null($return) && Uri::isInternal(base64_decode($return)))
        {
            $url = base64_decode($return);
        }

        return $url;
    }

    public function save($key = null, $urlVar = null)
    {
        // Check for request forgeries
        $this -> checkToken();

        $app        = Factory::getApplication();

        // Require Reject model from front-end
        $model      = $this->getModel();
        $table      = $model->getTable();
        $cid        = $this->input->get('cid', array(), 'array');
        $data       = $this->input->post->get('jform', array(), 'array');
        $context    = "$this->option.edit.$this->context";

        $url = $this -> getUrlRedirect();

        if (empty($cid))
        {
            Log::add(Text::_($this->text_prefix . '_NO_ITEM_SELECTED'), Log::WARNING, 'jerror');
        }else {
            // Make sure the item ids are integers
            $cid = ArrayHelper::toInteger($cid);

            // Access check.
            if (!$this->allowReject($data)) {

                $this->setError(Text::_('COM_TZ_PORTFOLIO_ERROR_NOT_REJECT_ARTICLE'));
                $this->setMessage($this->getError(), 'error');

                $this->setRedirect(
                    Route::_($url, false)
                );
                $this->redirect();
            }

            // Validate the posted data.
            // Sometimes the form needs some posted data, such as for plugins and modules.
            $form = $model->getForm($data, false);

            if (!$form) {
                $app->enqueueMessage($model->getError(), 'error');

                return false;
            }

            // Send an object which can be modified through the plugin event
            $objData = (object)$data;
            $app->triggerEvent(
                'onContentNormaliseRequestData',
                array($this->option . '.' . $this->context, $objData, $form)
            );
            $data = (array)$objData;

            // Test whether the data is valid.
            $validData = $model->validate($form, $data);

            // Attempt to save the data.
            if (!$model->save($validData, $cid)) {
                // Save the data in the session.
                $app->setUserState($context . '.data', $validData);

                // Redirect back to the edit screen.
                $this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
                $this->setMessage($this->getError(), 'error');

                $this->setRedirect(
                    Route::_(
                        $url, false
                    )
                );

                return false;
            }
        }

        $this->setMessage(Text::plural('COM_TZ_PORTFOLIO_N_ITEMS_REJECTED', count($cid)));

        // Redirect to the list screen.
        $this->setRedirect(Route::_($url, false));

        // Invoke the postSave method to allow for the child class to access the model.
        $this->postSaveHook($model, $validData);

        return true;
    }
}
