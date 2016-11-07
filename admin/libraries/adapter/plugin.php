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

JLoader::import('com_tz_portfolio_plus.includes.framework',JPATH_ADMINISTRATOR.'/components');

class TZ_Portfolio_PlusInstallerAdapterPlugin extends JInstallerAdapterPlugin{

    public function __construct(JInstaller $parent, JDatabaseDriver $db, array $options = array())
    {

        parent::__construct($parent, $db, $options);

        // Get a generic TZ_Portfolio_PlusTableExtension instance for use if not already loaded
        if (!($this->extension instanceof TZ_Portfolio_PlusTableExtensions)) {
            JTable::addIncludePath(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH . DIRECTORY_SEPARATOR . 'tables');
            $this->extension = JTable::getInstance('Extensions', 'TZ_Portfolio_PlusTable');
        }

        if (is_object($this->extension) && isset($this->extension->id)) {
            $this->extension->extension_id = $this->extension->id;
        }

        $this->type = 'tz_portfolio_plus-'.strtolower(str_replace('TZ_Portfolio_PlusInstallerAdapter', '', get_called_class()));
    }

    protected function setupInstallPaths()
    {
        parent::setupInstallPaths();

        $this->parent->setPath('extension_root', COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $this->group . '/' . $this->element);
    }

    protected function checkExistingExtension()
    {
        try
        {
            $this->currentExtensionId = $this->extension->find(
                array('type' => $this->type, 'element' => $this->element, 'folder' => $this->group)
            );

            // Set extension_id = id because table extension of joomla with key is "extension_id" so plus is "id"
            $this -> extension -> extension_id  = $this -> currentExtensionId;
        }
        catch (RuntimeException $e)
        {
            // Install failed, roll back changes
            throw new RuntimeException(
                JText::sprintf(
                    'JLIB_INSTALLER_ABORT_ROLLBACK',
                    JText::_('JLIB_INSTALLER_' . $this->route),
                    $e->getMessage()
                ),
                $e->getCode(),
                $e
            );
        }
    }

    public function getElement($element = null)
    {
        if (!$element)
        {
            // Backward Compatibility
            // @todo Deprecate in future version
            if (count($this->getManifest()->files->children()))
            {
                $type = (string) $this->getManifest()->attributes()->type;
                $type = str_replace('tz_portfolio_plus-','',$type);

                foreach ($this->getManifest()->files->children() as $file)
                {
                    if ((string) $file->attributes()->$type)
                    {
                        $element = (string) $file->attributes()->$type;

                        break;
                    }
                }
            }
        }

        return $element;
    }


    protected function setupScriptfile()
    {
        // If there is an manifest class file, lets load it; we'll copy it later (don't have dest yet)
        $manifestScript = (string) $this->getManifest()->scriptfile;

        if ($manifestScript)
        {
            $manifestScriptFile = $this->parent->getPath('source') . '/' . $manifestScript;

            if (is_file($manifestScriptFile))
            {
                // Load the file
                include_once $manifestScriptFile;
            }

            $classname = $this->getScriptClassName();

            if (class_exists($classname))
            {
                // Create a new instance
                $this->parent->manifestClass = new $classname($this);

                // And set this so we can copy it later
                $this->manifest_script = $manifestScript;
            }
        }
    }

