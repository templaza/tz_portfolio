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

use Joomla\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\Folder;

class TZ_PortfolioSetupControllerInstall_Copy extends TZ_PortfolioSetupControllerLegacy
{

    /**
     * Responsible to copy the necessary files over.
     *
     * @since	2.2.7
     * @access	public
     */
    public function initialize()
    {
        // Get which type of data we should be copying
        $type = $this->input->get('type', '');

        // Get the temporary path from the server.
        $tmpPath = $this->input->get('path', '', 'default');

        // Get the path to the zip file
        $archivePath = $tmpPath . '/' . $type . '.zip';

        // Where the extracted items should reside
        $path = $tmpPath . '/' . $type;

        if(file_exists($archivePath)){
            // Extract the admin folder
            $state = $this->tppExtract($archivePath, $path);

            if (!$state) {
                $this->setInfo(Text::sprintf('COM_TZ_PORTFOLIO_SETUP_COPY_ERROR_UNABLE_EXTRACT', $type), false);
                return $this->output();
            }
        }

        // Look for files in this path
        $files = Folder::files( $path , '.' , false , true );

        // Look for folders in this path
        $folders = Folder::folders( $path , '.' , false , true );

        // Construct the target path first.
        if ($type == 'admin') {
            $target = JPATH_ADMINISTRATOR . '/components/com_tz_portfolio';
        }

        if ($type == 'site') {
            $target = JPATH_ROOT . '/components/com_tz_portfolio';
        }

        if ($type == 'media') {
            $target = JPATH_ROOT . '/media';
        }

        // Ensure that the target folder exists
        if (!is_dir($target)) {
            Folder::create($target);
        }

        // Scan for files in the folder
        $totalFiles = 0;
        $totalFolders = 0;

        foreach ($files as $file) {
            $name = basename($file);

            $targetFile = $target . '/' . $name;

            // Copy the file
            File::copy($file, $targetFile);

            $totalFiles++;
        }

        // Scan for folders in this folder
        foreach ($folders as $folder) {
            $name = basename($folder);
            $targetFolder = $target . '/' . $name;

            // Copy the folder across
            Folder::copy($folder, $targetFolder, '', true);

            $totalFolders++;
        }

        // Replace uninstall sql of TZ Portfolio Plus
        $tzplusPath = JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus';
        if(file_exists($tzplusPath.'/tz_portfolio_plus.xml')
            && $type == 'admin'){
            File::write($tzplusPath.'/install/uninstall.sql', '');
        }

        $result = $this->getResultObj(Text::sprintf('COM_TZ_PORTFOLIO_SETUP_COPY_FILES_SUCCESS', $totalFiles, $totalFolders), true);

        return $this->output($result);
    }
}
