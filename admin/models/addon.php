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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

use Joomla\Registry\Registry;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.application.component.modeladmin');

class TZ_Portfolio_PlusModelAddon extends JModelAdmin
{
    protected $type         = 'tz_portfolio_plus-plugin';
    protected $accept_types = array();
    protected $_cache;
    protected $folder       = 'addons';

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this -> accept_types   = array('tz_portfolio_plus-plugin', 'tz_portfolio_plus-template');
    }

    public function getTable($type = 'Extensions', $prefix = 'TZ_Portfolio_PlusTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    function getForm($data = array(), $loadData = true){
        $input  = JFactory::getApplication() -> input;
        // The folder and element vars are passed when saving the form.
        if (empty($data))
        {
            $item		= $this->getItem();
            $folder		= $item->folder;
            $element	= $item->element;
        }
        else
        {
            $folder		= JArrayHelper::getValue($data, 'folder', '', 'cmd');
            $element	= JArrayHelper::getValue($data, 'element', '', 'cmd');
        }

        // These variables are used to add data from the plugin XML files.
        $this->setState('item.folder',	$folder);
        $this->setState('item.element',	$element);

        $control    = 'jform';
        if($input -> getCmd('layout') == 'upload'){
            $loadData   = false;
            $control    = '';
        }

        $form = $this->loadForm('com_tz_portfolio_plus.'.$input -> getCmd('view'), $input -> getCmd('view'),
            array('control' => $control, 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }
        return $form;
    }
    protected function loadFormData()
    {
        $input  = JFactory::getApplication() -> input;
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_tz_portfolio_plus.edit.addon.data', array());

        if (empty($data))
        {
            $data = $this->getItem();
        }

        $this->preprocessData('com_tz_portfolio_plus.'.$input -> getCmd('view'), $data);

        return $data;
    }


    protected function preprocessForm(JForm $form, $data, $group = 'content')
    {
        $input  = JFactory::getApplication() -> input;
        if($input -> getCmd('layout') != 'upload'){
            jimport('joomla.filesystem.path');

            $folder		= $this->getState('item.folder');
            $element	= $this->getState('item.element');
            $lang		= JFactory::getLanguage();

            // Load the core and/or local language sys file(s) for the ordering field.
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select($db->quoteName('element'))
                ->from($db->quoteName('#__tz_portfolio_plus_extensions'))
                ->where($db->quoteName('type') . ' = ' . $db->quote($this -> type))
                ->where($db->quoteName('folder') . ' = ' . $db->quote($folder));
            $db->setQuery($query);
            $elements = $db->loadColumn();

            if (empty($folder) || empty($element))
            {
                $app = JFactory::getApplication();
                $app->redirect(JRoute::_('index.php?option=com_tz_portfolio_plus&view=addons', false));
            }

            $formFile = JPath::clean(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $folder . '/' . $element . '/' . $element . '.xml');

            if (!file_exists($formFile))
            {
                throw new Exception(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_ADDONS_ERROR_FILE_NOT_FOUND', $element . '.xml'));
            }

            // Load the core and/or local language file(s).
            $lang->load('plg_' . $folder . '_' . $element, COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $folder . '/' . $element, null, false, true);

            if (file_exists($formFile))
            {
                // Get the plugin form.
                if (!$form->loadFile($formFile, false, '//config'))
                {
                    throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
                }
            }

            // Attempt to load the xml file.
            if (!$xml = simplexml_load_file($formFile))
            {
                throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
            }

            // Get the help data from the XML file if present.
            $help = $xml->xpath('/extension/help');

            if (!empty($help))
            {
                $helpKey = trim((string) $help[0]['key']);
                $helpURL = trim((string) $help[0]['url']);

                $this->helpKey = $helpKey ? $helpKey : $this->helpKey;
                $this->helpURL = $helpURL ? $helpURL : $this->helpURL;
            }
        }

        // Trigger the default form events.
        parent::preprocessForm($form, $data, $group);
    }

    public function getExtension($name, $type){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('*');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_extensions'));
        $query -> where($db -> quoteName('type').'='.$db -> quote($type));
        $query -> where($db -> quoteName('name').'='.$db -> quote($name));
        $db -> setQuery($query);
        if($data = $db -> loadObject()){
            return $data;
        }
        return false;
    }

    public function install()
    {
        $app        = JFactory::getApplication();
        $input      = $app -> input;

        // Load installer plugins for assistance if required:
        JPluginHelper::importPlugin('installer');
        $dispatcher = JEventDispatcher::getInstance();

        $package = null;

        // This event allows an input pre-treatment, a custom pre-packing or custom installation.
        // (e.g. from a JSON description).
        $results = $dispatcher->trigger('onInstallerBeforeInstallation', array($this, &$package));

        /* phan code working */
        if (in_array(true, $results, true))
        {
            return true;
        }

        if (in_array(false, $results, true))
        {
            return false;
        }
        /* end phan code working */

        $package        = $this -> _getPackageFromUpload();
        $extension_path = COM_TZ_PORTFOLIO_PLUS_PATH_SITE;
        $result         = true;
        $msg            = JText::sprintf('COM_TZ_PORTFOLIO_PLUS_INSTALL_SUCCESS', JText::_('COM_TZ_PORTFOLIO_PLUS_'.$input -> getCmd('view')));

        // This event allows a custom installation of the package or a customization of the package:
        $results = $dispatcher->trigger('onInstallerBeforeInstaller', array($this, &$package));

        if (in_array(true, $results, true))
        {
            return true;
        }

        if (in_array(false, $results, true))
        {
            return false;
        }

        // Was the package unpacked?
        if (!$package || !$package['type'])
        {
            JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

            $app->enqueueMessage(JText::_('COM_TZ_PORTFOLIO_PLUS_UNABLE_TO_FIND_INSTALL_PACKAGE'), 'error');

            return false;
        }

        // Get an installer instance.
        $installer  = JInstaller::getInstance($package['dir']);
        $installer -> setPath('source',$package['dir']);


        if($manifest = $installer ->getManifest()){
            $attrib = $manifest -> attributes();

            $name   = (string) $manifest -> name;
            $type   = (string) $attrib -> type;
            $group  = '';


            if(!in_array($type, $this -> accept_types) || (in_array($type, $this -> accept_types)
                    && $type != $this -> type)){
                $app->enqueueMessage(JText::_('COM_TZ_PORTFOLIO_PLUS_UNABLE_TO_FIND_INSTALL_PACKAGE'), 'error');
                return false;
            }


            $_type  = str_replace('tz_portfolio_plus-','',$type);
            tzportfolioplusimport('adapter.'.$_type);
            $class  = 'TZ_Portfolio_PlusInstallerAdapter'.$_type;

            $tzinstaller    = new $class($installer,$installer -> getDbo());
            $tzinstaller -> setRoute('install');
            $tzinstaller -> setManifest($installer -> getManifest());
            $tzinstaller -> setProperties(array('type' => $type));
            if(!$tzinstaller -> install()){
                // There was an error installing the package.
                $msg = JText::sprintf('COM_TZ_PORTFOLIO_PLUS_INSTALL_ERROR', $input -> getCmd('view'));
                $result = false;
                $msgType = 'error';
            }
            else
            {
                // Package installed sucessfully.
                $msg = JText::sprintf('COM_TZ_PORTFOLIO_PLUS_INSTALL_SUCCESS', JText::_('COM_TZ_PORTFOLIO_PLUS_'.$input -> getCmd('view')));
                $result = true;
                $msgType = 'message';
            }

            // This event allows a custom a post-flight:
            $dispatcher->trigger('onInstallerAfterInstaller', array($this, &$package, $installer, &$result, &$msg));

            $app->enqueueMessage($msg, $msgType);
        }

        JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

        return $result;
    }

    public function uninstall($eid = array())
    {
        $user   = JFactory::getUser();
        $app    = JFactory::getApplication();
        $view   = $app -> input -> getCmd('view');

        if (!$user->authorise('core.delete', 'com_tz_portfolio_plus'))
        {
            JError::raiseWarning(403, JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));

            return false;
        }

        $failed = array();

        /*
         * Ensure eid is an array of extension ids in the form id => client_id
         * TODO: If it isn't an array do we want to set an error and fail?
         */
        if (!is_array($eid))
        {
            $eid = array($eid => 0);
        }

        // Get an installer object for the extension type
        $table = $this -> getTable();

        // Uninstall the chosen extensions
        $msgs = array();
        $result = false;

        // Get an installer instance.
        $installer  = JInstaller::getInstance();

        foreach ($eid as $id)
        {
            $id = trim($id);
            $table->load($id);

            $langstring = 'COM_TZ_PORTFOLIO_PLUS_' . strtoupper($table->type);
            $rowtype = JText::_($langstring);

            if (strpos($rowtype, $langstring) !== false)
            {
                $rowtype = $table->type;
            }

            if ($table->type && $table->type == 'tz_portfolio_plus-plugin')
            {

                // Is the template we are trying to uninstall a core one?
                // Because that is not a good idea...
                if ($table->protected)
                {
                    JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_PLG_UNINSTALL_WARNCOREPLUGIN',
                        JText::_('COM_TZ_PORTFOLIO_PLUS_'.$view)), JLog::WARNING, 'jerror');

                    return false;
                }

                $_type  = str_replace('tz_portfolio_plus-','',$table->type);
                tzportfolioplusimport('adapter.'.$_type);
                $class  = 'TZ_Portfolio_PlusInstallerAdapter'.$_type;

                $tzinstaller    = new $class($installer,$installer -> getDbo());

                $result = $tzinstaller->uninstall($id);

                // Build an array of extensions that failed to uninstall
                if ($result === false)
                {
                    // There was an error in uninstalling the package
                    $msgs[] = JText::sprintf('COM_TZ_PORTFOLIO_PLUS_UNINSTALL_ERROR', JText::_('COM_TZ_PORTFOLIO_PLUS_'.$view));

                    continue;
                }

                // Package uninstalled sucessfully
                $msgs[] = JText::sprintf('COM_TZ_PORTFOLIO_PLUS_UNINSTALL_SUCCESS', JText::_('COM_TZ_PORTFOLIO_PLUS_'.$view));
                $result = true;
            }
        }

        $msg = implode("<br />", $msgs);
        $app->enqueueMessage($msg);

        return $result;
    }

    public function afterSave($data){}

    protected function _getPackageFromUpload()
    {
        // Get the uploaded file information.
        $input    = JFactory::getApplication()->input;
        // Do not change the filter type 'raw'. We need this to let files containing PHP code to upload. See JInputFiles::get.
        $userfile = $input->files->get('install_package', null, 'raw');

        // Make sure that file uploads are enabled in php.
        if (!(bool) ini_get('file_uploads'))
        {
            JError::raiseWarning('', JText::_('COM_TZ_PORTFOLIO_PLUS_MSG_INSTALL_WARNINSTALLFILE'));

            return false;
        }

        // Make sure that zlib is loaded so that the package can be unpacked.
        if (!extension_loaded('zlib'))
        {
            JError::raiseWarning('', JText::_('COM_TZ_PORTFOLIO_PLUS_MSG_INSTALL_WARNINSTALLZLIB'));

            return false;
        }

        // If there is no uploaded file, we have a problem...
        if (!is_array($userfile))
        {
            JError::raiseWarning('', JText::_('COM_TZ_PORTFOLIO_PLUS_MSG_INSTALL_NO_FILE_SELECTED'));

            return false;
        }

        // Is the PHP tmp directory missing?
        if ($userfile['error'] && ($userfile['error'] == UPLOAD_ERR_NO_TMP_DIR))
        {
            JError::raiseWarning('', JText::_('COM_TZ_PORTFOLIO_PLUS_MSG_INSTALL_WARNINSTALLUPLOADERROR') . '<br />' . JText::_('COM_TZ_PORTFOLIO_PLUS_MSG_WARNINGS_PHPUPLOADNOTSET'));

            return false;
        }

        // Is the max upload size too small in php.ini?
        if ($userfile['error'] && ($userfile['error'] == UPLOAD_ERR_INI_SIZE))
        {
            JError::raiseWarning('', JText::_('COM_TZ_PORTFOLIO_PLUS_MSG_INSTALL_WARNINSTALLUPLOADERROR') . '<br />' . JText::_('COM_TZ_PORTFOLIO_PLUS_MSG_WARNINGS_SMALLUPLOADSIZE'));

            return false;
        }

        // Check if there was a different problem uploading the file.
        if ($userfile['error'] || $userfile['size'] < 1)
        {
            JError::raiseWarning('', JText::_('COM_TZ_PORTFOLIO_PLUS_MSG_INSTALL_WARNINSTALLUPLOADERROR'));

            return false;
        }

        // Build the appropriate paths.
        $tmp_dest	= JPATH_ROOT . '/tmp/tz_portfolio_plus_install/' . $userfile['name'];
        $tmp_src	= $userfile['tmp_name'];

        if(!JFile::exists(JPATH_ROOT . '/tmp/tz_portfolio_plus_install/index.html')){
            JFile::write(JPATH_ROOT . '/tmp/tz_portfolio_plus_install/index.html',
                htmlspecialchars_decode('<!DOCTYPE html><title></title>'));
        }

        // Move uploaded file.
        jimport('joomla.filesystem.file');
        JFile::upload($tmp_src, $tmp_dest, false, true);

        // Unpack the downloaded package file.
        $package = JInstallerHelper::unpack($tmp_dest, true);

        return $package;
    }

    public function getItem($pk = null)
    {
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName().'.id');

        if (!isset($this->_cache[$pk]))
        {
            $false	= false;

            // Get a row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            $return = $table->load($pk);

            // Check for a table object error.
            if ($return === false && $table->getError())
            {
                $this->setError($table->getError());

                return $false;
            }

            // Convert to the JObject before adding other data.
            $properties = $table->getProperties(1);
            $this->_cache[$pk] = JArrayHelper::toObject($properties, 'JObject');

            // Convert the params field to an array.
            $registry = new Registry;
            $registry->loadString($table->params);
            $this->_cache[$pk]->params = $registry->toArray();

            $plugin = TZ_Portfolio_PlusPluginHelper::getInstance($this->_cache[$pk] -> folder, $this->_cache[$pk] -> element);

            $this->_cache[$pk] -> data_manager        = false;
            if(method_exists($plugin, 'getDataManager')){
                $this->_cache[$pk] -> data_manager    = $plugin -> getDataManager();
            }

            // Get the plugin XML.
            $path = JPath::clean(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $table->folder . '/'
                . $table->element . '/' . $table->element . '.xml');

            if (file_exists($path))
            {
                $xml                    = simplexml_load_file($path);
                $this->_cache[$pk]->xml = $xml;
            }
            else
            {
                $this->_cache[$pk]->xml = null;
            }
        }

        return $this->_cache[$pk];
    }

    public function getAddOnItem($pk = null){
        $pk         = (!empty($pk)) ? $pk : (int) $this->getState($this->getName().'.id');
        $storeId    = __METHOD__.'::' .$pk;

        if (!isset($this->_cache[$storeId]))
        {
            $false	= false;

            // Get a row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            $return = $table->load($pk);

            // Check for a table object error.
            if ($return === false && $table->getError())
            {
                $this->setError($table->getError());

                return $false;
            }

            // Convert to the JObject before adding other data.
            $properties = $table->getProperties(1);
            $this->_cache[$storeId] = JArrayHelper::toObject($properties, 'JObject');

            // Convert the params field to an array.
            $registry = new Registry;
            $registry->loadString($table->params);
            $this->_cache[$storeId]->params = $registry->toArray();

            $dispatcher     = JEventDispatcher::getInstance();
            $plugin         = TZ_Portfolio_PlusPluginHelper::getInstance($table -> folder,
                $table -> element, false, $dispatcher);
            if(method_exists($plugin, 'onAddOnDisplayManager')) {
                $this->_cache[$storeId]->manager = $plugin->onAddOnDisplayManager();
            }
        }

        return $this->_cache[$storeId];
    }

    public function getReturnLink(){
        $input  = JFactory::getApplication() -> input;
        if($return = $input -> get('return', null, 'base64')){
            return $return;
        }
        return false;
    }

    public function prepareTable($table){
        if(isset($table -> params) && is_array($table -> params)){
            $registry   = new Registry;
            $registry -> loadArray($table -> params);
            $table -> params    = $registry -> toString();
        }
    }

    protected function getReorderConditions($table)
    {
        $condition = array();
        $condition[] = 'type = ' . $this->_db->quote($table->type);
        $condition[] = 'folder = ' . $this->_db->quote($table->folder);

        return $condition;
    }

    protected function cleanCache($group = null, $client_id = 0)
    {
        parent::cleanCache('com_tz_portfolio_plus', 0);
        parent::cleanCache('com_tz_portfolio_plus', 1);
    }
}