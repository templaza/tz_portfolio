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

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;

$user   = Factory::getUser();
$doc    = Factory::getApplication() -> getDocument();
$wa     = $doc -> getWebAssetManager();

$wa -> useScript('com_tz_portfolio.admin-script');

$xml	= TZ_PortfolioHelper::getXMLManifest();

ob_start();
$date   = Factory::getDate();
?>
    <script type="text/html" id="tmpl-tpPortfolio-footer">
        <div class="tpFooter muted">
            <?php echo HTMLHelper::_('tzbootstrap.addrow');?>
            <div class="span5 col-md-5"><?php echo Text::sprintf('COM_TZ_PORTFOLIO_COPYRIGHT_FOOTER', $date ->year); ?></div>
            <?php if ($user->authorise('core.manage', 'com_installer')) { ?>
                <div class="span7 col-md-7">
                    <ul class="tpLinks inline unstyled list-unstyled">
                        <li class="list-inline-item"><a href="<?php echo $xml -> guideUrl; ?>" target="_blank"><i class="fas fa-book"></i> <?php echo Text::_('COM_TZ_PORTFOLIO_GUIDE'); ?></a></li>
                        <li class="list-inline-item"><a href="<?php echo $xml -> forumUrl; ?>" target="_blank"><i class="fas fa-comment"></i> <?php echo Text::_('COM_TZ_PORTFOLIO_FORUM'); ?></a></li>
                        <li class="list-inline-item"><a href="<?php echo $xml -> transifexUrl; ?>" target="_blank"><span class="fas fa-language"></span> <?php echo Text::_('COM_TZ_PORTFOLIO_FIND_HELP_TRANSLATE'); ?></a></li>
                        <li class="list-inline-item"><a href="<?php echo $xml -> jedUrl; ?>" target="_blank"><span class="fab fa-joomla"></span> <?php echo Text::_('COM_TZ_PORTFOLIO_RATE_ON_JED'); ?></a></li>
                    </ul>
                </div>
            <?php } ?>
            <?php echo HTMLHelper::_('tzbootstrap.endrow');?>
        </div>
    </script>
<?php
$script = ob_get_contents();
ob_end_clean();
$doc -> addCustomTag($script);
