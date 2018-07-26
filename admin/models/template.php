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

//no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\Filesystem\Folder;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

jimport('joomla.filesytem.folder');
jimport('joomla.application.component.modeladmin');
JLoader::import('addon', COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.DIRECTORY_SEPARATOR.'models');

class TZ_Portfolio_PlusModelTemplate extends TZ_Portfolio_PlusModelAddon
{
    protected $type         = 'tz_portfolio_plus-template';
    protected $folder       = 'templates';

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
            $this->_db = TZ_Portfolio_PlusDatabase::getDbo();
        }
    }

    protected function populateState(){
        parent::populateState();

        $this -> setState('template.id',JFactory::getApplication() -> input -> getInt('id'));

        $this -> setState('cache.filename', 'template_list');
    }

    public function getTable($type = 'Extensions', $prefix = 'TZ_Portfolio_PlusTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    function getForm($data = array(), $loadData = true){
        $form = $this->loadForm('com_tz_portfolio_plus.template', 'template', array('control' => ''));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    public function afterSave($data){
        // Add template's information to table tz_portfolio_plus_templates
        $tpl_data   = null;
        if(!$this -> getTemplateStyle($data['element'])){
            $tpl_path       = COM_TZ_PORTFOLIO_PLUS_PATH_SITE.DIRECTORY_SEPARATOR.'templates';

            $lang   = JFactory::getLanguage();
            $tpl_data['title']      = $data['element'].' - '.JText::_('JDEFAULT');
            if($lang -> load('tpl_'.$data['element'],$tpl_path.DIRECTORY_SEPARATOR.$data['element'])){
                if($lang ->hasKey('TZ_PORTFOLIO_PLUS_TPL_'.$data['element'])){
                    $tpl_data['title']      = JText::_('TZ_PORTFOLIO_PLUS_TPL_'.$data['element']).' - '.JText::_('JDEFAULT');
                }
            }
            $tpl_data['id']         = 0;
            $tpl_data['template']   = $data['element'];
            $tpl_data['home']       = 0;
            $tpl_data['params']     = '';

            $model  = JModelAdmin::getInstance('Template_Style','TZ_Portfolio_PlusModel');
            if($model){
                $model -> save($tpl_data);
            }
        }
        return true;
    }

    function getTemplateStyle($template){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('*');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_templates'));
        $query -> where($db -> quoteName('template').'='.$db -> quote($template));
        $query -> group($db -> quoteName('template'));
        $db -> setQuery($query);
        if($data = $db -> loadObject()){
            return $data;
        }
        return false;
    }

    public function uninstall($eid = array())
    {
        $user   = TZ_Portfolio_PlusUser::getUser();
        $app    = JFactory::getApplication();
        $view   = $app -> input -> getCmd('view');

        if (!$user->authorise('core.delete', 'com_tz_portfolio_plus.template'))
        {
            \JLog::add(\JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), \JLog::WARNING, 'jerror');
            return false;
        }

        if (!is_array($eid))
        {
            $eid = array($eid => 0);
        }

        // Get an installer object for the extension type
        $table = $this -> getTable();

        $template_table     = $this -> getTable('Templates');
        $template_default   = $template_table -> getHome();
        $template_style     = JModelAdmin::getInstance('Template_Style','TZ_Portfolio_PlusModel',array('ignore_request' => true));

        // Uninstall the chosen extensions
        $msgs = array();
        $result = false;

        foreach ($eid as $i => $id)
        {
            $id = trim($id);
            if($table -> load($id)){
                $langstring = 'COM_TZ_PORTFOLIO_PLUS_' . strtoupper($table -> type);
                $rowtype = JText::_($langstring);

                if (strpos($rowtype, $langstring) !== false)
                {
                    $rowtype = $table -> type;
                }

                if ($table -> type && $table -> type == 'tz_portfolio_plus-template')
                {

                    // Is the template we are trying to uninstall a core one?
                    // Because that is not a good idea...
                    if ($table ->protected)
                    {
                        JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_TPL_UNINSTALL_WARNCORETEMPLATE',
                            JText::_('COM_TZ_PORTFOLIO_PLUS_'.$view)), JLog::WARNING, 'jerror');
                        return false;
                    }

                    if($template_default -> template == $table -> element){
                        $msg    = JText::_('JLIB_INSTALLER_ERROR_TPL_UNINSTALL_TEMPLATE_DEFAULT');
                        $app->enqueueMessage($msg,'warning');
                        return false;
                    }

                    $tpl_path   = COM_TZ_PORTFOLIO_PLUS_PATH_SITE.DIRECTORY_SEPARATOR.'templates'
                        .DIRECTORY_SEPARATOR.$table -> element;

                    if(\JFolder::exists($tpl_path)){
                        if(!$template_style -> deleteTemplate($table -> name)){
                            $app -> enqueueMessage($template_style -> getError(),'warning');
                            return false;
                        }
                        if(Folder::delete($tpl_path)){
                            $result = $this->delete($id);
                        }
                    }

                    // Build an array of extensions that failed to uninstall
                    if ($result === false)
                    {
                        // There was an error in uninstalling the package
                        $msgs[] = JText::sprintf('COM_TZ_PORTFOLIO_PLUS_UNINSTALL_ERROR', JText::_('COM_TZ_PORTFOLIO_PLUS_'.$view));
                        $result = false;
                    }
                    else
                    {
                        // Package uninstalled sucessfully
                        $msgs[] = JText::sprintf('COM_TZ_PORTFOLIO_PLUS_UNINSTALL_SUCCESS', JText::_('COM_TZ_PORTFOLIO_PLUS_'.$view));
                        $result = true;
                    }
                }
            }else
            {
                $this->setError($table->getError());

                return false;
            }
        }

        $msg = implode("<br />", $msgs);
        $app->enqueueMessage($msg);

        return $result;
    }

    protected function canDelete($record)
    {
        if (!empty($record->id))
        {
            $user = JFactory::getUser();

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
        $user = JFactory::getUser();

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

    protected function getUrlFromServer($xmlTag = 'templateurl'){
        return parent::getUrlFromServer($xmlTag);
    }

    protected function getManifest_Cache($element, $folder = null, $type = 'tz_portfolio_plus-template'){
        return parent::getManifest_Cache($element, $folder, $type);
    }
}