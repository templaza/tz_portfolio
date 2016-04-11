<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2015 templaza.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

class PlgTZ_Portfolio_PlusContentVote extends TZ_Portfolio_PlusPlugin
{
    protected $autoloadLanguage     = true;

    public function onAddContentType(){
        $type = new stdClass();
        $lang = JFactory::getLanguage();
        $lang_key = 'PLG_' . $this->_type . '_' . $this->_name . '_TITLE';
        $lang_key = strtoupper($lang_key);

        if ($lang->hasKey($lang_key)) {
            $type->text = JText::_($lang_key);
        } else {
            $type->text = $this->_name;
        }

        $type->value = $this->_name;

        return $type;
    }

    public function onAlwaysLoadDocument($context){
//        $document = JFactory::getDocument();
//        $document->addStyleSheet(TZ_Portfolio_PlusUri::root(true).'/addons/content/vote/css/vote.css');
//        $document->addScript(TZ_Portfolio_PlusUri::root(true).'/addons/content/vote/js/vote.js');
//        $document->addScriptDeclaration( 'var tzPortfolioVoteFolder = "'.TZ_Portfolio_PlusUri::base(true).'";
//        var tzPortfolioPlusBase = "'.TZ_Portfolio_PlusUri::base(true).'/addons/content/vote";
//        var TzPortfolioPlusVote_text=Array("'.JTEXT::_('PLG_CONTENT_VOTE_NO_AJAX').'","'
//            .JTEXT::_('PLG_CONTENT_VOTE_LOADING').'","'.JTEXT::_('PLG_CONTENT_VOTE_THANKS').'","'
//            .JTEXT::_('PLG_CONTENT_VOTE_LOGIN').'","'.JTEXT::_('PLG_CONTENT_VOTE_RATED').'","'
//            .JTEXT::_('PLG_CONTENT_VOTE_VOTES').'","'.JTEXT::_('PLG_CONTENT_VOTE_VOTE').'");');
    }

    public function onBeforeDisplayAdditionInfo($context, &$article, $params, $page = 0, $layout = 'default'){
        list($extension, $vName)   = explode('.', $context);

        $item   = $article;

        if(isset($article -> id)){
            $item -> rating_count   = 0;
            $item -> rating_sum     = 0;

            $db	    = JFactory::getDBO();
            $query  = $db -> getQuery(true);
            $query -> select('*');
            $query -> from('#__tz_portfolio_plus_content_rating');
            $query -> where('content_id = '. $item -> id);
            $db -> setQuery($query);

            if($vote = $db->loadObject()) {
                foreach($vote as $key => $value){
                    $item -> $key   = $value;
                }
            }
        }

        if($extension == 'module' || $extension == 'modules'){
            if($path = $this -> getModuleLayout($this -> _type, $this -> _name, $extension, $vName, $layout)){
                // Display html
                ob_start();
                include $path;
                $html = ob_get_contents();
                ob_end_clean();
                $html = trim($html);
                return $html;
            }
        }elseif(in_array($context, array('com_tz_portfolio_plus.portfolio', 'com_tz_portfolio_plus.date'
        , 'com_tz_portfolio_plus.featured', 'com_tz_portfolio_plus.tags', 'com_tz_portfolio_plus.users'))){
            if($html = $this -> _getViewHtml($context,$item, $params, $layout)){
                return $html;
            }
        }
    }

    public function onAfterDisplayAdditionInfo($context, &$article, $params, $page = 0, $layout = 'default'){

    }

    public function onContentDisplayListView($context, &$article, $params, $page = 0, $layout = 'default'){

    }

    public function onContentDisplayArticleView($context, &$article, $params, $page = 0, $layout = null){
        list($extension, $vName)   = explode('.', $context);

        $item   = $article;

        if(isset($article -> id)){
            $item -> rating_count   = 0;
            $item -> rating_sum     = 0;

            $db	    = JFactory::getDBO();
            $query  = $db -> getQuery(true);
            $query -> select('*');
            $query -> from('#__tz_portfolio_plus_content_rating');
            $query -> where('content_id = '. $item -> id);
            $db -> setQuery($query);

            if($vote = $db->loadObject()) {
                foreach($vote as $key => $value){
                    $item -> $key   = $value;
                }
            }
        }
        return parent::onContentDisplayArticleView($context, $item, $params, $page, $layout);
    }
}
