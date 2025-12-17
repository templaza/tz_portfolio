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

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

$class  = '';
$item   = $this -> columnItem;
$columnToolId   = 'columntools-'.uniqid(rand());

if($item){
    if($this -> get_value($item,"type")=='component' or $this -> get_value($item,"type")=='message'){
        $class  .= 'type-'.$this -> get_value($item,"type");
    }
    if($this -> get_value($item,"col-lg")){
//        $class  .=  ' span'.$this -> get_value($item,"col-lg");
        $class  .=  ' col-md-'.$this -> get_value($item,"col-lg");
    }
    if(!empty($item->{"col-lg-offset"})){
        $class  .= ' offset'.$item ->{"col-lg-offset"};
    }
}
?>

<div class="column <?php echo $class; ?>">

    <span class="position-name"><?php echo $item?$this -> get_value($item,"type"):Text::_('JNONE'); ?></span>
    <div id="<?php echo $columnToolId; ?>" class="columntools">
        <a href="#columnsettingbox" rel="popover" data-placement="bottom" data-container="#<?php
        echo $columnToolId; ?>" data-bs-placement="bottom" data-bs-container="#<?php
        echo $columnToolId; ?>"
           title="<?php echo Text::_('COM_TZ_PORTFOLIO_COLUMN_SETTINGS');?>" class="fas fa-cog rowcolumnspop"></a>
        <a href="" title="<?php echo Text::_('COM_TZ_PORTFOLIO_ADD_NEW_ROW');?>" class="fas fa-bars add-rowin-column"></a>
        <a href="" title="<?php echo Text::_('COM_TZ_PORTFOLIO_REMOVE_COLUMN');?>" class="fas fa-times columndelete"></a>
        <a href="" title="<?php echo Text::_('COM_TZ_PORTFOLIO_MOVE_COLUMN');?>" class="fas fa-arrows-alt columnmove"></a>
    </div>

    <input type="hidden" class="widthinput-xs" name="" value="<?php echo $this -> get_value($item,"col-xs") ?>">
    <input type="hidden" class="widthinput-sm" name="" value="<?php echo $this -> get_value($item,"col-sm") ?>">
    <input type="hidden" class="widthinput-md" name="" value="<?php echo $this -> get_value($item,"col-md") ?>">
    <input type="hidden" class="widthinput-lg" name="" value="<?php echo $this -> get_value($item,"col-lg") ?>">
    <input type="hidden" class="offsetinput-xs" name="" value="<?php echo $this -> get_value($item,"col-xs-offset") ?>">
    <input type="hidden" class="offsetinput-sm" name="" value="<?php echo $this -> get_value($item,"col-sm-offset") ?>">
    <input type="hidden" class="offsetinput-md" name="" value="<?php echo $this -> get_value($item,"col-md-offset") ?>">
    <input type="hidden" class="offsetinput-lg" name="" value="<?php echo $this -> get_value($item,"col-lg-offset") ?>">
    <input type="hidden" class="typeinput" name="" value="<?php echo $this -> get_value($item,"type") ?>">
    <input type="hidden" class="customclassinput" name="" value="<?php echo $this -> get_value($item,"customclass") ?>">
    <input type="hidden" class="responsiveclassinput" name="" value="<?php echo $this -> get_value($item,"responsiveclass") ?>">
    <?php
    if( $item && !empty($item -> children) and is_array($item -> children) ){
        $this -> state -> set('template.rowincolumn', true);

        foreach($item -> children as $children) {
            $this->rowItem = $children;
            $this->setLayout('new-row');
            echo $this->loadTemplate();
        }
    }
    ?>
</div>