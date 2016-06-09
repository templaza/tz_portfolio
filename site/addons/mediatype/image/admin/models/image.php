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

// No direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

class PlgTZ_Portfolio_PlusMediaTypeModelImage extends TZ_Portfolio_PlusPluginModelAdmin{

    public function save($data){
        $app    = JFactory::getApplication();
        $input  = $app -> input;

        $_data      = array('id' => ($data -> id), 'asset_id' => ($data -> asset_id),'media' => '{}');
        $params     = $this -> getState('params');

        // Get some params
        $mime_types     = $params -> get('image_mime_type','image/jpeg,image/gif,image/png,image/bmp');
        $mime_types     = explode(',',$mime_types);
        $file_types     = $params -> get('image_file_type','bmp,gif,jpg,jpeg,png');
        $file_types     = explode(',',$file_types);
        $file_sizes     = $params -> get('image_file_size',10);
        $file_sizes     = $file_sizes * 1024 * 1024;

        // Get and Process data
        $image_data = $input -> get('jform', null, 'array');
        if(isset($image_data['media'])) {
            if(isset($image_data['media'][$this->getName()])) {
                $image_data = $image_data['media']['image'];
            }
        }

        $media  = null;
        if($data -> media && !empty($data -> media)) {
            $media  = new JRegistry;
            $media -> loadString($data -> media);
            $media  = $media -> get('image');
        }

        // Set data when save as copy article
        if($input -> getCmd('task') == 'save2copy' && $input -> getInt('id')){
            if((isset($image_data['url_remove']) && $image_data['url_remove'])){
                $image_data['url_remove']   = null;
                $image_data['url']          = '';
            }
            if((isset($image_data['url_hover_remove']) && $image_data['url_hover_remove'])){
                $image_data['url_hover_remove'] = '';
                $image_data['url_hover']        = '';
            }
            if(!isset($image_data['url_server'])
                || (isset($image_data['url_server']) && empty($image_data['url_server']))){
                if(isset($image_data['url']) && $image_data['url']) {
                    $ext        = JFile::getExt($image_data['url']);
                    $path_copy  = str_replace('.'.$ext,'_o.'.$ext, $image_data['url']);
                    if(JFile::exists(JPATH_ROOT.DIRECTORY_SEPARATOR.$path_copy)) {
                        $image_data['url_server']   = $path_copy;
                        $image_data['url']          = '';
                    }
                }
            }
            if(!isset($image_data['url_hover_server'])
                || (isset($image_data['url_hover_server']) && empty($image_data['url_hover_server']))){
                if(isset($image_data['url_hover']) && $image_data['url_hover']) {
                    $ext        = JFile::getExt($image_data['url_hover']);
                    $path_copy  = str_replace('.'.$ext,'_o.'.$ext, $image_data['url_hover']);
                    if(JFile::exists(JPATH_ROOT.DIRECTORY_SEPARATOR.$path_copy)) {
                        $image_data['url_hover_server']   = $path_copy;
                        $image_data['url_hover']          = '';
                    }
                }
            }
        }

        // Remove image and image hover with resized
        if($image_size = $params -> get('image_size', array())){

            $image_size = $this -> prepareImageSize($image_size);

            if(is_array($image_size) && count($image_size)){
                foreach($image_size as $_size){
                    $size           = json_decode($_size);

                    // Delete old image files
                    if((isset($image_data['url_remove']) && $image_data['url_remove'])
                    && $media && isset($media -> url) && !empty($media -> url)){
                        $image_url  = $media -> url;
                        $image_url  = str_replace('.'.JFile::getExt($image_url),'_'.$size ->image_name_prefix
                            .'.'.JFile::getExt($image_url),$image_url);
                        JFile::delete(JPATH_ROOT.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,
                                $image_url));
                    }

                    // Delete old image hover files
                    if((isset($image_data['url_hover_remove']) && $image_data['url_hover_remove'])
                        && $media && isset($media -> url_hover) && !empty($media -> url_hover)){
                        $image_url  = $media -> url_hover;
                        $image_url  = str_replace('.'.JFile::getExt($image_url),'_'.$size ->image_name_prefix
                            .'.'.JFile::getExt($image_url),$image_url);
                        JFile::delete(JPATH_ROOT.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,
                                $image_url));
                    }
                }
            }
        }

        // Remove Image file when tick to remove file box
        if(isset($image_data['url_remove']) && $image_data['url_remove']){
            // Before upload image to file must delete original file
            if($media && isset($media -> url) && !empty($media -> url)){
                $image_url  = $media -> url;
                $image_url  = str_replace('.'.JFile::getExt($image_url),'_o'
                    .'.'.JFile::getExt($image_url),$image_url);

                if(JFile::delete(JPATH_ROOT.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,
                        $image_url))){
                    $image_data['url']    = '';
                    unset($image_data['url_remove']);
                }
            }
        }else{
            unset($image_data['url']);
        }

        // Remove Image hover file when tick to remove file box
        if(isset($image_data['url_hover_remove']) && $image_data['url_hover_remove']){
            // Before upload image to file must delete original file
            if($media && isset($media -> url_hover) && !empty($media -> url_hover)){
                $image_url  = $media -> url_hover;
                $image_url  = str_replace('.'.JFile::getExt($image_url),'_o'
                    .'.'.JFile::getExt($image_url),$image_url);

                if(JFile::delete(JPATH_ROOT.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,
                        $image_url))){
                    $image_data['url_hover']    = '';
                    unset($image_data['url_hover_remove']);
                }
            }
        }else{
            unset($image_data['url_hover']);
        }

        $images         = array();
        $images_hover   = array();
        $imageObj       = new JImage();

        // Upload image or image hover
        if($files = $input -> files -> get('jform', array(), 'array')) {

            if(isset($files['media']) && isset($files['media']['image'])){
                $files  = $files['media']['image'];

                // Get image from form
                if(isset($files['url_client']['name']) && !empty($files['url_client']['name'])) {
                    $images = $files['url_client'];
                }

                // Get image hover data from form
                if(isset($files['url_hover_client']['name']) && !empty($files['url_hover_client']['name'])) {
                    $images_hover    = $files['url_hover_client'];
                }
            }
        }

            $path               = '';
            $path_hover         = '';

            jimport('joomla.filesystem.file');

            $imageType              = null;
            $imageMimeType          = null;
            $imageSize              = null;
            $image_hoverType        = null;
            $image_hoverMimeType    = null;
            $image_hoverSize        = null;

            // Create original image with new name (upload from client)
            if(count($images) && !empty($images['tmp_name'])) {

                // Get image file type
                $imageType  = JFile::getExt($images['name']);
                $imageType  = strtolower($imageType);

                // Get image's mime type
                $imageMimeType  = $images['type'];

                // Get image's size
                $imageSize  = $images['size'];

                $path   = COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_ROOT.DIRECTORY_SEPARATOR;
                $path  .=  $data -> alias . '-' . $data -> id . '_o';
                $path  .= '.' . JFile::getExt($images['name']);

                if($input -> getCmd('task') == 'save2copy' && $input -> getInt('id')){
                    $image_data['url_server']   = null;
                }
            }elseif(isset($image_data['url_server'])
                && !empty($image_data['url_server'])){ // Create original image with new name (upload from server)

                // Get image file type
                $imageType  = JFile::getExt($image_data['url_server']);
                $imageType  = strtolower($imageType);

                // Get image's mime type
                $imageObj -> loadFile(JPATH_ROOT . DIRECTORY_SEPARATOR
                    . $image_data['url_server']);
                $imageMimeType  = $imageObj->getImageFileProperties($imageObj->getPath());
                $imageMimeType  = $imageMimeType -> mime;

                // Get image's size
                $imageSize  = $imageMimeType -> filesize;

                $path   = COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_ROOT.DIRECTORY_SEPARATOR;
                $path  .=  $data -> alias . '-' . $data -> id . '_o';
                $path  .= '.' . JFile::getExt($image_data['url_server']);
            }

            // Create original image hover with new name (upload from client)
            if(count($images_hover) && !empty($images_hover['tmp_name'])) {

                // Get image hover file type
                $image_hoverType  = JFile::getExt($images_hover['name']);
                $image_hoverType  = strtolower($image_hoverType);

                // Get image hover's mime type
                $image_hoverMimeType    = $images_hover['type'];

                // Get image's size
                $image_hoverSize    = $images_hover['size'];

                $path_hover     = COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_ROOT.DIRECTORY_SEPARATOR;
                $path_hover    .= $data -> alias . '-' . $data -> id . '-h_o';
                $path_hover    .= '.' . JFile::getExt($images_hover['name']);

                if($input -> getCmd('task') == 'save2copy' && $input -> getInt('id')){
                    $image_data['url_hover_server']   = null;
                }
            }elseif(isset($image_data['url_hover_server'])
                && !empty($image_data['url_hover_server'])){ // Create original image with new name (upload from server)

                // Get image hover file type
                $image_hoverType  = JFile::getExt($image_data['url_hover_server']);
                $image_hoverType  = strtolower($image_hoverType);

                // Get image hover's mime type
                $imageObj -> loadFile(JPATH_ROOT . DIRECTORY_SEPARATOR
                    . $image_data['url_hover_server']);

                $image_hoverMimeType    = $imageObj->getImageFileProperties($imageObj->getPath());
                $image_hoverMimeType    = $image_hoverMimeType -> mime;

                // Get image hover's size
                $image_hoverSize  = $image_hoverMimeType -> filesize;

                $path_hover     = COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_ROOT.DIRECTORY_SEPARATOR;
                $path_hover    .=  $data -> alias . '-' . $data -> id . '-h_o';
                $path_hover    .= '.' . JFile::getExt($image_data['url_hover_server']);
            }

            // Upload original image
            if($path && !empty($path)){

                //-- Check image information --//
                // Check MIME Type
                if (!in_array($imageMimeType, $mime_types)) {
                    $app->enqueueMessage(JText::_('PLG_MEDIATYPE_IMAGE_ERROR_WARNINVALID_MIME'), 'notice');
                    return false;
                }

                // Check file type
                if (!in_array($imageType, $file_types)) {
                    $app->enqueueMessage(JText::_('PLG_MEDIATYPE_IMAGE_ERROR_WARNFILETYPE'), 'notice');
                    return false;
                }

                // Check file size
                if ($imageSize > $file_sizes) {
                    $app->enqueueMessage(JText::_('PLG_MEDIATYPE_IMAGE_ERROR_WARNFILETOOLARGE'), 'notice');
                    return false;
                }
                //-- End check image information --//

                // Before upload image to file must delete original file
                if($media && isset($media -> url) && !empty($media -> url)){
                    $image_url  = $media -> url;
                    $image_url  = str_replace('.'.JFile::getExt($image_url),'_o'
                        .'.'.JFile::getExt($image_url),$image_url);
                    JFile::delete(JPATH_ROOT.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,
                            $image_url));
                }

                if(isset($images['tmp_name']) && !empty($images['tmp_name'])
                    && !JFile::upload($images['tmp_name'],$path)){
                    $path       = '';
                }elseif(isset($image_data['url_server']) && !empty($image_data['url_server'])
                    && !JFile::copy(JPATH_ROOT.DIRECTORY_SEPARATOR.$image_data['url_server'],$path)){
                    $path       = '';
                }
            }

            // Upload original image hover
            if($path_hover && !empty($path_hover)){

                //-- Check image information --//
                // Check MIME Type
                if (!in_array($image_hoverMimeType, $mime_types)) {
                    $app->enqueueMessage(JText::_('PLG_MEDIATYPE_IMAGE_ERROR_WARNINVALID_MIME'), 'notice');
                    return false;
                }

                // Check file type
                if (!in_array($image_hoverType, $file_types)) {
                    $app->enqueueMessage(JText::_('PLG_MEDIATYPE_IMAGE_ERROR_WARNFILETYPE'), 'notice');
                    return false;
                }

                // Check file size
                if ($image_hoverSize > $file_sizes) {
                    $app->enqueueMessage(JText::_('PLG_MEDIATYPE_IMAGE_ERROR_WARNFILETOOLARGE'), 'notice');
                    return false;
                }
                //-- End check image information --//

                // Before upload image hover file to file must delete original file
                if($media && isset($media -> url_hover) && !empty($media -> url_hover)){
                    $image_url  = $media -> url_hover;
                    $image_url  = str_replace('.'.JFile::getExt($image_url),'_o'
                        .'.'.JFile::getExt($image_url),$image_url);
                    JFile::delete(JPATH_ROOT.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,
                            $image_url));
                }

                if(isset($images_hover['tmp_name']) && !empty($images_hover['tmp_name'])
                    && !JFile::upload($images_hover['tmp_name'],$path_hover)){
                    $path_hover = '';
                }elseif(isset($image_data['url_hover_server']) && !empty($image_data['url_hover_server'])
                    && !JFile::copy(JPATH_ROOT.DIRECTORY_SEPARATOR.$image_data['url_hover_server'],$path_hover)){
                    $path_hover = '';
                }
            }

            // Upload image and image hover with resize
            if($image_size = $params -> get('image_size')){
                $image_size = $this -> prepareImageSize($image_size);

                $image              = null;
                $image_hover        = null;

                if(is_array($image_size) && count($image_size)){
                    foreach($image_size as $_size){
                        $size           = json_decode($_size);

                        // Upload image with resize
                        if($path) {
                            // Create new ratio from new with of image size param
                            $imageObj -> loadFile($path);
                            $imgProperties  = $imageObj->getImageFileProperties($imageObj -> getPath());
                            $newH           = ($imgProperties -> height * $size -> width) / ($imgProperties -> width);
                            $newImage       = $imageObj->resize($size -> width, $newH);

                            $newPath = COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_ROOT . DIRECTORY_SEPARATOR
                                . $data->alias . '-' . $data->id . '_' . $size->image_name_prefix
                                . '.' . JFile::getExt($path);

                            // Before generate image to file must delete old files
                            if($media && isset($media -> url) && !empty($media -> url)){
                                $image_url  = $media -> url;
                                $image_url  = str_replace('.'.JFile::getExt($image_url),'_'.$size ->image_name_prefix
                                    .'.'.JFile::getExt($image_url),$image_url);
                                JFile::delete(JPATH_ROOT.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,
                                        $image_url));
                            }

                            // Generate image to file
                            $newImage->toFile($newPath, $imgProperties->type);
                        }

                        // Upload image hover with resize
                        if($path_hover) {
                            // Create new ratio from new with of image size param
                            $imageObj -> loadFile($path_hover);
                            $imgHoverProperties = $imageObj->getImageFileProperties($imageObj -> getPath());
                            $newH               = ($imgHoverProperties -> height * $size -> width) / ($imgHoverProperties -> width);
                            $newHImage          = $imageObj->resize($size -> width, $newH);
                            $newHPath           = COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_ROOT . DIRECTORY_SEPARATOR
                                . $data->alias . '-' . $data->id . '-h_' . $size -> image_name_prefix
                                . '.' . JFile::getExt($path_hover);

                            // Before generate image hover to file must delete old files
                            if($media && isset($media -> url_hover) && !empty($media -> url_hover)){
                                $image_url_hover    = $media -> url_hover;
                                $image_url_hover    = str_replace('.'.JFile::getExt($image_url_hover),'_'.$size ->image_name_prefix
                                    .'.'.JFile::getExt($image_url_hover),$image_url_hover);
                                JFile::delete(JPATH_ROOT.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,
                                        $image_url_hover));
                            }

                            // Generate image to file
                            $newHImage->toFile($newHPath, $imgHoverProperties->type);
                        }
                    }
                }
            }

            if($path && !empty($path)){
                $image_data['url']   = COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_BASE.'/'
                    .$data -> alias . '-' . $data -> id. '.' . JFile::getExt($path);
            }

            if($path_hover && !empty($path_hover)){
                $image_data['url_hover']   = COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_BASE.'/'
                    .$data -> alias . '-' . $data -> id. '-h.' . JFile::getExt($path_hover);
            }

            unset($image_data['url_server']);
            unset($image_data['url_hover_server']);

            $this -> __save($data,$image_data);
