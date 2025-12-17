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

use Joomla\CMS\Factory;

$defLayout = $this -> getLayout();
?>
<div class="generator">
    <?php
    if($this -> tzlayout){
        $input  = Factory::getApplication() -> input;
        foreach($this -> tzlayout as $items )
        {
            $containerType  = '';
            if(isset($items -> containertype)){
                $containerType  = $items -> containertype;
            }
            $parentId   = uniqid(rand());
            $id         = uniqid(rand());

            $this -> state -> set('template.rowincolumn', false);
            $this -> rowItem  = $items;
            $this -> setLayout('new-row');
            echo $this -> loadTemplate();
        }
    }
    ?>

</div>
<?php
$this -> setLayout($defLayout);