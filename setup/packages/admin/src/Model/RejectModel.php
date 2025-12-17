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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Model;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Asset;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

class RejectModel extends AdminModel
{

    protected function populateState()
    {
        parent::populateState();

        $app = Factory::getApplication();

        $cid = $app->input->get('cid', array(), 'array');
        $this -> setState('article.id', $cid);

        $return = $app->input->get('return', null, 'base64');
        $return = !empty($return)?base64_decode($return):$return;

        $this->setState('return_page', $return);
    }

    public function getTable($type = 'ContentRejected', $prefix = 'Administrator', $config = array())
    {
        return parent::getTable($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this -> option.'.'.$this -> getName(), $this -> getName(),
            array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $app = Factory::getApplication();
        $data = $app->getUserState('com_tz_portfolio.edit.'.$this -> getName().'.data', array());

        if (empty($data))
        {
            $data = $this-> getItem();
        }

        $this->preprocessData('com_tz_portfolio.'.$this -> getName(), $data);

        return $data;
    }

    public function getItem($pk = null)
    {
        $item = parent::getItem();

        return $item;
    }

    public function save($data, $articleIds = array())
    {
        $table      = $this -> getTable();
        $tblContent = $this -> getTable('Content');

        if(count($articleIds)){
            foreach($articleIds as $i => $articleId){
                $_data  = $data;
                $table -> reset();
                $_data['id']    = 0;
                $_data['content_id'] = $articleId;
                if($table -> load(array('content_id' => $articleId))) {
                    $_data['id'] = $table->id;
                }
                $result = parent::save($_data);
                if($result){
                    $tblContent -> publish($articleId, -3);
                }
            }
        }
        return true;
    }
}