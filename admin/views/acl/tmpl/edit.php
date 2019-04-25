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

$user		= JFactory::getUser();
$document   = JFactory::getDocument();
?>
<form name="adminForm" id="adminForm" method="post" action="<?php
    echo JRoute::_('index.php?option=com_tz_portfolio_plus&view=acl&layout=edit&section='
    .$this->state->get('acl.section')); ?>">
    <div class="tpContainer ">
        <div class="control-group">
            <?php echo $this->form->getInput('rules'); ?>
        </div>

        <?php
        if(version_compare(JVERSION, '4.0', '>=')) {
            $scriptOptions  = $document -> getScriptOptions('webcomponents');
            $scriptOptions = array_map (function($value){
                if(strpos($value, JUri::root(true).'/media/system/js/fields/joomla-field-permissions.js') !== false){
                    return JUri::base(true).'/components/com_tz_portfolio_plus/js/tpp-field-permissions.min.js';
                }
                return $value;
            },$scriptOptions );

            $document->addScriptOptions('webcomponents', $scriptOptions, false);
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
