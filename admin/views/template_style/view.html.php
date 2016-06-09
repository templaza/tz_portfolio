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


class TZ_Portfolio_PlusViewTemplate_Style extends JViewLegacy
{
    protected $item         = null;
    protected $tzlayout     = null;
    protected $form         = null;
    protected $childrens    = null;
    protected $includeTypes = null;
    protected $presets      = null;

    public function display($tpl=null)
    {
        JFactory::getLanguage() -> load('com_templates');
        $document   = JFactory::getDocument();
        $this -> document -> addCustomTag('<link rel="stylesheet" href="'.JUri::base(true).'/components/com_tz_portfolio_plus/css/admin-layout.min.css" type="text/css"/>');
        $this -> document -> addCustomTag('<link rel="stylesheet" href="'.JUri::base(true).'/components/com_tz_portfolio_plus/css/spectrum.min.css" type="text/css"/>');

        $this -> item       = $this -> get('Item');
        $this -> tzlayout   = $this -> get('TZLayout');
        $this -> form       = $this -> get('Form');
        $this -> presets    = $this -> get('Presets');

        if($includeTypes = TZ_Portfolio_PlusPluginHelper::getContentTypes()) {
            $this->includeTypes = $includeTypes;
        }

        $this -> addToolbar();

        parent::display($tpl);

        $this -> document -> addScript(JUri::base(true).'/components/com_tz_portfolio_plus/js/libs.min.js');
        $this -> document -> addScript(JUri::base(true).'/components/com_tz_portfolio_plus/js/jquery-ui.min.js');
        $this -> document -> addScript(JUri::base(true).'/components/com_tz_portfolio_plus/js/layout-admin.min.js');
        $this -> document -> addScript(JUri::base(true).'/components/com_tz_portfolio_plus/js/spectrum.min.js');
        $this -> document -> addScriptDeclaration('
        jQuery(document).ready(function(){
            jQuery.tzLayoutAdmin({
                pluginPath  : "'.JURI::root(true).'/administrator/components/com_tz_portfolio_plus/views/template_style/tmpl",
                fieldName   : "jform[attrib]"
            });
        })
        Joomla.submitbutton = function(task) {
            if (task == \'template.cancel\' || document.formvalidator.isValid(document.id(\'template-form\'))) {
                jQuery.tzLayoutAdmin.tzTemplateSubmit();
                Joomla.submitform(task, document.getElementById(\'template-form\'));
            }else {
                alert("'.$this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')).'");
            }
        };');
    }

    protected function addToolbar(){
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $doc    = JFactory::getDocument();
        $bar    = JToolBar::getInstance();
        $user	= JFactory::getUser();

        $canDo = JHelperContent::getActions('com_tz_portfolio_plus');

        $isNew  = ($this -> item -> id == 0);

        JToolBarHelper::title(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_TEMPLATES_MANAGER_TASK',
            JText::_(($isNew)?'COM_TZ_PORTFOLIO_PLUS_PAGE_ADD_TEMPLATE':'COM_TZ_PORTFOLIO_PLUS_PAGE_EDIT_TEMPLATE')), 'palette');

        if ($canDo->get('core.edit')) {
            JToolBarHelper::apply('template_style.apply');
            JToolBarHelper::save('template_style.save');
        }

        // If checked out, we can still save
        if (!$isNew && $user->authorise('core.edit.state', 'com_tz_portfolio_plus')) {
            JToolBarHelper::save2copy('template_style.save2copy');
        }

        JToolBarHelper::cancel('template_style.cancel',JText::_('JTOOLBAR_CLOSE'));

        JToolBarHelper::divider();

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,'http://wiki.templaza.com/TZ_Portfolio_Plus_v3:Administration#How_to_Add_or_Edit_3');

        $doc -> addStyleSheet(JURI::base(true).'/components/com_tz_portfolio_plus/fonts/font-awesome-4.5.0/css/font-awesome.min.css');

        $doc -> addStyleSheet(JURI::base(true).'/components/com_tz_portfolio_plus/css/style.min.css');

        // Special HTML workaround to get send popup working
        $docClass       = ' class="btn btn-small"';
        $youtubeIcon    = '<i class="tz-icon-youtube tz-icon-14"></i>&nbsp;';
        $wikiIcon       = '<i class="tz-icon-wikipedia tz-icon-14"></i>&nbsp;';

        $youtubeTitle   = JText::_('COM_TZ_PORTFOLIO_PLUS_VIDEO_TUTORIALS');
        $wikiTitle      = JText::_('COM_TZ_PORTFOLIO_PLUS_WIKIPEDIA_TUTORIALS');

        $videoTutorial    ='<a'.$docClass.' onclick="Joomla.popupWindow(\'http://www.youtube.com/channel/UCykS6SX6L2GOI-n3IOPfTVQ/videos\', \''
            .$youtubeTitle.'\', 800, 500, 1)"'.' href="#">'
            .$youtubeIcon.$youtubeTitle.'</a>';

        $wikiTutorial    ='<a'.$docClass.' onclick="Joomla.popupWindow(\'http://wiki.templaza.com/Main_Page\', \''
            .$wikiTitle.'\', 800, 500, 1)"'.' href="#">'
            .$wikiIcon
            .$wikiTitle.'</a>';

        $bar->appendButton('Custom',$videoTutorial,'youtube');
        $bar->appendButton('Custom',$wikiTutorial,'wikipedia');
    }

    protected function get_value($item, $method){
        if (!isset($item -> $method)) {
            if (preg_match('/offset/', $method)) {
                return isset($item -> offset) ? $item -> offset : '';
            }
            if (preg_match('/col/', $method)) {
                return isset($item -> span) ? $item -> span : '12';
            }
        }
        return isset($item -> $method) ? $item -> $method : '';
    }

    protected function get_color($item, $method){
        return isset($item -> $method) ? $item -> $method : 'rgba(255, 255, 255, 0)';
    }
}