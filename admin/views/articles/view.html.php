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

jimport('joomla.application.component.view');
//JHtml::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/helpers');
JHtml::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/helpers/html');

/**
 * View class for a list of articles.

 */
class TZ_Portfolio_PlusViewArticles extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 *
	 * @return	void
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->authors		= $this->get('Authors');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Levels filter.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', JText::_('J1'));
		$options[]	= JHtml::_('select.option', '2', JText::_('J2'));
		$options[]	= JHtml::_('select.option', '3', JText::_('J3'));
		$options[]	= JHtml::_('select.option', '4', JText::_('J4'));
		$options[]	= JHtml::_('select.option', '5', JText::_('J5'));
		$options[]	= JHtml::_('select.option', '6', JText::_('J6'));
		$options[]	= JHtml::_('select.option', '7', JText::_('J7'));
		$options[]	= JHtml::_('select.option', '8', JText::_('J8'));
		$options[]	= JHtml::_('select.option', '9', JText::_('J9'));
		$options[]	= JHtml::_('select.option', '10', JText::_('J10'));

		$this->assign('f_levels', $options);



		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}

        TZ_Portfolio_PlusHelper::addSubmenu('articles');
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
		$canDo	= TZ_Portfolio_PlusHelper::getActions($this->state->get('filter.category_id'));
		$user		= JFactory::getUser();
        
        // Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_PLUS_ARTICLES_TITLE'), 'stack article');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_tz_portfolio_plus', 'core.create'))) > 0 ) {
			JToolBarHelper::addNew('article.add');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) {
			JToolBarHelper::editList('article.edit');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::publish('articles.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('articles.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::custom('articles.featured', 'featured.png', 'featured_f2.png', 'JFEATURED', true);
			JToolBarHelper::divider();
			JToolBarHelper::checkin('articles.checkin');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'articles.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		}
		elseif ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('articles.trash');
			JToolBarHelper::divider();
		}
        
//         //Add a batch button
//		if ($user->authorise('core.edit'))
//		{
//			JHtml::_('bootstrap.modal', 'collapseModal');
//
//            $title      = JText::_('JTOOLBAR_BATCH');
//            $batchIcon  = '<i class="icon-checkbox-partial" title="'.$title.'"></i>';
//            $batchClass = ' class="btn btn-small"';
//
//			$dhtml = '<a'.$batchClass.' href="#" data-toggle="modal" data-target="#collapseModal">';
//            $dhtml .= $batchIcon.$title.'</a>';
//
//			$bar->appendButton('Custom', $dhtml, 'batch');
//		}

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_tz_portfolio_plus');
			JToolBarHelper::divider();
		}

        $doc    = JFactory::getDocument();
        // If the joomla is version 3.0
		$doc -> addStyleSheet(TZ_Portfolio_PlusUri::base(true, true).'/fonts/font-awesome-4.5.0/css/font-awesome.min.css');

        $doc -> addStyleSheet(TZ_Portfolio_PlusUri::base(true, true).'/css/style.min.css');

		JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER');

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

        JHtmlSidebar::setAction('index.php?option=com_tz_portfolio_plus&view=articles');

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived' => false)), 'value', 'text', $this->state->get('filter.published'), true)
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_CATEGORY'),
			'filter_category_id',
			JHtml::_('select.options', JHtml::_('tzcategory.options', 'com_tz_portfolio_plus'), 'value', 'text', $this->state->get('filter.category_id'))
		);

        JHtmlSidebar::addFilter(
			JText::_('COM_TZ_PORTFOLIO_PLUS_OPTION_SELECT_MEDIA_TYPE'),
			'filter_mediatype',
			JHtml::_('select.options', JHtml::_('mediatypes.options'), 'value', 'text', $this->state->get('filter.mediatype'))
		);

        JHtmlSidebar::addFilter(
			JText::_('COM_TZ_PORTFOLIO_PLUS_OPTION_SELECT_FIELDS_GROUP'),
			'filter_group',
			JHtml::_('select.options', JHtml::_('fieldgroups.options'), 'value', 'text', $this->state->get('filter.group'))
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_MAX_LEVELS'),
			'filter_level',
			JHtml::_('select.options', $this->f_levels, 'value', 'text', $this->state->get('filter.level'))
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_ACCESS'),
			'filter_access',
			JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_AUTHOR'),
			'filter_author_id',
			JHtml::_('select.options', $this->authors, 'value', 'text', $this->state->get('filter.author_id'))
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_LANGUAGE'),
			'filter_language',
			JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'))
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
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.state' => JText::_('JSTATUS'),
			'a.title' => JText::_('JGLOBAL_TITLE'),
			'category_title' => JText::_('JCATEGORY'),
			'access_level' => JText::_('JGRID_HEADING_ACCESS'),
			'a.created_by' => JText::_('JAUTHOR'),
			'language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.created' => JText::_('JDATE'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
