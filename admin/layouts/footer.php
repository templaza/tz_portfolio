<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2018 tzportfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - http://tzportfolio.com/forum

# Family website: http://www.templaza.com

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

$doc    = JFactory::getDocument();
$doc -> addScript(TZ_Portfolio_PlusUri::base(true, true).'/js/script.min.js',
    array('version' => 'auto', 'relative' => true));

$xml	= simplexml_load_file(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.'/tz_portfolio_plus.xml');
ob_start();
?>
<script type="text/html" id="tmpl-tpPortfolio-footer">
    <div class="tpFooter muted<?php if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE){ echo ' container-fluid'; }?>">
        <?php echo JHtml::_('tzbootstrap.addrow');?>
            <div class="span5 col-md-5"><?php echo $xml->copyright; ?></div>
            <div class="span7 col-md-7">
                <ul class="tpLinks inline unstyled list-unstyled">
                    <li class="list-inline-item"><a href="<?php echo $xml -> guideUrl; ?>" target="_blank">Guide</a></li>
                    <li class="list-inline-item"><a href="<?php echo $xml -> forumUrl; ?>" target="_blank">Forum</a></li>
                    <li class="list-inline-item"><a href="<?php echo $xml -> transifexUrl; ?>" target="_blank">Find & Help Translate</a></li>
                    <li class="list-inline-item"><a href="<?php echo $xml -> jedUrl; ?>" target="_blank"><span class="icon-joomla"></span> Rate on JED</a></li>
                </ul>
            </div>
        <?php echo JHtml::_('tzbootstrap.endrow');?>
    </div>
</script>
<?php
$script = ob_get_contents();
ob_end_clean();
$doc -> addCustomTag($script);