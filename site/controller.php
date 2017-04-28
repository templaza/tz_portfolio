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
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Content Component Controller.
 */
class TZ_Portfolio_PlusController extends TZ_Portfolio_PlusControllerLegacy
{
    protected $input;
	function __construct($config = array())
	{
        $this->input    = JFactory::getApplication()->input;
        $params         = JFactory::getApplication() -> getParams();

		// Article frontpage Editor pagebreak proxying:
		if (($this->input -> get('view') == 'article')
            && $this->input -> get('layout') == 'pagebreak') {
			$config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
		}
		// Article frontpage Editor article proxying:
		elseif($this->input -> get('view') == 'articles' && $this->input -> get('layout') == 'modal') {
			JHtml::_('stylesheet', 'system/adminlist.css', array(), true);
			$config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
		}

		parent::__construct($config);
	}

	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$app		= JFactory::getApplication('site');
		$doc    	= JFactory::getDocument();
		$params     = $app -> getParams();
		$cachable 	= true;

		$user = JFactory::getUser();

		JHtml::_('behavior.caption');

		// Set the default view name and format from the Request.
		// Note we are using a_id to avoid collisions with the router and the return page.
		// Frontend is a bit messier than the backend.
		$id		= $this -> input -> get('a_id');
		$vName	= $this -> input -> get('view', 'portfolio');

        $this->input->set('view', $vName);

		if ($user->get('id') || strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' || $vName == 'search')
		{
			$cachable = false;
		}

        $safeurlparams = array('catid' => 'INT', 'id' => 'INT', 'cid' => 'ARRAY', 'year' => 'INT', 'month' => 'INT', 'limit' => 'UINT', 'limitstart' => 'UINT',
        			'showall' => 'INT', 'return' => 'BASE64', 'filter' => 'STRING', 'filter_order' => 'CMD', 'filter_order_Dir' => 'CMD', 'filter-search' => 'STRING', 'print' => 'BOOLEAN', 'lang' => 'CMD', 'Itemid' => 'INT');

		// Check for edit form.
		if ($vName == 'form' && !$this->checkEditId('com_tz_portfolio_plus.edit.article', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			return JError::raiseError(403, JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
		}

		//Add Script to the header
		if($params -> get('enable_jquery',0)){
			$doc -> addScript(TZ_Portfolio_PlusUri::base(true).'/js/jquery-1.11.3.min.js');
			$doc -> addScript(TZ_Portfolio_PlusUri::base(true).'/js/jquery-noconflict.min.js');
			$doc -> addScript(TZ_Portfolio_PlusUri::base(true).'/js/jquery-migrate-1.2.1.js');
		}
		if($params -> get('enable_bootstrap',1)){
			$doc -> addScript(TZ_Portfolio_PlusUri::base(true).'/bootstrap/js/bootstrap.min.js');
			$doc -> addStyleSheet(TZ_Portfolio_PlusUri::base(true).'/bootstrap/css/bootstrap.min.css');
			$doc -> addScriptDeclaration('
				(function($){
					$(document).off(\'click.modal.data-api\')
					.on(\'click.modal.data-api\', \'[data-toggle="modal"]\', function (e) {
						var $this = $(this)
						  , href = $this.attr(\'href\')
						  , $target = $($this.attr(\'data-target\') || (href && href.replace(/.*(?=#[^\s]+$)/, \'\'))) //strip for ie7
						  , option = $target.data(\'modal\') ? \'toggle\' : $.extend({ remote:!/#/.test(href) && href }, $target.data(), $this.data())
					
						e.preventDefault();
					
						$target
						  .modal(option)
						  .one(\'hide\', function () {
							$this.focus()
						  });
					  });
				})(jQuery);
			');
		}

		return parent::display($cachable, $safeurlparams);
	}
}
