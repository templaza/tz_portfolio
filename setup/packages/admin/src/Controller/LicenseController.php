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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\Filesystem\File;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Uri\Uri;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\AddonsHelper;

class LicenseController extends BaseController
{

    public function verify()
    {
        $params = ComponentHelper::getParams('com_tz_portfolio');

        $key    = $params -> get('token_key');
        $result = new \stdClass();

        if (!$key) {
            $result -> type     = 'warning';
            $result -> state    = 400;
            $result -> message  = Text::_('COM_TZ_PORTFOLIO_INVALID_TOKEN_KEY');
            return $this->output($result);
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

    public function activePro(){
        $uri        = Uri::getInstance();
        $license    = $this -> input -> get('license');
        $lang       = Factory::getApplication('administrator') -> getLanguage();

        $response = HttpFactory::getHttp()->post(COM_TZ_PORTFOLIO_ACTIVE_LICENSE,
            array(
                'license'   => $license,
                'language'  => ($lang -> getTag()),
                'domain'    => ($uri -> getHost()),
                'produce'   => 'tz-portfolio-plus'
            )
        );

        if (!$response) {
            return false;
        }

        $_result    = new \stdClass();
        $result     = json_decode($response -> body);
        if($result){
            if($result -> state == 200 && $result -> license){

                $lic    = $result -> license;
                $data   = '<?php die("Access Denied"); ?>#x#' . serialize($lic);

                $licPath    = COM_TZ_PORTFOLIO_ADMIN_PATH.'/includes/license.php';

                if(file_exists($licPath)){
                    File::delete($licPath);
                }

                File::write($licPath, $data);

                $_result -> state   = 200;
                $_result -> success   = true;
                $_result -> message = Text::_('COM_TZ_PORTFOLIO_SETUP_ACTIVE_PRO_VERSION_SUCCESS');
                $_result -> license = $license;

                $app    = Factory::getApplication();
                $app -> enqueueMessage(Text::_('COM_TZ_PORTFOLIO_SETUP_ACTIVE_PRO_VERSION_SUCCESS'));
                $app->getSession()->set('application.queue', $app->getMessageQueue());

                return $this -> output($_result);
            }else{
                $_result -> state   = 400;
                $_result -> success = false;
                $_result -> message = $result -> message;

                return $this -> output($_result);
            }
        }

        return false;
    }

    public function verifyLicense($key){
        $post       = array('token_key' => $key, 'produce' => 'tz-portfolio-plus');

        try{
            if($response = HttpFactory::getHttp() -> post(COM_TZ_PORTFOLIO_VERIFY_LICENSE, $post)){
                if($response -> code == 200) {
                    return json_decode($response->body);
                }
            }
        }catch (\Exception $exception){
            var_dump($exception); die();
        }

        return false;
    }

    public function deleteLicense(){

        $result = new \stdClass();
        $file   = COM_TZ_PORTFOLIO_ADMIN_PATH.'/includes/license.php';

        if(file_exists($file)){
            File::delete($file);
        }

        $result -> state    = 200;
        $result -> success  = true;
        $result -> message  = Text::_('COM_TZ_PORTFOLIO_DELETED_LICENSE');

        $app    = Factory::getApplication();
        $app -> enqueueMessage(Text::_('COM_TZ_PORTFOLIO_DELETED_LICENSE'));
        $app->getSession()->set('application.queue', $app->getMessageQueue());

        return $this -> output($result);
    }

    public function output($data = array())
    {
        header('Content-type: text/x-json; UTF-8');

        if (empty($data)) {
            $data = $this->result;
        }

        echo json_encode($data);
        exit;
    }
}
