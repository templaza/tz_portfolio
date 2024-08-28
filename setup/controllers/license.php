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

// No direct access
defined('_JEXEC') or die;

use Joomla\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Http\HttpFactory;

class TZ_PortfolioSetupControllerLicense extends TZ_PortfolioSetupControllerLegacy
{

    public function verify()
    {

        $key = $this->input->get('token_key', COM_TZ_PORTFOLIO_SETUP_TOKEN_KEY, 'default');
        $result = new stdClass();

        if (!$key) {
            $result -> type     = 'warning';
            $result -> state    = 400;
            return $this->output($result);
        }

        $license = $this -> getLicense();
        if($license){
            $post   = array('license' => $license -> reference, 'produce' => 'tz-portfolio-plus');
            if($activeResponse = HttpFactory::getHttp() -> post(COM_TZ_PORTFOLIO_SETUP_ACTIVE, $post)){
                $active = $activeResponse -> body;
                $active = json_decode($active);
                if($active -> state == 400 || ($active -> state == 200 && (!isset($active -> license) ||
                            (isset($active -> license) && !$active -> license)))){
                    $result->state = 400;
                    $result -> type     = 'error';
                    $result->message = Text::_('COM_TZ_PORTFOLIO_SETUP_LICENSE_EXPIRED');
                    return $this->output($result);
                }
            }
        }

        if(file_exists(COM_TZ_PORTFOLIO_SETUP_LICENCE_PATH.'/license.php')
            && (!$license || ($license && (!isset($license -> reference) || (isset($license -> reference)
                            && !$license -> reference))))){
            File::delete(COM_TZ_PORTFOLIO_SETUP_LICENCE_PATH.'/license.php');
        }

        // Verify the key
        $response   = $this -> verifyLicense($key);

        if ($response === false) {
            $result->state = 400;
            $result->message = Text::_('COM_TZ_PORTFOLIO_SETUP_UNABLE_TO_VERIFY');
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

        if($response = HttpFactory::getHttp() -> post(COM_TZ_PORTFOLIO_SETUP_VERIFY, $post,
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

        $file    = COM_TZ_PORTFOLIO_SETUP_LICENCE_PATH.'/license.php';

        if(file_exists($file)){
            $license    = file_get_contents($file);
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