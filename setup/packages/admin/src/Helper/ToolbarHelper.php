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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Helper;

// No direct access
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

class ToolbarHelper{

    public static function customHelp($url, $title, $icon = null, $id=null, $width=800, $height=500){

        $bar    = Toolbar::getInstance('toolbar');
        $text   = $title?Text::_($title):Text::_('JTOOLBAR_HELP');
        $doTask = 'Joomla.popupWindow(\''.$url.'\',\''.$text.'\', '.$width.', '.$height.', 1)';
        $id     = $id?$id:'customhelp';

        $layout = new FileLayout('toolbar.customhelp');
        $html   = $layout->render(array('doTask' => $doTask, 'text' => $text, 'icon' => $icon, 'id' => $id));
        $bar -> appendButton('Custom', $html, $id);
    }

    public static function draft($task = 'draft', $alt = 'COM_TZ_PORTFOLIO_SAVE_DRAFT', $check = false)
    {
        $bar = Toolbar::getInstance('toolbar');

        // Add a publish button.
        $bar->appendButton('Standard', 'pencil-2 text-success', $alt, $task, $check);
    }

    public static function approve($task = 'approve', $alt = 'COM_TZ_PORTFOLIO_APPROVE', $check = false)
    {
        $bar = Toolbar::getInstance('toolbar');

        // Add a publish button.
        $bar->appendButton('Standard', 'checkmark-2 text-success', $alt, $task, $check);
    }

    public static function reject($task = 'reject', $alt = 'COM_TZ_PORTFOLIO_REJECT', $check = false)
    {
        $bar = Toolbar::getInstance('toolbar');

        // Add a publish button.
        $bar->appendButton('Standard', 'minus text-error text-danger', $alt, $task, $check);
    }

    public static function preferencesAddon($addonId, $height = '550', $width = '875', $alt = 'Toolbar_Options')
    {
        $bar = Toolbar::getInstance('toolbar');

        $uri = (string) Uri::getInstance();
        $return = urlencode(base64_encode($uri));

        // Add a button linking to config for component.
        $bar->appendButton(
            'Link',
            'options',
            $alt,
            'index.php?option=com_tz_portfolio&task=addon.edit&id=' . $addonId . '&amp;return=' . $return
        );
    }

    public static function addonDataManager($alt='COM_TZ_PORTFOLIO_ADDONS_MANAGER', $icon = 'puzzle'){

        $bar = Toolbar::getInstance('toolbar');

        // Add a button linking to config for component.
        $bar->appendButton(
            'Link',
            $icon,
            $alt,
            'index.php?option=com_tz_portfolio&view=addons'
        );
    }

}