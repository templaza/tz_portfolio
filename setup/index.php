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
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;

/* Require defines */
require_once (dirname(__FILE__).'/includes/defines.php');
require_once (dirname(__FILE__).'/includes/string.php');

// Get application
$app = Factory::getApplication();
$input = $app->input;

$lang = Factory::getApplication() -> getLanguage();
$lang -> load('com_tz_portfolio', JPATH_ADMINISTRATOR);

// Ensure that the Joomla sections don't appear.
$input->set('tmpl', 'component');

// Cancel setup file
$cancelSetup = $input->get('cancelSetup', false, 'bool');

if($cancelSetup){
    // Remove folder installation
    if(is_dir(COM_TZ_PORTFOLIO_SETUP_PATH)){
        Folder::delete(COM_TZ_PORTFOLIO_SETUP_PATH);
    }

    // Redirect the user back to TZ Portfolio Plus
    return $app->redirect('index.php?option=com_tz_portfolio');
}

// Determines if the current mode is re-install
$reinstall = $input->get('reinstall', false, 'bool') ||
    $input->get('install', false, 'bool');

// If the mode is update, we need to get the latest version
$update = $input->get('update', false, 'bool');

// Process controller
$task = $input->get('task', null);

if($task){

    require_once JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/setup/controllers/legacy.php';

//    JLoader::registerNamespace('\TemPlaza\Component\TZ_Portfolio\Setup',
//        JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/setup/src');

    $controller	= \Joomla\CMS\MVC\Controller\BaseController::getInstance('TZ_PortfolioSetup',
        array('base_path' => COM_TZ_PORTFOLIO_SETUP_PATH));

    if (!empty($controller)) {
        $controller->execute($input->get('task'));
        $controller->redirect();
    }
}

//Initialize steps
$contents = file_get_contents(COM_TZ_PORTFOLIO_SETUP_CONFIG . '/install.json');
$steps = json_decode($contents);

if(ComponentHelper::isInstalled('com_tz_portfolio_plus')
    || PluginHelper::getPlugin('system','tz_portfolio_plus')){
//    array_shift($steps);
//    $lastStep   = array_pop($steps);
//    $lastStep -> index++;
    $lastStep   = end($steps);
    array_push($steps,
        (object)[
            "index"     => $lastStep -> index + 1,
            "title"     => "COM_TZ_PORTFOLIO_INSTALLATION_DISABLE_TZ_PORTFOLIO_PLUS",
            "desc"      => "COM_TZ_PORTFOLIO_INSTALLATION_DISABLE_TZ_PORTFOLIO_PLUS_DESC",
            "template"  => "disable",
        ]);
}

// Workflow
$active = $input->get('active', 0, 'default');

