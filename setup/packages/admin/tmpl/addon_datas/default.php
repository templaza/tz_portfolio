<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    Sonny

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;
if (!empty($this->submenu)) {
    echo '<nav class="navbar navbar-expand-lg bg-body-tertiary">';
    echo '<div class="container-fluid">';
    echo '<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>';
    echo '<div class="collapse navbar-collapse" id="navbarNavAltMarkup">';
    echo '<div class="navbar-nav">';
    foreach ($this->submenu as $value => $text) {
        echo '<a class="nav-link" href="'.\Joomla\CMS\Router\Route::_('index.php?option=com_tz_portfolio&view=addon_datas&addon_id='.$this->state->get($this -> getName().'.addon_id')).'&addon_view='.$value.'">'.$text.'</a>';
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</nav>';
}
echo $this->addon_view;