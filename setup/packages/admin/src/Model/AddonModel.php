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
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioInstaller;
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
class AddonModel extends AdminModel implements WorkflowModelInterface
{

    use WorkflowBehaviorTrait;
    use VersionableModelTrait;
    use MVCFactoryServiceTrait;

    protected $type         = 'tz_portfolio-addon';
    protected $accept_types = array();

    public function __construct($config = array(), ?MVCFactoryInterface $factory = null, ?FormFactoryInterface $formFactory = null)
    {
        parent::__construct($config, $factory, $formFactory);

        $this -> accept_types   = array(
            'tz_portfolio-plugin',
            'tz_portfolio-addon',
            'tz_portfolio-style',
            'tz_portfolio_plus-plugin',
            'tz_portfolio_plus-addon',
            'tz_portfolio_plus-template');

//        // Set the model dbo
//        if (array_key_exists('dbo', $config))
//        {
//            $this->_db = $config['dbo'];
//        }
//        else
//        {
//            $this->_db = TZ_Portfolio_PlusDatabase::getDbo();
//        }
    }

    public function getTable($type = 'Extensions', $prefix = 'Table', $options = array())
    {
        return parent::getTable($type, $prefix, $options);
    }

    protected function populateState()
    {
        parent::populateState();

        $app    = Factory::getApplication();

        $filters = $app->getUserStateFromRequest($this->option . '.'.$this -> getName().'.filter', 'filter', array(), 'array');
        $this -> setState('filters', $filters);

        $limitstart  = $app -> getUserStateFromRequest($this->option . '.'.$this -> getName().'.limitstart', 'limitstart', 0,'int');
        $this -> setState('list.start', $limitstart);

        $search      = isset($filters['search'])?$filters['search']:null;
        $search  = $app -> getUserStateFromRequest($this->option . '.'.$this -> getName().'.filter_search', 'filter_search', $search,'string');
        $this -> setState('filter.search',$search);

        $type  = $app -> getUserStateFromRequest($this->option . '.'.$this -> getName().'.filter_type', 'filter_type',
            (isset($filters['type'])?$filters['type']:null), 'string');
        $this -> setState('filter.type',$type);

        if ($list = $app->getUserStateFromRequest($this->option . '.'.$this -> getName() . '.list', 'list', array(), 'array'))
        {
            $ordering   = 'rdate';
            if(isset($list['fullordering'])) {
                $ordering = $list['fullordering'];
            }
            $this->setState('list.ordering', $ordering);
        }

        if($listSubmit  = $app -> input -> get('list', array(), 'array')){
            if(isset($list['form_submited'])) {
                $this->setState('list.form_submited', $list['form_submited']);
            }
        }

//        // Support old ordering field
//        $oldOrdering = $app->input->get('filter_order', 'rdate');
//
//        if (!empty($oldOrdering) && in_array($oldOrdering, $this->filter_fields))
//        {
//            $this->setState('list.ordering', $oldOrdering);
//        }

        $this -> setState('cache.filename', $this -> getName().'_list');

    }

