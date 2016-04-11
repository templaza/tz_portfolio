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

JLoader::register('TZ_Portfolio_PlusHelperAddon_Datas', COM_TZ_PORTFOLIO_PLUS_ADMIN_HELPERS_PATH
    .DIRECTORY_SEPARATOR.'addon_datas.php');
JLoader::import('com_tz_portfolio_plus.helpers.addons', JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components');

class TZ_Portfolio_PlusViewAddons extends JViewLegacy
{
    protected $state;
    protected $items;
    protected $templates;
    protected $form;
    protected $sidebar;
    protected $pagination;

    public function display($tpl=null){

        $this->state        = $this->get('State');
        $this->items        = $this->get('Items');
        $this->pagination   = $this->get('pagination');

        TZ_Portfolio_PlusHelper::addSubmenu($this -> getName());

        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal' && $this->getLayout() !== 'upload') {
            $this -> addToolbar();
        }

        $this -> sidebar    = JHtmlSidebar::render();

        parent::display($tpl);
    }

    protected function addToolbar(){

        $canDo	= JHelperContent::getActions('com_tz_portfolio_plus');

        $user		= JFactory::getUser();

        $bar    = JToolBar::getInstance();

        JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_PLUS_ADDONS_MANAGER'), 'puzzle');

        JToolbarHelper::addNew('addon.upload','JTOOLBAR_UPLOAD');
        JToolBarHelper::divider();

        if ($canDo->get('core.delete')){
            JToolBarHelper::deleteList(JText::_('COM_TZ_PORTFOLIO_PLUS_QUESTION_DELETE'),'addon.uninstall','JTOOLBAR_UNINSTALL');
            JToolBarHelper::divider();
        }

        if ($canDo->get('core.edit.state')) {
            JToolBarHelper::publish('addons.publish','JENABLED', true);
        }

        if ($canDo->get('core.edit.state')) {
            JToolBarHelper::unpublish('addons.unpublish','JDISABLED', true);
        }

        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_tz_portfolio_plus');
            JToolBarHelper::divider();
        }

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

        $bar->appendButton('Custom',$wikiTutorial,'wikipedia');
        $bar->appendButton('Custom',$videoTutorial,'youtube');

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

        JHtmlSidebar::addFilter(
            JText::_('COM_TZ_PORTFOLIO_PLUS_OPTION_SELECT_TYPE'),
            'filter_folder',
            JHtml::_('select.options', TZ_Portfolio_PlusHelperAddons::folderOptions(), 'value', 'text', $this->state->get('filter.folder'))
        );
    }

    protected function getSortFields()
    {
        return array(
            'ordering' => JText::_('JGRID_HEADING_ORDERING'),
            'published' => JText::_('JSTATUS'),
            'name' => JText::_('JGLOBAL_TITLE'),
            'folder' => JText::_('COM_TZ_PORTFOLIO_PLUS_TYPE'),
            'element' => JText::_('COM_TZ_PORTFOLIO_PLUS_ELEMENT'),
            'id' => JText::_('JGRID_HEADING_ID')
        );
    }
}