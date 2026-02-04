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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Service\HTML;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

class TZTemplates
{
    public static function thumb($template, $clientId = 0)
    {
        $client = ApplicationHelper::getClientInfo($clientId);
        $basePath = $client->path . '/components/com_tz_portfolio/styles/' . $template;
        $baseUrl = ($clientId == 0) ? Uri::root(true) : Uri::root(true) . '/administrator';
        $preview = $basePath . '/template_preview.png';
        $html = '';

        if (file_exists($preview))
        {
            HTMLHelper::_('bootstrap.tooltip');

            $preview = $baseUrl . '/components/com_tz_portfolio/styles/' . $template . '/template_preview.png';

            $html = HTMLHelper::_('image', 'components/com_tz_portfolio/styles/' . $template . '/template_preview.png'
                , Text::_('COM_TEMPLATES_PREVIEW'));
            $html = '<a href="#' . $template . '-Modal" role="button" class="thumbnail float-left hasTooltip" data-bs-toggle="modal" data-toggle="modal" title="' .
                HTMLHelper::_('tooltipText', 'COM_TEMPLATES_CLICK_TO_ENLARGE') . '">' . $html . '</a>';


            $footer = '<button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal" aria-hidden="true">'
                . Text::_('JTOOLBAR_CLOSE') . '</button>';

            $html .= HTMLHelper::_(
                'bootstrap.renderModal',
                $template . '-Modal',
                array(
                    'title'  => Text::_('COM_TEMPLATES_BUTTON_PREVIEW'),
                    'height' => '500px',
                    'width'  => '800px',
                    'footer' => $footer,
                ),
                $body = '<div><img src="' . $preview . '" style="max-width:100%" alt="' . $template . '"></div>'
            );
        }

        return $html;
    }
}