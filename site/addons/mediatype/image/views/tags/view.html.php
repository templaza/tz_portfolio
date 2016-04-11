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

class PlgTZ_Portfolio_PlusMediaTypeImageViewTags extends JViewLegacy{

    protected $item     = null;
    protected $params   = null;
    protected $image    = null;
    protected $head     = false;

    public function display($tpl = null){
        $state          = $this -> get('State');
        $params         = $state -> get('params');
        $item = $this -> get('Item');
        $this -> params = $params;
        $this -> image  = null;

        if($item){
            if($media = $item -> media){
                if(isset($media -> image)){
                    $image  = clone($media -> image);

                    if(isset($image -> url) && $image -> url) {
                        if(!$this -> head) {
                            $doc = JFactory::getDocument();
                            $doc->addStyleSheet(JUri::base() . '/addons/mediatype/image/css/style.css');
                            $this -> head   = true;
                        }

                        $image->temp = $image->url;

                        if ($size = $params->get('mt_cat_image_size', 'o')) {
                            if (isset($image->url) && !empty($image->url)) {
                                $image_url_ext = JFile::getExt($image->url);
                                $image_url = str_replace('.' . $image_url_ext, '_' . $size . '.'
                                    . $image_url_ext, $image->url);
                                $image->url = JURI::root() . $image_url;
                            }

                            if (isset($image->url_hover) && !empty($image->url_hover)) {
                                $image_url_ext = JFile::getExt($image->url_hover);
                                $image_url = str_replace('.' . $image_url_ext, '_' . $size . '.'
                                    . $image_url_ext, $image->url_hover);
                                $image->url_hover = JURI::root() . $image_url;
                            }
                        }
                        $this->image = $image;
                    }
                }
            }
            $this -> item   = $item;
        }

        parent::display($tpl);
    }
}