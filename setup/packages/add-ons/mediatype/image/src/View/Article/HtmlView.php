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

namespace TemPlaza\Component\TZ_Portfolio\AddOn\Mediatype\Image\Site\View\Article;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\File;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\Filesystem\Path;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioUri;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\ArticleHelper;

/**
 * Categories view class for the Category package.
 */
class HtmlView extends BaseHtmlView
{
    protected $item     = null;
    protected $params   = null;
    protected $image    = null;
    protected $state    = null;
    protected $head     = false;
    protected $image_properties     = false;

    public function display($tpl = null){
        $state          = $this -> get('State');
        $params         = $state -> get('params');
        $this -> state  = $state;
        $this -> params = $params;
        $item           = $this -> get('Item');
        $this -> image  = null;

        if($item){
            if($media = $item -> media){
                if(isset($media -> image)){
                    $doc    = Factory::getDocument();
//                    Factory::getApplication() -> bootComponent('com_tz_portfolio');

                    $wa     = $doc -> getWebAssetManager();

                    $wa -> useStyle('com_tz_portfolio.fancybox');
                    $wa -> useScript('com_tz_portfolio.fancybox');

                    $wa -> registerAndUseScript('com_tz_portfolio.addon.mediatype.image',TZ_PortfolioUri::root()
                        .'/add-ons/mediatype/image/js/lightbox.min.js');

                    $lightboxopt    =   $params->get('image_lightbox_option',['zoom', 'slideShow', 'fullScreen', 'thumbs', 'close']);
                    $lightboxopts   = array(
                        'buttons'   => $lightboxopt
                    );

                    $wa -> addInlineScript('jQuery(function($){
                        $.image_addon_lightbox('.json_encode($lightboxopts).');
                    });', ['com_tz_portfolio.fancybox', 'com_tz_portfolio.addon.mediatype.image']);

                    $image  = clone($media -> image);

                    if(isset($image -> url) && $image -> url) {
                        if (!file_exists(Path::clean(JPATH_ROOT.DIRECTORY_SEPARATOR.$image -> url))) {
                            $image -> url = str_replace('tz_portfolio_plus', 'com_tz_portfolio', $image -> url);
                        }
                        if ($size = $params->get('mt_image_related_size', 'o')) {
                            if (isset($image->url) && !empty($image->url)) {
                                $image_url_ext = File::getExt($image->url);
                                if ($params->get('mt_show_original_gif',1) && $image_url_ext == 'gif') {
                                    $size = 'o';
                                }
                                $image_url = str_replace('.' . $image_url_ext, '_' . $size . '.'
                                    . $image_url_ext, $image->url);
                                $image->related_url = Uri::base( true ) . '/' . ArticleHelper::convertImageLegacy($image_url) ;
                            }
                        }

                        if ($size = $params->get('mt_image_size', 'o')) {
                            if (isset($image->url) && !empty($image->url)) {
                                $image_url_ext = File::getExt($image->url);
                                if ($params->get('mt_show_original_gif',1) && $image_url_ext == 'gif') {
                                    $size = 'o';
                                }
                                $image_url = str_replace('.' . $image_url_ext, '_' . $size . '.'
                                    . $image_url_ext, $image->url);
                                if ($params->get('mt_image_uikit',0) && file_exists(JPATH_BASE.'/'.$image_url)) {
                                    $this->image_properties =   getimagesize(JPATH_BASE.'/'.$image_url);
                                }
                                $image->url = Uri::base( true ) . '/' . ArticleHelper::convertImageLegacy($image_url);

                                if($this -> getLayout() != 'related') {
                                    Factory::getDocument()->addCustomTag('<meta property="og:image" content="' . $image->url . '"/>');
                                    if ($author = $item->author_info) {
                                        Factory::getDocument()->setMetaData('twitter:image', $image->url);
                                    }
                                }
                            }

                            if (isset($image->url_detail) && !empty($image->url_detail)) {
                                $image_url_ext = File::getExt($image->url_detail);
                                $image_url = str_replace('.' . $image_url_ext, '_' . $size . '.'
                                    . $image_url_ext, $image->url_detail);
                                if ($params->get('mt_image_uikit',0) && file_exists(JPATH_BASE.'/'.$image_url)) {
                                    $this->image_properties =   getimagesize(JPATH_BASE.'/'.$image_url);
                                }
                                $image->url_detail = Uri::base( true ) . '/' . ArticleHelper::convertImageLegacy($image_url);
                            }
                        }

                        $this -> image  = $image;
                    }

                }
            }
            $this -> item   = $item;
        }

        parent::display($tpl);
    }
}