    public function getItemsFromServer(){

        $data           = false;
        $value          = $this -> getState('list.start');
        $limitstart     = $value;
        $search         = $this -> getState('filter.search');
        $type           = $this -> getState('filter.type');
        $params         = $this -> getState('params');
        $filters        = $this -> getState('filters');
        $cacheFileName  = $this -> getState('cache.filename');
        $ordering       = $this -> getState('list.ordering');
        $formSubmited   = $this -> getState('list.form_submited');

        // Cache time is 1 day
        $cacheTime      = 24 * 60 * 60;
        $cacheFilters   = null;
        $hasCache       = true;
        $cacheFolder    = JPATH_CACHE.'/'.$this -> option;
        $cacheFile      = $cacheFolder.'/'. $cacheFileName .'.json';


        // Get data from cache
        if(file_exists($cacheFile) && (filemtime($cacheFile) > (time() - $cacheTime ))){
            $items  = file_get_contents($cacheFile);
            $items  = trim($items);
            if(!empty($items)){
                $data   = json_decode($items);
                if($data && isset($data -> filters) && $data -> filters){
                    $cacheFilters   = $data -> filters;
                }else{
                    $hasCache  = false;
                }
            }
        }

        if($cacheFilters && count((array) $cacheFilters) && $filters){
            foreach($cacheFilters as $k => $v){
                if(isset($filters[$k]) && $filters[$k] != $v){
                    $hasCache  = false;
                    $limitstart = 0;
                    break;
                }
            }
        }

        if($formSubmited){
            $hasCache   = false;
        }

        if($hasCache && $data && isset($data -> start) && $data -> start != $limitstart){
            $hasCache  = false;
        }

        if(!$data && $hasCache) {
            $hasCache = false;
        }

        $needUpdate = $this -> __get_extensions_installed();

        if(!empty($needUpdate)){
            $hasCache   = false;
        }

        if(!$hasCache) {

            $url    = $this -> getUrlFromServer();

            if(!$url){
                return false;
            }

            // Get data from server
            $edition = '';
            if (COM_TZ_PORTFOLIO_EDITION == 'commercial' && $apiKey = $params->get('token_key')) {
                $edition = '&token_key=' . $apiKey;
            }

            $url .= ($limitstart ? '&start=' . $limitstart : '') . ($type ? '&type=' . urlencode($type) : '')
                . ($search ? '&search=' . urlencode($search) : '') . $edition;

            if($ordering){
                $url    .= '&order='.$ordering;
            }

            if(!empty($needUpdate)){
                $order_list = http_build_query(array('order_list'=>$needUpdate));
                $order_list = preg_replace('/%5B[0-9]+%5D/simU', '%5B%5D', $order_list);
                $url    .= '&'.$order_list;
            }

            $response = TZ_PortfolioHelper::getDataFromServer($url, 'post');

            if(!$response){
                return false;
            }

            $data   = json_decode($response -> body);

            if(!$data){
                return false;
            }

            $items  = $data -> items;
            unset($data -> items);

            $data -> start      = $limitstart;
            $data -> filters    = $filters;
            $data -> items      = $items;

            $_data   = json_encode($data);

            File::write($cacheFile, $_data);
        }

        if(!$data || ($data && !isset($data -> items) )){
            return false;
        }

        if($data -> items){
            foreach($data -> items as &$item){
                $item -> pProduce           = null;
                $item -> installedVersion   = null;

                $editionName    = 'pProduce';

                if($item -> pElement && isset($item -> pType)){
                    if($extension = $this -> getManifest_Cache($item -> pElement, $item -> pType)){
                        if(isset($extension -> edition) && $extension -> edition) {
                            $editionName = $extension->edition;
                        }
                        $item -> installedVersion   = $extension -> version;
                    }
                }

                if($pProduces = $item -> pProduces) {
                    if(isset($pProduces -> {$editionName}) && $pProduces -> {$editionName}) {
                        $item->pProduce = $pProduces->{$editionName};
                    }
                }
            }
        }

        $this -> setState('list.dataserver', true);

        $limit  = $data -> limit;

        $limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);

        $this -> setState('list.start', $limitstart);
        $this -> setState('list.limit', $limit);
        $this -> setState('list.total', $data -> total);

