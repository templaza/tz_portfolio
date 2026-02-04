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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\View\Field;

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
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\ToolbarHelper as TZ_PortfolioToolbarHelper;

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
    protected $form     = null;
    protected $item     = null;
    protected $canDo    = null;
    protected $groups   = null;

    /**
     * Is this view an Empty State
     *
     * @var  boolean
     */
    private $isEmptyState = false;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        $this -> state  = $this -> get('State');
        $this -> form   = $this -> get('Form');
        $this -> item   = $this -> get('Item');
        $this -> canDo	= TZ_PortfolioHelper::getActions('field'
            , $this -> item -> id);

//        $groupModel = $this -> getModel('Groups');
//
//        if($groupModel) {
//            $groupModel -> setState('filter_order', 'name');
//            $groupModel -> setState('filter_order_Dir', 'ASC');
//
//            $this -> groups = $groupModel -> getItems();
//        }

        if($this -> item -> id == 0){
            $this -> item -> published = 'P';
        }
        else{
            if($this -> item -> published == 1){
                $this -> item -> published  = 'P';
            }
            else{
                $this -> item -> published  = 'U';
            }
        }

        $this -> addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar(){

        Factory::getApplication()->input->set('hidemainmenu', true);

        $user	    = Factory::getUser();
        $userId     = $user -> id;
        $isNew      = ($this -> item -> id == 0);
        $canDo      = $this -> canDo;
        $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
        $toolbar    = Toolbar::getInstance();

        ToolBarHelper::title(Text::sprintf('COM_TZ_PORTFOLIO_FIELDS_MANAGER_TASK',
            Text::_(($isNew)?'COM_TZ_PORTFOLIO_PAGE_ADD_FIELD':'COM_TZ_PORTFOLIO_PAGE_EDIT_FIELD')),'file-plus');

        if($isNew && $canDo->get('core.create')){
            $toolbar -> apply('field.apply');

            $saveGroup = $toolbar->dropdownButton('save-group');

            $saveGroup -> configure(
                function (Toolbar $childBar) use ($user) {
                    $childBar -> save('field.save');

//                    if ($user -> authorise('core.create', 'com_menus.menu')) {
//                        $childBar -> save('field.save2menu', 'JTOOLBAR_SAVE_TO_MENU');
//                    }

                    $childBar -> save2new('field.save2new');
                }
            );

            $toolbar -> cancel('field.cancel', 'JTOOLBAR_CANCEL');
        }else{
            // Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
            $itemEditable = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId);

            if (!$checkedOut && $itemEditable) {
                $toolbar->apply('field.apply');
            }

            $saveGroup = $toolbar->dropdownButton('save-group');

            $saveGroup->configure(
                function (Toolbar $childBar) use ($checkedOut, $itemEditable, $canDo, $user) {
                    // Can't save the record if it's checked out and editable
                    if (!$checkedOut && $itemEditable) {
                        $childBar->save('field.save');

                        // We can save this record, but check the create permission to see if we can return to make a new one.
                        if ($canDo->get('core.create')) {
                            $childBar->save2new('field.save2new');
                        }
                    }

//                    // If checked out, we can still save2menu
//                    if ($user->authorise('core.create', 'com_menus.menu')) {
//                        $childBar->save('field.save2menu', 'JTOOLBAR_SAVE_TO_MENU');
//                    }
//
//                    // If checked out, we can still save
//                    if ($canDo->get('core.create')) {
//                        $childBar->save2copy('field.save2copy');
//                    }
                }
            );

            $toolbar->cancel('field.cancel', 'JTOOLBAR_CLOSE');
        }

        $toolbar -> help('JHELP_CONTENT_ARTICLE_MANAGER',false,
            'https://www.tzportfolio.com/document/administration/30-how-to-use-fields-in-tz-portfolio-plus.html?tmpl=component');

        TZ_PortfolioToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_VIDEO_TUTORIALS', 'youtube', 'youtube');
    }
}
