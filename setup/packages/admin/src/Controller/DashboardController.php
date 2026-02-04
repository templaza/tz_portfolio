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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Http\HttpFactory;
use stdClass;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\AddonsHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;

class DashboardController extends BaseController
{


    public function checkUpdate(){

        $json   = new JsonResponse();

        try{
            $xml    = TZ_PortfolioHelper::getXMLManifest();

            if(isset($xml -> updateservers)){
                $updateServers = $xml -> updateservers;
                if(isset($updateServers -> server )){
                    $server     = $updateServers -> server;
                    $updateLink = trim((string) $server);
                    $pirority   = $server['pirority'];

                    $response = HttpFactory::getHttp() -> get($updateLink);

                    if($response  -> getStatusCode() != 200){
                        $json -> success    = false;
                        $json -> message    = Text::_('COM_TZ_PORTFOLIO_SERVER_LOADING_ERROR');
                    }else{
                        $updateXML = simplexml_load_string($response -> getBody());
                        if(isset($updateXML -> update)){
                            $updateXML      = $updateXML -> update[$pirority - 1];
                            $json -> data   = (string) $updateXML -> version;
                        }
                    }
                }
            }
        }catch (\Exception $exception){
            $json -> success    = false;
            $json -> message    = $exception -> getMessage();
        }

        echo json_encode($json);

        Factory::getApplication() -> close();
    }

    public function statistics(){

        $json   = new JsonResponse();

        $data   = array('addons' => array(), 'styles' => array());

        if($adoModels = $this -> getModel('Addons')) {
            $adoInstTotal   = $adoModels -> getTotal();
            $data['addons']['installed']  = (int) $adoInstTotal;
            try{
                $adosUpdate = $adoModels -> getItemsUpdate();
                $adosUpdateTotal = $adosUpdate?count($adosUpdate):0;
                $data['addons']['update']  = $adosUpdateTotal;
            }catch (\Exception $exception){}
        }
//        if($adoModel = $this -> getModel('AddOn')) {
//            try {
//                $addon = $adoModel->getItemsFromServer();
//            }catch (\Exception $exception){}
//
//            $adoTotal   = $adoModel->getState('list.total', 0);
//            $data['addons']['total']  = $adoTotal - 1
//                + AddonsHelper::getTotal(array('protected' => 1));
//        }
//        if($stlModel = $this -> getModel('Template')) {
//            try {
//                $style = $stlModel->getItemsFromServer();
//            }catch (Exception $exception){}
//            $stlTotal   = $stlModel->getState('list.total', 0);
//            $data['styles']['total'] = $stlTotal + TZ_Portfolio_PlusHelperTemplates::getTotal(array('protected' => 1));
//        }
//        if($stlModels = $this -> getModel('Templates')) {
//            $stlInstTotal   = $stlModels -> getTotal();
//            $data['styles']['installed'] = $stlInstTotal;
//            try {
//                $stlsUpdate = $stlModels->getItemsUpdate();
//                $stlsUpdateTotal = $stlsUpdate?count($stlsUpdate):0;
//                $data['styles']['update'] = $stlsUpdateTotal;
//            }catch (\Exception $exception){}
//        }
        if(count($data)){
            $json -> data   = $data;
        }
        echo json_encode($json);

        Factory::getApplication() -> close();
    }
}