//        }
    }

    public function delete(&$article){
        if($article){
            if(is_object($article)){
                if($article -> media && !empty($article -> media)) {
                    $media  = new JRegistry;
                    $media -> loadString($article -> media);

                    $media  = $media -> get('image');
                    $params = $this -> getState('params');

                    if($media){
                        if(isset($media -> url) && !empty($media -> url)){
                            // Delete original image
                            $image_url  = str_replace('.'.JFile::getExt($media->url),
                                '_o.'.JFile::getExt($media->url),$media->url);
                            JFile::delete(JPATH_ROOT.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,
                                    $image_url));
                        }

                        if(isset($media -> url_hover) && !empty($media -> url_hover)){
                            // Delete original image hover
                            $image_url  = str_replace('.'.JFile::getExt($media->url_hover),
                                '_o.'.JFile::getExt($media->url_hover),$media->url_hover);
                            JFile::delete(JPATH_ROOT.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,
                                    $image_url));
                        }

                        // Delete image with some size
                        if($image_size = $params -> get('image_size', array())){

                            $image_size = $this -> prepareImageSize($image_size);

                            if(is_array($image_size) && count($image_size)){
                                foreach($image_size as $_size){
                                    $size           = json_decode($_size);

                                    // Delete image
                                    if(isset($media -> url) && !empty($media -> url)) {
                                        // Create file name and execute delete image
                                        $image_url = str_replace('.' . JFile::getExt($media->url), '_' . $size->image_name_prefix
                                            . '.' . JFile::getExt($media->url), $media->url);
                                        JFile::delete(JPATH_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR,
                                                $image_url));
                                    }

                                    // Delete image hover
                                    if(isset($media -> url_hover) && !empty($media -> url_hover)) {
                                        // Create file name and execute delete image
                                        $image_url = str_replace('.' . JFile::getExt($media->url_hover), '_' . $size->image_name_prefix
                                            . '.' . JFile::getExt($media->url_hover), $media->url_hover);
                                        JFile::delete(JPATH_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR,
                                                $image_url));
                                    }
                                }
                            }
                        }
                    }
                }

            }
        }
    }
}