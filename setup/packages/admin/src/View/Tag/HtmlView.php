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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\View\Tag;

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

    protected $item     = null;
    protected $form     = null;
    protected $canDo    = null;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        $this -> item   = $this -> get('Item');
        $this -> form   = $this -> get('Form');

        $this -> canDo  = TZ_PortfolioHelper::getActions(COM_TZ_PORTFOLIO, 'tag');

        $this -> addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar(){
        Factory::getApplication()->input->set('hidemainmenu', true);

        $user	    = Factory::getUser();
        $canDo      = $this -> canDo;
        $userId     = $user -> id;
        $isNew      = ($this -> item -> id == 0);
        $toolbar    = Toolbar::getInstance();

        ToolbarHelper::title(Text::sprintf('COM_TZ_PORTFOLIO_TAGS_MANAGER_TASK',
            Text::_(($isNew)?'COM_TZ_PORTFOLIO_PAGE_ADD_TAG':'COM_TZ_PORTFOLIO_PAGE_EDIT_TAG')),'tag');

        if($isNew && $canDo->get('core.create')){
            $toolbar->apply('tag.apply');

            $saveGroup = $toolbar->dropdownButton('save-group');

            $saveGroup->configure(
                function (Toolbar $childBar) use ($user) {
                    $childBar->save('tag.save');

//                    if ($user->authorise('core.create', 'com_menus.menu')) {
//                        $childBar->save('tag.save2menu', 'JTOOLBAR_SAVE_TO_MENU');
//                    }

                    $childBar->save2new('tag.save2new');
                }
            );

            $toolbar->cancel('tag.cancel', 'JTOOLBAR_CANCEL');
        }else{
            // Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
            $itemEditable = $canDo->get('core.edit') || ($canDo->get('core.edit.own'));

            if ($itemEditable) {
                $toolbar->apply('tag.apply');
            }

            $saveGroup = $toolbar->dropdownButton('save-group');

            $saveGroup->configure(
                function (Toolbar $childBar) use ($itemEditable, $canDo, $user) {
                    // Can't save the record if it's checked out and editable
                    if ($itemEditable) {
                        $childBar->save('tag.save');

                        // We can save this record, but check the create permission to see if we can return to make a new one.
                        if ($canDo->get('core.create')) {
                            $childBar->save2new('tag.save2new');
                        }
                    }
                }
            );

            $toolbar->cancel('tag.cancel', 'JTOOLBAR_CLOSE');
        }

        $toolbar -> help('JHELP_CONTENT_ARTICLE_MANAGER',false,
            'https://www.tzportfolio.com/document/administration/29-how-to-use-group-fields-in-tz-portfolio-plus.html?tmpl=component');

        TZ_PortfolioToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_VIDEO_TUTORIALS', 'youtube', 'youtube');
    }
}
