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

        // Redirect to the edit screen.
        $this->setRedirect(
            JRoute::_(
                'index.php?option=' . $this->option . '&view=' . $this->view_item.'&layout=upload', false
            )
        );

        return true;
    }

    public function install(){
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

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

}