    protected function parseQueries()
    {
        // Let's run the queries for the extension
        if (in_array($this->route, array('install', 'discover_install', 'uninstall')))
        {
            // This method may throw an exception, but it is caught by the parent caller
            if (!$this->doDatabaseTransactions())
            {
                throw new RuntimeException(
                    JText::sprintf(
                        'JLIB_INSTALLER_ABORT_SQL_ERROR',
                        JText::_('JLIB_INSTALLER_' . strtoupper($this->route)),
                        $this->db->stderr(true)
                    )
                );
            }

//            // Set the schema version to be the latest update version
//            if ($this->getManifest()->update)
//            {
//                $this->parent->setSchemaVersion($this->getManifest()->update->schemas, $this->extension->extension_id);
//            }
        }
//        elseif ($this->route == 'update')
//        {
//            if ($this->getManifest()->update)
//            {
//                $result = $this->parent->parseSchemaUpdates($this->getManifest()->update->schemas, $this->extension->extension_id);
//
//                if ($result === false)
//                {
//                    // Install failed, rollback changes
//                    throw new RuntimeException(
//                        JText::sprintf(
//                            'JLIB_INSTALLER_ABORT_SQL_ERROR',
//                            JText::_('JLIB_INSTALLER_' . strtoupper($this->route)),
//                            $this->db->stderr(true)
//                        )
//                    );
//                }
//            }
//        }
    }

    protected function storeExtension($deleteExisting = false)
    {
        // The extension is stored during prepareDiscoverInstall for discover installs
        if ($this->route == 'discover_install')
        {
            return;
        }

        // Add or update an entry to the extension table
        $this->extension->id        = $this -> extension -> extension_id;
        $this->extension->name      = $this->name;
        $this->extension->type      = $this->type;
        $this->extension->folder    = $this->group;
        $this->extension->element   = $this->element;

        unset($this -> extension -> extension_id);

        // If we are told to delete existing extension entries then do so.
        if ($deleteExisting)
        {
            $db = $this->parent->getDbo();

            $query = $db->getQuery(true)
                ->select($db->qn('id'))
                ->from($db->qn('#__tz_portfolio_plus_extensions'))
                ->where($db->qn('name') . ' = ' . $db->q($this->extension->name))
                ->where($db->qn('type') . ' = ' . $db->q($this->extension->type))
                ->where($db->qn('element') . ' = ' . $db->q($this->extension->element))
                ->where($db->qn('folder') . ' = ' . $db->q($this->extension->folder));

            $db->setQuery($query);

            $extension_ids = $db->loadColumn();

            if (!empty($extension_ids))
            {
                foreach ($extension_ids as $eid)
                {

                    // Remove the extension record itself
                    /** @var JTableExtension $extensionTable */
                    $extensionTable = JTable::getInstance('Extensions','TZ_Portfolio_PlusTable');
                    $extensionTable->delete($eid);
                }
            }
        }

        // If there is not already a row, generate a heap of defaults
        if (!$this->currentExtensionId)
        {
            $this->extension->folder    = $this -> group;
            $this->extension->published   = 1;
            $this->extension->protected = 0;
            $this->extension->access    = 0;
            $this->extension->params    = $this->parent->getParams();
        }

        $this->extension->manifest_cache = $this->parent->generateManifestCache();

        $couldStore = $this->extension->store();

        if (!$couldStore && $deleteExisting)
        {
            // Install failed, roll back changes
            throw new RuntimeException(
                JText::sprintf(
                    'JLIB_INSTALLER_ABORT_COMP_INSTALL_ROLLBACK',
                    $this->extension->getError()
                )
            );
        }

        if (!$couldStore && !$deleteExisting)
        {
            // Maybe we have a failed installation (e.g. timeout). Let's retry after deleting old records.
            $this->storeExtension(true);
        }

        // Set extension_id = id because table extension of joomla with key is "extension_id" so plus is "id"
        $this -> extension -> extension_id  = $this -> extension -> id;
    }


