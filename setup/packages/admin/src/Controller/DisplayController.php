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
use Joomla\Filesystem\Path;
use Joomla\Filesystem\File;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use stdClass;

class DisplayController extends BaseController
{

    protected $default_view = 'dashboard';

    public function display($cachable = false, $urlparams = array()) {

        $comp   = $this -> input -> get('option');
        $view   = $this -> input -> get('view');
        $layout = $this -> input -> get('layout');
        $format = $this->input->get('format', 'html');

        $doc    = Factory::getApplication()->getDocument();
        $wa     = $doc -> getWebAssetManager();

        $doc -> addScriptOptions('tz_component.settings', [
            'option' => $comp,
            'view' => $view,
            'task' => $this -> getTask(),
            'layout' => $layout,
            'ajaxUrl'   => 'index.php?option='.$comp
        ]);

        if($format != 'ajax' && $format != 'tpl') {
            $wa->useStyle('com_tz_portfolio.admin-style');
//            $wa->useScript('com_tz_portfolio.admin-script');
        }

        $display    = parent::display($cachable, $urlparams);

        // Footer
        LayoutHelper::render('footer');

        return $display;
    }

    /**
     * Store intro guide when skip
     * */
    public function introGuide(){

        $this -> checkToken();

        $app    = Factory::getApplication();
        $input  = $this -> input;
        $view   = $input -> get('v');

        $folderPath = COM_TZ_PORTFOLIO_ADMIN_PATH.'/cache';

        if(!$view){
            $app -> close();
        }

        $filePath   = Path::clean($folderPath.'/introguide.json');

        $config     = new stdClass();

        if(file_exists($filePath)) {
            $config = file_get_contents($filePath);
            $config = json_decode($config);
        }
        $config -> {$view}    = 1;

        $config = json_encode($config);

        try {
            echo File::write($filePath, $config);
        }catch (\Exception $e){
        }
        Factory::getApplication() -> close();
    }
}
