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

namespace TemPlaza\Component\TZ_Portfolio\Plugin\System\TZ_Portfolio\Extension;

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\DispatcherInterface;
use Joomla\Filesystem\Path;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;

class TZ_Portfolio extends CMSPlugin {

    /**
     * Constructor
     *
     * @param   DispatcherInterface  &$subject  The object to observe
     * @param   array                $config    An optional associative array of configuration settings.
     *                                          Recognized key values include 'name', 'group', 'params', 'language'
     *                                         (this list is not meant to be comprehensive).
     */
    public function __construct($subject, $config = array())
    {
        if (file_exists(JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/includes/defines.php')) {
            require_once JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/includes/defines.php';
        }
        parent::__construct($subject,$config);
    }

    public function onAfterRoute(){
        $app    = Factory::getApplication();
        $option = $app -> input -> get('option');
        $task   = $app -> input -> get('task');
        if(class_exists('TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper')
            && $this -> _tppAllowImport()) {
            if(file_exists(COM_TZ_PORTFOLIO_ADDON_PATH)){
                $plgGroups  = Folder::folders(COM_TZ_PORTFOLIO_ADDON_PATH);
                if(count($plgGroups)){

                    foreach($plgGroups as $group){
                        if($group != 'extrafields') {
                            if($app ->isClient('administrator')  || ($app ->isClient('site')
                                    && ($group != 'user' ||($group == 'user'
                                            && $option == 'com_users' && $task != 'user.login'
                                            && $task != 'user.logout')))) {
                                AddonHelper::importPlugin($group);
                            }
                        }
                    }
                }
            }
        }

    }

    public function onContentPrepareForm($form, $data){
        if(version_compare(JVERSION, '3.10', '<')) {
            $form_name = $form->getName();
            list($options, $view) = explode('.', $form_name);
            $is_my_module  = false;
            if($form_name == 'com_modules.module'){
                $module     	= (!empty($data) && isset($data -> module) && !empty($data -> module))?$data -> module:false;
                $is_my_module  	= ($module && preg_match('/^mod_tz_portfolio/i', $module))?true:$is_my_module;
            }

            if ($options == 'com_tz_portfolio' || $is_my_module) {
                $fieldsets   = $form -> getFieldsets();
                if($fieldsets && count($fieldsets)){
                    foreach($fieldsets as $fsname => $fieldset){
                        $fields = $form -> getFieldset($fsname);
                        if($fields && count($fields)){
                            foreach($fields as &$field){
                                $f_type = strtolower($field -> __get('type'));
                                if($f_type != 'radio'){
                                    continue;
                                }
                                if($field -> __get('layout') == 'joomla.form.field.radio.switcher') {
                                    $form->setFieldAttribute($field->__get('fieldname'), 'layout', '', $field->__get('group'));
                                }

                            }
                        }
                    }
                }
            }
        }
    }

    protected function _tppAllowImport(){

        $app    = Factory::getApplication();
        $option = $app -> input -> get('option');
        $task   = $app -> input -> get('task');
        $view   = $app -> input -> get('view');

        if($app -> isClient('administrator')){
            $optionAllows   = array(
                'com_config',
                'com_login',
                'com_checkin',
                'com_cache',
                'com_admin',
                'com_installer',
                'com_plugins'
            );
            if(!$option || ($option && in_array($option, $optionAllows))){
                return false;
            }
            elseif($option == 'com_menus' && ($view == 'menus' || $view == 'items')){
                return false;
            }
            elseif($option == 'com_users' && (!in_array($view, array('user')) && !$task)){
                return false;
            }
        }

        return true;
    }
}
?>