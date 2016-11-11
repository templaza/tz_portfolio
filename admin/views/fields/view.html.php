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
JHtml::addIncludePath(COM_TZ_PORTFOLIO_PLUS_ADMIN_HELPERS_PATH.DIRECTORY_SEPARATOR.'html');

class TZ_Portfolio_PlusViewFields extends JViewLegacy
{
    protected $items        = null;
    protected $state        = null;
    protected $pagination   = null;
    protected $sidebar      = null;

    public function display($tpl = null){

        $this -> items      = $this -> get('Items');
        $this -> state      = $this -> get('State');
        $this -> pagination = $this -> get('Pagination');

        TZ_Portfolio_PlusHelper::addSubmenu('fields');

        $this -> addToolbar();

        $this -> sidebar    = JHtmlSidebar::render();
        parent::display($tpl);
    }

    protected function addToolbar(){
        $doc    = JFactory::getDocument();
        $bar    = JToolBar::getInstance();

        JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_PLUS_FIELDS_MANAGER'),'file-2');
        JToolBarHelper::addNew('field.add');
        JToolBarHelper::editList('field.edit');
        JToolBarHelper::divider();
        JToolBarHelper::publish('fields.publish', 'JTOOLBAR_PUBLISH', true);
        JToolBarHelper::unpublish('fields.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        JToolBarHelper::deleteList(JText::_('COM_TZ_PORTFOLIO_PLUS_QUESTION_DELETE'),'fields.delete');
        JToolBarHelper::divider();
        JToolBarHelper::preferences('com_tz_portfolio_plus');
        JToolBarHelper::divider();


        $doc -> addStyleSheet(JURI::base(true).'/components/com_tz_portfolio_plus/fonts/font-awesome-4.5.0/css/font-awesome.min.css');

        $doc -> addStyleSheet(JURI::base(true).'/components/com_tz_portfolio_plus/css/style.min.css');

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,'http://wiki.templaza.com/TZ_Portfolio_Plus_v3:Administration#Fields');

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

        JHtmlSidebar::addFilter(
            JText::_('COM_TZ_PORTFOLIO_PLUS_OPTION_SELECT_TYPE'),
            'filter_type',
            JHtml::_('fields.options',null,'value','text',$this->state->get('filter.type'))
        );

        $groupModel = JModelLegacy::getInstance('Groups','TZ_Portfolio_PlusModel');
        $groups     = $groupModel -> getItemsArray();

        JHtmlSidebar::addFilter(
            JText::_('COM_TZ_PORTFOLIO_PLUS_OPTION_SELECT_GROUP'),
            'filter_group',
            JHtml::_('select.options',$groups,'value','text',$this->state->get('filter.group'))
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
        return array('f.ordering' => JText::_('JGRID_HEADING_ORDERING'),
            'a.state' => JText::_('JSTATUS'),
            'f.title' => JText::_('COM_TZ_PORTFOLIO_PLUS_HEADING_TITLE'),
            'groupname' => JText::_('COM_TZ_PORTFOLIO_PLUS_GROUP'),
            'f.type' => JText::_('COM_TZ_PORTFOLIO_PLUS_TYPE'),
            'f.published' => JText::_('JSTATUS'),
            'f.id' => JText::_('JGRID_HEADING_ID'));
    }
}