    public function uninstall($id)
    {
        $this->route = 'uninstall';

        $row = null;
        $retval = true;
        $db = $this->parent->getDbo();

        // First order of business will be to load the plugin object table from the database.
        // This should give us the necessary information to proceed.
        $row = JTable::getInstance('Extensions','TZ_Portfolio_PlusTable');

        if (!$row->load((int) $id))
        {
            JLog::add(JText::_('JLIB_INSTALLER_ERROR_PLG_UNINSTALL_ERRORUNKOWNEXTENSION'), JLog::WARNING, 'jerror');

            return false;
        }

        // Is the plugin we are trying to uninstall a core one?
        // Because that is not a good idea...
        if ($row->protected)
        {
            JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_PLG_UNINSTALL_WARNCOREPLUGIN', $row->name), JLog::WARNING, 'jerror');

            return false;
        }

        // Get the plugin folder so we can properly build the plugin path
        if (trim($row->folder) == '')
        {
            JLog::add(JText::_('JLIB_INSTALLER_ERROR_PLG_UNINSTALL_FOLDER_FIELD_EMPTY'), JLog::WARNING, 'jerror');

            return false;
        }

        // Set the plugin root path
        $this->parent->setPath('extension_root', COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $row->folder . '/' . $row->element);

        $this->parent->setPath('source', $this->parent->getPath('extension_root'));

        $this->parent->findManifest();
        $this->setManifest($this->parent->getManifest());

        // Attempt to load the language file; might have uninstall strings
        $this->parent->setPath('source', COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $row->folder . '/' . $row->element);
        $this->loadLanguage(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH . '/' . $row->folder . '/' . $row->element);

        /**
         * ---------------------------------------------------------------------------------------------
         * Installer Trigger Loading
         * ---------------------------------------------------------------------------------------------
         */

        // If there is an manifest class file, let's load it; we'll copy it later (don't have dest yet)
        $manifestScript = (string) $this->getManifest()->scriptfile;

        if ($manifestScript)
        {
            $manifestScriptFile = $this->parent->getPath('source') . '/' . $manifestScript;

            if (is_file($manifestScriptFile))
            {
                // Load the file
                include_once $manifestScriptFile;
            }
            // If a dash is present in the folder, remove it
            $folderClass = str_replace('-', '', $row->folder);

            // Set the class name
            $classname = 'Plg' . $folderClass . $row->element . 'InstallerScript';

            if (class_exists($classname))
            {
                // Create a new instance
                $this->parent->manifestClass = new $classname($this);

                // And set this so we can copy it later
                $this->set('manifest_script', $manifestScript);
            }
        }

        // Run preflight if possible (since we know we're not an update)
        ob_start();
        ob_implicit_flush(false);

        if ($this->parent->manifestClass && method_exists($this->parent->manifestClass, 'preflight'))
        {
            if ($this->parent->manifestClass->preflight($this->route, $this) === false)
            {
                // Preflight failed, rollback changes
                $this->parent->abort(JText::_('JLIB_INSTALLER_ABORT_PLG_INSTALL_CUSTOM_INSTALL_FAILURE'));

                return false;
            }
        }

        // Create the $msg object and append messages from preflight
        $msg = ob_get_contents();
        ob_end_clean();

        // Let's run the queries for the plugin
        $utfresult = $this->parent->parseSQLFiles($this->getManifest()->uninstall->sql);

        if ($utfresult === false)
        {
            // Install failed, rollback changes
            $this->parent->abort(JText::sprintf('JLIB_INSTALLER_ABORT_PLG_UNINSTALL_SQL_ERROR', $db->stderr(true)));

            return false;
        }

        // Run the custom uninstall method if possible
        ob_start();
        ob_implicit_flush(false);

        if ($this->parent->manifestClass && method_exists($this->parent->manifestClass, 'uninstall'))
        {
            $this->parent->manifestClass->uninstall($this);
        }

        // Append messages
        $msg .= ob_get_contents();
        ob_end_clean();

        // Remove the plugin files
        $this->parent->removeFiles($this->getManifest()->files, -1);

        // Remove all media and languages as well
        $this->parent->removeFiles($this->getManifest()->media);
        $this->parent->removeFiles($this->getManifest()->languages, 1);

        // Now we will no longer need the plugin object, so let's delete it
        $row->delete($row->extension_id);
        unset($row);

        // Remove the plugin's folder
        JFolder::delete($this->parent->getPath('extension_root'));

        if ($msg != '')
        {
            $this->parent->set('extension_message', $msg);
        }

        return $retval;
    }
}