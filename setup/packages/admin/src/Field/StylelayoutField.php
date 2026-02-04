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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Field;

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Form\Form;
use Joomla\Filesystem\Folder;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;
use Joomla\Database\DatabaseInterface;

class StyleLayoutField extends ListField
{
    protected $type = 'StyleLayout';
    protected $module;

    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        $return = parent::setup($element, $value, $group);

        if ($return)
        {
            $module = (string) $this->element['module'];

            if(!empty($module)){
                if($module == 'true' || $module = 1){
                    if($this->form instanceof Form)
                    {
                        $module = $this->form->getValue('module');
                    }
                }
                $this -> module = $module;
            }
        }

        $lang   = Factory::getApplication() -> getLanguage();
        $lang -> load('com_tz_portfolio', JPATH_ADMINISTRATOR);

        return $return;
    }

    protected function getOptions()
    {
        $options = array();

        $db     = Factory::getContainer()->get(DatabaseInterface::class);
        $query  = $db -> getQuery(true);

        $query -> select('t.*');
        $query -> from('#__tz_portfolio_plus_templates AS t');
        $query -> where('NOT t.template =""');

        /**
         * Filter by style from styles directory
         * @deprecated Will be removed when TZ Portfolio Plus wasn't supported
         */
        if (file_exists(COM_TZ_PORTFOLIO_STYLE_PATH)) {
            $filter_styles  = Folder::folders(COM_TZ_PORTFOLIO_STYLE_PATH);
            if(!empty($filter_styles)){
                $filter_styles  = array_map(function($value) use($db){
                    return $db -> quote($value);
                }, $filter_styles);
                $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e ON e.element = t.template')
                    -> where('e.published = 1')
                    ->where('(e.type=' . $db->quote('tz_portfolio-style').' OR e.type = '
                        .$db -> quote('tz_portfolio_plus-template').')');
                $query -> where('e.element IN('.implode(',', $filter_styles).')');
            }
        }

        $db -> setQuery($query);

        if($items = $db -> loadObjectList()){
            foreach($items as $i => $item){
                $options[$i] = new \stdClass();
                $options[$i] -> text    = $item -> title;
                $options[$i] -> value   = $item -> id;
            }
        }

        return array_merge(parent::getOptions(), $options);
    }
}