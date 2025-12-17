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

use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

if($itemsServer = $this -> itemsServer){
    $jTable = Table::getInstance('Extension','Joomla\\CMS\\Table\\');
    foreach ($itemsServer as $i => $item) {
        $detailUrl  = $item -> link;
        if(strpos($detailUrl,'?')){
            $detailUrl  .= '&tmpl=component';
        }else{
            $detailUrl  .= '?tmpl=component';
        }

        $addon      = null;
        $version    = $item -> installedVersion;

        $exID       = null;
        if($jTable && $jTable -> load(array('type' => 'module', 'element' => $item -> pElement))){
            $exID   = $jTable -> get('extension_id');
        }
        ?>
        <div class="tpp-extension__col">
            <div class="tpp-extension__item">
                <div class="top">
                    <h3 class="title">
                        <a data-toggle="modal" data-bs-toggle="modal" data-bs-toggle="modal" href="#tpp-addon__modal-detail-<?php echo $i; ?>">
                            <?php echo $item -> title; ?>
                            <?php if(isset($item -> imageUrl) && $item -> imageUrl){ ?>
                                <img src="<?php echo $item -> imageUrl;?>" alt="<?php echo $item -> title; ?>">
                            <?php } ?>
                        </a>
                    </h3>
                    <div class="action-links">
                        <ul class="pl-0 ps-0">
                            <?php
                            $addOnButton    = null;
                            if($item -> pProduce && $item -> pProduce -> pCommercial == true && !$item -> pProduce -> pHasPurchased) {
                                $addOnButton    = 'buynow';
                            }else{
                                $addOnButton = 'install';
                            }

                            if($version && $item -> pProduce){
                                if(!$item -> pProduce ->  pVersion || ($item -> pProduce -> pVersion
                                        && version_compare($version, $item -> pProduce -> pVersion, '>='))){
                                    $addOnButton    = 'installed';
                                }elseif($item -> pProduce -> pVersion && version_compare($version, $item -> pProduce -> pVersion, '<')){
                                    $addOnButton    = 'update';
                                }
                            }
                            ?>
                            <?php if(!$addOnButton || $addOnButton == 'install'){?>
                                <li>
                                    <a href="<?php echo $item -> pProduce -> pProduceUrl;
                                    ?>" class="install-now btn btn-outline-secondary"><span class="fas fa-download"></span> <?php
                                        echo Text::_('COM_TZ_PORTFOLIO_INSTALL_NOW'); ?></a>
                                </li>
                            <?php }elseif($addOnButton == 'buynow'){?>
                                <li>
                                    <a href="<?php echo $item -> pProduce ->  pProduceUrl?$item -> pProduce ->  pProduceUrl:$item -> link;
                                    ?>" target="_blank" class="btn btn-outline-secondary"><span class="fas fa-shopping-cart"></span> <?php
                                        echo Text::_('COM_TZ_PORTFOLIO_BUY_NOW'); ?></a>
                                </li>
                            <?php }else{?>
                                <li>
                                    <div data-uk-dropnav="mode: click">
                                        <?php if($addOnButton == 'installed'){?>
                                            <button type="button" class="btn btn-outline-success disabled"><span class="installed"><span class="fas fa-check"></span> <?php echo Text::_('COM_TZ_PORTFOLIO_INSTALLED'); ?></button>
                                        <?php } ?>
                                        <?php if($addOnButton == 'update'){?>
                                            <a href="<?php echo $item -> pProduce ->  pProduceUrl;
                                            ?>" class="install-now btn btn-secondary"><span class="fas fa-sync-alt text-update"></span> <?php
                                                echo Text::_('COM_TZ_PORTFOLIO_UPDATE_NOW'); ?></a>
                                        <?php } ?>
                                        <button type="button" class="btn btn-default btn-outline-secondary dropdown-toggle hasTooltip" title="<?php
                                        echo Text::_('Actions');?>" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <?php if(version_compare(JVERSION, '4.0', '<')){ ?>
                                                <span class="fas fa-angle-down"></span>
                                            <?php } ?>
                                        </button>
                                        <div class="uk-dropdown">
                                            <ul class="uk-nav uk-dropdown-nav">
                                                <?php if($item -> installedVersion){?>
                                                    <li><a href="<?php echo JRoute::_('index.php?option=com_modules&filter_module='
                                                            .$item -> pElement); ?>" target="_blank" class="dropdown-item"><i class="fas fa-tools"></i> <?php
                                                            echo Text::_('COM_TZ_PORTFOLIO_CONFIGURE'); ?></a></li>
                                                    <li class="uk-nav-divider"></li>
                                                <?php
                                                    $exURL  = JRoute::_('index.php?option=com_installer&view=manage&filter_search=id:'
                                                        .$exID);
                                                    ?>
                                                    <li><a href="<?php echo $exURL; ?>" target="_blank" class="dropdown-item"><i class="far fa-trash-alt"></i> <?php
                                                            echo Text::_('JTOOLBAR_UNINSTALL');?></a></li>
                                                <?php }?>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            <?php } ?>
                            <?php if(isset($item -> liveDemoUrl) && $item -> liveDemoUrl){ ?>
                                <li>
                                    <a target="_blank" class="btn btn-success js-tpp-live-demo" href="<?php
                                    echo $item -> liveDemoUrl; ?>"><i class="far fa-eye"></i> <?php
                                        echo Text::_('COM_TZ_PORTFOLIO_LIVE_DEMO');?></a>
                                </li>
                            <?php } ?>
                            <li>
                                <a data-toggle="modal" data-bs-toggle="modal" href="#tpp-addon__modal-detail-<?php echo $i;
                                ?>" data-url="<?php echo $detailUrl; ?>"><?php
                                    echo Text::_('COM_TZ_PORTFOLIO_MORE_DETAIL');?></a>
                            </li>
                        </ul>
                    </div>
                    <div class="desc">
                        <?php echo $item -> introtext; ?>
                        <p class="author">
                            <?php
                            $author = '<strong>'.$item -> author.'</strong>';
                            echo Text::sprintf('COM_TZ_PORTFOLIO_BY', $author);
                            ?>
                        </p>
                    </div>
                    <?php
                    echo HTMLHelper::_(
                        'bootstrap.renderModal',
                        'tpp-addon__modal-detail-'.$i,
                        array(
                            'url'        => $detailUrl,
                            'title'      => $item -> title,
                            'width'      => '400px',
                            'height'     => '800px',
                            'modalWidth' => '70',
                            'bodyHeight' => '70',
                            'closeButton' => true,
                            'footer'      => '<a class="btn" data-dismiss="modal" data-bs-dismiss="modal" aria-hidden="true">' . Text::_('JCANCEL') . '</a>',
                        )
                    );
                    ?>
                </div>
                <div class="bottom">
                    <ul class="unstyled list-unstyled pull-left float-left float-md-start float-none mb-1 mb-md-3">
                        <li><?php echo Text::sprintf('COM_TZ_PORTFOLIO_LATEST_VERSION', '') ?><span><?php
                                echo $item -> pProduce -> pVersion?$item -> pProduce ->  pVersion:Text::_('COM_TZ_PORTFOLIO_NA');
                                ?></span>
                        </li>
                        <li><?php echo Text::sprintf('COM_TZ_PORTFOLIO_INSTALLED_VERSION', ''); ?><span><?php
                                echo $item -> installedVersion?$item -> installedVersion:Text::_('COM_TZ_PORTFOLIO_NA');
                                ?></span>
                        </li>
                    </ul>
                    <ul class="unstyled list-unstyled pull-right float-right float-md-end float-none text-right">
                        <li><?php
                            $updated = '<span>'.HTMLHelper::_('date', $item -> modified, Text::_('DATE_FORMAT_LC4')).'</span>';
                            echo Text::sprintf('COM_TZ_PORTFOLIO_LAST_UPDATED', $updated);
                            ?></li>
                    </ul>
                </div>
            </div>
        </div>
    <?php }
}