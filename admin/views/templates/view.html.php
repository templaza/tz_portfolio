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
JHtml::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/helpers');

class TZ_Portfolio_PlusViewTemplates extends JViewLegacy
{
    protected $state;
    protected $items;
    protected $templates;
    protected $form;
    protected $sidebar;
    protected $pagination;

    public function display($tpl=null){
        if($this -> getLayout() == 'upload') {
            $this->form = $this->get('Form');
        }
        $this->state        = $this->get('State');
        $this->items        = $this->get('Items');
        $this -> templates  = $this -> get('Templates');
        $this->pagination   = $this->get('pagination');

        JFactory::getLanguage() -> load('com_templates');

        TZ_Portfolio_PlusHelper::addSubmenu('templates');

        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal' && $this->getLayout() !== 'upload') {
            $this -> addToolbar();
        }

        $this -> sidebar    = JHtmlSidebar::render();

        parent::display($tpl);
    }

    protected function addToolbar(){

        $canDo	= JHelperContent::getActions('com_tz_portfolio_plus');

        $bar    = JToolBar::getInstance();

        JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATES_MANAGER'),'eye');
        JToolbarHelper::addNew('template.upload','JTOOLBAR_UPLOAD');
        JToolBarHelper::divider();

        if ($canDo->get('core.delete')){
            JToolBarHelper::deleteList(JText::_('COM_TZ_PORTFOLIO_PLUS_QUESTION_DELETE'),'template.uninstall','JTOOLBAR_UNINSTALL');
            JToolBarHelper::divider();
        }

        if ($canDo->get('core.edit.state')) {
            JToolBarHelper::publish('templates.publish','JENABLED', true);
        }

        if ($canDo->get('core.edit.state')) {
            JToolBarHelper::unpublish('templates.unpublish','JDISABLED', true);
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
            JText::_('JOPTION_SELECT_PUBLISHED'),
            'filter_status',
            JHtml::_(
                'select.options',
                array('0' => 'JDISABLED', '1' => 'JENABLED', '2' => 'JPROTECTED', '3' => 'JUNPROTECTED'),
                'value',
                'text',
                $this->state->get('filter.status'),
                true
            )
        );

    }
}