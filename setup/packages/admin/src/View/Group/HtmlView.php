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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\View\Group;

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
    /**
     * An array of items
     *
     * @var  array
     */
	protected $items;
    protected $f_levels;
	protected $pagination;
	protected $listsGroup;

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
        $this -> form   = $this -> get('Form');
        $this -> item   = $this -> get('Item');
        $canDo	        = TZ_PortfolioHelper::getActions( 'group'
            , $this -> item -> id);
        $this -> canDo	= $canDo;

        $this -> addToolbar();
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

        ToolbarHelper::title(Text::sprintf('COM_TZ_PORTFOLIO_GROUP_FIELDS_MANAGER_TASK',
            Text::_(($isNew)?'COM_TZ_PORTFOLIO_PAGE_ADD_GROUP_FIELD':'COM_TZ_PORTFOLIO_PAGE_EDIT_GROUP_FIELD')),'folder-plus-2');

        if($isNew && $canDo->get('core.create')){
            $toolbar->apply('group.apply');

            $saveGroup = $toolbar->dropdownButton('save-group');

            $saveGroup->configure(
                function (Toolbar $childBar) use ($user) {
                    $childBar->save('group.save');
                    $childBar->save2new('group.save2new');
                }
            );

            $toolbar->cancel('group.cancel', 'JTOOLBAR_CANCEL');
        }else{
            // Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
            $itemEditable = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId);

            if (!$checkedOut && $itemEditable) {
                $toolbar->apply('group.apply');
            }

            $saveGroup = $toolbar->dropdownButton('save-group');

            $saveGroup->configure(
                function (Toolbar $childBar) use ($checkedOut, $itemEditable, $canDo, $user) {
                    // Can't save the record if it's checked out and editable
                    if (!$checkedOut && $itemEditable) {
                        $childBar->save('group.save');

                        // We can save this record, but check the create permission to see if we can return to make a new one.
                        if ($canDo->get('core.create')) {
                            $childBar->save2new('group.save2new');
                        }
                    }
                }
            );

            $toolbar->cancel('group.cancel', 'JTOOLBAR_CLOSE');
        }

        $toolbar -> help('JHELP_CONTENT_ARTICLE_MANAGER',false,
            'https://www.tzportfolio.com/document/administration/29-how-to-use-group-fields-in-tz-portfolio-plus.html?tmpl=component');

        TZ_PortfolioToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_VIDEO_TUTORIALS', 'youtube', 'youtube');
    }
}
