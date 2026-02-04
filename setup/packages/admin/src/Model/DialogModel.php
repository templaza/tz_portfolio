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

class DialogModel extends AdminModel
{
    protected $formName;
    protected $rejectModel;

    public function __construct()
    {
        /* @var MVCFactoryInterface $mvcFactory */
        $mvcFactory   = Factory::getApplication() -> bootComponent('com_tz_portfolio') -> getMVCFactory();

        $client     = Factory::getApplication() -> isClient('administrator')?'Administrator':'Site';

        $this -> rejectModel    = $mvcFactory -> createModel('Reject', $client);

        parent::__construct();
    }

    protected function populateState()
    {

        $app = Factory::getApplication();

        // Load state from the request.
        $cid = $app->input->get('cid', array(), 'array');
        $this->setState('article.id', $cid);

        $return = $app->input->get('return', null, 'base64');
        $return = !empty($return)?base64_decode($return):$return;

        $this->setState('return_page', $return);

        $this->setState('layout', $app->input->getString('layout'));

        $this -> rejectModel -> setState('article.id', $cid);

        $this -> setState('state.reject', $this -> rejectModel -> get('state'));

    }

    public function getFormReject($data = array(), $loadData = true)
    {
        $form   = false;

        /* @var MVCFactoryInterface $mvcFactory */
        $mvcFactory   = Factory::getApplication() -> bootComponent('com_tz_portfolio') -> getMVCFactory();

        $client     = Factory::getApplication() -> isClient('administrator')?'Administrator':'Site';

//        if($model = JModelLegacy::getInstance('Reject', 'TZ_Portfolio_PlusModel')){
        if($model = $mvcFactory -> createModel('Reject', $client)){
            $form   = $model -> getForm($data, $loadData);
        }

        return $form;
    }

    public function getForm($data = array(), $loadData = true)
    {
        return false;
    }
}