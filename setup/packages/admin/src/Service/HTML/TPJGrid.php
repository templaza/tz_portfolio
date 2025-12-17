<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Service\HTML;

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\HTML\Helpers\JGrid;

class TPJGrid {

    public static function taskLink($value, $i, $prefix = '', $enabled = true, $checkbox = 'cb',
                                    $publish_up = null, $publish_down = null, $attributes = array())
    {
        $states = array(
            1 => array('unpublish', 'COM_TZ_PORTFOLIO_UNPUBLISH'),
            0 => array('publish', 'COM_TZ_PORTFOLIO_PUBLISH'),
            -2 => array('publish', 'COM_TZ_PORTFOLIO_RESTORE'),
            -3 => array('trash', 'JTRASH'),
            3 => array('trash', 'JTRASH'),
        );
        if(in_array($value, array(-3, 3))){
            return self::trashLink($i, $prefix, $enabled, $checkbox, $attributes);
        }

        return HTMLHelper::link('javascript:void(0);', Text::_($states[$value][1]), array('onclick' => 'return Joomla.listItemTask(\''
            .$checkbox.$i.'\',\''.$prefix.'.'.$states[$value][0].'\')'));
    }
    public static function trashLink($i, $prefix = '', $enabled = true, $checkbox = 'cb', $attributes = array())
    {
        if(!$enabled){
            return false;
        }
        $_attributes2   = array('class' => 'text-danger text-error');
        $_attributes    = array('onclick' => 'return Joomla.listItemTask(\''
            .$checkbox.$i.'\',\''.$prefix.'.trash\')');
        $_attributes2   = array_merge($_attributes2, $attributes);
        $_attributes    = array_merge($_attributes2, $_attributes);

        return HTMLHelper::link('javascript:void(0);', Text::_('JTRASH'), $_attributes);
    }
    public static function deleteLink($i, $prefix = '', $enabled = true, $checkbox = 'cb', $attributes = array())
    {
        if(!$enabled){
            return false;
        }
        $_attributes2   = array('class' => 'text-danger text-error');
        $_attributes    = array('onclick' => 'return listItemTask(\''
            .$checkbox.$i.'\',\''.$prefix.'.delete\')');
        $_attributes2   = array_merge($_attributes2, $attributes);
        $_attributes    = array_merge($_attributes2, $_attributes);

        return HTMLHelper::link('javascript:void(0);', Text::_('COM_TZ_PORTFOLIO_DELETE_PERMANENTLY'), $_attributes);
    }
    public static function approveLink($value, $i, $prefix = '', $enabled = true, $checkbox = 'cb')
    {
        $value  = $value != null?$value:3;
        if(!$enabled){
            return false;
        }

        if(!in_array($value, array(3, 4))){
            return false;
        }

        return HTMLHelper::link('javascript:void(0);', Text::_('COM_TZ_PORTFOLIO_APPROVE'), array('onclick' => 'return Joomla.listItemTask(\''
            . $checkbox . $i . '\',\'' . $prefix . '.approve\')'));

    }
    public static function rejectLink($i, $prefix = '', $enabled = true, $checkbox = 'cb', $publish_up = null, $publish_down = null)
    {
        if(!$enabled){
            return false;
        }

        return HTMLHelper::link('javascript:void(0);', Text::_('COM_TZ_PORTFOLIO_REJECT'), array('onclick' => 'return Joomla.listItemTask(\''
            . $checkbox . $i . '\',\'' . $prefix . '.reject\')', 'class' => 'text-error text-danger'));

    }
    public static function featuredLink($value, $i, $prefix = '', $checkbox = 'cb', $canChange = true)
    {
        $value  = $value != null?$value:0;
        $states = array(
            0 => array('featured', 'COM_TZ_PORTFOLIO_FEATURE'),
            1 => array('unfeatured', 'COM_TZ_PORTFOLIO_UNFEATURE')
        );
        return HTMLHelper::link('javascript:void(0);', Text::_($states[$value][1]), array('onclick' => 'return Joomla.listItemTask(\''
            .$checkbox.$i.'\',\''.$prefix.'.'.$states[$value][0].'\')'));
    }


    /**
     * Returns a checked-out icon
     *
     * @param   integer       $i           The row index.
     * @param   string        $editorName  The name of the editor.
     * @param   string        $time        The time that the object was checked out.
     * @param   string|array  $prefix      An optional task prefix or an array of options
     * @param   boolean       $enabled     True to enable the action.
     * @param   string        $checkbox    An optional prefix for checkboxes.
     *
     * @return  string  The HTML markup
     *
     * @since   1.6
     */
    public static function checkedout($i, $editorName, $time, $prefix = '', $enabled = false, $checkbox = 'cb')
    {
        $html   = HTMLHelper::_('jgrid.checkedout', $i, $editorName, $time, $prefix, $enabled, $checkbox);

        // Replace icon
        if(!preg_match('/<[span|i].*?class=["|\'].*?(fas fa-lock).*?["|\']/', $html) && preg_match('/icon-checkedout/', $html)) {
            $html   = preg_replace('/(<[span|i].*?class=["|\'].*?)(icon-checkedout)(.*?["|\'])/','$1fas fa-lock$3', $html);
        }
        // Add btn-outline-secondary class
        if(!preg_match('/<[a|button].*?class=["|\'].*?(uk-button uk-button-default).*?["|\']/', $html)){
            $html   = preg_replace('/(<[a|button].*?class=["|\'])(.*?["|\'])/','$1uk-button uk-button-default $2', $html);
        }
        // Replace btn-sm class
        if(!preg_match('/<[a|button].*?class=["|\'].*?(uk-button-small).*?["|\']/', $html)){
            $html   = preg_replace('/(<[a|button].*?class=["|\'])(.*?["|\'])/','$1uk-button-small $2', $html);
        }
        return $html;
    }
}