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

jimport('joomla.application.component.view');

class TZ_Portfolio_PlusViewField extends JViewLegacy
{
    protected $form     = null;
    protected $item     = null;
    protected $groups   = null;

    public function display($tpl = null){
        $this -> form   = $this -> get('Form');
        $this -> item   = $this -> get('Item');

        $buttons_plugin = JPluginHelper::getPlugin('editors-xtd');

        if($buttons_plugin){

        }

        $groupModel = JModelLegacy::getInstance('Groups','TZ_Portfolio_PlusModel',array('ignore_request' => true));
        $groupModel -> setState('filter_order','name');
        $groupModel -> setState('filter_order_Dir','ASC');

        $this -> groups = $groupModel -> getItems();

        $editor = JFactory::getEditor();
        $this -> assign('editor',$editor);

        if($this -> item -> id == 0){
            $this -> item -> published = 'P';
        }
        else{
            if($this -> item -> published == 1){
                $this -> item -> published  = 'P';
            }
            else{
                $this -> item -> published  = 'U';
            }
        }

        $this -> addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar(){
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $doc    = JFactory::getDocument();
        $bar    = JToolBar::getInstance();

        $isNew  = ($this -> item -> id == 0);

        JToolBarHelper::title(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_FIELDS_MANAGER_TASK',
            JText::_(($isNew)?'COM_TZ_PORTFOLIO_PLUS_PAGE_ADD_FIELD':'COM_TZ_PORTFOLIO_PLUS_PAGE_EDIT_FIELD')),'file-plus');
        JToolBarHelper::apply('field.apply');
        JToolBarHelper::save('field.save');
        JToolBarHelper::save2new('field.save2new');
        JToolBarHelper::cancel('field.cancel',JText::_('JTOOLBAR_CLOSE'));

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
}