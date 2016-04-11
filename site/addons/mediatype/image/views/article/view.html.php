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

// No direct access.
defined('_JEXEC') or die;

class PlgTZ_Portfolio_PlusMediaTypeImageViewArticle extends JViewLegacy{

    protected $item     = null;
    protected $params   = null;
    protected $image    = null;
    protected $state    = null;
    protected $head     = false;

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
                    $image  = clone($media -> image);

                    if(isset($image -> url) && $image -> url) {
                        if ($size = $params->get('mt_image_related_size', 'o')) {
                            if (isset($image->url) && !empty($image->url)) {
                                $image_url_ext = JFile::getExt($image->url);
                                $image_url = str_replace('.' . $image_url_ext, '_' . $size . '.'
                                    . $image_url_ext, $image->url);

                                $image->related_url = JURI::root() . $image_url;
                            }
                        }

                        if ($params->get('mt_image_use_cloud', 1)) {
                            $doc = JFactory::getDocument();

                            if(!$this -> head) {
                                $doc->addStyleSheet(TZ_Portfolio_PlusUri::base(true) . '/addons/mediatype/image/css/cloud-zoom.min.css');
                                $doc->addScript(TZ_Portfolio_PlusUri::base(true) . '/addons/mediatype/image/js/cloud-zoom.1.0.3.min.js');
                                $this -> head   = true;
                            }

                            if ($params->get('mt_image_cloud_size', 'o')) {
                                $image_url_ext = JFile::getExt($image->url);
                                $image_url = str_replace('.' . $image_url_ext, '_' . $params->get('mt_image_cloud_size', 'o') . '.'
                                    . $image_url_ext, $image->url);
                                $image->url_cloud_zoom = JURI::root() . $image_url;
                            }
                        }

                        if ($size = $params->get('mt_image_size', 'o')) {
                            if (isset($image->url) && !empty($image->url)) {
                                $image_url_ext = JFile::getExt($image->url);
                                $image_url = str_replace('.' . $image_url_ext, '_' . $size . '.'
                                    . $image_url_ext, $image->url);
                                $image->url = JURI::root() . $image_url;

                                if($this -> getLayout() != 'related') {
                                    JFactory::getDocument()->addCustomTag('<meta property="og:image" content="' . $image->url . '"/>');
                                    if ($author = $item->author_info) {
                                        JFactory::getDocument()->setMetaData('twitter:image', $image->url);
                                    }
                                }
                            }

                            if (isset($image->url_hover) && !empty($image->url_hover)) {
                                $image_url_ext = JFile::getExt($image->url_hover);
                                $image_url = str_replace('.' . $image_url_ext, '_' . $size . '.'
                                    . $image_url_ext, $image->url_hover);
                                $image->url_hover = JURI::root() . $image_url;
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