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

// No direct access
defined('_JEXEC') or die;

class TZ_Portfolio_PlusSetupControllerLicense extends TZ_Portfolio_PlusSetupControllerLegacy
{

    public function verify()
    {

        $key = $this->input->get('token_key', COM_TZ_PORTFOLIO_PLUS_SETUP_TOKEN_KEY, 'default');
        $result = new stdClass();

        if (!$key) {
            $result -> type     = 'warning';
            $result -> state    = 400;
            return $this->output($result);
        }

        $license = $this -> getLicense();
        if($license){
            $post   = array('license' => $license -> reference, 'produce' => 'tz-portfolio-plus');
            if($activeResponse = JHttpFactory::getHttp() -> post(COM_TZ_PORTFOLIO_PLUS_SETUP_ACTIVE, $post)){
                $active = $activeResponse -> body;
                $active = json_decode($active);
                if($active -> state == 400 || ($active -> state == 200 && (!isset($active -> license) ||
                            (isset($active -> license) && !$active -> license)))){
                    $result->state = 400;
                    $result -> type     = 'error';
                    $result->message = JText::_('COM_TZ_PORTFOLIO_PLUS_SETUP_LICENSE_EXPIRED');
                    return $this->output($result);
                }
            }
        }

        if(JFile::exists(COM_TZ_PORTFOLIO_PLUS_SETUP_LICENCE_PATH.'/license.php')
            && (!$license || ($license && (!isset($license -> reference) || (isset($license -> reference)
                            && !$license -> reference))))){
            JFile::delete(COM_TZ_PORTFOLIO_PLUS_SETUP_LICENCE_PATH.'/license.php');
        }

        // Verify the key
        $response   = $this -> verifyLicense($key);

        if ($response === false) {
            $result->state = 400;
            $result->message = JText::_('COM_TZ_PORTFOLIO_PLUS_SETUP_UNABLE_TO_VERIFY');
            return $this->output($result);
        }

        if ($response->state == 400) {
            return $this->output($response);
        }
        $response -> type   = 'message';

        ob_start();
        ?>
        <select name="license" data-source-license>
            <?php foreach ($response->licenses as $license) { ?>
                <option value="<?php echo $license->reference;?>"><?php echo $license->title;?> - <?php echo $license->reference; ?></option>
            <?php } ?>
        </select>
        <?php
        $output = ob_get_contents();
        ob_end_clean();

        $response->html = $output;
        return $this->output($response);
    }

    public function verifyLicense($key){
        $post       = array('token_key' => $key, 'produce' => 'tz-portfolio-plus');
        $header     = array();

        if($response = JHttpFactory::getHttp() -> post(COM_TZ_PORTFOLIO_PLUS_SETUP_VERIFY, $post,
            $header)){
            if($response -> code == 200) {
                return json_decode($response->body);
            }
        }

        return json_encode('{"state": 400, "success": "false", "message": "Could not connected."}');
    }

    /*
     *  Get license info
     *  @since v2.2.7
     * */
    public function getLicense(){

        $file    = COM_TZ_PORTFOLIO_PLUS_SETUP_LICENCE_PATH.'/license.php';

        if(JFile::exists($file)){
            $license    = JFile::read($file);
            $license    = str_replace('<?php die("Access Denied"); ?>#x#', '', $license);
            $license    = trim($license);
            if(!empty($license)) {
                $license = unserialize($license);
                return $license;
            }
        }

        return false;
    }
}