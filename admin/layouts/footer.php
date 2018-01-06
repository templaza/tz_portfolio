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
$doc -> addScript(TZ_Portfolio_PlusUri::base(true, true).'/js/script.min.js');

$xml	= simplexml_load_file(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.'/tz_portfolio_plus.xml');
ob_start();
?>
<script type="text/html" id="tmpl-tpPortfolio-footer">
    <div class="tpFooter muted">
        <div class="row-fluid">
            <div class="span5"><?php echo $xml->copyright; ?></div>
            <div class="span7">
                <ul class="tpLinks inline unstyled">
                    <li><a href="<?php echo $xml -> guideUrl; ?>" target="_blank">Guide</a></li>
                    <li><a href="<?php echo $xml -> forumUrl; ?>" target="_blank">Forum</a></li>
                    <li><a href="<?php echo $xml -> transifexUrl; ?>" target="_blank">Find & Help Translate</a></li>
                    <li><a href="<?php echo $xml -> jedUrl; ?>" target="_blank"><span class="icon-joomla"></span> Rate on JED</a></li>
                </ul>
            </div>
        </div>
    </div>
</script>
<?php
$script = ob_get_contents();
ob_end_clean();
$doc -> addCustomTag($script);