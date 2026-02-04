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

use Joomla\CMS\Dispatcher\Dispatcher;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Router\Route;
use Joomla\Input\Input;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Versioning\VersionableControllerTrait;
use Joomla\Utilities\ArrayHelper;

/**
 * The Style Controller.
 */
class StyleController extends FormController
{
    protected $view_list = 'styles';
    
    public function upload()
    {
        // Check for request forgeries.
        $this -> checkToken();

        // Access check.
        if (!$this->allowAdd())
        {
            // Set the internal error and also the redirect error.
            $this->setMessage(Text::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'), 'error');

            $this->setRedirect(
                Route::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend(), false
                )
            );

            return false;
        }

        // Redirect to the edit screen.
        $this->setRedirect(
            Route::_(
                'index.php?option=' . $this->option . '&view=' . $this->view_item . '&layout=upload', false
            )
        );

        return true;
    }

    public function install(){
        // Check for request forgeries.
        $this -> checkToken();

        // Access check.
        if (!$this->allowAdd())
        {
            // Set the internal error and also the redirect error.
            $this->setMessage(Text::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'), 'error');

            $this->setRedirect(
                Route::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend(), false
                )
            );

            return false;
        }

        $model  = $this -> getModel();
        if(!$model -> install()){
            $this -> setMessage($model -> getError(), 'error');
        }else{
            $this -> setMessage(Text::sprintf('COM_TZ_PORTFOLIO_INSTALL_SUCCESS',
                Text::_('COM_TZ_PORTFOLIO_TEMPLATE')));
        }

        $this -> setRedirect('index.php?option=com_tz_portfolio&view='.$this -> view_item.'&layout=upload');
    }

    public function uninstall(){

        // Check for request forgeries.
        $this -> checkToken();

        $eid    = $this->input->get('cid', array(), 'array');
        $model  = $this->getModel('Style');
        $eid    = ArrayHelper::toInteger($eid);

        $model->uninstall($eid);
        $this->setRedirect(Route::_('index.php?option=com_tz_portfolio&view='.$this -> view_list, false));
    }

    protected function allowAdd($data = array())
    {
        $user = Factory::getUser();
        return ($user->authorise('core.create','com_tz_portfolio.'.$this -> getName()));
    }

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
        $record = $this->getModel()->getItem($recordId);

        // Check edit on the record asset (explicit or inherited)
        if(isset($record -> asset_id) && $record -> asset_id){
            return $user->authorise('core.edit', $this -> option.'.tag.' . $recordId);
        }else{
            return $user->authorise('core.edit', $this -> option.'.tag');
        }

        return false;
    }

    public function ajax_install()
    {
        // Check for request forgeries.
        $this -> checkToken();

        $result = null;
        $app    = Factory::getApplication();


        // Access check.
        if (!$this->allowAdd())
        {
            // Set the internal error and also the redirect error.
            $app->enqueueMessage(Text::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'), 'error');
        }else{
            $model  = $this -> getModel();
            if($result = $model -> install()){
                $app -> enqueueMessage(Text::sprintf('COM_TZ_PORTFOLIO_INSTALL_SUCCESS'
                    , Text::_('COM_TZ_PORTFOLIO_TEMPLATE')));
            }else{
                $this -> setMessage($model -> getError());
                $app -> enqueueMessage($model -> getError(), 'error');
            }
        }

        $message = $this->message;

        $this->setRedirect(
            Route::_(
                'index.php?option=' . $this->option . '&view=' . $this->view_item.'&layout=upload', false
            )
        );

        $redirect   = $this -> redirect;

        // Push message queue to session because we will redirect page by Javascript, not $app->redirect().
        // The "application.queue" is only set in redirect() method, so we must manually store it.
        $app->getSession()->set('application.queue', $app->getMessageQueue());

        header('Content-Type: application/json');

        echo new JsonResponse(array('redirect' => $redirect), $message, !$result);

        exit();
    }
}