        return $data -> items;
    }

    public function getFilterForm($data = array(), $loadData = true)
    {
        // Try to locate the filter form automatically. Example: ContentModelArticles => "filter_articles"
        if (empty($this->filterFormName)) {
            $classNameParts = explode('Model', \get_called_class());

            if (\count($classNameParts) >= 2) {
                $this->filterFormName = 'filter_' . str_replace('\\', '', strtolower($classNameParts[1]));
            }
        }

        if (empty($this->filterFormName)) {
            return null;
        }

        try {
            // Get the form.
            return $this->loadForm($this->option . '.'.$this -> getName().'.filter', $this->filterFormName, array('control' => '', 'load_data' => $loadData));
        } catch (\RuntimeException $e) {
        }

        return null;
    }

    public function getForm($data = array(), $loadData = true){
        $input  = Factory::getApplication() -> input;
        // The folder and element vars are passed when saving the form.
        if (empty($data))
        {
            $item		= $this->getItem();
            $folder		= $item->folder;
            $element	= $item->element;
        }
        else
        {
            $folder		= ArrayHelper::getValue($data, 'folder', '', 'cmd');
            $element	= ArrayHelper::getValue($data, 'element', '', 'cmd');
        }

        // These variables are used to add data from the plugin XML files.
        $this->setState('item.folder',	$folder);
        $this->setState('item.element',	$element);

        $control    = 'jform';
        if($input -> getCmd('layout') == 'upload'){
            $loadData   = false;
            $control    = '';
        }

        $form = $this->loadForm('com_tz_portfolio.'.$this -> getName(), $this -> getName(),
            array('control' => $control, 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }

        /* @var Form $form */
        // Replace COM_TZ_PORTFOLIO to COM_TZ_PORTFOLIO
        $formXML    = $form -> getXml();
        $xml        = $formXML -> asXML();
        $orgData    = $form -> getData();

        $xml    = str_replace(array('COM_TZ_PORTFOLIO', 'com_tz_portfolio_plus'),
            array('COM_TZ_PORTFOLIO', 'com_tz_portfolio'), $xml);

        $form -> reset(true);
        $form -> load($xml, true);
        $form -> bind($orgData);

//        $elSpacers  = $formXML -> xpath('//*/field[@type="spacer" and not(@hr)]');
        $elSpacers  = $form -> getXml() -> xpath('(//fieldset/field[@type="spacer"])[1 or 2]');

        if(!empty($elSpacers)){
            foreach ($elSpacers as $elSpacer){
                $dom = dom_import_simplexml($elSpacer);
                $dom->parentNode->removeChild($dom);
            }
        }

        return $form;
    }

    public function getUrlFromServer($xmlTag = 'addonurl'){

        if(!$xmlTag){
            return false;
        }

        $url    = false;

        // Get update data
        $xml        = TZ_PortfolioHelper::getXMLManifest();
        if($updateServer = $xml -> updateservers){
            if(isset($updateServer -> server) && $updateServer -> server){

                foreach ($updateServer -> server as $server){
                    if($responseUpdate = TZ_PortfolioHelper::getDataFromServer((string) $server)){
                        $xmlUpdate  = simplexml_load_string( $responseUpdate -> body);

                        if($update = $xmlUpdate -> xpath('update['.((int) $server['pirority']).']')) {
                            $update = $update[0];
                            if(isset($update -> listupdate)) {
                                $listUpdate = $update -> listupdate;
                                if(isset($listUpdate -> {$xmlTag}) && $listUpdate -> {$xmlTag}){
                                    $url    = (string) $listUpdate -> {$xmlTag};
                                }
                            }
                        }
                        break;
                    }
                }
            }
        }
        return $url;
    }

    public function install()
    {
        $app = Factory::getApplication();
        $input = $app->input;

        // Load installer plugins for assistance if required:
//        JPluginHelper::importPlugin('installer');
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

//            $this->setError(Text::_('COM_TZ_PORTFOLIO_UNABLE_TO_FIND_INSTALL_PACKAGE'));
            $app->enqueueMessage(Text::_('COM_TZ_PORTFOLIO_UNABLE_TO_FIND_INSTALL_PACKAGE'), 'error');

            return false;
        }

        // Get an installer instance.
        $installer  = TZ_PortfolioInstaller::getInstance();
        $installer -> setPath('source',$package['dir']);

        if($manifest = $installer ->getManifest()){
            $attrib = $manifest -> attributes();

            /** Check add-on supported with tz portfolio
             * @var \SimpleXMLElement $targetPlatForm
             */
            $targetPlatForm = $manifest ->xpath('tpTargetPlatforms/tpTargetPlatform[@name="com_tz_portfolio"]');
            $hasSupported   = !empty($targetPlatForm);

            $component = ComponentHelper::getComponent('com_tz_portfolio');
            $extension = Table::getInstance('extension');

            $extension -> load($component->id);

            $compManifest   = new Registry($extension->manifest_cache);
            $compVersion    = $compManifest->get('version');

            if(!$hasSupported){
                $app -> enqueueMessage(sprintf(Text::_('This add-on not supported for this component version %s'),
                    $compVersion), 'error');
                return false;
            }

            $platFormAttrib     = $targetPlatForm[0] -> attributes();
            $platFormVersion    = (string)$platFormAttrib -> version;

            if(!empty($platFormVersion)) {

                if(!preg_match('/^' . $platFormVersion . '/', $compVersion)){
                    $app -> enqueueMessage(sprintf(Text::_('This add-on not supported for this component version %s'),
                        $compVersion), 'error');
                    return false;
                }
            }

            $name   = (string) $manifest -> name;
            $type   = (string) $attrib -> type;

            if(!in_array($type, $this -> accept_types)){
                $app -> enqueueMessage(Text::_('COM_TZ_PORTFOLIO_UNABLE_TO_FIND_INSTALL_PACKAGE'), 'error');
                return false;
            }

            $_type  = explode('-',$type);
            $_type  = end($_type);

            $_type  = $_type == 'plugin'?'addon':$_type;
            $_type  = $_type == 'template'?'style':$_type;

            // Install for add-ons to update version
            $class  = 'TemPlaza\Component\TZ_Portfolio\Administrator\Library\Adapter\\'.ucfirst($_type).'Adapter';

            if(!class_exists($class)){
                \JLoader::registerPrefix(ucfirst($_type),COM_TZ_PORTFOLIO_ADMIN_PATH.'/src/Library/Adapter/'
                    .ucfirst($_type).'Adapter.php');
            }

            $tzinstaller    = new $class($installer,$this -> getDatabase());
            $tzinstaller -> setMVCFactory($this -> getMVCFactory());
            $tzinstaller -> setRoute('install');
            $tzinstaller -> setManifest($installer -> getManifest());

            if(!$tzinstaller -> install()){
                // There was an error installing the package.
                $msg = Text::sprintf('COM_TZ_PORTFOLIO_INSTALL_ERROR', $input -> getCmd('view'));
                $result = false;
                $this -> setError($msg);
            }

            if(method_exists($this, 'afterInstall')) {
                $this -> afterInstall($manifest);
            }

            $eventAfterInst = new AfterInstallerEvent('onInstallerAfterInstaller',[
                'subject'         => $this,
                'package'         => &$package, // @todo: Remove reference in Joomla 6, see InstallerEvent::__constructor()
                'installer'       => $installer,
                'installerResult' => &$result, // @todo: Remove reference in Joomla 6, see AfterInstallerEvent::__constructor()
                'message'         => &$msg, // @todo: Remove reference in Joomla 6, see AfterInstallerEvent::__constructor()
            ]);

            $dispatcher = $this->getDispatcher();
            $dispatcher->dispatch('onInstallerAfterInstaller', $eventAfterInst);
        }

        InstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

        return $result;
    }

    public function uninstall($eid = array())
    {
        $user   = Factory::getUser();
        $app    = Factory::getApplication();
        $view   = $app -> input -> getCmd('view');

        if (!$user->authorise('core.delete', 'com_tz_portfolio.addon'))
        {
            \Log::add(\Text::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), \Log::WARNING, 'jerror');
            return false;
        }

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
        $installer  = Installer::getInstance();

        foreach ($eid as $id)
        {
            $id = trim($id);
            $table->load($id);

            $langstring = 'COM_TZ_PORTFOLIO_' . strtoupper($table->type);
            $rowtype = Text::_($langstring);

            if (strpos($rowtype, $langstring) !== false)
            {
                $rowtype = $table->type;
            }

            if ($table->type && ($table->type == 'tz_portfolio_plus-plugin' || $table -> type == $this -> type))
            {

                // Is the template we are trying to uninstall a core one?
                // Because that is not a good idea...
                if ($table->protected)
                {
                    Log::add(Text::sprintf('JLIB_INSTALLER_ERROR_PLG_UNINSTALL_WARNCOREPLUGIN',
                        Text::_('COM_TZ_PORTFOLIO_'.$view)), Log::WARNING, 'jerror');

                    return false;
                }

                $_type  = str_replace('tz_portfolio_plus-','',$table->type);
                $_type  = str_replace('tz_portfolio-','',$_type);

                $_type  = $_type == 'plugin'?'addon':$_type;

                $class  = 'TemPlaza\Component\TZ_Portfolio\Administrator\Library\Adapter\\'.$_type.'Adapter';

                $tzinstaller    = new $class($installer,$installer -> getDbo());

                $result = $tzinstaller->uninstall($id);

                // Build an array of extensions that failed to uninstall
                if ($result === false)
                {
                    // There was an error in uninstalling the package
                    $msgs[] = Text::sprintf('COM_TZ_PORTFOLIO_UNINSTALL_ERROR', Text::_('COM_TZ_PORTFOLIO_'.$view));

                    continue;
                }

                // Package uninstalled sucessfully
                $msgs[] = Text::sprintf('COM_TZ_PORTFOLIO_UNINSTALL_SUCCESS', Text::_('COM_TZ_PORTFOLIO_'.$view));
                $result = true;
            }
        }

        $msg = implode("<br />", $msgs);
        $app->enqueueMessage($msg);

        return $result;
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
            $this->_cache[$pk] = ArrayHelper::toObject($properties, CMSObject::class);

            // Convert the params field to an array.
            $registry = new Registry;
            if($table -> params) {
                $registry->loadString($table->params);
            }
            $this->_cache[$pk]->params = $registry->toArray();

            $addon = AddonHelper::getInstance($this->_cache[$pk] -> folder, $this->_cache[$pk] -> element);

            $this->_cache[$pk] -> data_manager        = false;
            if(is_object($addon) && method_exists($addon, 'getDataManager')){
                $this->_cache[$pk] -> data_manager    = $addon -> getDataManager();
            }

            // Get the plugin XML.
            $path = Path::clean(COM_TZ_PORTFOLIO_ADDON_PATH . '/' . $table->folder . '/'
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

    public function getStart()
    {
        $store = __METHOD__;

        // Try to load the data from internal storage.
        if (isset($this->cache[$store]))
        {
            return $this->cache[$store];
        }

        $start = $this->getState('list.start');

        if ($start > 0)
        {
            $limit = $this->getState('list.limit', 0);
            $total = $this -> getState('list.total', 0);

            if ($limit > 0 && $start > $total - $limit)
            {
                $start = max(0, (int) (ceil($total / $limit) - 1) * $limit);
            }
        }

        // Add the total to the internal cache.
        $this->cache[$store] = $start;

        return $this->cache[$store];
    }

    public function getPaginationFromServer()
    {
        // Get a storage key.
        $store  = __METHOD__;

        // Try to load the data from internal storage.
        if (isset($this->cache[$store]))
        {
            return $this->cache[$store];
        }

        $limit = (int) $this->getState('list.limit');

        // Create the pagination object and add the object to the internal cache.
        $this->cache[$store] = new Pagination($this -> getState('list.total'), $this->getStart(), $limit);

        return $this->cache[$store];
    }

    protected function loadFormData()
    {
        $app    = Factory::getApplication();
        $input  = $app -> getInput();
        // Check the session for previously entered form data.
        $data   = $app->getUserState('com_tz_portfolio.edit.addon.data', array());

        if (empty($data))
        {
            $data = $this->getItem();
        }

        // Pre-fill the list options
        if (!property_exists($data, 'list'))
        {
            $data->{'list'} = array(
                'fullordering'  => $this->getState('list.ordering')
            );
        }

        $this->preprocessData('com_tz_portfolio.'.$input -> getCmd('view'), $data);

        return $data;
    }

    protected function preprocessForm(Form $form, $data, $group = 'content')
    {
        $input  = Factory::getApplication() -> input;

        if($input -> getCmd('layout') != 'upload'){

            $folder		= $this->getState('item.folder');
            $element	= $this->getState('item.element');
            $lang		= Factory::getApplication() -> getLanguage();

            // Load the core and/or local language sys file(s) for the ordering field.
            $db     = $this -> getDbo();
            $query  = $db->getQuery(true)
                ->select($db->quoteName('element'))
                ->from($db->quoteName('#__tz_portfolio_plus_extensions'))
                ->where($db->quoteName('type') . ' = ' . $db->quote($this -> type))
                ->where($db->quoteName('folder') . ' = ' . $db->quote($folder));
            $db->setQuery($query);

            if (empty($folder) || empty($element))
            {
                $app = Factory::getApplication();
                $app->redirect(Route::_('index.php?option=com_tz_portfolio&view=addons', false));
            }

            $formFile = Path::clean(COM_TZ_PORTFOLIO_ADDON_PATH . '/' . $folder . '/' . $element . '/' . $element . '.xml');

            if (!file_exists($formFile))
            {
                throw new \Exception(Text::sprintf('COM_TZ_PORTFOLIO_ADDONS_ERROR_FILE_NOT_FOUND', $element . '.xml'));
            }

            // Load the core and/or local language file(s).
            AddonHelper::loadLanguage($element, $folder);

            if (file_exists($formFile))
            {
                // Get the plugin form.
                if (!$form->loadFile($formFile, false, '/extension/config/fields'))
                {
                    throw new \Exception(Text::_('JERROR_LOADFILE_FAILED'));
                }
            }

            if($form -> getField('rules')){
                if($data) {
                    if(isset($folder) && $folder && isset($element) && $element) {
                        $form -> setFieldAttribute('title', 'value', Text::_('PLG_' . strtoupper($folder . '_' . $element)));
                        if(!$form -> getFieldAttribute('rules', 'group')) {
                            $form->setFieldAttribute('rules', 'group', $folder);
                        }
                        if(!$form -> getFieldAttribute('rules', 'addon')) {
                            $form -> setFieldAttribute('rules', 'addon', $element);
                        }
                    }
                }
            }

            if($addonId = $input -> getInt('id')){

                $user       = Factory::getUser();

                if(!$user->authorise('core.edit', 'com_tz_portfolio.addon.'.$addonId)){
                    $form -> setFieldAttribute('folder', 'type', 'hidden');
                    $form -> setFieldAttribute('element', 'type', 'hidden');
                    $form -> removeField('access');
                    $form -> removeField('published');
                }
            }

            // Attempt to load the xml file.
            if (!$xml = simplexml_load_file($formFile))
            {
                throw new \Exception(Text::_('JERROR_LOADFILE_FAILED'));
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

        // Insert parameter from extrafield
        ExtraFieldsHelper::prepareForm($form, $data);

        // Trigger the default form events.
        parent::preprocessForm($form, $data, $group);

    }

    protected function getManifest_Cache($element, $folder = null, $type = 'tz_portfolio-addon', $key = null){

        if(!$element){
            return false;
        }

        if(!$type){
            $type   = 'tz_portfolio-addon';
        }

        $option = array('element' => $element, 'type' => $type);

        if($folder){
            $option['folder']   = $folder;
        }

        $table  = $this -> getTable();

        if(!$table -> load($option)){
            return false;
        }


        if (empty($key))
        {
            $key = $table->getKeyName();
        }

        if(!$table -> $key){
            return false;
        }

        $manifestCache  = false;
        if(isset($table -> manifest_cache) && $table -> manifest_cache && is_string($table -> manifest_cache)){
            $manifestCache    = json_decode($table -> manifest_cache);
        }


        return $manifestCache;
    }

    protected function __get_extensions_installed(&$update = array(), $model_type = 'Addons',
                                                  $model_prefix = 'Administrator', &$limit_start = 0){
        $limit  = 9;
        $total  = 0;
        $items  = false;

        if(strtolower($model_type) == 'extensions'){
            // Get update data
            $xml        = TZ_PortfolioHelper::getXMLManifest();
            $modules_core   = $xml -> xpath('modules/module/@module');

            $db = $this->getDatabase();
            $query = $db->getQuery(true)
                ->select('*')
                ->from('#__extensions')
                ->where('state = 0')
                ->where('type='.$db -> quote('module'))
                -> where('element LIKE '.$db -> quote('%mod_tz%'));
            if(!empty($modules_core)){
                $query -> where('element NOT IN('.$db -> quote(implode($db -> quote(','),$modules_core), false).')');
            }
            $db -> setQuery($query);

            $items  = $db -> loadObjectList();

            $query -> clear('select');
            $query -> select('COUNT(extension_id)');
            $db -> setQuery($query);
            $total  = $db -> loadResult();
        }else {
//            $model = JModelLegacy::getInstance($model_type, $model_prefix, array('ignore_request' => true));

//            $model  = Factory::getApplication()->bootComponent('tz_portfolio')
//                ->getMVCFactory()->createModel($model_type, 'Administrator');
            $model  = $this -> getMVCFactory()->createModel($model_type, $model_prefix);

            $model->setState('filter.status', 3);
            $model->setState('list.start', $limit_start);
            $model->setState('list.limit', $limit);
            $items = $model -> getItems();

            $total  = $model -> getTotal();
        }

        if(!empty($items)){

            $url    = $this -> getUrlFromServer();

            if(!$url){
                return false;
            }

            $params         = $this -> getState('params');

            // Get data from server
            $edition = '';
            if (COM_TZ_PORTFOLIO_EDITION == 'commercial' && $apiKey = $params->get('token_key')) {
                $edition = '&token_key=' . $apiKey;
            }
            $url .= $edition;

            $url    = str_replace('format=list', 'format=item', $url);

            foreach($items as $item){

                $_url   = $url;
                if(isset($item -> folder) && !empty($item -> folder)) {
                    $_url .= '&type=' . $item->folder;
                }
                $_url  .= '&element='.$item -> element;
                $response = TZ_PortfolioHelper::getDataFromServer($_url);

                if(!$response){
                    continue;
                }

                $data   = json_decode($response -> body);

                if(!$data){
                    continue;
                }

                $sitem  = $data -> item;

                $pProduct   = '';
                if(isset($sitem -> pProduces) && !empty($sitem -> pProduces) && isset($sitem -> pProduces -> pProduce)) {
                    $pProduct = $sitem -> pProduces -> pProduce;
                }

                $version    = '';
                if(isset($item -> version) && !empty($item -> version)){
                    $version    = $item -> version;
                }else{
                    if (strlen($item -> manifest_cache))
                    {
                        $manifest = json_decode($item -> manifest_cache);
                        if(!empty($manifest) && isset($manifest -> version) && !empty($manifest -> version)) {
                            $version = $manifest->version;
                        }
                    }
                }

                // Extension has update
                if(!empty($pProduct) && version_compare( $pProduct -> pVersion, $version, '>')){
                    $update[]   = $sitem -> id;
                }

            }

            $limit_start    += $limit;
            if($limit_start < $total){
                $this -> __get_extensions_installed($update, $model_type, $model_prefix, $limit_start);
            }

            return $update;
        }

        return array();

    }


    protected function _getPackageFromUrl($url)
    {
        // Capture PHP errors
        $track_errors = ini_get('track_errors');
        ini_set('track_errors', true);

        // Load installer plugins, and allow URL and headers modification
        $headers = array();
        \JPluginHelper::importPlugin('installer');
        Factory::getApplication() -> triggerEvent('onInstallerBeforePackageDownload', array(&$url, &$headers));

        $response   = TZ_PortfolioHelper::getDataFromServer($url);

        // Was the package downloaded?
        if (!$response)
        {
            throw new \Exception(Text::sprintf('COM_INSTALLER_PACKAGE_DOWNLOAD_FAILED', $url));
//            JError::raiseWarning('', Text::sprintf('COM_INSTALLER_PACKAGE_DOWNLOAD_FAILED', $url));
//
//            return false;
        }

        $target     = null;

        // Parse the Content-Disposition header to get the file name
        $contentDisposition = false;
        if(isset($response->headers['Content-Disposition'])){
            $contentDisposition = 'Content-Disposition';
        }elseif(isset($response -> headers['CONTENT-DISPOSITION'])){
            $contentDisposition = 'CONTENT-DISPOSITION';
        }if(isset($response -> headers['content-disposition'])){
        $contentDisposition = 'content-disposition';
    }

        if ($contentDisposition && ($content = $response->headers[$contentDisposition])) {
            if (is_array($content)) {
                $content = array_shift($content);
            }
            if (preg_match("/\s*filename\s?=\s?(.*)/", $content, $parts)) {
                $flds = explode(';', $parts[1]);
                $target = trim($flds[0], '"');
            }
        }

        if(!$target){
            return false;
        }

        $tmp_dest	= JPATH_ROOT . '/tmp/tz_portfolio_install/' . $target;

        if(!file_exists(JPATH_ROOT . '/tmp/tz_portfolio_install/index.html')){
            $html   = htmlspecialchars_decode('<!DOCTYPE html><title></title>');
            File::write(JPATH_ROOT . '/tmp/tz_portfolio_install/index.html', $html);
        }

        $resbody   = $response -> body;

        // Write buffer to file
        File::write($tmp_dest, $resbody);

        // Restore error tracking to what it was before
        ini_set('track_errors', $track_errors);

        // Bump the max execution time because not using built in php zip libs are slow
        @set_time_limit(ini_get('max_execution_time'));

        // Unpack the downloaded package file
        $package = InstallerHelper::unpack($tmp_dest, true);

        return $package;
    }

    protected function _getPackageFromUpload()
    {
        // Get the uploaded file information.
        $input    = Factory::getApplication()->input;
        // Do not change the filter type 'raw'. We need this to let files containing PHP code to upload. See JInputFiles::get.
        $userfile = $input->files->get('install_package', null, 'raw');

        // Make sure that file uploads are enabled in php.
        if (!(bool) ini_get('file_uploads'))
        {
            throw new NotAllowed(Text::_('COM_TZ_PORTFOLIO_MSG_INSTALL_WARNINSTALLFILE'));
        }

        // Make sure that zlib is loaded so that the package can be unpacked.
        if (!extension_loaded('zlib'))
        {
            throw new NotAllowed(Text::_('COM_TZ_PORTFOLIO_MSG_INSTALL_WARNINSTALLZLIB'));
        }

        // If there is no uploaded file, we have a problem...
        if (!is_array($userfile))
        {
            throw new NotAllowed(Text::_('COM_TZ_PORTFOLIO_MSG_INSTALL_NO_FILE_SELECTED'));
        }

        // Is the PHP tmp directory missing?
        if ($userfile['error'] && ($userfile['error'] == UPLOAD_ERR_NO_TMP_DIR))
        {
            throw new NotAllowed(Text::_('COM_TZ_PORTFOLIO_MSG_INSTALL_WARNINSTALLUPLOADERROR') . '<br />' . Text::_('COM_TZ_PORTFOLIO_MSG_WARNINGS_PHPUPLOADNOTSET'));
        }

        // Is the max upload size too small in php.ini?
        if ($userfile['error'] && ($userfile['error'] == UPLOAD_ERR_INI_SIZE))
        {
            throw new NotAllowed(Text::_('COM_TZ_PORTFOLIO_MSG_INSTALL_WARNINSTALLUPLOADERROR') . '<br />' . Text::_('COM_TZ_PORTFOLIO_MSG_WARNINGS_SMALLUPLOADSIZE'));
        }

        // Check if there was a different problem uploading the file.
        if ($userfile['error'] || $userfile['size'] < 1)
        {
            throw new NotAllowed(Text::_('COM_TZ_PORTFOLIO_MSG_INSTALL_WARNINSTALLUPLOADERROR'));
        }

        // Build the appropriate paths.
        $tmp_dest	= JPATH_ROOT . '/tmp/tz_portfolio_install/' . $userfile['name'];
        $tmp_src	= $userfile['tmp_name'];

        if(!file_exists(JPATH_ROOT . '/tmp/tz_portfolio_install/index.html')){
            File::write(JPATH_ROOT . '/tmp/tz_portfolio_install/index.html',
                htmlspecialchars_decode('<!DOCTYPE html><title></title>'));
        }

        // Move uploaded file.
        File::upload($tmp_src, $tmp_dest, false, true);

        // Unpack the downloaded package file.
        $package = InstallerHelper::unpack($tmp_dest, true);

        return $package;
    }
}
