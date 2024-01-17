<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2017 tzportfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - http://tzportfolio.com/forum

# Family website: http://www.templaza.com

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

$user		= Factory::getUser();
$document   = Factory::getApplication() -> getDocument();
?>
<form name="adminForm" id="adminForm" method="post" action="<?php
echo JRoute::_('index.php?option=com_tz_portfolio_plus&view=acl&layout=edit&section='
    .$this->state->get('acl.section')); ?>">
    <div class="tpContainer ">
        <?php if(!COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE){ ?>
        <div class="control-group">
            <?php }?>
            <?php echo $this->form->getInput('rules'); ?>
            <?php if(!COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE){ ?>
        </div>
    <?php }?>

        <?php
        if(version_compare(JVERSION, '4.0', '>=')) {
            $wa = $document->getWebAssetManager();

            /* Remove joomla-field-permissions javascript file link */
            $wa->disableAsset("script", "webcomponent.field-permissions");

            /* Add my field-permissions javascript file link */
            $wa -> registerAndUseScript('com_tz_portfolio_plus.field-permissions',
                Uri::base().'/components/com_tz_portfolio_plus/js/tpp-field-permissions.min.js',
                ['version' => 'auto', 'relative' => false], ['defer' => true]);
        }else{
            $document -> addScriptVersion(JUri::base(true).'/components/com_tz_portfolio_plus/js/tpp-field-permissions.min.js', array('version' => 'auto'));
        }
        ?>

        <?php echo $this->form->getInput('title'); ?>
        <?php echo $this->form->getInput('section'); ?>
        <input type="hidden" value="" name="task">
        <input type="hidden" value="com_tz_portfolio_plus" name="comp">
        <input type="hidden" value="0" name="boxchecked">
        <?php echo JHTML::_('form.token');?>
    </div>
</form>
