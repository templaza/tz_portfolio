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

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

if($fieldGroups = $this -> extraFields){
?>

<div class="form-horizontal">
    <?php
    echo HTMLHelper::_('bootstrap.startAccordion', 'fieldGroupAdditionalAccordion',
        array('active' => 'fieldGroupCollapse0', 'parent' => true));?>
    <?php
        foreach($fieldGroups as $i => $group) {
            ?>
            <?php
            if ($fields = $group->fields) {
                if(count($fields)) {
                    // Start accordion
                    echo HTMLHelper::_('bootstrap.addSlide', 'fieldGroupAdditionalAccordion', ucwords($group -> name), 'fieldGroupCollapse' . $i);
                    foreach ($fields as $field) {
                        if(!empty($field)){
                        ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $field->getLabel(); ?></div>
                            <div class="controls"><?php echo $field->getInput(); ?></div>
                        </div>
                    <?php }
                    }
                    echo HTMLHelper::_('bootstrap.endSlide');
                }
            }
        }
    echo HTMLHelper::_('bootstrap.endAccordion');
    ?>
</div>
<?php
}else{
?>
    <div id="system-message-container"><div id="system-message">
            <div class="alert alert-warning">
                <h4 class="alert-heading"><?php echo Text::_('WARNING');?></h4>
                <div>
                    <p><?php echo Text::_('COM_TZ_PORTFOLIO_FIELD_GROUP_DESC');?></p>
                </div>
            </div>
        </div>
    </div>
<?php
}