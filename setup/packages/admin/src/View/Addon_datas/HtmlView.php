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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\View\Addon_datas;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\ToolbarHelper as TZ_PortfolioToolbarHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\Helper\AddonHelper;

/**
 * Dashboard view.
 *
 * @package		Joomla.Administrator
 * @subpakage	TZ.Portfolio
 */
class HtmlView extends BaseHtmlView {
    protected $addon;
    protected $submenu;
    protected $state;
    protected $addon_view;

    public function display($tpl = null) {
        $model = $this->getModel();
        $this -> addon = $model -> getAddon();
        $this -> submenu = $model->getDataMenu();
        $this -> state = $model -> getState();
        $this -> addon_view = $model -> getAddonView();

        $this -> addToolbar();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since	1.6
     */
    protected function addToolbar()
    {
        $user    = $this->getCurrentUser();
        $toolbar = $this->getDocument()->getToolbar();
        $app = Factory::getApplication();
        $viewName   = $app->input->get('addon_view', '');
        $id = $app->input->getInt('id');
        $layout = $app->input->get('layout');

        $text = Text::_( 'Addon Manager' );
        ToolbarHelper::title( Text::_( 'TZ Portfolio' ).': ' . $text, 'puzzle');
        if (empty($viewName)) {
            return;
        }

        if ($layout == 'edit') {
            $isNew = ($id == 0);
            $toolbar->apply('addon_data.apply');
            $toolbar->save('addon_data.save');

            if ($isNew) {
                $toolbar->cancel('addon_data.cancel');
            } else {
                $toolbar->cancel('addon_data.cancel', Text::_('JTOOLBAR_CLOSE'));
            }
        } else {
            $toolbar->addNew('addon_data.add');
            $toolbar->delete('addon_data.delete', 'JTOOLBAR_DELETE_FROM_TRASH')
                ->message('JGLOBAL_CONFIRM_DELETE')
                ->listCheck(true);
        }
    }
}