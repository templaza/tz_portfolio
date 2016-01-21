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
jimport('joomla.application.component.view');
JHtml::addIncludePath(COM_TZ_PORTFOLIO_PLUS_ADMIN_HELPERS_PATH.DIRECTORY_SEPARATOR.'html');
JLoader::import('templates', COM_TZ_PORTFOLIO_PLUS_ADMIN_HELPERS_PATH);

class TZ_Portfolio_PlusViewTemplate_Styles extends JViewLegacy
{
    protected $state;
    protected $items;
    protected $sidebar;
    protected $pagination;

    public function display($tpl=null){

        $this->items		= $this->get('Items');
        $this->state		= $this->get('State');
        $this->pagination	= $this->get('pagination');

        JFactory::getLanguage() -> load('com_templates');

        TZ_Portfolio_PlusHelper::addSubmenu('template_styles');
        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal') {
            $this -> addToolbar();
        }

        $this -> sidebar    = JHtmlSidebar::render();

        parent::display($tpl);
    }

    protected function addToolbar(){

        $canDo	= JHelperContent::getActions('com_tz_portfolio_plus');

        $bar    = JToolBar::getInstance();

        JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATE_STYLES_MANAGER'), 'palette');
        if ($canDo->get('core.edit.state'))
        {
            JToolbarHelper::makeDefault('template_styles.setDefault', 'COM_TEMPLATES_TOOLBAR_SET_HOME');
            JToolbarHelper::divider();
        }

        JToolBarHelper::editList('template_style.edit');
        JToolBarHelper::divider();

        if ($canDo->get('core.create'))
        {
            JToolbarHelper::custom('template_styles.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
        }

        if ($canDo->get('core.delete')){
            JToolBarHelper::deleteList(JText::_('COM_TZ_PORTFOLIO_PLUS_QUESTION_DELETE'),'template_styles.delete');
            JToolBarHelper::divider();
        }

        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_tz_portfolio_plus');
            JToolBarHelper::divider();
        }

        $doc    = JFactory::getDocument();
        // If the joomla is version 3.0
        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_COMPARE){
            $doc -> addStyleSheet(JURI::base(true).'/components/com_tz_portfolio_plus/fonts/font-awesome-4.5.0/css/font-awesome.min.css');
        }

        $doc -> addStyleSheet(JURI::base(true).'/components/com_tz_portfolio_plus/css/style.min.css');

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,'http://wiki.templaza.com/TZ_Portfolio_Plus_v3:Administration#Tags');

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

        JHtmlSidebar::addFilter(
            JText::_('COM_TZ_PORTFOLIO_PLUS_OPTION_SELECT_TEMPLATE'),
            'filter_template',
            JHtml::_('select.options', TZ_Portfolio_PlusHelperTemplates::getTemplateOptions(), 'value', 'text', $this->state->get('filter.template'))
        );
    }
}