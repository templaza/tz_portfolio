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

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
tzportfolioplusimport('fields.extrafield');

abstract class JHtmlFields
{
    public static function options($arr, $optKey = 'value', $optText = 'text', $selected = null){
        if(!$arr) {
            $arr = self::_getFieldTypes();
        }
        return JHtml::_('select.options', $arr, $optKey, $optText, $selected);
    }

    protected static function _getFieldTypes(){
        $data       = array();
        $core_path  = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.DIRECTORY_SEPARATOR.'extrafields';
        if(JFolder::exists($core_path)){
            if($core_folders = JFolder::folders($core_path)){
                $lang   = JFactory::getLanguage();

                foreach($core_folders as $i => $folder){

                    $core_f_xml_path    = $core_path.DIRECTORY_SEPARATOR.$folder
                        .DIRECTORY_SEPARATOR.$folder.'.xml';
                    if(JFile::exists($core_f_xml_path)){
                        $core_class         = 'TZ_Portfolio_PlusExtraField'.ucfirst($folder);
                        if(!class_exists($core_class)){
                            JLoader::import('com_tz_portfolio_plus.addons.extrafields.'.$folder.'.'.$folder,
                                JPATH_SITE.DIRECTORY_SEPARATOR.'components');
                        }
                        $core_class         = new $core_class($folder);

                        $data[$i]           = new stdClass();
                        $data[$i] -> value  = $folder;
                        $core_class -> loadLanguage($folder);
                        $key_lang           = 'PLG_EXTRAFIELDS_'.strtoupper($folder).'_TITLE';
                        if($lang ->hasKey($key_lang)) {
                            $data[$i]->text = JText::_($key_lang);
                        }else{
                            $data[$i]->text = (string)$folder;
                        }
                    }
                }
            }
        }
        return $data;
    }

    public static function action($i, $task, $prefix = '', $text = '', $active_title = '', $inactive_title = '', $tip = false, $active_class = '',
                                  $inactive_class = '', $enabled = true, $translate = true, $checkbox = 'cb')
    {
        if (is_array($prefix))
        {
            $options = $prefix;
            $active_title = array_key_exists('active_title', $options) ? $options['active_title'] : $active_title;
            $inactive_title = array_key_exists('inactive_title', $options) ? $options['inactive_title'] : $inactive_title;
            $tip = array_key_exists('tip', $options) ? $options['tip'] : $tip;
            $active_class = array_key_exists('active_class', $options) ? $options['active_class'] : $active_class;
            $inactive_class = array_key_exists('inactive_class', $options) ? $options['inactive_class'] : $inactive_class;
            $enabled = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
            $translate = array_key_exists('translate', $options) ? $options['translate'] : $translate;
            $checkbox = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
            $prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
        }

        if ($tip)
        {
            JHtml::_('bootstrap.tooltip');

            $title = $enabled ? $active_title : $inactive_title;
            $title = $translate ? JText::_($title) : $title;
            $title = JHtml::tooltipText($title, '', 0);
        }

        if ($enabled)
        {
            $html[] = '<a class="btn btn-micro' . ($active_class == 'publish' ? ' active' : '') . ($tip ? ' hasTooltip' : '') . '"';
            $html[] = ' href="javascript:void(0);" onclick="return listItemTask(\'' . $checkbox . $i . '\',\'' . $prefix . $task . '\')"';
            $html[] = $tip ? ' title="' . $title . '"' : '';
            $html[] = '>';
            $html[] = '<span class="icon-' . $active_class . '"></span>';
            $html[] = '</a>';
        }
        else
        {
            $html[] = '<a class="btn btn-micro disabled jgrid' . ($tip ? ' hasTooltip' : '') . '"';
            $html[] = $tip ? ' title="' . $title . '"' : '';
            $html[] = '>';

            if ($active_class == "protected")
            {
                $html[] = '<span class="icon-lock"></span>';
            }
            else
            {
                $html[] = '<span class="icon-' . $inactive_class . '"></span>';
            }

            $html[] = '</a>';
        }

        return implode($html);
    }
}
 
