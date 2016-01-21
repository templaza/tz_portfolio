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

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.components.view');

class TZ_Portfolio_PlusViewGroups extends JViewLegacy
{
    protected $items        = null;
    protected $pagination   = null;
    protected $state        = null;

    function display($tpl = null){

        $this -> items      = $this -> get('Items');
        $this -> pagination = $this -> get('Pagination');
        $this -> state      = $this -> get('State');

        TZ_Portfolio_PlusHelper::addSubmenu('groups');

        $this -> addToolbar();

        $this -> sidebar    = JHtmlSidebar::render();

        parent::display($tpl);
    }

    protected function addToolbar(){
        $doc    = JFactory::getDocument();
        $bar    = JToolBar::getInstance();

        JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_PLUS_GROUP_FIELDS_MANAGER'), 'folder-3');
        JToolBarHelper::addNew('group.add');
        JToolBarHelper::editList('group.edit');
        JToolBarHelper::publish('groups.publish', 'JTOOLBAR_PUBLISH', true);
        JToolBarHelper::unpublish('groups.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        JToolBarHelper::deleteList(JText::_('COM_TZ_PORTFOLIO_PLUS_QUESTION_DELETE'),'groups.delete');
        JToolBarHelper::divider();
        JToolBarHelper::preferences('com_tz_portfolio_plus');
        JToolBarHelper::divider();


        // If the joomla is version 3.0
        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_COMPARE){
            $doc -> addStyleSheet(JURI::base(true).'/components/com_tz_portfolio_plus/fonts/font-awesome-4.5.0/css/font-awesome.min.css');
        }

        $doc -> addStyleSheet(JURI::base(true).'/components/com_tz_portfolio_plus/css/style.min.css');

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,'http://wiki.templaza.com/TZ_Portfolio_Plus_v3:Administration#Group_Fields');

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
            'filter_published',
            JHtml::_('select.options',JHtml::_('jgrid.publishedOptions', array('archived' => false, 'trash' => false))
                ,'value','text', $this->state->get('filter.published'), true)
        );
    }
}