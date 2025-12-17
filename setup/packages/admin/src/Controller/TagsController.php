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

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Controller\AdminController;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TagsHelper;

class TagsController extends AdminController
{
    protected $text_prefix  = 'COM_TZ_PORTFOLIO_TAGS';

    public function getModel($name = 'Tag', $prefix = 'Administrator', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function delete()
    {
        // Check for request forgeries
        $this -> checkToken();

        // Get items to remove from the request.
        $cid = $this -> input -> get('cid', array(), 'array');

        if (!is_array($cid) || count($cid) < 1)
        {
            Factory::getApplication() -> enqueueMessage(Text::_($this->text_prefix . '_NO_ITEM_SELECTED'), 'error');
        }
        else
        {
            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            jimport('joomla.utilities.arrayhelper');
            $cid    = ArrayHelper::toInteger($cid);

            // Remove the items.
            if ($model->delete($cid))
            {
                $this->setMessage(Text::plural('COM_TZ_PORTFOLIO_TAGS_COUNT_DELETED', count($cid)));
            }
            else
            {
                $this->setMessage($model->getError());
            }
        }

        $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
    }

    public function searchAjax(){
        $app    = Factory::getApplication();
        $input  = $app -> input;
//        JLoader::import('com_tz_portfolio_plus.helpers.tags', JPATH_ADMINISTRATOR.'/components');

        $like   = $input->get('like', null, 'string');
        $like   = !empty($like)?trim($like):$like;
        $title  = $input->get('title', null, 'string');
        $title  = !empty($title)?trim($title):$title;

        // Receive request data
        $filters = array(
            'like'      => $like,
            'title'     => $title,
            'published' => $input->get('published', 1, 'int')
        );

        if ($results = TagsHelper::searchTags($filters))
        {
            // Output a JSON object
            echo json_encode($results);
        }

        $app->close();
    }

}