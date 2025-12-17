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

namespace TemPlaza\Component\TZ_Portfolio\AddOn\User\Profile\Extension;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\WebAsset\WebAssetManager;
use Joomla\Filesystem\Path;
use Joomla\Registry\Registry;
use Psr\Container\ContainerInterface;
use TemPlaza\Component\TZ_Portfolio\AddOn\Content\Vote\Helper\VoteHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\AddOn\AddOn;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioUri;

defined('_JEXEC') or die;

/**
 * Field Vote Add-On
 */
class Profile extends AddOn
{
    protected $autoloadLanguage = true;

    public function onContentPrepareForm($form, $data){
        $app    = Factory::getApplication();
        $name   = $form->getName();

        if($app -> isClient('administrator')){
            if($name == 'com_users.user' || $name == 'com_admin.profile') {
                $file   = Path::clean($this -> getAddOnPath().'/forms/profile.xml');
                if(file_exists($file)){
                    $form -> loadFile($file, false);
                }
            }
        }else{
            if($name == 'com_users.profile') {
                $file   = Path::clean($this -> getAddOnPath().'/forms/profile.xml');
                if(file_exists($file)){
                    $form -> loadFile($file, false);
                }
            }
        }
        return parent::onContentPrepareForm($form, $data);
    }


    public function onAfterDisplayAdditionInfo($context, &$article, $params, $page = 0, $layout = 'default'){}
    public function onContentDisplayListView($context, &$article, $params, $page = 0, $layout = 'default'){}
    public function onContentDisplayArticleView($context, &$article, $params, $page = 0, $layout = 'default'){}
    public function onBeforeDisplayAdditionInfo($context, &$article, $params, $page = 0, $layout = 'default'){}
    public function onContentAfterSave($context, $data, $isnew){}

    /** Display author about for listing or article view.
     * @param string $context
     * @param int $authorId The id of user to get information of user
     * @param string $params the params of listing or article view.
     * @param string $page
     * @param string $layout the layout of add-on similar listing or article view.
     **/
    public function onContentDisplayAuthorAbout($context, $authorId, $params, &$article = null, $page = 0, $layout = 'default'){

        list($extension, $vName)   = explode('.', $context);

        if($extension == 'module' || $extension == 'modules'){
            if($path = $this -> getModuleLayout($this -> _type, $this -> _name, $extension, $vName, $layout, $params)){
                // Display html
                ob_start();
                include $path;
                $html = ob_get_contents();
                ob_end_clean();
                $html = trim($html);
                return $html;
            }
        }else {
//            tzportfolioplusimport('plugin.modelitem');

            $addon      = AddonHelper::getAddOn($this -> _type, $this -> _name);

            if($controller = AddonHelper::getAddonController($addon -> id, array(
                'article' => $article,
                'authorId' => $authorId,
                'trigger_params' => $params
            ))){
                $input      = Factory::getApplication()->input;
                $task   = $input->get('addon_task');
                $input->set('addon_view', $vName);
                $input->set('addon_layout', 'default');
                if($layout) {
                    $input->set('addon_layout', $layout);
                }

                $html   = null;
                try {
                    ob_start();
                    $controller->execute($task);
                    $controller->redirect();
                    $html = ob_get_contents();
                    ob_end_clean();
                }catch (\Exception $e){
                    return false;
                }

                if($html){
                    $html   = trim($html);
                }
                $input -> set('addon_task', null);
                return $html;

            }
        }
    }
}