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
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioUser;

/**
 * The Category Controller.
 */
class FieldController extends FormController
{
    protected $view_list = 'fields';

    protected function allowAdd($data = array())
    {
        $user = Factory::getUser();
        return ($user->authorise('core.create','com_tz_portfolio.group')
            || count($user->getAuthorisedFieldGroups('core.create')) > 0);
    }

    protected function allowEdit($data = array(), $key = 'id')
    {
        $recordId   = (int) isset($data[$key]) ? $data[$key] : 0;
        $user       = TZ_PortfolioUser::getUser();

        // Existing record already has an owner, get it
        $record = $this->getModel()->getItem($recordId);

        if (empty($record))
        {
            return false;
        }

        $canEdit	    = $user->authorise('core.edit',		  $this -> option.'.field.'.$recordId)
            && (count($user -> getAuthorisedFieldGroups('core.edit', $record -> groupid)) > 0);
        $canEditOwn	    = $user->authorise('core.edit.own', $this -> option.'.field.'.$recordId)
            && $record->created_by == $user->id
            && (count($user -> getAuthorisedFieldGroups('core.edit.own', $record -> groupid)) > 0);

        // Check edit on the record asset (explicit or inherited)
        if ($canEdit)
        {
            return true;
        }

        // Check edit own on the record asset (explicit or inherited)
        if ($canEditOwn)
        {
            return true;
        }

        // Zero record (id:0), return component edit permission by calling parent controller method
        if (!$recordId)
        {
            return parent::allowEdit($data, $key);
        }

        return false;
    }
}
