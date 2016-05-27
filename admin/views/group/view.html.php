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

class TZ_Portfolio_PlusViewGroup extends JViewLegacy
{
    protected $item = null;
    protected $form = null;

    function display($tpl = null){
        $this -> form   = $this -> get('Form');
        $this -> item   = $this -> get('Item');

        $editor = JFactory::getEditor();
        $this -> assign('editor',$editor);
        $this -> addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar(){
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $bar    = JToolBar::getInstance();
        $doc    = JFactory::getDocument();

        $isNew  = ($this -> item -> id == 0);

        JToolBarHelper::title(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_GROUP_FIELDS_MANAGER_TASK',
            JText::_(($isNew)?'COM_TZ_PORTFOLIO_PLUS_PAGE_ADD_GROUP_FIELD':'COM_TZ_PORTFOLIO_PLUS_PAGE_EDIT_GROUP_FIELD')),'folder-plus-2');
        JToolBarHelper::apply('group.apply');
        JToolBarHelper::save('group.save');
        JToolBarHelper::save2new('group.save2new');
        JToolBarHelper::cancel('group.cancel',JText::_('JTOOLBAR_CLOSE'));

        JToolBarHelper::divider();

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,'http://wiki.templaza.com/TZ_Portfolio_Plus_v3:Administration#How_to_Add_or_Edit');

        // If the joomla is version 3.0
        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_COMPARE){
            $doc -> addStyleSheet(JURI::base(true).'/components/com_tz_portfolio_plus/fonts/font-awesome-4.5.0/css/font-awesome.min.css');
        }

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
}