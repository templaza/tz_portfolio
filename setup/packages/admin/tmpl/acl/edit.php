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

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$user		= Factory::getUser();
$document   = Factory::getApplication() -> getDocument();
?>
<form name="adminForm" id="adminForm" method="post" action="<?php
echo Route::_('index.php?option=com_tz_portfolio&view=acl&layout=edit&section='
    .$this->state->get('acl.section')); ?>">
        <div class="main-card">

            <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'general', 'recall' => true, 'breakpoint' => 768]); ?>

                <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'general', Text::_('JDETAILS')); ?>
                <?php echo $this->form->getInput('rules'); ?>

                <?php
                /* @var \Joomla\CMS\WebAsset\WebAssetManager $wa*/
                    $wa = $document->getWebAssetManager();

                    /* Remove joomla-field-permissions javascript file link */
                    $wa->disableAsset("script", "webcomponent.field-permissions");

                    /* Add my field-permissions javascript file link */
                    $wa -> useScript('com_tz_portfolio.field-permissions');
                ?>

                <?php echo $this->form->getInput('title'); ?>
                <?php echo $this->form->getInput('section'); ?>

                <?php echo HTMLHelper::_('uitab.endTab'); ?>
            <?php echo HTMLHelper::_('uitab.endTabSet'); ?>

            <input type="hidden" value="" name="task">
            <input type="hidden" value="com_tz_portfolio" name="comp">
            <input type="hidden" value="0" name="boxchecked">
            <?php echo HTMLHelper::_('form.token');?>
        </div>
</form>
