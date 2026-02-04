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

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\CheckboxesField;
use Joomla\CMS\HTML\HTMLHelper;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

class CategoriesAssignmentField extends CheckboxesField
{
    protected $type = 'CategoriesAssignment';
    protected $layout = 'form.field.categoriesassignment';

    protected function getOptions()
    {
        $options    = array();
        $db         = Factory::getDbo();
        $query      = $db -> getQuery(true);

        $query -> select('c.title AS text, c.id AS value,c.template_id, c.level');
        $query -> from('#__tz_portfolio_plus_categories AS c');
        $query -> where('(extension = '.$db -> quote('com_tz_portfolio')
            .' OR extension='.$db -> quote('com_tz_portfolio_plus').')');
        $query -> order('c.lft');

        $db -> setQuery($query);
        if($rows = $db -> loadObjectList()){
            foreach($rows as $option){
                $tmp = HTMLHelper::_('select.option', (string) $option -> value, trim($option -> text), 'value', 'text');

                $checked    = false;
                $app    = Factory::getApplication();
                $input  = $app -> input;
                $curTemplateId  = null;

                if(!isset($this -> element['template_id'])){
                    if($input -> get('option') == 'com_tz_portfolio' && $input -> get('view') == 'template_style'){
                        $curTemplateId  = $input -> get('id');
                    }
                }else{
                    $curTemplateId  = $this -> element['template_id'];
                }

                if(isset($option -> template_id) && $option -> template_id && !empty($option -> template_id)){
                    if($option -> template_id == $curTemplateId){
                        $checked    = true;
                    }
                }

                $checked = ($checked == 'true' || $checked == 'checked' || $checked == '1');

                // Set some option attributes.
                $tmp->checked = $checked;

                $tmp -> level   = $option -> level;

                // Add the option object to the result set.
                $options[] = $tmp;
            }
        }

        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
?>