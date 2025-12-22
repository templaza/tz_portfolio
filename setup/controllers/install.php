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

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Http\HttpFactory;

class TZ_PortfolioSetupControllerInstall extends TZ_PortfolioSetupControllerLegacy
{
    public function activePro(){

        $uri        = Uri::getInstance();
        $license    = $this -> input -> get('license');
        $header     = array('content-type' => 'text/x-json; charset=UTF-8');
        $lang       = Factory::getApplication('administrator') -> getLanguage();

        $response = HttpFactory::getHttp()->post(COM_TZ_PORTFOLIO_SETUP_ACTIVE,
            array(
                'license' => $license,
                'language'  => ($lang -> getTag()),
                'domain'    => ($uri -> getHost()),
                'produce' => 'tz-portfolio-plus'
            )
        );

        if (!$response) {
            return false;
        }

        $result     = json_decode($response -> body);
        if($result){
            if($result -> state == 200 && $result -> license){

                $lic    = $result -> license;
                $data   = '<?php die("Access Denied"); ?>#x#' . serialize($lic);

                $licPath    = COM_TZ_PORTFOLIO_SETUP_LICENCE_PATH.'/license.php';

                if(file_exists($licPath)){
                    File::delete($licPath);
                }

                File::write($licPath, $data);

                $this->setInfo('COM_TZ_PORTFOLIO_SETUP_ACTIVE_PRO_VERSION_SUCCESS', true, array('license' => $license));
            }else{
                $this->setInfo($result -> message, false);
            }
        }


        return $this->output();
    }

    public function extract(){

        // Check the api key from the request
        $license    = $this->input->get('license', '');

        if(!$license && file_exists(COM_TZ_PORTFOLIO_SETUP_LICENCE_PATH.'/license.php')){
            File::delete(COM_TZ_PORTFOLIO_SETUP_LICENCE_PATH.'/license.php');
        }

        // Test if package really exists
        if (!file_exists(COM_TZ_PORTFOLIO_SETUP_PACKAGES)) {
            $this->setInfo('COM_TZ_PORTFOLIO_SETUP_ERROR_PACKAGE_DOESNT_EXIST', false);
            return $this->output();
        }

        $this->setInfo('COM_TZ_PORTFOLIO_SETUP_EXTRACT_SUCCESS', true, array('path' => COM_TZ_PORTFOLIO_SETUP_PACKAGES));
        return $this->output();
    }
}