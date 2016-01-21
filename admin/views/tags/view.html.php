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

class TZ_Portfolio_PlusViewTags extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;

    public function display($tpl=null){

        $this -> state  = $this -> get('State');
        $this -> items  = $this -> get('Items');
        $this -> pagination  = $this -> get('Pagination');

        TZ_Portfolio_PlusHelper::addSubmenu('tags');
        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal') {
            $this -> addToolbar();
        }

        $this -> sidebar    = JHtmlSidebar::render();

        parent::display($tpl);

    }

    protected function addToolbar(){
        $bar    = JToolBar::getInstance();

        JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_PLUS_TAGS_MANAGER'),'tags');
        JToolBarHelper::addNew('tag.add');
        JToolBarHelper::editList('tag.edit');
        JToolBarHelper::divider();
        JToolBarHelper::publish('tags.publish', 'JTOOLBAR_PUBLISH', true);
        JToolBarHelper::unpublish('tags.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        JToolBarHelper::deleteList(JText::_('COM_TZ_PORTFOLIO_PLUS_QUESTION_DELETE'),'tags.delete');
        JToolBarHelper::divider();
        JToolBarHelper::preferences('com_tz_portfolio_plus');
        JToolBarHelper::divider();

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

        $state = array( 'P' => JText::_('JPUBLISHED'), 'U' => JText::_('JUNPUBLISHED'));
        JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
            'filter_state',
            JHtml::_('select.options',$state,'value','text',$this -> state -> filter_state)
        );

    }

    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     *
     * @since   3.0
     */
    protected function getSortFields()
    {
        return array(
            'published' => JText::_('JSTATUS'),
            'name' => JText::_('COM_TZ_PORTFOLIO_PLUS_NAME'),
            'id' => JText::_('JGRID_HEADING_ID')
        );
    }
}