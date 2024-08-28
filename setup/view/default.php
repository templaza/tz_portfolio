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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

//$doc =  Factory::getApplication()->getDocument();
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa =  Factory::getApplication()->getDocument()
    -> getWebAssetManager();
$wa ->useStyle('fontawesome')
    ->useStyle('switcher')
    ->useStyle('template.atum.ltr');
$wa -> useScript('core')
    ->useScript('webcomponent.toolbar-button');

//var_dump($wa -> getAsset('style','switcher') -> getUri());
//die(__FILE__);
?>
<!DOCTYPE html>
<html class="demo-mobile-horizontal" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?php echo COM_TZ_PORTFOLIO_SETUP_URL;?>/assets/images/logo.png" rel="shortcut icon" type="image/vnd.microsoft.icon"/>

    <link type="text/css" href="<?php echo $wa -> getAsset('style','switcher') -> getUri(); ?>" rel="stylesheet"/>
    <link type="text/css" href="<?php echo $wa -> getAsset('style','fontawesome') -> getUri(); ?>" rel="stylesheet"/>
    <link type="text/css" href="<?php echo $wa -> getAsset('style','template.atum.ltr') -> getUri(); ?>" rel="stylesheet"/>

    <?php if($active == 'complete'){ ?>
        <link type="text/css" href="<?php echo Uri::base(true);
        ?>/components/com_tz_portfolio/css/style.min.css?<?php echo COM_TZ_PORTFOLIO_SETUP_HASH; ?>" rel="stylesheet" />
    <?php } ?>
    <link type="text/css" href="<?php echo COM_TZ_PORTFOLIO_SETUP_URL;?>/assets/css/style.min.css?<?php
    echo COM_TZ_PORTFOLIO_SETUP_HASH; ?>" rel="stylesheet" />

    <script src="<?php echo $wa -> getAsset('script', 'core') -> getUri();?>"></script>
    <script src="<?php echo $wa -> getAsset('script', 'jquery') -> getUri();?>"></script>
    <script src="<?php echo $wa -> getAsset('script', 'webcomponent.toolbar-button') -> getUri();?>"></script>

    <script type="text/javascript">
        <?php
        // The $lang variable to use in script.js file
        $lang   = Factory::getApplication('administrator') -> getLanguage();
        require(COM_TZ_PORTFOLIO_SETUP_PATH.'/assets/js/script.js'); ?>
    </script>
</head>

<body class="step<?php echo $active;?>">

<div class="tpp-installation<?php echo (JVERSION >= 4.0)?' is-joomla-4':'';?>">
    <div class="head text-center">
        <div class="container-fluid">
            <div class="top-bar d-flex">
                <div>
                    <img src="<?php echo COM_TZ_PORTFOLIO_SETUP_URL;?>/assets/images/logo.png" height="48" width="48" />
                </div>
                <div class="text-left">
                    <h3><?php echo Text::_('COM_TZ_PORTFOLIO');?></h3>
                    <span><?php echo Text::_('COM_TZ_PORTFOLIO_DESCRIPTION_3');?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <ul class="step-indicator" data-installation-steps>
                <?php include(__DIR__ . '/default_steps.php'); ?>
            </ul>
            <div class="installation-methods">
                <?php include(__DIR__ . '/steps/' . $activeStep->template . '.php'); ?>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="container-fluid">
            <?php include(__DIR__ . '/default_footer.php'); ?>
        </div>
    </div>
</div>
</body>
</html>
