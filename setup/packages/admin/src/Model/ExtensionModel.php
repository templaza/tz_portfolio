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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Model;

// no direct access
defined('_JEXEC') or die;

use Akeeba\WebPush\WebPush\VAPID;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Factory\MVCFactoryServiceTrait;
use Joomla\CMS\Table\Table;
use Joomla\DI\Container;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Path;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\Installer\InstallerHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\WorkflowBehaviorTrait;
use Joomla\CMS\MVC\Model\WorkflowModelInterface;
use Joomla\CMS\Versioning\VersionableModelTrait;
use Joomla\CMS\Event\Installer\AfterInstallerEvent;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\ExtraFieldsHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;

/**
 * About Page Model
 */
class ExtensionModel extends AddonModel
{
    use MVCFactoryServiceTrait;
    
    protected $type         = 'module';
    protected $folder       = 'modules';

    public function __construct($config = array(), MVCFactoryInterface $factory = null)
    {
        parent::__construct($config, $factory);

        // Set the model dbo
        if (array_key_exists('dbo', $config))
        {
            $this->_db = $config['dbo'];
        }
        else
        {
            $this->_db = Factory::getDbo();
        }
    }

    protected function populateState(){
        parent::populateState();

        $this -> setState($this -> getName().'.id',Factory::getApplication() -> input -> getInt('id'));

//        $this -> setState('cache.filename', $this -> getName().'_list');
    }

    public function getTable($type = 'Extension', $prefix = 'Joomla\\CMS\\Table\\', $config = array())
    {
        return Table::getInstance($type, $prefix, $config);
    }

    public function install()
    {
        $app = Factory::getApplication();
        $input = $app->input;

        // Load installer plugins for assistance if required:
        PluginHelper::importPlugin('installer');

        $package = null;

        // This event allows an input pre-treatment, a custom pre-packing or custom installation.
        // (e.g. from a JSON description).
        $results = $app->triggerEvent('onInstallerBeforeInstallation', array($this, &$package));

        /* phan code working */
        if (in_array(true, $results, true)) {
            return true;
        }

        if (in_array(false, $results, true)) {
            return false;
        }
        /* end phan code working */

        if ($input->get('task') == 'ajax_install') {
            $url = $input->post->get('pProduceUrl', null, 'string');
            $package = $this->_getPackageFromUrl($url);
        } else {
            $package = $this->_getPackageFromUpload();
        }

        $result = true;
        $msg = Text::sprintf('COM_TZ_PORTFOLIO_INSTALL_SUCCESS', Text::_('COM_TZ_PORTFOLIO_' . $input->getCmd('view')));

        // This event allows a custom installation of the package or a customization of the package:
        $results = $app->triggerEvent('onInstallerBeforeInstaller', array($this, &$package));

        if (in_array(true, $results, true)) {
            return true;
        }

        if (in_array(false, $results, true)) {
            return false;
        }

        // Was the package unpacked?
        if (!$package || !$package['type']) {
            InstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

            $this->setError(Text::_('COM_TZ_PORTFOLIO_UNABLE_TO_FIND_INSTALL_PACKAGE'));

            return false;
        }

        $installer  = Installer::getInstance();
        $installer -> setPath('source',$package['dir']);


        if($manifest = $installer ->getManifest()){
            $attrib = $manifest -> attributes();

            $name   = (string) $manifest -> name;
            $type   = (string) $attrib -> type;

            if(!in_array($type, $this -> accept_types) || (in_array($type, $this -> accept_types)
                    && $type != $this -> type)){
                $this -> setError(Text::_('COM_TZ_PORTFOLIO_UNABLE_TO_FIND_INSTALL_PACKAGE'));
                return false;
            }

            if(!$installer -> install($package['dir'])){
                // There was an error installing the package.
                $msg = Text::sprintf('COM_TZ_PORTFOLIO_INSTALL_ERROR', $input -> getCmd('view'));
                $result = false;
                $this -> setError($msg);
            }

            // This event allows a custom a post-flight:
            $app->triggerEvent('onInstallerAfterInstaller', array($this, &$package, $installer, &$result, &$msg));
        }

        InstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

        return $result;
    }

    protected function canDelete($record)
    {
        if (!empty($record->id))
        {
            $user = Factory::getUser();

            if(isset($record -> asset_id) && !empty($record -> asset_id)) {
                $state = $user->authorise('core.delete', $this->option . '.template.' . (int)$record->id);
            }else{
                $state = $user->authorise('core.delete', $this->option . '.template');
            }
            return $state;
        }

        return parent::canDelete($record);
    }

    protected function canEditState($record)
    {
        $user = Factory::getUser();

        // Check for existing group.
        if (!empty($record->id))
        {
            if(isset($record -> asset_id) && $record -> asset_id) {
                $state = $user->authorise('core.edit.state', $this->option . '.template.' . (int)$record->id);
            }else{
                $state = $user->authorise('core.edit.state', $this->option . '.template');
            }
            return $state;
        }

        return parent::canEditState($record);
    }

    public function getUrlFromServer($xmlTag = 'extensionurl'){
        return parent::getUrlFromServer($xmlTag);
    }

    protected function getManifest_Cache($element, $folder = null, $type = 'module', $key = null){
        return parent::getManifest_Cache($element, $folder, $type, $key);
    }

    protected function __get_extensions_installed(&$update = array(), $model_type = 'Extensions',
                                                  $model_prefix = 'Administrator', &$limit_start = 0){
        return parent::__get_extensions_installed($update, $model_type, $model_prefix, $limit_start);
    }
}
