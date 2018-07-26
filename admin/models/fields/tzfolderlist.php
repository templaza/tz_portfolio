<?php
/**
 *------------------------------------------------------------------------------
 * @package       Plazart Framework for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2012-2013 TemPlaza.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       TemPlaza
 * @Link:         http://templaza.com
 *------------------------------------------------------------------------------
 */
/**
 *------------------------------------------------------------------------------
 * @package       T3 Framework for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2013 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt, JoomlaBamboo, (contribute to this project at github
 *                & Google group to become co-author)
 * @Google group: https://groups.google.com/forum/#!forum/t3fw
 * @Link:         http://t3-framework.org
 *------------------------------------------------------------------------------
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('folderlist');

/**
 * Supports an HTML select list of files
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldTZFolderList extends JFormFieldFolderList
{

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'TZFolderList';

    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        $setup  = parent::setup($element, $value, $group);

        if($this -> multiple) {
            JHtml::_('formbehavior.chosen', '#' . $this->id);
        }

        return $setup;
    }

	/**
	 * Method to get the list of files for the field options.
	 * Specify the target directory with a directory attribute
	 * Attributes allow an exclude mask and stripping of extensions from file name.
	 * Default attribute may optionally be set to null (no file) or -1 (use a default).
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		$path = (string)$this->element['directory'];

		if (!is_dir($path)) {
			$this->element['directory'] = JPATH_SITE . DIRECTORY_SEPARATOR . $path;
		}
		// Get the path in which to search for file options.
		$directory = (string)$this->element['directory'];
		$directory = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $directory);
		$this->directory = $directory;
		return parent::getOptions();
	}
}
