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
jimport('joomla.event.dispatcher');

JHtml::addIncludePath(COM_TZ_PORTFOLIO_PLUS_PATH_SITE . '/helpers');

tzportfolioplusimport('http_fetcher');

class TZ_Portfolio_PlusViewPortfolio extends JViewLegacy
{
    protected $item             = null;
    protected $items            = null;
    protected $media            = null;
    protected $ajaxLink         = null;
    protected $lang_sef         = '';
    protected $itemTags         = null;
    protected $itemCategories   = null;
    protected $params           = null;
    protected $pagination       = null;
    protected $Itemid           = null;
    protected $char             = null;
    protected $availLetter      = null;

    function __construct($config = array()){
        $this -> item           = new stdClass();
        parent::__construct($config);
    }

    function display($tpl=null){
        $app        = JFactory::getApplication('site');
        $input      = $app -> input;
        $config     = JFactory::getConfig();
        if($config -> get('sef')){
            $language   = JLanguageHelper::getLanguages('lang_code');
        }else{
            $language   = JLanguageHelper::getLanguages('sef');
        }

        JHtml::_('behavior.framework');
        $menus		= JMenu::getInstance('site');
        $active     = $menus->getActive();

        $doc            = JFactory::getDocument();

        $params         = null;
        $state          = $this -> get('State');
        $params         = $state -> get('params');

        // Create ajax link
        $this -> ajaxLink   = JURI::root().'index.php?option=com_tz_portfolio_plus&amp;view=portfolio&amp;task=portfolio.ajax'
            .'&amp;layout=default:item'.(($state -> get('filter.char'))?'&amp;char='.$state -> get('filter.char'):'')
            .($state -> get('filter.category_id')?'&amp;catid='.$state -> get('filter.category_id'):'');

        if($active) {
            $this->ajaxLink .= '&amp;Itemid=' . $active->id;
        }
        $this -> ajaxLink   .=  '&amp;page=2';

        $doc -> addStyleSheet('components/com_tz_portfolio_plus/css/isotope.min.css');
        $doc -> addScript('components/com_tz_portfolio_plus/js/jquery.isotope.min.js');
        $doc -> addScript('components/com_tz_portfolio_plus/js/html5.js');

        if($params -> get('tz_portfolio_plus_layout', 'ajaxButton') == 'ajaxButton'
            || $params -> get('tz_portfolio_plus_layout', 'ajaxButton') == 'ajaxInfiScroll'){
            $doc -> addScript('components/com_tz_portfolio_plus/js/jquery.infinitescroll.min.js');
            if($params -> get('tz_portfolio_plus_layout', 'ajaxButton') == 'ajaxButton'){
                $doc->addStyleDeclaration('
                    #infscr-loading {
                        position: absolute;
                        padding: 0;
                        left: 35%;
                        bottom:0;
                        background:none;
                    }
                    #infscr-loading div,#infscr-loading img{
                        display:inline-block;
                    }
                ');
            }
            if($params -> get('tz_portfolio_plus_layout', 'ajaxButton') == 'ajaxInfiScroll'){
                $doc->addStyleDeclaration('
                    #tz_append{
                        cursor: auto;
                    }
                    #tz_append a{
                        color:#000;
                        cursor:auto;
                    }
                    #tz_append a:hover{
                        color:#000 !important;
                    }
                    #infscr-loading {
                        position: absolute;
                        padding: 0;
                        left: 38%;
                        bottom:-35px;
                    }

                ');
            }
        }

        if($params -> get('tz_use_lightbox',0)){
            $doc -> addScript('components/com_tz_portfolio_plus/js/jquery.fancybox.pack.js');
            $doc -> addStyleSheet('components/com_tz_portfolio_plus/css/fancybox.min.css');

            $width      = null;
            $height     = null;
            $autosize   = null;
            if($params -> get('tz_lightbox_width')){
                if(preg_match('/%|px/',$params -> get('tz_lightbox_width'))){
                    $width  = 'width:"'.$params -> get('tz_lightbox_width').'",';
                }
                else
                    $width  = 'width:'.$params -> get('tz_lightbox_width').',';
            }
            if($params -> get('tz_lightbox_height')){
                if(preg_match('/%|px/',$params -> get('tz_lightbox_height'))){
                    $height  = 'height:"'.$params -> get('tz_lightbox_height').'",';
                }
                else
                    $height  = 'height:'.$params -> get('tz_lightbox_height').',';
            }
            if($width || $height){
                $autosize   = 'fitToView: false,autoSize: false,';
            }

            $doc -> addScriptDeclaration('
                jQuery(".fancybox").fancybox({
                    type:"iframe",
                    openSpeed:'.$params -> get('tz_lightbox_speed',350).',
                    openEffect: "'.$params -> get('tz_lightbox_transition','elastic').'",
                    '.$width.$height.$autosize.'
                    helpers:  {
                        title : {
                            type : "inside"
                        },
                        overlay : {
                            css : {background: "rgba(0,0,0,'.$params -> get('tz_lightbox_opacity',0.75).')"}
                        }
                    }
                });
            ');
        }

        $list   = $this -> get('Items');
        
        if($params -> get('show_all_filter',0)){
            if(!$this -> itemTags) {
                $this->itemTags = $this->get('AllTags');
            }
            if(!$this -> itemCategories) {
                $this->itemCategories = $this->get('AllCategories');
            }
        }
        else{
            if(!$this -> itemTags) {
                $this -> itemTags       = $this -> get('TagsByArticle');
            }
            if(!$this -> itemCategories) {
                $this->itemCategories = $this->get('CategoriesByArticle');
            }
        }
        //Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

        if ($active)
        {
            $params->def('page_heading', $params->get('page_title', $active->title));
        }
        else
        {
            $params->def('page_heading', JText::_('JGLOBAL_ARTICLES'));
        }

        $this -> items          = $list;
        $this -> params         = $params;
        $this -> pagination     = $this -> get('Pagination');
        if($active) {
            $this->Itemid = $active->id;
        }
        $this -> char           = $state -> get('filter.char');
        $this -> availLetter    = $this -> get('AvailableLetter');

        $doc -> addStyleSheet('components/com_tz_portfolio_plus/css/tzportfolioplus.min.css');

        $doc -> addScript('components/com_tz_portfolio_plus/js/tz_portfolio_plus.min.js');

        $this -> _prepareDocument();

        // Add feed links
		if ($params->get('show_feed_link', 1)) {
			$link = '&format=feed&limitstart=';
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$doc->addHeadLink(JRoute::_($link . '&type=rss'), 'alternate', 'rel', $attribs);
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$doc->addHeadLink(JRoute::_($link . '&type=atom'), 'alternate', 'rel', $attribs);
		}

        parent::display($tpl);
    }

    protected function _prepareDocument()
    {
        $app    = JFactory::getApplication();
        $title  = $this->params->get('page_title', '');

        if (empty($title)) {
            $title = $app->getCfg('sitename');
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }

        $this->document->setTitle($title);

        if ($this->params->get('menu-meta_description'))
        {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords'))
        {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots'))
        {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }
    }
}