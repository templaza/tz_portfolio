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

/**
 * View to edit an article.
 *
 */
class TZ_Portfolio_PlusViewArticle extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;
    protected $pluginsTab;
    protected $pluginsMediaTypeTab	= array();
	protected $formfields	= null;
	protected $extraFields	= null;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
        if(JFactory::getApplication()->input->getCmd('task')!='lists'){
            if ($this->getLayout() == 'pagebreak') {
                $eName		= JFactory::getApplication()->input->get('e_name');
                $eName		= preg_replace( '#[^A-Z0-9\-\_\[\]]#i', '', $eName );
                $document	= JFactory::getDocument();
                $document->setTitle(JText::_('COM_CONTENT_PAGEBREAK_DOC_TITLE'));
                $this-> eName    = $eName;
                parent::display($tpl);
                return;
            }

            // Initialiase variables.
            $this->form		= $this->get('Form');
            $this->item		= $this->get('Item');
            $this->state	= $this->get('State');

            $canDo	= TZ_Portfolio_PlusHelper::getActions(COM_TZ_PORTFOLIO_PLUS, 'article', $this -> item -> id);
			$this -> canDo	= $canDo;


            // Check for errors.
            if (count($errors = $this->get('Errors'))) {
                JError::raiseError(500, implode("\n", $errors));
                return false;
            }

            $this -> extraFields	= $this -> get('ExtraFields');

			// Load Tabs's title from plugin group tz_portfolio_plus_mediatype
			$dispatcher	= JDispatcher::getInstance();
			TZ_Portfolio_PlusPluginHelper::importPlugin('mediatype');
			if($mediaType  = $dispatcher -> trigger('onAddMediaType')){
				$mediaForm	= $dispatcher -> trigger('onMediaTypeDisplayArticleForm',array($this -> item));
				if(count($mediaType)){
					$plugin	= array();
					foreach($mediaType as $i => $type){
						$plugin[$i]			= new stdClass();
						$plugin[$i] -> type	= $type;
						$plugin[$i] -> html	= '';
						if($mediaForm && count($mediaForm) && isset($mediaForm[$i])) {
							$plugin[$i]->html = $mediaForm[$i];
						}
						$this -> pluginsMediaTypeTab[$i]	= $plugin[$i];
					}
				}
			}

            // If we are forcing a language in modal (used for associations).
            if ($this->getLayout() === 'modal' && $forcedLanguage = JFactory::getApplication()->input->get('forcedLanguage', '', 'cmd')) {
                // Set the language field to the forcedLanguage and disable changing it.
                $this->form->setValue('language', null, $forcedLanguage);
                $this->form->setFieldAttribute('language', 'readonly', 'true');
            }

            $this->addToolbar();
        }
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$user		= TZ_Portfolio_PlusUser::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		$canDo		= $this -> canDo;

		JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_PLUS_PAGE_'.($checkedOut ? 'VIEW_ARTICLE' : ($isNew ? 'ADD_ARTICLE' : 'EDIT_ARTICLE'))), 'pencil-2');

		// Built the actions for new and existing records.

		// For new records, check the create permission.
		if ($isNew && (count($user->getAuthorisedCategories('com_tz_portfolio_plus', 'core.create')) > 0)) {
			JToolBarHelper::apply('article.apply');
//			JToolBarHelper::save('article.save');
			JToolBarHelper::save2new('article.save2new');
			JToolBarHelper::cancel('article.cancel');
		}
		else {
			// Can't save the record if it's checked out.
			if (!$checkedOut) {
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId)) {
					JToolBarHelper::apply('article.apply');
//					JToolBarHelper::save('article.save');

					// We can save this record, but check the create permission to see if we can return to make a new one.
					if ($canDo->get('core.create')) {
						JToolBarHelper::save2new('article.save2new');
					}
				}
			}

			// If checked out, we can still save
			if ($canDo->get('core.create')) {
				JToolBarHelper::save2copy('article.save2copy');
			}

			JToolBarHelper::cancel('article.cancel', 'JTOOLBAR_CLOSE');
		}

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER_EDIT',false,
            'https://www.tzportfolio.com/document/administration/41-how-to-create-edit-an-article-in-tz-portfolio-plus.html?tmpl=component');

        TZ_Portfolio_PlusToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_PLUS_VIDEO_TUTORIALS', 'youtube', 'youtube');

	}
}
