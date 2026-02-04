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

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

$form   = $this -> form;
?>

<form name="adminForm" method="post" class="form-validate tpArticle" id="adminForm"
      action="index.php?option=com_tz_portfolio&view=tag&layout=edit&id=<?php echo $this -> item -> id?>">

    <div class="form-horizontal">
        <div class="main-card">
            <fieldset class="adminform">
                <?php
                $article_assign = $form -> getField('articles_assignment');
                ?>

                <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'details')); ?>

                    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('JDETAILS', true)); ?>
                        <?php echo $this -> form -> renderField('title');?>
                        <?php echo $this -> form -> renderField('alias');?>
                        <?php echo $this -> form -> renderField('published');?>
                        <?php echo $this -> form -> renderField('id');?>
                        <?php echo $this -> form -> renderField('description');?>
                    <?php echo HTMLHelper::_('uitab.endtab'); ?>

                    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'articles_assignment',
                        Text::_($article_assign -> getAttribute('label'), true)); ?>
                        <?php echo $form->getInput('articles_assignment'); ?>
                    <?php echo HTMLHelper::_('uitab.endtab'); ?>

                <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
            </fieldset>

        </div>
    </div>

    <input type="hidden" value="" name="task">
    <?php echo HTMLHelper::_('form.token');?>
</form>