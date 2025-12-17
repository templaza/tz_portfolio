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

namespace TemPlaza\Component\TZ_Portfolio\Site\Dispatcher;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Dispatcher\ComponentDispatcher;

\defined('_JEXEC') or die;

/**
 * ComponentDispatcher class for com_tz_portfolio
 *
 */
class Dispatcher extends ComponentDispatcher
{
    /**
     * Dispatch a controller task. Redirecting the user if appropriate.
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function dispatch()
    {
//        $checkCreateEdit = ($this->input->get('view') === 'articles' && $this->input->get('layout') === 'modal')
//            || ($this->input->get('view') === 'article' && $this->input->get('layout') === 'pagebreak');
//
//        if ($checkCreateEdit) {
//            // Can create in any category (component permission) or at least in one category
//            $canCreateRecords = $this->app->getIdentity()->authorise('core.create', 'com_content')
//                || \count($this->app->getIdentity()->getAuthorisedCategories('com_content', 'core.create')) > 0;
//
//            // Instead of checking edit on all records, we can use **same** check as the form editing view
//            $values           = (array) $this->app->getUserState('com_content.edit.article.id');
//            $isEditingRecords = \count($values);
//            $hasAccess        = $canCreateRecords || $isEditingRecords;
//
//            if (!$hasAccess) {
//                $this->app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'warning');
//
//                return;
//            }
//        }
//
        parent::dispatch();
    }
}
