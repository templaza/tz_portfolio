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
use Joomla\Input\Input;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Versioning\VersionableControllerTrait;

/**
 * The Category Controller.
 */
class GroupController extends FormController
{
    protected $view_list = 'groups';

    public function delete(&$pks){
        // Initialise variables.
        $dispatcher = Dispatcher::getInstance();
        $pks = (array) $pks;
        $table = $this->getTable();

        // Iterate the items to delete each one.
        foreach ($pks as $i => $pk)
        {

            if ($table->load($pk))
            {

                if ($this->canDelete($table))
                {

                    if (!$table->delete($pk))
                    {
                        $this->setMessage($table->getError(), 'error');
                        return false;
                    }

                }
                else
                {

                    // Prune items that you can't change.
                    unset($pks[$i]);
                    $error = $this->getError();
                    if ($error)
                    {
                        Factory::getApplication() -> enqueueMessage($error, 'error');
                        return false;
                    }
                    else
                    {
                        Factory::getApplication() -> enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), 'error');
                        return false;
                    }
                }

            }
            else
            {
                $this->setMessage($table->getError(), 'error');
                return false;
            }
        }

        // Clear the component's cache
        $this->cleanCache();

        return true;
    }

    protected function allowAdd($data = array())
    {
        $user = Factory::getApplication()->getIdentity();
        return ($user->authorise('core.create','com_tz_portfolio.group'));
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

        // Check edit on the record asset (explicit or inherited)
        if ($user->authorise('core.edit', $this -> option.'.group.' . $recordId))
        {
            return true;
        }

        // Check edit own on the record asset (explicit or inherited)
        if ($user->authorise('core.edit.own', $this -> option.'.group.' . $recordId))
        {
            // Existing record already has an owner, get it
            $record = $this->getModel()->getItem($recordId);

            if (empty($record))
            {
                return false;
            }

            // Grant if current user is owner of the record
            return $user->id == $record->created_by;
        }

        return false;
    }
}
