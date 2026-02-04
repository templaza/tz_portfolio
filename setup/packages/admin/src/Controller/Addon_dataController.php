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

use Joomla\CMS\Application\CMSWebApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\CurrentUserInterface;
use Joomla\Filesystem\Path;
use Joomla\Input\Input;
use Joomla\Utilities\ArrayHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\AddOn\AddOnModel;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;

class Addon_dataController extends FormController
{
    public $addon_id = null;
    public $addon_view = null;
    public function __construct($config = [], ?MVCFactoryInterface $factory = null, ?CMSWebApplicationInterface $app = null, ?Input $input = null, ?FormFactoryInterface $formFactory = null)
    {
        parent::__construct($config, $factory, $app, $input, $formFactory);
        $this->addon_id = $this->input->getInt('addon_id');
        $this->addon_view = $this->input->getCmd('addon_return');
    }

    public function add(): bool
    {
        $context = "$this->option.edit.$this->context";

        // Access check.
        if (!$this->allowAdd()) {
            // Set the internal error and also the redirect error.
            $this->setMessage(Text::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'), 'error');

            $this->setRedirect(
                Route::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend(),
                    false
                )
            );

            return false;
        }

        // Clear the record edit information from the session.
        $this->app->setUserState($context . '.data', null);

        $addonID = $this->input->getCmd('addon_id');
        $addonView = $this->input->getCmd('addon_view');

        // Redirect to the edit screen.
        $this->setRedirect(
            Route::_(
                'index.php?option=' . $this->option . '&view=addon_datas&addon_id=' . $addonID . '&addon_view=' . $addonView
                . $this->getRedirectToItemAppend(),
                false
            )
        );

        return true;
    }

    public function save($key = null, $urlVar = null) {
        $this->checkToken();
        $model   = $this->getModel();
        $data = $input   = $this->input->post->get('jform', [], 'array');
        $addon = AddonHelper::getPluginById($this->addon_id);
        $item = array();
        $item['id'] = $data['id'];
        unset($data['id']);
        $item['extension_id'] = $this->addon_id;
        if (empty($data['element'])) {
            $data['element'] = $addon->name;
        }
        $item['element'] = $data['element'];
        unset($data['element']);
        $item['value'] = json_encode($data);
        $table = $model->save($item);

        $addOnModel = $this->getAddOnModel($item['element'], $addon->type, $addon->name);

        if ($addOnModel) {
            $input['id'] = $table->id;
            $addOnModel->save($input);
        }

        $url = 'index.php?option=' . $this->option . '&view=' . $this->view_list
            . '&addon_id=' . $this->addon_id ;
        if (!empty($this->addon_view)) {
            $url .= '&addon_view=' . $this->addon_view;
        }
        // Check if there is a return value
        $return = $this->input->get('return', null, 'base64');

        if (!\is_null($return) && Uri::isInternal(base64_decode($return))) {
            $url = base64_decode($return);
        }

        // Redirect to the list screen.
        $this->setRedirect(Route::_($url, false));
    }

    protected function getAddOnModel($element, $type, $name)
    {
        $app = Factory::getApplication();
        $prefix = empty($prefix) ? ($app->isClient('administrator') ? 'Administrator' : 'Site') : $prefix;

        $modelClass = '\\TemPlaza\\Component\\TZ_Portfolio\\AddOn\\' . ucfirst($type) . '\\' . ucfirst($name) . '\\'.$prefix.'\\Model\\' . ucfirst($element) . 'Model';

        if (!class_exists($modelClass)) {
            return false;
        }

        $model = new $modelClass();

        if ($model === null) {
            return false;
        }

        if ($model instanceof CurrentUserInterface && $this->app->getIdentity()) {
            $model->setCurrentUser($this->app->getIdentity());
        }

        return $model;
    }

    public function cancel($key = null)
    {
        $this->checkToken();

        $url = 'index.php?option=' . $this->option . '&view=' . $this->view_list
            . '&addon_id=' . $this->addon_id ;
        if (!empty($this->addon_view)) {
            $url .= '&addon_view=' . $this->addon_view;
        }
        // Check if there is a return value
        $return = $this->input->get('return', null, 'base64');

        if (!\is_null($return) && Uri::isInternal(base64_decode($return))) {
            $url = base64_decode($return);
        }

        // Redirect to the list screen.
        $this->setRedirect(Route::_($url, false));
    }

    public function delete()
    {
        $cid = $this->input->get('cid', array(), 'array');
        $model = $this->getModel();
        $model->remove($cid);
        $url = 'index.php?option=' . $this->option . '&view=' . $this->view_list
            . '&addon_id=' . $this->addon_id ;
        if (!empty($this->addon_view)) {
            $url .= '&addon_view=' . $this->addon_view;
        }
        // Check if there is a return value
        $return = $this->input->get('return', null, 'base64');

        if (!\is_null($return) && Uri::isInternal(base64_decode($return))) {
            $url = base64_decode($return);
        }

        // Redirect to the list screen.
        $this->setRedirect(Route::_($url, false));
    }
}