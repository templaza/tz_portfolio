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

// No direct access.
defined('_JEXEC') or die;

class PlgTZ_Portfolio_PlusContentVoteViewArticle extends JViewLegacy{
    protected $item     = null;
    protected $params   = null;
    protected $audio    = null;
    protected $head     = false;

    public function display($tpl = null){
        $this -> item   = $this -> get('Item');
        $state          = $this -> get('State');
        $params         = $state -> get('params');
        $this -> params = $params;

        if(!$this -> head) {
            $document = JFactory::getDocument();
            $document->addStyleSheet(TZ_Portfolio_PlusUri::root(true) . '/addons/content/vote/css/vote.css');
            $document->addScript(TZ_Portfolio_PlusUri::root(true) . '/addons/content/vote/js/vote.js');
            $document->addScriptDeclaration('var tzPortfolioVoteFolder = "' . TZ_Portfolio_PlusUri::base(true) . '";
        var tzPortfolioPlusBase = "' . TZ_Portfolio_PlusUri::base(true) . '/addons/content/vote";
        var TzPortfolioPlusVote_text=Array("' . JTEXT::_('PLG_CONTENT_VOTE_NO_AJAX') . '","'
                . JTEXT::_('PLG_CONTENT_VOTE_LOADING') . '","' . JTEXT::_('PLG_CONTENT_VOTE_THANKS') . '","'
                . JTEXT::_('PLG_CONTENT_VOTE_LOGIN') . '","' . JTEXT::_('PLG_CONTENT_VOTE_RATED') . '","'
                . JTEXT::_('PLG_CONTENT_VOTE_VOTES') . '","' . JTEXT::_('PLG_CONTENT_VOTE_VOTE') . '");');
            $this -> head   = true;
        }

        parent::display($tpl);
    }
}