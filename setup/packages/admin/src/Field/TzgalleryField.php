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

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

class TzgalleryField extends FormField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  11.3
     */
    protected $type = 'Tzgallery';

    /**
     * The control.
     *
     * @var    mixed
     * @since  3.2
     */
    protected $addon_type = '';
    protected $addon_name = '';

    /**
     * Method to attach a JForm object to the field.
     *
     * @param   \SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
     * @param   mixed             $value    The form field value to validate.
     * @param   string            $group    The field name group control value. This acts as as an array container for the field.
     *                                      For example if the field has name="foo" and the group value is set to "bar" then the
     *                                      full field name would end up being "bar[foo]".
     *
     * @return  boolean  True on success.
     *
     * @see     FormField::setup()
     * @since   3.2
     */
    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        $return = parent::setup($element, $value, $group);

        if ($return)
        {
            $this->addon_type  = isset($this->element['data-type']) ? (string) $this->element['data-type'] : '';
            $this->addon_name = isset($this->element['data-name']) ? (string) $this->element['data-name'] : '';
        }

        return $return;
    }

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     *
     * @since   11.3
     */
    protected function getInput()
    {
        $class = !empty($this->class) ? ' class="' . $this->class . '"' : '';
        $app            =   Factory::getApplication();
        $input          =   $app -> input;
        $gallery_tmp       =   uniqid('gallery_');
        $gallery_file_type =   'bmp,gif,jpg,jpeg,png';
        $gallery_file_type =   explode(',', $gallery_file_type);
        for ($i = 0 ; $i< count($gallery_file_type); $i++) {
            $gallery_file_type[$i]  =   '"'.trim($gallery_file_type[$i]).'"';
        }
        $gallery_file_type=   is_array($gallery_file_type) ? implode(',', $gallery_file_type) : '';
        $japp = Factory::getApplication();
        $doc            = Factory::getApplication()->getDocument();
        $wa = $doc -> getWebAssetManager();

        $wa -> useStyle('com_tz_portfolio.dm-uploader');
        $wa -> useScript('com_tz_portfolio.dm-uploader');
        $wa -> useStyle('com_tz_portfolio.dm-uploader.gallery_upload');
        $ajaxUrl    =   'index.php?option=com_tz_portfolio&task=ajax.gallery_upload&type='.$this->addon_type.'&name='.$this->addon_name.'&folder='.$gallery_tmp;
        $wa -> addInlineScript('
var GalleryContent = window.GalleryContent || {};
    jQuery.extend(GalleryContent, {
    ajaxUrl                : "'.$ajaxUrl.'",
    maxFileSize            : 20,
    extFilter              : ['.$gallery_file_type.']
    });
');
        $wa -> useScript('com_tz_portfolio.dm-uploader.style_ui');
        $wa -> useScript('com_tz_portfolio.dm-uploader.gallery_uploader');
        ob_start();
        ?>


        <div class="container-addon">
            <div class="row-addon">
                <div class="col-addon">

                    <!-- Our markup, the important part here! -->
                    <div id="gallery_uploader" class="dm-uploader p-5">
                        <h3 class="mb-5 mt-5 text-muted"><?php echo Text::_('PLG_CONTENT_GALLERY_DROP_DRAG'); ?></h3>

                        <div class="btn btn-primary btn-block mb-5">
                            <span><?php echo Text::_('PLG_CONTENT_GALLERY_OPEN_FILE'); ?></span>
                            <input type="file" title='Click to add Files' />
                        </div>
                    </div><!-- /uploader -->

                </div>
                <div class="col-addon">
                    <div class="card h-100">
                        <div class="card-header">
                            <?php echo Text::_('PLG_CONTENT_GALLERY_FILE_LIST'); ?>
                        </div>

                        <ul class="list-unstyled p-2 d-flex flex-column col" id="gallery_files">
                            <?php if (isset($this->value->gallery_image) && count($this->value->gallery_image)) : ?>
                                <?php for ($i=0; $i<count($this->value->gallery_image); $i++) :
                                    $image     =   $this->value->gallery_image[$i];
                                    $title     =   isset($this->value->gallery_image_title) ? $this->value->gallery_image_title[$i] : '';
                                    ?>
                                    <li class="media" data-name="<?php echo $image; ?>" data-source="server">
                                        <img class="mr-3 mb-2 preview-img" src="<?php echo Uri::root().'/images/tz_portfolio_plus/gallery/'.$input->getInt('id').'/'.$image; ?>" alt="Generic placeholder image">
                                        <div class="media-body mb-1">
                                            <p class="mb-2">
                                                <strong class="filename"><?php echo $image; ?></strong> - Status: <span class="status text-success">Available</span> - <em class="grid_featured"><input type="radio" name="<?php echo $this->name; ?>[image_featured]" class="grid_image_featured" value="<?php echo $image; ?>"<?php if (isset($this->value->image_featured) && $this->value->image_featured == $image) echo ' checked="checked"'; ?> /> <?php echo Text::_('JFEATURED'); ?></em> - <a href="#" class="delete_grid_image"><?php echo Text::_('JACTION_DELETE'); ?></a>
                                            </p>
                                            <p class="mb-2">
                                                <input type="text" class="inputbox" name="<?php echo $this->name; ?>[gallery_image_title][]" placeholder="Title..." value="<?php echo $title; ?>" />
                                            </p>
                                            <div class="progress mb-2">
                                                <div class="progress-bar bg-primary bg-success"
                                                     role="progressbar"
                                                     style="width: 100%"
                                                     aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                            <hr class="mt-1 mb-1" />
                                        </div>
                                        <input type="hidden" name="<?php echo $this->name; ?>[gallery_image][]" class="gallery_url" value="<?php echo $image; ?>" />
                                        <input type="hidden" name="<?php echo $this->name; ?>[gallery_source][]" class="gallery_source" value="server" />
                                    </li>
                                <?php endfor; ?>
                            <?php endif; ?>
                            <li class="text-muted text-center empty"<?php if (isset($this->value->gallery_image) && is_array($this->value->gallery_image) && count($this->value->gallery_image)) echo ' style="display: none;"'; ?>><?php echo Text::_('PLG_CONTENT_GALLERY_NO_FILE_UPLOADED'); ?></li>
                        </ul>
                    </div>
                </div>
            </div><!-- /file list -->

            <div class="row-addon">
                <div class="col-addon">
                    <div class="card h-100">
                        <div class="card-header">
                            <?php echo Text::_('PLG_CONTENT_GALLERY_DEBUG_MESSAGES'); ?>
                        </div>

                        <ul class="list-group list-group-flush" id="gallery_debug">
                            <li class="list-group-item text-muted empty"><?php echo Text::_('PLG_CONTENT_GALLERY_LOADING_PLUGIN'); ?></li>
                        </ul>
                    </div>
                </div>
            </div> <!-- /debug -->

        </div> <!-- /container -->
        <input type="hidden" name="<?php echo $this->name; ?>[gallery_folder]" value="<?php echo $gallery_tmp; ?>" />
        <!-- File item template -->
        <script type="text/html" id="gallery_files_template">
            <li class="media">
                <img class="mr-3 mb-2 preview-img" src="https://via.placeholder.com/150" alt="Generic placeholder image">
                <div class="media-body mb-1">
                    <p class="mb-2">
                        <strong class="filename">%%filename%%</strong> - Status: <span class="text-muted">Waiting</span> - <em class="grid_featured"><input type="radio" name="<?php echo $this->name; ?>[image_featured]" class="grid_image_featured" value="" /> <?php echo Text::_('JFEATURED'); ?></em> - <a href="#" class="delete_grid_image"><?php echo Text::_('JACTION_DELETE'); ?></a>
                    </p>
                    <p class="mb-2">
                        <input type="text" class="inputbox" name="<?php echo $this->name; ?>[gallery_image_title][]" placeholder="Title..." />
                    </p>
                    <div class="progress mb-2">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                             role="progressbar"
                             style="width: 0%"
                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <hr class="mt-1 mb-1" />
                </div>
                <input type="hidden" name="<?php echo $this->name; ?>[gallery_image][]" class="gallery_url" value="" />
                <input type="hidden" name="<?php echo $this->name; ?>[gallery_source][]" class="gallery_source" value="client" />
            </li>
        </script>

        <!-- Debug item template -->
        <script type="text/html" id="gallery_debug_template">
            <li class="list-group-item text-%%color%%"><strong>%%date%%</strong>: %%message%%</li>
        </script>

        <?php
        $html   =   '</div><div ' . $class . ' style="margin-top: 30px;">' .ob_get_clean();
//        $html   .= '<input type="hidden" name="'.$this->name.'" id="'.$this->id.'" class="tzgallery" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '" />';
        return $html;
    }
}