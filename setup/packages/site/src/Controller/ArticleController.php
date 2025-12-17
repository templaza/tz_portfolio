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

namespace TemPlaza\Component\TZ_Portfolio\Site\Controller;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Menu\SiteMenu;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use TemPlaza\Component\TZ_Portfolio\Site\Model\PortfolioModel;

// no direct access
defined('_JEXEC') or die;

class ArticleController extends FormController
{
    protected $view_item = 'form';
    protected $view_list = 'portfolio';

    /**
     * Method to add a new record.
     *
     * @return	boolean	True if the article can be added, false if not.
     * @since	1.6
     */
    public function add()
    {
        if (!parent::add()) {
            // Redirect to the return page.
            $this->setRedirect($this->getReturnPage());
        }
    }

    /**
     * Method to cancel an edit.
     *
     * @param	string	$key	The name of the primary key of the URL variable.
     *
     * @return	Boolean	True if access level checks pass, false otherwise.
     * @since	1.6
     */
    public function cancel($key = 'a_id')
    {
        $result = parent::cancel($key);

        /** @var SiteApplication $app */
        $app    = Factory::getApplication();

        // Load the parameters.
        $params = $app->getParams();

        $customCancelRedir = (bool) $params->get('custom_cancel_redirect');

        if ($customCancelRedir) {
            $cancelMenuitemId = (int) $params->get('cancel_redirect_menuitem');

            if ($cancelMenuitemId > 0) {
                $item = $app->getMenu()->getItem($cancelMenuitemId);
                $lang = '';

                if (Multilanguage::isEnabled()) {
                    $lang = !is_null($item) && $item->language != '*' ? '&lang=' . $item->language : '';
                }

                // Redirect to the user specified return page.
                $redirlink = $item->link . $lang . '&Itemid=' . $cancelMenuitemId;
            } else {
                // Redirect to the same article submission form (clean form).
                $redirlink = $app->getMenu()->getActive()->link . '&Itemid=' . $app->getMenu()->getActive()->id;
            }
        } else {
            $menuitemId = (int) $params->get('redirect_menuitem');

            if ($menuitemId > 0) {
                $lang = '';
                $item = $app->getMenu()->getItem($menuitemId);

                if (Multilanguage::isEnabled()) {
                    $lang = !is_null($item) && $item->language != '*' ? '&lang=' . $item->language : '';
                }

                // Redirect to the general (redirect_menuitem) user specified return page.
                $redirlink = $item->link . $lang . '&Itemid=' . $menuitemId;
            } else {
                // Redirect to the return page.
                $redirlink = $this->getReturnPage();
            }
        }

        $this->setRedirect(Route::_($redirlink, false));

        return $result;
    }

    /**
     * Method to edit an existing record.
     *
     * @param	string	$key	The name of the primary key of the URL variable.
     * @param	string	$urlVar	The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
     *
     * @return	Boolean	True if access level check and checkout passes, false otherwise.
     * @since	1.6
     */
    public function edit($key = null, $urlVar = 'a_id')
    {
        $result = parent::edit($key, $urlVar);

        if (!$result) {
            $this->setRedirect(Route::_($this->getReturnPage(), false));
        }

        return $result;
    }

    /**
     * Method to get a model object, loading it if required.
     *
     * @param	string	$name	The model name. Optional.
     * @param	string	$prefix	The class prefix. Optional.
     * @param	array	$config	Configuration array for model. Optional.
     *
     * @return	object	The model.
     * @since	1.5
     */
    public function getModel($name = 'Form', $prefix = 'Site', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    /**
     * Gets the URL arguments to append to an item redirect.
     *
     * @param	int		$recordId	The primary key id for the item.
     * @param	string	$urlVar		The name of the URL variable for the id.
     *
     * @return	string	The arguments to append to the redirect URL.
     * @since	1.6
     */
    protected function getRedirectToItemAppend($recordId = null, $urlVar = 'a_id')
    {
        // Need to override the parent method completely.
        $tmpl		= $this -> input -> getCmd('tmpl');
        $layout		= $this -> input -> getCmd('layout', 'edit');
        $append		= '';

        // Setup redirect info.
        if ($tmpl) {
            $append .= '&tmpl='.$tmpl;
        }

        // TODO This is a bandaid, not a long term solution.
        $append .= '&layout=edit';

        if ($recordId) {
            $append .= '&'.$urlVar.'='.$recordId;
        }

        $itemId	= $this -> input -> getInt('Itemid');
        $return	= $this->getReturnPage();
        $catId = $this -> input -> getInt('catid', null, 'get');

        if ($itemId) {
            $append .= '&Itemid='.$itemId;
        }

        if($catId) {
            $append .= '&catid='.$catId;
        }

        if ($return) {
            $append .= '&return='.base64_encode($return);
        }

        return $append;
    }

    /**
     * Get the return URL.
     *
     * If a "return" variable has been passed in the request
     *
     * @return	string	The return URL.
     * @since	1.6
     */
    protected function getReturnPage()
    {
        $return = $this->input->get('return', null, 'base64');

        if (empty($return)){
            return Uri::base();
        }elseif($return && !Uri::isInternal(base64_decode($return))) {
            return base64_decode($return);
        }
        else {
            return Route::_(base64_decode($return));
        }
    }

    /**
     * Method to save a record.
     *
     * @param	string	$key	The name of the primary key of the URL variable.
     * @param	string	$urlVar	The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
     *
     * @return	Boolean	True if successful, false otherwise.
     * @since	1.6
     */
    public function save($key = null, $urlVar = 'a_id')
    {
//        // Check for request forgeries.
//        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $result    = parent::save($key, $urlVar);
        $app       = Factory::getApplication();
        $articleId = $app->input->getInt('a_id');

        // Load the parameters.
        $params   = $app->getParams();
        $menuitem = (int) $params->get('redirect_menuitem');

        // Check for redirection after submission when creating a new article only
        if ($menuitem > 0 && $articleId == 0) {
            $lang = '';

            if (Multilanguage::isEnabled()) {
                $item = $app->getMenu()->getItem($menuitem);
                $lang = !is_null($item) && $item->language != '*' ? '&lang=' . $item->language : '';
            }

            // If ok, redirect to the return page.
            if ($result) {
                $this->setRedirect(Route::_('index.php?Itemid=' . $menuitem . $lang, false));
            }
        } elseif ($this->getTask() === 'save2copy') {
            // Redirect to the article page, use the redirect url set from parent controller
        } else {
            // If ok, redirect to the return page.
            if ($result) {
                $this->setRedirect(Route::_($this->getReturnPage(), false));
            }
        }

        return $result;
    }
}