<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Technology Ltd (http://extstore.com). All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Dashboard view.
 *
 * @package		Joomla.Administrator
 * @subpakage	TZ.Portfolio
 */
class TZ_Portfolio_PlusViewDashboard extends JViewLegacy {
    /**
     * Display the view.
     */
    public function display($tpl = null) {
        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal') {
            $this->addToolbar();
        }

        TZ_Portfolio_PlusHelper::addSubmenu('dashboard');
        $this->sidebar = JHtmlSidebar::render();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since	1.6
     */
    protected function addToolbar()
    {
        $canDo	= TZ_Portfolio_PlusHelper::getActions();

        // Get the toolbar object instance
        $bar = JToolBar::getInstance('toolbar');

        JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_PLUS_DASHBOARD'));

        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_tz_portfolio_plus');
//			JToolBarHelper::preferences('com_content');
            JToolBarHelper::divider();
        }

        $doc    = JFactory::getDocument();
        // If the joomla is version 3.0
        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_COMPARE){
            $doc -> addStyleSheet(JURI::base(true).'/components/com_tz_portfolio_plus/fonts/font-awesome-4.5.0/css/font-awesome.min.css');
        }

        //$doc -> addStyleSheet('administrator/components/com_tz_portfolio_plus/css/style.min.css');

        JHtmlSidebar::setAction('index.php?option=com_tz_portfolio_plus&view=dashboard');



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

    /**
     * Display quick icon button.
     *
     * @param	string	$link
     * @param	string	$image
     * @param	string	$text
     */
    protected function _quickIcon($link, $image, $text) {
        $button	= array(
            'link'	=> JRoute::_($link),
            'image'	=> 'administrator/components/com_tz_portfolio_plus/assets/' . $image,
            'text'	=> JText::_($text)
        );

        $this->button	= $button;
        echo $this->loadTemplate('button');
    }
}