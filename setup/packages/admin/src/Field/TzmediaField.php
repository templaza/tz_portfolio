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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Image\Image;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Filesystem\File;
use Joomla\CMS\Form\Field\MediaField;
use Joomla\CMS\WebAsset\WebAssetManager;

class TzmediaField extends MediaField {
    protected $type = 'TZMedia';

    protected function getName($fieldName)
    {
        $name   = parent::getName($fieldName);
        $element    = $this -> element;

        if(isset($element['index']) && $element['index'] != null){
            $name   = preg_replace('/\[\]$/','['.$element['index'].']',$name);
        }
        return $name;
    }

    protected function getInput()
    {
        $attr       = '';
        $html       = array();
        $field_name = $this -> fieldname;

        if (!empty($this->class))
        {
            $this->class .= ' hasTipImgpath';
        }
        else
        {
            $this->class = 'hasTipImgpath';
        }

        // Initialize some field attributes.
        $class = !empty($this->class) ? ' class="' . $this->class . '"' : '';
        $disabled = $this->disabled ? ' disabled' : '';

        // Initialize JavaScript field attributes.
        $onchange = $this->onchange ? ' onchange="' . $this->onchange . '"' : '';

        $this -> __set('name',$field_name.'_client');

        $element    = $this -> element;

        $attr .= ' title="' . htmlspecialchars('<span id="TipImgpath"></span>', ENT_COMPAT, 'UTF-8') . '"';

        // Initialize some field attributes.
        $attr .= !empty($this->class) ? ' class="input-small ' . $this->class . '"' : ' class="input-small"';
        $attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';

        // Initialize JavaScript field attributes.
        $attr .= !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';

        if(isset($element['index']) && $element['index'] != null){
            $this->__set('id', $field_name . '_client'.$element['index']);
        }else {
            $this->__set('id', $field_name . '_client');
        }

        $html[] = '	<input type="file" name="' . $this -> name . '" id="' . $this->id . '"' . $attr . ' />';

        $this -> __set('name',$field_name.'_server');
        if(isset($element['index']) && $element['index'] != null){
            $this->__set('id', $field_name . '_server'.$element['index']);
        }else {
            $this -> __set('id',$field_name.'_server');
        }

        $value  = $this -> value;
        $this -> value  = '';

        $html[] = '<div style="padding-top: 5px;">'.parent::getInput();
        if($value && !empty($value) && is_string($value)){
            $html[] = '<a href="'.Uri::root().str_replace('.'
                    .File::getExt($value),($this -> element['img_prefix']?'_'.$this -> element['img_prefix']:'')
                    .'.'.File::getExt($value),$value).'?time='.time().'"'
                .' class="tp-image-preview tp-image-preview__modal" rel="{handler: \'image\'}" style="display: table; padding-top: 5px;">';

            $urlImg = Uri::root() . str_replace('.' . File::getExt($value),
                    ($this->element['img_prefix'] ? '_' . $this->element['img_prefix'] : '')
                    . '.' . File::getExt($value), $value);

            $urlImg .= '?time=' . time();
            $mUrlImg    = $urlImg;

            $img = '<img src="' . $urlImg. '" style="'
                . ($this->element['img_max-width'] ? 'max-width: 200px; ' : '') . 'cursor: pointer;" title="">';
            $html[] = $img;
            $html[] = '</a>';

            $file   = JPATH_SITE . '/' . str_replace('.' . File::getExt($value),
                    ($this->element['img_prefix'] ? '_' . $this->element['img_prefix'] : '')
                    . '.' . File::getExt($value), $value);
            if(file_exists($file)) {
                $image = new Image();
                $image->loadFile($file);

                $imgHtml    = '<img src="' . $mUrlImg. '" alt="'.Text::_('JLIB_FORM_MEDIA_PREVIEW_ALT').'" class="img-fluid">';
                $imgHtml    = '<div style="text-align:center; overflow-y: auto;">'.$imgHtml.'</div>';

                $unix   = null;
                if($this -> multiple){
                    $unix   = uniqid();
                }

                $html[] = HTMLHelper::_('bootstrap.renderModal',
                    'tp-image-preview__modal-'.$field_name.$unix,
                    array(
                        'title' => Text::_('JGLOBAL_PREVIEW'),
                        //                    'height' => '100%',
                        //                    'width' => '100%',
                        //                    'modalWidth' => '60',
                        //                    'bodyHeight' => '60',
                        'footer' => '<button type="button" class="btn" data-dismiss="modal" data-bs-dismiss="modal">'
                            . Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>',
                    ),
                    $imgHtml);

                $doc    = Factory::getApplication() -> getDocument();
                /* @var WebAssetManager $wa */
                $wa     = $doc -> getWebAssetManager();

                $wa -> addInlineScript('
                    (function($, window){
                        $(document).ready(function(){
                            $("#tp-image-preview__modal-' . $field_name . $unix . '").parent()
                                .find(".tp-image-preview__modal").on("click", function(e){
                                e.preventDefault();
                                $("#tp-image-preview__modal-' . $field_name . $unix . '")
                                    .on("show.bs.modal", function(){
                                        $(this).find(".modal-dialog").width(' . ($image->getWidth() + 2) . ');
                                    })
                                    .modal("show");
                            });
                        });
                    })(jQuery, window);');
            }
        }
        $html[] = '</div>';

        $this -> __set('name',$field_name);
        $this -> __set('id',$field_name);

        $html[] = '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="'
            . (!empty($value)?htmlspecialchars($value, ENT_COMPAT, 'UTF-8'):$value) . '"' . $class . $disabled . $onchange . ' />';

        return implode("\n", $html);
    }
}