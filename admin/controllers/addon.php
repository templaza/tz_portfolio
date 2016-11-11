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

class TZ_Portfolio_PlusControllerAddon extends JControllerForm
{
    public function __construct($config = array()){
        parent::__construct($config);
    }
    public function display($cachable = false, $urlparams = false)
    {
        parent::display($cachable,$urlparams);
    }

    public function manager(){
        $app   = JFactory::getApplication();
        $model = $this->getModel();
        $table = $model->getTable();
        $cid    = array();
        $context = "$this->option.edit.$this->context";
        $this -> input -> set('layout','manager');

        $addon_view     = $this -> input -> getCmd('addon_view');
        $addon_task     = $this -> input -> getCmd('addon_task');
        $addon_layout   = $this -> input -> getCmd('addon_layout');

        $link           = '';
        if($addon_view){
            $link   .= '&addon_view='.$addon_view;
        }
        if($addon_task){
            $link   .= '&addon_task='.$addon_task;
        }
        if($addon_layout){
            $link   .= '&addon_layout='.$addon_layout;
        }

        // Determine the name of the primary key for the data.
        if (empty($key))
        {
            $key = $table->getKeyName();
        }

        // To avoid data collisions the urlVar may be different from the primary key.
        if (empty($urlVar))
        {
            $urlVar = $key;
        }

        // Get the previous record id (if any) and the current record id.
        $recordId = (int) (count($cid) ? $cid[0] : $this->input->getInt($urlVar));
        $checkin = property_exists($table, 'checked_out');

        // Access check.
        if (!$this->allowEdit(array($key => $recordId), $key))
        {
            $this->setError(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
            $this->setMessage($this->getError(), 'error');

            $this->setRedirect(
                JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend().$link, false
                )
            );

            return false;
        }

        // Attempt to check-out the new record for editing and redirect.
        if ($checkin && !$model->checkout($recordId))
        {
            // Check-out failed, display a notice but allow the user to see the record.
            $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()));
            $this->setMessage($this->getError(), 'error');

            $this->setRedirect(
                JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar).$link, false
                )
            );

            return false;
        }
        else
        {
            // Check-out succeeded, push the new record id into the session.
            $this->holdEditId($context, $recordId);
            $app->setUserState($context . '.data', null);


            $this->setRedirect(
                JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar).$link, false
                )
            );

            return true;
        }
    }

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

        $this -> setRedirect('index.php?option=com_tz_portfolio_plus&view='.$this -> view_item.'&layout=upload');
    }

    public function uninstall(){

        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $eid   = $this->input->get('cid', array(), 'array');
        $model = $this->getModel();

        JArrayHelper::toInteger($eid, array());
        $model->uninstall($eid);
        $this->setRedirect(JRoute::_('index.php?option=com_tz_portfolio_plus&view=addons', false));
    }

    public function cancel($key = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        if($return = $this -> input -> get('return', null, 'base64')){
            $this -> setRedirect(base64_decode($return));
            return true;
        }
        return parent::cancel($key);
    }

    public function save($key = null, $urlVar = null)
    {
        if (parent::save($key, $urlVar)) {
            if($return = $this->input->get('return', null, 'base64')){
                $task   = $this->getTask();
                $model  = $this->getModel();
                $table  = $model->getTable();

                // Determine the name of the primary key for the data.
                if (empty($key))
                {
                    $key = $table->getKeyName();
                }

                // To avoid data collisions the urlVar may be different from the primary key.
                if (empty($urlVar))
                {
                    $urlVar = $key;
                }

                $recordId = $this->input->getInt($urlVar);

                switch ($task)
                {
                    case 'apply':
                        // Redirect back to the edit screen.
                        $this->setRedirect(
                            JRoute::_(
                                'index.php?option=' . $this->option . '&view=' . $this->view_item
                                . $this->getRedirectToItemAppend($recordId, $urlVar).'&return='.$return, false
                            )
                        );
                        break;
                    case 'save':
                        $this->setRedirect(base64_decode($return));
                        break;
                    default:
                        break;
                }
            }
            return true;
        }
        return false;
    }

}