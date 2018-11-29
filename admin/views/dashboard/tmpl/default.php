<?php
/**
 * @copyright	Copyright (c) 2017 TemPlaza.com (http://tzportfolio.com). All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die;
$xml	= simplexml_load_file(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.'/tz_portfolio_plus.xml');
?>
<script type="text/javascript">
    "use strict";
    (function($){
        $(document).ready(function(){
            var parseXml;
            if (typeof window.DOMParser != "undefined") {
                parseXml = function(xmlStr) {
                    return ( new window.DOMParser() ).parseFromString(xmlStr, "text/xml");
                };
            } else if (typeof window.ActiveXObject != "undefined" &&
                new window.ActiveXObject("Microsoft.XMLDOM")) {
                parseXml = function(xmlStr) {
                    var xmlDoc = new window.ActiveXObject("Microsoft.XMLDOM");
                    xmlDoc.async = "false";
                    xmlDoc.loadXML(xmlStr);
                    return xmlDoc;
                };
            } else {
                throw new Error("No XML parser found");
            }
            var compareVersion = function (curVer, onVer) {
                for (var i=0; i< curVer.length || i< onVer.length; i++){
                    if (curVer[i] < onVer[i]) {
                        return true;
                    }
                }
                return false;
            }
            $.ajax({url: "https://raw.githubusercontent.com/templaza/tz_portfolio_plus/master/tz_portfolio_plus.xml",
                success: function(result){
                    var xml = parseXml(result);
                    var latestVersion = xml.getElementsByTagName("version")[0].childNodes[0].nodeValue;
                    var currentVersion = $(".local-version span").attr('data-local-version');
                    $(".latest-version span").attr('data-online-version',latestVersion).html(latestVersion);
                    $(".checking").css('display', 'none');
                    if (compareVersion(currentVersion, latestVersion)) {
                        $('.requires-updating').css('display','block');
                        $(".local-version span").addClass('oldversion');
                    } else {
                        $('.latest').css('display','block');
                    }
                },
                beforeSend: function() {
                    $(".checking").css('display', 'block');
                }
            });
        });
    })(jQuery);
</script>

<?php echo JHtml::_('tzbootstrap.addrow');?>
    <?php if(!empty($this -> sidebar)){?>
        <div id="j-sidebar-container" class="span2 col-md-2">
            <?php echo $this -> sidebar; ?>
        </div>
    <?php } ?>

    <?php echo JHtml::_('tzbootstrap.startcontainer', '10', !empty($this -> sidebar),
        array('containerclass' => false));?>

        <div class="tpDashboard">
            <div class="tpHeadline">
                <h2 class="reset-heading"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_DASHBOARD'); ?></h2>
                <p><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_DASHBOARD_DESC'); ?></p>
            </div>
            <?php echo JHtml::_('tzbootstrap.addrow');?>
                <div class="span7 col-md-7">
                    <div class="tpQuicklink">
                        <?php
                        $this->_quickIcon('index.php?option=com_tz_portfolio_plus&view=articles', 'icon-64-articles.png', 'COM_TZ_PORTFOLIO_PLUS_ARTICLES');
                        $this->_quickIcon('index.php?option=com_tz_portfolio_plus&view=categories', 'icon-64-categories.png', 'COM_TZ_PORTFOLIO_PLUS_CATEGORIES');
                        $this->_quickIcon('index.php?option=com_tz_portfolio_plus&view=featured', 'icon-64-featured.png', 'COM_TZ_PORTFOLIO_PLUS_FEATURED');
                        $this->_quickIcon('index.php?option=com_tz_portfolio_plus&view=fields', 'icon-64-fields.png', 'COM_TZ_PORTFOLIO_PLUS_FIELDS');
                        $this->_quickIcon('index.php?option=com_tz_portfolio_plus&view=groups', 'icon-64-groups.png', 'COM_TZ_PORTFOLIO_PLUS_FIELD_GROUPS');
                        $this->_quickIcon('index.php?option=com_tz_portfolio_plus&view=tags', 'icon-64-tags.png', 'COM_TZ_PORTFOLIO_PLUS_TAGS');
                        $this->_quickIcon('index.php?option=com_tz_portfolio_plus&view=addons', 'icon-64-addons.png', 'COM_TZ_PORTFOLIO_PLUS_ADDONS');
                        $this->_quickIcon('index.php?option=com_tz_portfolio_plus&view=template_styles', 'icon-64-styles.png', 'COM_TZ_PORTFOLIO_PLUS_TEMPLATE_STYLES');
                        $this->_quickIcon('index.php?option=com_tz_portfolio_plus&view=templates', 'icon-64-templates.png', 'COM_TZ_PORTFOLIO_PLUS_TEMPLATES');
                        $this->_quickIcon('index.php?option=com_tz_portfolio_plus&view=acls', 'icon-64-security.png', 'COM_TZ_PORTFOLIO_PLUS_ACL');
                        $this->_quickIcon('index.php?option=com_config&view=component&component=com_tz_portfolio_plus&return=' . urlencode(base64_encode(JUri::getInstance())), 'icon-64-configure.png', 'COM_TZ_PORTFOLIO_PLUS_CONFIGURE');
                        ?>
                    </div>
                </div>
                <div class="span5 col-md-5">
                    <div class="tpInfo">
                        <div class="tpDesc">
                            <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_DESCRIPTION_2'); ?>
                        </div>
                        <div class="tpVersion">
                            <b class="checking">
                                <i class="fa fa-circle-o-notch fa-spin"></i>Checking For Updates ...</b>
                            <b class="latest">Software Is Up To Date</b>
                            <b class="requires-updating">
                                Requires Updating
                                <a href="http://www.tzportfolio.com/" class="btn btn-default btn-sm btn-secondary">Update Now</a>
                            </b>
                            <div class="versions-meta">
                                <div class="text-muted local-version">Installed Version: <span data-local-version="<?php echo $xml->version; ?>"><?php echo $xml->version; ?></span></div>
                                <div class="text-muted latest-version">Latest Version: <span data-online-version="">N/A</span></div>
                            </div>
                        </div>
                        <div class="tpDetail">
                            <ul>
                                <li><span><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_AUTHOR'); ?>:</span> <a href="<?php echo $xml -> authorUrl;?>" target="_blank"><?php echo $xml->author; ?></a></li>
                                <li><span><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_COPYRIGHT'); ?>:</span> <?php echo $xml->copyright; ?></li>
                                <li><span><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_SUPPORT'); ?>:</span> <a href="<?php echo $xml->forumUrl; ?>" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_SUPPORT'); ?>" target="_blank"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_SUPPORT_DESC'); ?></a></li>
                                <li><span><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_GROUP'); ?>:</span> <a href="<?php echo $xml->facebookGroupUrl; ?>" target="_blank"><?php echo $xml->facebookGroupUrl; ?></a></li>
                                <li><span><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_FANPAGE'); ?>:</span> <a href="<?php echo $xml->facebookUrl; ?>" target="_blank"><?php echo $xml->facebookUrl; ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php echo JHtml::_('tzbootstrap.endrow');?>
        </div>
    <?php echo JHtml::_('tzbootstrap.endcontainer');?>
<?php echo JHtml::_('tzbootstrap.endrow');?>