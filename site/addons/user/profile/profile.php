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

// no direct access
defined('_JEXEC') or die('Restricted access');

class PlgTZ_Portfolio_PlusUserProfile extends TZ_Portfolio_PlusPlugin
{
    protected $autoloadLanguage = true;

    public function onContentPrepareForm($form, $data){
        $app    = JFactory::getApplication();
        $name   = $form->getName();

        if($app -> isAdmin()){
            if($name == 'com_users.user' || $name == 'com_admin.profile') {
                JForm::addFieldPath(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.DIRECTORY_SEPARATOR
                    .'models'.DIRECTORY_SEPARATOR.'fields');
                JForm::addFormPath(__DIR__.'/forms');
                $form->loadFile('profile', false);
            }
        }else{
            if($name == 'com_users.profile') {
                JForm::addFieldPath(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.DIRECTORY_SEPARATOR
                    .'models'.DIRECTORY_SEPARATOR.'fields');
                JForm::addFormPath(__DIR__.'/forms');
                $form->loadFile('profile', false);
            }
        }
        return true;
    }
}