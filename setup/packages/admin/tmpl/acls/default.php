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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$user		= Factory::getUser();
?>
<form name="adminForm" id="adminForm" method="post" action="index.php?option=com_tz_portfolio&view=acls">

<?php echo HTMLHelper::_('tzbootstrap.addrow');?>
    <?php if(!empty($this -> sidebar)){?>
        <div id="j-sidebar-container" class="col-md-2">
            <?php echo $this -> sidebar; ?>
        </div>
    <?php } ?>

    <?php echo HTMLHelper::_('tzbootstrap.startcontainer', '10', !empty($this -> sidebar));?>

        <div class="tpContainer">
            <table class="table" id="groups">
                <thead>
                <tr>
                    <th class="w-1 text-center d-none d-md-table-cell"></th>
                    <th class="nowrap">
                        <?php echo Text::_('COM_TZ_PORTFOLIO_SECTION');?>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php if($items = $this -> items){
                    foreach($items as $i => $item) {
                ?>
                    <tr>
                        <td><?php echo HTMLHelper::_('grid.id', $i, $item -> section, false, 'section'); ?></td>
                        <td>
                            <?php if ($user -> authorise('core.edit', 'com_tz_portfolio')){ ?>
                                <a href="index.php?option=com_tz_portfolio&task=acl.edit&section=<?php echo $item -> section;?>">
                                    <?php echo $this->escape($item -> title);?>
                                </a>
                            <?php }else{ ?>
                                <?php echo $this->escape($item->title); ?>
                            <?php } ?>
                        </td>
                    </tr>
                <?php
                    }
                } ?>
                </tbody>

            </table>
        </div>
    <?php echo HTMLHelper::_('tzbootstrap.endcontainer');?>
<?php echo HTMLHelper::_('tzbootstrap.endrow');?>

    <input type="hidden" value="" name="task">
    <input type="hidden" value="0" name="boxchecked">
    <?php echo HTMLHelper::_('form.token');?>
</form>
