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

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.application.component.modeladmin');
JLoader::import('addon', COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.DIRECTORY_SEPARATOR.'models');

class TZ_Portfolio_PlusModelTemplate extends TZ_Portfolio_PlusModelAddon
{
    protected $type         = 'tz_portfolio_plus-template';
    protected $folder       = 'templates';

    protected function populateState(){
        parent::populateState();

        $this -> setState('template.id',JFactory::getApplication() -> input -> getInt('id'));
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
        $user   = JFactory::getUser();
        $app    = JFactory::getApplication();
        $view   = $app -> input -> getCmd('view');

        if ($user->authorise('core.delete', 'com_tz_portfolio_plus'))
        {
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
            $row = $this -> getTable();

            $template_table     = $this -> getTable('Templates');
            $template_default   = $template_table -> getHome();
            $template_style     = JModelAdmin::getInstance('Template_Style','TZ_Portfolio_PlusModel',array('ignore_request' => true));

            // Uninstall the chosen extensions
            $msgs = array();
            $result = false;

            foreach ($eid as $id)
            {
                $id = trim($id);
                $row->load($id);

                $langstring = 'COM_TZ_PORTFOLIO_PLUS_' . strtoupper($row->type);
                $rowtype = JText::_($langstring);

                if (strpos($rowtype, $langstring) !== false)
                {
                    $rowtype = $row->type;
                }

                if ($row->type && $row->type == 'tz_portfolio_plus-template')
                {

                    // Is the template we are trying to uninstall a core one?
                    // Because that is not a good idea...
                    if ($row->protected)
                    {
                        JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_TPL_UNINSTALL_WARNCORETEMPLATE', JText::_('COM_TZ_PORTFOLIO_PLUS_'.$view)), JLog::WARNING, 'jerror');

                        return false;
                    }

                    if($template_default -> template == $row -> element){
                        $msg    = JText::_('JLIB_INSTALLER_ERROR_TPL_UNINSTALL_TEMPLATE_DEFAULT');
                        $app->enqueueMessage($msg,'warning');
                        return false;
                    }

                    $tpl_path   = COM_TZ_PORTFOLIO_PLUS_PATH_SITE.DIRECTORY_SEPARATOR.'templates'
                        .DIRECTORY_SEPARATOR.$row -> element;

                    if(JFolder::exists($tpl_path)){
                        if(!$template_style -> deleteTemplate($row -> name)){
                            $app -> enqueueMessage($template_style -> getError(),'warning');
                            return false;
                        }
                        if(JFolder::delete($tpl_path)){
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
            }

            $msg = implode("<br />", $msgs);
            $app->enqueueMessage($msg);

            return $result;
        }
        else
        {
            JError::raiseWarning(403, JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
        }
    }

    public function publish(&$pks, $value = 1)
    {
        $table  = $this -> getTable();
        $app    = JFactory::getApplication();
        $result = false;
        if(is_array($pks)){
            foreach($pks as $i => $pk){
                if($table -> load($pk)){
                    if((int)$table ->protected){
                        unset($pks[$i]);
                        $app -> enqueueMessage(JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATES_ERROR_DISABLE_DEFAULT_TEMPLATE_NOT_PERMITTED'),'notice');
                    }else{
                        $result = true;
                    }
                }
            }

        }else{

        }

        if($result) {
            return parent::publish($pks, $value); // TODO: Change the autogenerated stub
        }
        return $result;
    }
}