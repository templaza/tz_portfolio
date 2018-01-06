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

// No direct access.
defined('_JEXEC') or die;
jimport('joomla.application.component.controllerform');

class TZ_Portfolio_PlusControllerTemplate extends JControllerForm
{
    public function upload()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Access check.
        if (!$this->allowAdd())
        {
            // Set the internal error and also the redirect error.
            $this->setError(\JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'));
            $this->setMessage($this->getError(), 'error');

            $this->setRedirect(
                \JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend(), false
                )
            );

            return false;
        }

        // Redirect to the edit screen.
        $this->setRedirect(
            JRoute::_(
                'index.php?option=' . $this->option . '&view=' . $this->view_item . '&layout=upload', false
            )
        );

        return true;
    }

    public function install(){
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Access check.
        if (!$this->allowAdd())
        {
            // Set the internal error and also the redirect error.
            $this->setError(\JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'));
            $this->setMessage($this->getError(), 'error');

            $this->setRedirect(
                \JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend(), false
                )
            );

            return false;
        }

        $model  = $this -> getModel();
        $model -> install();

        $this -> setRedirect('index.php?option=com_tz_portfolio_plus&view=template&layout=upload');
    }

    public function uninstall(){

        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $eid   = $this->input->get('cid', array(), 'array');
        $model = $this->getModel('Template');

        JArrayHelper::toInteger($eid, array());
        $model->uninstall($eid);
        $this->setRedirect(JRoute::_('index.php?option=com_tz_portfolio_plus&view=templates', false));
    }

    protected function allowAdd($data = array())
    {
        $user = TZ_Portfolio_PlusUser::getUser();
        return ($user->authorise('core.create','com_tz_portfolio_plus.template'));
    }

    protected function allowEdit($data = array(), $key = 'id')
    {
        $recordId = (int) isset($data[$key]) ? $data[$key] : 0;
        $user = JFactory::getUser();

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