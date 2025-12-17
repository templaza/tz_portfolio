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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$html = "";
if ($options)
{
    $number_columns = $this->params->get("number_columns", 0);
?>
<fieldset id="<?php echo $this -> getId()?>" class="checkboxes <?php echo $this -> getInputClass(); ?>">
    <?php if($number_columns){ ?>
    <ul class='nav'>
    <?php }?>

    <?php foreach ($options AS $key => $option){
        $optText        = null;
        $optValue       = null;
        $optDisabled    = null;

        if(is_object($option)){
            $optText    = $option -> text;
            $optValue   = $option -> value;
            if ((isset($option->disabled) && $option->disabled))
            {
                $optDisabled    = $option -> disabled;
            }
        }else{
            $optText    = $option['text'];
            $optValue   = $option['value'];
            if ((isset($option['disabled']) && $option['disabled']))
            {
                $optDisabled    = $option['disabled'];
            }
        }
        ?>

        <?php if($number_columns){ ?>
        <?php
            $width = 100 / (int) $number_columns;
        ?>
        <li style="width: <?php echo $width; ?>%; float: left; clear: none;">
        <?php }?>
            <?php
            if ($optText == strtoupper($optText))
            {
                $text = JText::_($optText);
            }
            else
            {
                $text = $optText;
            }

            $this->setAttribute("value", htmlspecialchars($optValue, ENT_COMPAT, 'UTF-8'), "input");
            $this -> setAttribute("class", "uk-checkbox", "input");

            if (in_array($optValue, $value))
            {
                $this->setAttribute("checked", "checked", "input");
            }
            else
            {
                $this->setAttribute("checked", null, "input");
            }

            if ((isset($optDisabled) && $optDisabled))
            {
                $this->setAttribute("disabled", "disabled", "input");
            }
            else
            {
                $this->setAttribute("disabled", null, "input");
            }
            ?>
            <?php ?>
            <div class="form-check form-check-inline">
                <label for="<?php echo $this -> getId().$key; ?>" class="form-check-label">
                    <input id="<?php echo $this -> getId().$key; ?>" name="<?php echo $this -> getName();?>" <?php
                    echo $this -> getAttribute(null, null, "input"); ?>/><?php echo ' '.$text; ?></label>
            </div>
        <?php if($number_columns){ ?>
        </li>
        <?php }?>
    <?php } ?>

    <?php if($number_columns){ ?>
    </ul>
    <?php } ?>
</fieldset>
<?php
}