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

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

$form   = $this -> form;
?>
<form name="adminForm" method="post" class="form-validate tpArticle" id="adminForm"
      action="index.php?option=com_tz_portfolio&view=group&layout=edit&id=<?php echo (int) $this -> item -> id?>">
    <?php echo HTMLHelper::_('tzbootstrap.addrow');?>
        <div class="col-md-8 form-horizontal">
            <div class="main-card">
                <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'details')); ?>

                    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('JDETAILS', true)); ?>
                        <?php echo HTMLHelper::_('tzbootstrap.addrow');?>
                            <div class="col-md-6">
                                <?php echo $this -> form -> renderField('title');?>
                                <?php echo $this -> form -> renderField('field_ordering_type');?>
                            </div>
                            <div class="col-md-6">
                                <?php echo $this -> form -> renderField('published');?>
                                <?php echo $this -> form -> renderField('access');?>
                            </div>
                        <?php echo HTMLHelper::_('tzbootstrap.endrow');?>

                        <div class="form-vertical form-no-margin">
                        <?php echo $this -> form -> renderField('description');?>
                        </div>
                    <?php echo HTMLHelper::_('uitab.endTab'); ?>

                    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'categories_assignment', Text::_('COM_TZ_PORTFOLIO_CATEGORIES_ASSIGNMENT', true)); ?>
                    <?php echo $form->getInput('categories_assignment'); ?>
                    <?php echo HTMLHelper::_('uitab.endTab'); ?>

                    <?php if ($this->canDo->get('core.admin')) : ?>
                        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'permissions', Text::_('JCONFIG_PERMISSIONS_LABEL')); ?>
                        <?php echo $this->form->getInput('rules'); ?>
                        <?php echo HTMLHelper::_('uitab.endTab'); ?>
                    <?php endif; ?>

                <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
            </div>
        </div>

        <div class="col-md-4 form-vertical">
        <?php echo HTMLHelper::_('bootstrap.startAccordion', 'groupOptions', array('active' => 'collapse0'
        , 'parent' => true));?>
            <?php echo HTMLHelper::_('bootstrap.addSlide', 'groupOptions', Text::_('JGLOBAL_FIELDSET_PUBLISHING'), 'collapse0'); ?>

                <?php echo $this -> form -> renderField('created');?>
                <?php echo $this -> form -> renderField('created_by');?>
                <?php if ($this->item && $this->item->modified_by){ ?>
                    <?php echo $this -> form -> renderField('modified_by');?>
                    <?php echo $this -> form -> renderField('modified');?>
                <?php } ?>
                <?php echo $this -> form -> renderField('id');?>

            <?php echo HTMLHelper::_('bootstrap.endSlide');?>
        <?php echo HTMLHelper::_('bootstrap.endAccordion');?>
        </div>
    <?php echo HTMLHelper::_('tzbootstrap.endrow');?>

    <input type="hidden" value="" name="task">
    <?php echo HTMLHelper::_('form.token');?>

</form>