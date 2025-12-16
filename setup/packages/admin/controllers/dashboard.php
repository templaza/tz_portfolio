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

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class TZ_Portfolio_PlusControllerDashboard extends FormController
{
    public function feedBlog()
    {
        $app      = Factory::getApplication();
        $document = $app->getDocument();
        $vType    = $document->getType();
        $vType    = 'ajax';
        $vName    = $this->view_item;
        $vLayout  = $this->input->get('layout', 'default', 'string');

        $json = ['success' => true, 'message' => '', 'data' => null];

//        $app->setHeader('Content-Type', 'application/json', true);
        $app->sendHeaders();

        if ($view = $this->getView($vName, $vType, '', ['layout' => $vLayout])) {

            // Get/Create the model
            $model = $this->getModel();
            if (!$model) {
                $json['success'] = false;
                $json['message'] = $model->getError();
                echo json_encode($json);
                $app->close();
            }

            // Push the model into the view (as default)
            $view->setModel($model, true);

            // Display the view
            ob_start();
            $view->display('feed');
            $content = ob_get_contents();
            ob_end_clean();
        }
    }

    public function checkUpdate(){
        $app  = Factory::getApplication();
        $json = ['success' => true, 'message' => '', 'data' => null];

        try{
            $xml    = simplexml_load_file(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.'/tz_portfolio_plus.xml');

            if (isset($xml->updateservers)) {
                $updateServers = $xml->updateservers;
                if (isset($updateServers->server)) {
                    $server     = $updateServers->server;
                    $updateLink = trim((string) $server);
                    $pirority   = $server['pirority'];

                    $updateXML = @simplexml_load_file($updateLink);
                    if ($updateXML && isset($updateXML->update)) {
                        $updateXML    = $updateXML->update[$pirority - 1];
                        $json['data'] = (string) $updateXML->version;
                    }
                }
            }
        }catch (Exception $exception){
            $json['success'] = false;
            $json['message'] = $exception->getMessage();
        }
        echo json_encode($json);
        $app->close();
    }

    public function statistics(){
        $app  = Factory::getApplication();
        $json = ['success' => true, 'message' => '', 'data' => null];

        $data   = array('addons' => array(), 'styles' => array());

        if ($adoModels = BaseDatabaseModel::getInstance('AddOns', 'TZ_Portfolio_PlusModel')) {
            $adoInstTotal   = $adoModels -> getTotal();
            $data['addons']['installed']  = $adoInstTotal;
            try{
//                if($adosUpdate = $adoModels -> getItemsUpdate()) {
                $adosUpdate = $adoModels -> getItemsUpdate();
                $adosUpdateTotal = $adosUpdate?count($adosUpdate):0;
                $data['addons']['update']  = $adosUpdateTotal;
//                }
            }catch (Exception $exception){}
        }
        if($adoModel = BaseDatabaseModel::getInstance('AddOn', 'TZ_Portfolio_PlusModel')) {
            try {
                $addon = $adoModel->getItemsFromServer();
            }catch (Exception $exception){}

            $adoTotal   = $adoModel->getState('list.total', 0);
            $data['addons']['total']  = $adoTotal - 1
                + TZ_Portfolio_PlusHelperAddons::getTotal(array('protected' => 1));
        }
        if($stlModel = BaseDatabaseModel::getInstance('Template', 'TZ_Portfolio_PlusModel')) {
            try {
                $style = $stlModel->getItemsFromServer();
            }catch (Exception $exception){}
            $stlTotal   = $stlModel->getState('list.total', 0);
            $data['styles']['total'] = $stlTotal + TZ_Portfolio_PlusHelperTemplates::getTotal(array('protected' => 1));
        }
        if($stlModels = BaseDatabaseModel::getInstance('Templates', 'TZ_Portfolio_PlusModel')) {
            $stlInstTotal   = $stlModels -> getTotal();
            $data['styles']['installed'] = $stlInstTotal;
            try {
//                if($stlsUpdate = $stlModels -> getItemsUpdate()) {
                $stlsUpdate = $stlModels->getItemsUpdate();
                $stlsUpdateTotal = $stlsUpdate?count($stlsUpdate):0;
                $data['styles']['update'] = $stlsUpdateTotal;
//            }

            }catch (Exception $exception){}
        }
        if(count($data)){
            $json['data'] = $data;
        }
        echo json_encode($json);
        $app->close();
    }
}