if ($active === 'complete') {
    if($input -> get('disable_tz_portfolio_plus') && (ComponentHelper::isInstalled('com_tz_portfolio_plus')
            || PluginHelper::getPlugin('system','tz_portfolio_plus'))){

        $db     = Factory::getDbo();

        // Disable TZ Portfolio Plus component
        if(ComponentHelper::isInstalled('com_tz_portfolio_plus')){
            $query  = $db -> getQuery(true);

            $query -> update('#__extensions');
            $query -> set('enabled = 0');
            $query -> where('type='.$db -> quote('component'));
            $query -> where('element='.$db -> quote('com_tz_portfolio_plus'));

            $db -> setQuery($query);
            $db -> execute();
        }

        // Disable System TZ Portfolio Plus plugin
        if(PluginHelper::getPlugin('system','tz_portfolio_plus')){

            $query  = $db -> getQuery(true);

            $query -> update('#__extensions');
            $query -> set('enabled = 0');
            $query -> where('type='.$db -> quote('plugin'));
            $query -> where('folder='.$db -> quote('system'));
            $query -> where('element='.$db -> quote('tz_portfolio_plus'));

            $db -> setQuery($query);
            $db -> execute();
        }
    }

    $activeStep = new stdClass();

    $activeStep->title = Text::_('COM_TZ_PORTFOLIO_INSTALLER_INSTALLATION_COMPLETED');
    $activeStep->template = 'complete';

    // Assign class names to the step items.
    if ($steps) {
        foreach ($steps as $step) {
            $step->className = ' done';
        }
    }
} else {

    if ($active == 0) {
        $active = 1;
        $stepIndex = 0;
    } else {
        $active += 1;
        $stepIndex = $active - 1;
    }

    // Get the active step object.
    $activeStep = $steps[$stepIndex];

    // Assign class names to the step items.
    foreach ($steps as $step) {
        $step->className = $step->index == $active || $step->index < $active ? ' current' : '';
        $step->className .= $step->index < $active ? ' done' : '';
    }

    // If this site meets all requirement, we skip the requirement page
    if ($stepIndex == 0) {

        $gd = function_exists('gd_info');
        $curl = is_callable('curl_init');

        // MySQL info
        $db = Factory::getDBO();
        $mysqlVersion = $db->getVersion();

        // PHP info
        $phpVersion = phpversion();
        $uploadLimit = ini_get('upload_max_filesize');
        $memoryLimit = ini_get('memory_limit');
        $postSize = ini_get('post_max_size');
		
        if ($phpVersion < 7.4 && function_exists('get_magic_quotes_gpc') && JVERSION > 3) {
            $magicQuotes = get_magic_quotes_gpc() && JVERSION > 3;
        }		

        if (stripos($memoryLimit, 'G') !== false) {

            list($memoryLimit) = explode('G', $memoryLimit);

            $memoryLimit = $memoryLimit * 1024;
        }

        $postSize = 4;
        $hasErrors = false;

        if (!$gd || !$curl || (isset($magicQuotes) && $magicQuotes)) {
            $hasErrors = true;
        }

        $files = array();

        $files['admin'] = new stdClass();
        $files['admin']->path = JPATH_ROOT . '/administrator/components';
        $files['site'] = new stdClass();
        $files['site']->path = JPATH_ROOT . '/components';
        $files['tmp'] = new stdClass();
        $files['tmp']->path = JPATH_ROOT . '/tmp';
        $files['user'] = new stdClass();
        $files['user']->path = JPATH_ROOT . '/plugins/user';
        $files['module'] = new stdClass();
        $files['module']->path = JPATH_ROOT . '/modules';

        // Debugging
        $posixExists = function_exists('posix_getpwuid');

        if ($posixExists) {
            $owners = array();
        }

        // If until here no errors, we don't display the setting section
        $showSettingsSection = $hasErrors;

        // Determines write permission on folders
        $showDirectorySection = false;

        foreach ($files as $file) {

            // The only proper way to test this is to not use is_writable
            $contents = "<body></body>";
            $state = File::write($file->path . '/tmp.html', $contents);

            // Initialize this to false by default
            $file->writable = false;

            if ($state) {
                File::delete($file->path . '/tmp.html');

                $file->writable = true;
            }

            if (!$file->writable) {
                $showDirectorySection = true;
                $hasErrors = true;
            }

            if ($posixExists) {
                $owner = posix_getpwuid(fileowner($file->path));
                $group = posix_getpwuid(filegroup($file->path));

                $file->owner = $owner['name'];
                $file->group = !empty($group) && is_array($group)?$group['name']:'';
                $file->permissions = substr(decoct(fileperms($file->path)), 1);
            }
        }

        if ($hasErrors) {
            $errorStep = new stdCLass;
            $errorStep->index = 0;
            $errorStep->title = 'COM_EB_INSTALLATION_REQUIREMENTS_ERROR';
            $errorStep->desc = 'COM_EB_INSTALLATION_REQUIREMENTS_ERROR_DESC';
            $errorStep->template = 'requirements';
            $activeStep = $errorStep;

            require(COM_TZ_PORTFOLIO_SETUP_VIEW_PATH . '/default.php');
            return;
        }
    }
}

require(COM_TZ_PORTFOLIO_SETUP_VIEW_PATH . '/default.php');
exit;
