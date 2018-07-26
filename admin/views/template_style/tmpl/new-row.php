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

// No direct access
defined('_JEXEC') or die('Restricted access');

$id         = uniqid(rand());
$parentId   = uniqid(rand());
$items      = $this -> rowItem;

$containerType  = '';
if(isset($items -> containertype)){
    $containerType  = $items -> containertype;
}

$newRowClass    = 'add-row';
$rowClass       = 'layoutmainrow';
if($rowInColumn = $this -> state -> get('template.rowincolumn', false)){
    $rowClass       = 'child-row';
    $newRowClass    = 'add-rowin-column';
}
?>

<?php echo JHtml::_('tzbootstrap.addrow', array('class' => $rowClass.' tpp-sortable'));?>
    <div class="span12 col-md-12">
        <div class="rowpropperties pull-left">
            <span class="rowname"><?php echo $items?$items -> name:''; ?></span>
            <span class="rowdocs">
                <input type="hidden" class="rownameinput" name="" value="<?php echo $this -> get_value($items,"name"); ?>">
                <input type="hidden" class="rowcustomclassinput" name="" value="<?php echo $this -> get_value($items,"class") ?>">
                <input type="hidden" class="rowresponsiveinput" name="" value="<?php echo $this -> get_value($items,"responsive") ?>">

                <input type="hidden" class="rowbackgroundcolorinput" name="" value="<?php echo $this -> get_color($items,'backgroundcolor') ?>">
                <input type="hidden" class="rowtextcolorinput" name="" value="<?php echo $this -> get_color($items,'textcolor') ?>">
                <input type="hidden" class="rowlinkcolorinput" name="" value="<?php echo $this -> get_color($items,'linkcolor') ?>">
                <input type="hidden" class="rowlinkhovercolorinput" name="" value="<?php echo $this -> get_color($items,'linkhovercolor') ?>">
                <input type="hidden" class="rowmargininput" name="" value="<?php echo $this -> get_value($items,'margin') ?>">
                <input type="hidden" class="rowpaddinginput" name="" value="<?php echo $this -> get_value($items,'padding') ?>">
            </span>
        </div>
        <div id="<?php echo $parentId; ?>" class="pull-right row-tools row-container">
            <?php if(!$rowInColumn){ ?>
            <select class="containertype custom-select" name="" aria-invalid="false">
                <option<?php echo ($containerType == '')?' selected=""':''?> value=""><?php echo JText::_('JNONE');?></option>
                <option<?php echo ($containerType == 'container')?' selected=""':''?> value="container"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_FIXED_WIDTH');?></option>
                <option<?php echo ($containerType == 'container-fluid')?' selected=""':''?> value="container-fluid"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_FULL_WIDTH');?></option>
            </select>
            <?php } ?>
            <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_MOVE_THIS_ROW');?>" class="fa fa-arrows <?php
            echo $rowInColumn?'row-move-in-column':'rowmove'; ?>"></a>
            <a href="javascript:" class="accordion-toggle"
               title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_TOGGLE_THIS_ROW');?>"
               data-toggle="collapse" data-parent="#<?php echo $parentId;?>"
               data-target="#<?php echo $id;?>">
                <span class="fa fa-chevron-up"></span><span class="fa fa-chevron-down"></span>
            </a>
            <a href="#rowsettingbox" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ROW_SETTINGS');
            ?>" class="fa fa-cog rowsetting" rel="rowpopover" data-container="#<?php echo $parentId; ?>"></a>
            <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ADD_NEW_ROW');?>" class="fa fa-bars <?php echo $newRowClass; ?>"></a>
            <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ADD_NEW_COLUMN');?>" class="fa fa-columns add-column"></a>
            <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_DELETE_ROW');?>" class="fa fa-times rowdelete"></a>
        </div>
        <div class="hr clr"></div>

        <?php echo JHtml::_('tzbootstrap.addrow', array('attribute' => 'id="'.$id.'"', 'class' => 'show-grid collapse in show tpp-sortable'));?>
            <?php
            //-- Columns --//
            if($items && isset($items -> children)) {
                foreach ($items->children as $item) {
                    $this -> columnItem    = $item;
                    $this->setLayout('new-column');
                    echo $this->loadTemplate();
                }
            }else{
                $this->setLayout('new-column');
                echo $this->loadTemplate();
            }
            //-- End Columns --//
            ?>
        <?php echo JHtml::_('tzbootstrap.endrow');?>

    </div>
<?php echo JHtml::_('tzbootstrap.endrow');?>
