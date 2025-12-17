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

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;

class AclController extends FormController
{

//    public function getModel($name = 'Acl', $prefix = 'TZ_Portfolio_PlusModel', $config = array('ignore_request' => true))
//    {
//        $model = parent::getModel($name, $prefix, $config);
//
//        return $model;
//    }

    public function edit($key = null, $urlVar = null)
    {
        // Do not cache the response to this, its a redirect, and mod_expires and google chrome browser bugs cache it forever!
        Factory::getApplication()->allowCache(false);

        $model      = $this->getModel();
//        $table      = $model->getTable();
        $section    = $this->input->post->get('section', array(), 'array');
        $context    = "$this->option.edit.$this->context";

        // Determine the name of the primary key for the data.
        if (empty($key))
        {
            $key = 'section';
        }

        // To avoid data collisions the urlVar may be different from the primary key.
        if (empty($urlVar))
        {
            $urlVar = $key;
        }

        // Get the previous record id (if any) and the current record id.
        $recordSection  = (string) (count($section) ? $section[0] : $this->input->get($urlVar));

        // Access check.
        if (!$this->allowEdit(array($key => $recordSection), $key))
        {
            $this->setMessage(Text::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'), 'error');

            $this->setRedirect(
                Route::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend(), false
                )
            );

            return false;
        }

        // Check-out succeeded, push the new record id into the session.
        $this->holdEditSection($context, $recordSection);
        Factory::getApplication()->setUserState($context . '.data', null);

        $this->setRedirect(
            Route::_(
                'index.php?option=' . $this->option . '&view=' . $this->view_item
                . $this->getRedirectToItemAppend($recordSection, $urlVar), false
            )
        );

        return true;
    }

    protected function holdEditSection($context, $section)
    {
        $app = Factory::getApplication();
        $values = (array) $app->getUserState($context . '.section');

        // Add the id to the list if non-zero.
        if (!empty($section))
        {
            $values[] = (string) $section;
            $values   = array_unique($values);
            $app->setUserState($context . '.section', $values);

            if (defined('JDEBUG') && JDEBUG)
            {
                Log::add(
                    sprintf(
                        'Holding edit ID %s.%s %s',
                        $context,
                        $section,
                        str_replace("\n", ' ', print_r($values, 1))
                    ),
                    Log::INFO,
                    'controller'
                );
            }
        }
    }

    protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id'){
        $append = parent::getRedirectToItemAppend($recordId, $urlVar);
        $section    = $this -> input -> get('section');

        if($section){
            $append .= '&section='.$section;
        }

        return $append;
    }
}