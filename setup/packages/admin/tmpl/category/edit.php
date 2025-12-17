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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

$assoc      = Associations::isEnabled();
// Are associations implemented for this extension?
$extensionassoc = array_key_exists('item_associations', $this->form->getFieldsets());

?>

<form method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal tpArticle" enctype="multipart/form-data"
	action="<?php echo Route::_('index.php?option=com_tz_portfolio&extension='
        .Factory::getApplication()->input->getCmd('extension', 'com_tz_portfolio').'&layout=edit&id='
        .(int) $this->item->id); ?>">
    <?php echo HTMLHelper::_('tzbootstrap.addrow');?>
        <div class="col-md-8">

            <div class="main-card">
                <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'details')); ?>

                    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('JDETAILS', true)); ?>
                    <?php echo HTMLHelper::_('tzbootstrap.addrow');?>
                        <div class="col-md-6">
                            <?php echo $this -> form -> renderField('title');?>
                            <?php echo $this -> form -> renderField('alias');?>
                            <?php echo $this -> form -> renderField('groupid');?>
                            <?php echo $this -> form -> renderField('images');?>
                            <?php echo $this -> form -> renderField('parent_id');?>
                            <?php echo $this -> form -> renderField('template_id');?>
                        </div>
                        <div class="col-md-6">
                            <div class="control-group">
                                <div class="control-label max-width-180">
                                    <?php echo $this->form->getLabel('inheritFrom','params'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('inheritFrom','params'); ?>
                                </div>
                            </div>
                            <?php echo $this -> form -> renderField('published');?>
                            <?php echo $this -> form -> renderField('access');?>
                            <?php echo $this -> form -> renderField('language');?>
                            <?php echo $this -> form -> renderField('id');?>
                        </div>
                    <?php echo HTMLHelper::_('tzbootstrap.endrow');?>

                    <?php echo $this -> form -> renderField('description');?>
                    <?php echo $this -> form -> renderField('extension');?>

                    <?php echo HTMLHelper::_('uitab.endTab'); ?>

                    <?php if ($assoc && $extensionassoc) : ?>
                    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'associations', Text::_('JGLOBAL_FIELDSET_ASSOCIATIONS', true)); ?>
                    <?php echo $this->loadTemplate('associations'); ?>
                    <?php echo HTMLHelper::_('uitab.endTab'); ?>
                    <?php endif;?>

                    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'metadata', Text::_('JGLOBAL_FIELDSET_METADATA_OPTIONS', true)); ?>
                    <?php echo $this->loadTemplate('metadata'); ?>
                    <?php echo HTMLHelper::_('uitab.endTab'); ?>

                    <?php if ($this->canDo->get('core.admin')): ?>
                        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'permissions', Text::_('JGLOBAL_ACTION_PERMISSIONS_LABEL', true)); ?>
                        <?php echo $this->form->getInput('rules'); ?>
                        <?php echo HTMLHelper::_('uitab.endTab'); ?>
                    <?php endif; ?>

                <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
            </div>
        </div>

        <div class="col-md-4">
            <?php echo $this->loadTemplate('options'); ?>
        </div>
    <?php echo HTMLHelper::_('tzbootstrap.endrow');?>
    <input type="hidden" name="task" value="" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
