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

//no direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');

$form   = $this -> form;

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "tag.cancel" || document.formvalidator.isValid(document.getElementById("tag-form")))
		{
			' . $form->getField("description")->save() . '
			Joomla.submitform(task, document.getElementById("tag-form"));
		}
	};
');
?>

<form name="adminForm" method="post" class="form-validate tpArticle" id="tag-form"
      action="index.php?option=com_tz_portfolio_plus&view=tag&layout=edit&id=<?php echo $this -> item -> id?>">

    <div class="form-horizontal">
        <fieldset class="adminform">

            <?php
            $article_assign = $form -> getField('articles_assignment');
            ?>
            <ul class="nav nav-tabs">
                <li class="active"><a href="#details" data-toggle="tab"><?php echo JText::_('JDETAILS');?></a></li>
                <li><a href="#articles_assignment" data-toggle="tab"><?php echo JText::_($article_assign -> getAttribute('label'));?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="details">
                    <?php echo $this -> form -> renderField('title');?>
                    <?php echo $this -> form -> renderField('alias');?>
                    <?php echo $this -> form -> renderField('published');?>
                    <?php echo $this -> form -> renderField('id');?>
                    <?php echo $this -> form -> renderField('description');?>
                </div>

                <div class="tab-pane assignment" id="articles_assignment">
                    <?php echo $form->getInput('articles_assignment'); ?>
                </div>
            </div>
        </fieldset>

    </div>

    <input type="hidden" value="" name="task">
    <?php echo JHTML::_('form.token');?>
</form>