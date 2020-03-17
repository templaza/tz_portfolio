<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2019 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

class TZ_Portfolio_PlusSetupControllerInstall extends TZ_Portfolio_PlusSetupControllerLegacy
{
    public function activePro(){

        $uri        = JUri::getInstance();
        $license    = $this -> input -> get('license');
        $header     = array('content-type' => 'text/x-json; charset=UTF-8');
        $lang       = JFactory::getApplication('administrator') -> getLanguage();

        $response = \JHttpFactory::getHttp()->post(COM_TZ_PORTFOLIO_PLUS_SETUP_ACTIVE,
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

                $licPath    = COM_TZ_PORTFOLIO_PLUS_SETUP_LICENCE_PATH.'/license.php';

                if(JFile::exists($licPath)){
                    JFile::delete($licPath);
                }

                JFile::write($licPath, $data);

                $this->setInfo('COM_TZ_PORTFOLIO_PLUS_SETUP_ACTIVE_PRO_VERSION_SUCCESS', true, array('license' => $license));
            }else{
                $this->setInfo($result -> message, false);
            }
        }


        return $this->output();
    }

    public function extract(){

        // Check the api key from the request
        $license    = $this->input->get('license', '');

        if(!$license && JFile::exists(COM_TZ_PORTFOLIO_PLUS_SETUP_LICENCE_PATH.'/license.php')){
            JFile::delete(COM_TZ_PORTFOLIO_PLUS_SETUP_LICENCE_PATH.'/license.php');
        }

        // Get the package
        $package = COM_TZ_PORTFOLIO_PLUS_SETUP_PACKAGE;

        // Construct storage path
        $storage = COM_TZ_PORTFOLIO_PLUS_SETUP_PACKAGES . '/' . $package;

        $exists = JFile::exists($storage);

        // Test if package really exists
        if (!$exists) {
            $this->setInfo('COM_TZ_PORTFOLIO_PLUS_SETUP_ERROR_PACKAGE_DOESNT_EXIST', false);
            return $this->output();
        }

        // Remove all files in tmp
        try{
            if(JFolder::exists(COM_TZ_PORTFOLIO_PLUS_SETUP_TMP)) {
                JFolder::delete(COM_TZ_PORTFOLIO_PLUS_SETUP_TMP);
            }
        }catch (Exception $e){

        }

        // Check if the temporary folder exists
        if (!JFolder::exists(COM_TZ_PORTFOLIO_PLUS_SETUP_TMP)) {
            JFolder::create(COM_TZ_PORTFOLIO_PLUS_SETUP_TMP);
        }

        // Generate a temporary folder name
        $fileName = 'com_tz_portfolio_plus_package_' . uniqid();
        $tmp = COM_TZ_PORTFOLIO_PLUS_SETUP_TMP . '/' . $fileName;


        // Delete any folders that already exists
        if (JFolder::exists($tmp)) {
            JFolder::delete($tmp);
        }

        // Try to extract the files
        $state = $this->tppExtract($storage, $tmp);

        if (!$state) {
            $this->setInfo('COM_TZ_PORTFOLIO_PLUS_SETUP_ERROR_EXTRACT_ERRORS', false);
            return $this->output();
        }

        $this->setInfo('COM_TZ_PORTFOLIO_PLUS_SETUP_EXTRACT_SUCCESS', true, array('path' => $tmp));
        return $this->output();
    }
}