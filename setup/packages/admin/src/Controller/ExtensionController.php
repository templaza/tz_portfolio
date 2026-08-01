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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;

class ExtensionController extends AddonController
{
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
        $result = parent::install();
        if($result){
            $this -> setMessage(Text::sprintf('COM_TZ_PORTFOLIO_PLUS_INSTALL_SUCCESS',
                Text::_('COM_TZ_PORTFOLIO_PLUS_EXTENSION')));
        }
        return true;
    }

    public function uninstall(){

        // Check for request forgeries.
        $this -> checkToken();

        $eid    = $this->input->get('cid', array(), 'array');
        $model  = $this->getModel('Template');
        $eid    = ArrayHelper::toInteger($eid);

        $model->uninstall($eid);
        $this->setRedirect(Route::_('index.php?option=com_tz_portfolio&view=templates', false));
    }

    protected function allowAdd($data = array())
    {
        $user = Factory::getApplication()->getIdentity();
        return ($user->authorise('core.create','com_tz_portfolio.style'));
    }

    protected function allowEdit($data = array(), $key = 'id')
    {
        $recordId = (int) isset($data[$key]) ? $data[$key] : 0;
        $user = Factory::getApplication()->getIdentity();

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
}
