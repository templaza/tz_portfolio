<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    Sonny

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

namespace TemPlaza\Component\TZ_Portfolio\Administrator\View\Article;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Button\DropdownButton;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\ACLHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\ToolbarHelper as TZ_PortfolioToolbarHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;

/**
 * Categories view class for the Category package.
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The model state
     *
     * @var   \Joomla\Registry\Registry
     */
    protected $state;
    /**
     * An array of items
     *
     * @var  array
     */
    protected $form;
    protected $item;
    protected $pluginsTab;
    protected $pluginsMediaTypeTab	= array();
    protected $formfields	= null;
    protected $extraFields	= null;

    /**
     * Is this view an Empty State
     *
     * @var  boolean
     * @since 4.0.0
     */
    private $isEmptyState = false;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        $app    = Factory::getApplication();
        $input  = $app -> input;
        if($input->get('task')!='lists'){
            if ($this->getLayout() == 'pagebreak') {
                $eName		= Factory::getApplication()->input->get('e_name');
                $eName		= preg_replace( '#[^A-Z0-9\-\_\[\]]#i', '', $eName );
                $document	= Factory::getApplication() -> getDocument();
                $document->setTitle(Text::_('COM_CONTENT_PAGEBREAK_DOC_TITLE'));
                $this-> eName    = $eName;
                parent::display($tpl);
                return;
            }

            // Initialiase variables.
            $this->form		= $this->get('Form');
            $this->item		= $this->get('Item');
            $this->state	= $this->get('State');

            $canDo	= TZ_PortfolioHelper::getActions(COM_TZ_PORTFOLIO, 'article', $this -> item -> id);
            $this -> canDo	= $canDo;

//            // Check for errors.
//            if (count($errors = $this->get('Errors'))) {
//                JError::raiseError(500, implode("\n", $errors));
//                return false;
//            }

            $this -> extraFields	= $this -> get('ExtraFields');

            // Import all add-ons
            AddonHelper::importAllAddOns();

            $this -> advancedDesc       = $app -> triggerEvent('onAddFormToArticleDescription', array($this -> item));
            $this -> beforeDescription  = $app -> triggerEvent('onAddFormBeforeArticleDescription', array($this -> item));
            $this -> afterDescription   = $app -> triggerEvent('onAddFormAfterArticleDescription', array($this -> item));

            // Load Tabs's title from plugin group tz_portfolio_plus_mediatype
            if($mediaType  = $app -> triggerEvent('onAddMediaType')){
                $mediaType  = array_filter($mediaType);
                $mediaType  = array_reverse($mediaType);

                $mediaForm	= $app -> triggerEvent('onMediaTypeDisplayArticleForm',array($this -> item));

                $mediaForm  = array_filter($mediaForm);
                $mediaForm  = array_reverse($mediaForm);
                if(count($mediaType)){
                    $plugin	= array();
                    foreach($mediaType as $i => $type){
                        $plugin[$i]			= new \stdClass();
                        $plugin[$i] -> type	= $type;
                        $plugin[$i] -> html	= '';
                        if($mediaForm && count($mediaForm) && isset($mediaForm[$i])) {
                            $plugin[$i]->html = $mediaForm[$i];
                        }
                    }
                    $this -> pluginsMediaTypeTab    = $plugin;
                }
            }

            // If we are forcing a language in modal (used for associations).
            if ($this->getLayout() === 'modal' && $forcedLanguage = $input->get('forcedLanguage', '', 'cmd')) {
                // Set the language field to the forcedLanguage and disable changing it.
                $this->form->setValue('language', null, $forcedLanguage);
                $this->form->setFieldAttribute('language', 'readonly', 'true');
            }

            $this->addToolbar();
        }
        parent::display($tpl);
    }

    protected function addToolbar(){
        Factory::getApplication()->input->set('hidemainmenu', true);

        $user	    = Factory::getApplication()->getIdentity();
        $canDo      = $this -> canDo;
        $userId     = $user -> id;
        $isNew      = ($this -> item -> id == 0);
        $checkedOut = !($this -> item -> checked_out == 0 || $this -> item -> checked_out == $userId);
        $toolbar    = Toolbar::getInstance();
        
        ToolBarHelper::title(Text::_('COM_TZ_PORTFOLIO_PAGE_'.($checkedOut ? 'VIEW_ARTICLE' : ($isNew ? 'ADD_ARTICLE' : 'EDIT_ARTICLE'))), 'pencil-2');

        // For new records, check the create permission.
        $approvePer     = ACLHelper::allowApprove($this -> item);
        $applyText      = $approvePer?'JTOOLBAR_APPLY':'COM_TZ_PORTFOLIO_SUBMIT_APPROVE';

        if($isNew && $canDo->get('core.create')){
            $toolbar->apply('article.apply', $applyText);

            $saveGroup = $toolbar->dropdownButton('save-group');

            $saveGroup->configure(
                function (Toolbar $childBar) use ($user, $approvePer, $canDo) {
                    $saveText       = $approvePer?'JTOOLBAR_SAVE':'COM_TZ_PORTFOLIO_SUBMIT_APPROVE_AND_CLOSE';
                    $save2newText   = $approvePer?'JTOOLBAR_SAVE_AND_NEW':'COM_TZ_PORTFOLIO_SUBMIT_APPROVE_AND_NEW';

                    $childBar->save('article.save', $saveText);
                    $childBar->save2new('article.save2new', $save2newText);
                    if(!$approvePer){
                        TZ_PortfolioToolbarHelper::draft('article.draft');
                    }
                }
            );

            $toolbar->cancel('article.cancel', 'JTOOLBAR_CANCEL');
        }else{
            // Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
            $itemEditable = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId);

            if (!$checkedOut && $itemEditable) {
                $toolbar->apply('article.apply');
            }

            $saveGroup = $toolbar->dropdownButton('save-group');

            $saveGroup->configure(
                function (Toolbar $childBar) use ($checkedOut, $itemEditable, $canDo, $user, $approvePer) {
                    // Can't save the record if it's checked out and editable
                    if (!$checkedOut && $itemEditable) {
                        $childBar->save('article.save');

                        // We can save this record, but check the create permission to see if we can return to make a new one.
                        if ($canDo->get('core.create')) {
                            $childBar->save2new('article.save2new');
                        }
                    }

                    // If checked out, we can still save
                    if ($canDo->get('core.create') && $approvePer && $this -> item -> state != 3 && $this -> item -> state != 4) {
                        $childBar -> save2copy('article.save2copy');
                    }

                    if($approvePer && ($this -> item -> state == 3 || $this -> item -> state == 4)){
                        $childBar -> custom('article.reject', 'minus text-danger text-error',
                            '',  Text::_('COM_TZ_PORTFOLIO_REJECT'), false);
                    }
                }
            );

            $toolbar->cancel('article.cancel', 'JTOOLBAR_CLOSE');
        }

        $toolbar -> help('JHELP_CONTENT_ARTICLE_MANAGER_EDIT',false,
            'https://www.tzportfolio.com/document/administration/41-how-to-create-edit-an-article-in-tz-portfolio-plus.html?tmpl=component');

        TZ_PortfolioToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_VIDEO_TUTORIALS', 'youtube', 'youtube');
    }
}
