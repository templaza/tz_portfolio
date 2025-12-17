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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\View\Layout;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Button\DropdownButton;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Uri\Uri;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\AddonDatasHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\ToolbarHelper as TZ_PortfolioToolbarHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;

/**
 * Categories view class for the Category package.
 */
class HtmlView extends BaseHtmlView
{
    protected $state        = null;
    protected $item         = null;
    protected $form         = null;
    protected $presets      = null;
    protected $rowItem      = null;
    protected $rowOuter     = null;
    protected $tzlayout     = null;
    protected $childrens    = null;
    protected $columnItem   = null;
    protected $includeTypes = null;

    public function display($tpl=null)
    {
        Factory::getApplication() -> getLanguage() -> load('com_templates');
        $document   = Factory::getApplication() -> getDocument();
//        $this -> document -> addCustomTag('<link rel="stylesheet" href="'.Uri::base(true).'/components/com_tz_portfolio/css/admin-layout.min.css" type="text/css"/>');
//        $this -> document -> addCustomTag('<link rel="stylesheet" href="'.Uri::base(true).'/components/com_tz_portfolio/css/spectrum.min.css" type="text/css"/>');

        $this -> state      = $this -> get('State');
        $this -> item       = $this -> get('Item');
        $this -> tzlayout   = $this -> get('TZLayout');
        $this -> form       = $this -> get('Form');
        $this -> presets    = $this -> get('Presets');

        if($includeTypes = AddonHelper::getContentTypes()) {
            $this->includeTypes = $includeTypes;
        }

        $this -> addToolbar();

        parent::display($tpl);

        /** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
        $wa = $this->document->getWebAssetManager();
        $wa -> useScript('com_tz_portfolio.jquery-ui')
            -> useScript('minicolors')
            -> useScript('bootstrap.popover')
            -> useScript('com_tz_portfolio.admin-layout');

        $wa -> useStyle('minicolors');

//        $this -> document -> addScript(Uri::base(true).'/components/com_tz_portfolio/js/libs.min.js', array('version' => 'auto'));
//        $this -> document -> addScript(Uri::base(true).'/components/com_tz_portfolio/js/jquery-ui.min.js', array('version' => 'v=1.11.4'));
//        $this -> document -> addScript(Uri::base(true).'/components/com_tz_portfolio/js/layout-admin.min.js', array('version' => 'auto'));
//        $this -> document -> addScript(Uri::base(true).'/components/com_tz_portfolio/js/spectrum.min.js', array('version' => 'auto'));
        $wa -> addInlineScript('
        (function($){
            $(document).ready(function(){
                $.tpLayoutAdmin({
                    basePath    : "'.Uri::base().'",
                    pluginPath  : "'.Uri::root(true).'/administrator/components/com_tz_portfolio/tmpl/layout",
                    fieldName   : "jform[attrib]",
                    j4Compare   : '.(version_compare(JVERSION,'4.0', '>=')?'true':'false').',
                    token       : "'.Session::getFormToken().'"
                    
                });
            })
            Joomla.submitbutton = function(task) {
                if (task == \'style.cancel\' || document.formvalidator.isValid(document.getElementById(\'layout-form\'))) {
                    jQuery.tpLayoutAdmin.tzTemplateSubmit();
                    Joomla.submitform(task, document.getElementById(\'layout-form\'));
                }else {
                    alert("'.$this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')).'");
                }
            };
        })(jQuery)');
    }

    protected function addToolbar(){

        Factory::getApplication()->input->set('hidemainmenu', true);

        $user   = Factory::getUser();
        $isNew  = ($this -> item -> id == 0);
        $canDo  = ContentHelper::getActions('com_tz_portfolio');

        ToolBarHelper::title(Text::sprintf('COM_TZ_PORTFOLIO_TEMPLATES_MANAGER_TASK',
            Text::_(($isNew)?'COM_TZ_PORTFOLIO_PAGE_ADD_TEMPLATE':'COM_TZ_PORTFOLIO_PAGE_EDIT_TEMPLATE')), 'palette');

        if ($canDo->get('core.edit')) {
            ToolbarHelper::apply($this -> getName().'.apply');
            ToolbarHelper::save($this -> getName().'.save');
        }

        // If checked out, we can still save
        if (!$isNew && $user->authorise('core.edit.state', 'com_tz_portfolio')) {
            ToolbarHelper::save2copy($this -> getName().'.save2copy');
        }

        ToolbarHelper::cancel($this -> getName().'.cancel',Text::_('JTOOLBAR_CLOSE'));

        ToolbarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,
            'https://www.tzportfolio.com/document/administration/32-how-to-use-template-styles-in-tz-portfolio-plus.html?tmpl=component');

        TZ_PortfolioToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_VIDEO_TUTORIALS', 'youtube', 'youtube');
    }

    protected function get_value($item, $method){
        if (!isset($item -> $method)) {
            if (preg_match('/offset/', $method)) {
                return isset($item -> offset) ? $item -> offset : '';
            }
            if (preg_match('/col/', $method)) {
                return isset($item -> span) ? $item -> span : '1-1';
            }
        }
        return isset($item -> $method) ? $item -> $method : '';
    }

    protected function get_color($item, $method){
        return isset($item -> $method) ? $item -> $method : 'rgba(255, 255, 255, 0)';
    }
}
