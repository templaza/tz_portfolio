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

class TZ_Portfolio_PlusInstallerAdapterTemplate extends JInstallerAdapterTemplate{

    public function __construct(JInstaller $parent, JDatabaseDriver $db, array $options = array())
    {

        // Get a generic JTableExtension instance for use if not already loaded
        if (!($this->extension instanceof JTableInterface))
        {
            $this->extension = JTable::getInstance('Extensions','TZ_Portfolio_PlusTable');
            $this -> extension -> extension_id  = $this -> extension -> id;
        }

        // Sanity check, make sure the type is set by taking the adapter name from the class name
        if (!$this->type)
        {
            $this->type = 'tz_portfolio_plus-'.strtolower(str_replace('TZ_Portfolio_PlusInstallerAdapter', '', get_called_class()));
        }

        parent::__construct($parent, $db, $options);
    }

    protected function checkExistingExtension()
    {
        try
        {
            $this->currentExtensionId = $this->extension->find(
                array(
                    'element'   => $this->element,
                    'type'      => $this->type
                )
            );
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

    protected function setupInstallPaths()
    {
//        // Get the client application target
//        $cname = (string) $this->getManifest()->attributes()->client;
//
//        if ($cname)
//        {
//            // Attempt to map the client to a base path
//            $client = JApplicationHelper::getClientInfo($cname, true);
//
//            if ($client === false)
//            {
//                throw new RuntimeException(JText::sprintf('JLIB_INSTALLER_ABORT_TPL_INSTALL_UNKNOWN_CLIENT', $cname));
//            }
//
//            $basePath = $client->path;
//            $this->clientId = $client->id;
//        }
//        else
//        {
//            // No client attribute was found so we assume the site as the client
//            $basePath = JPATH_SITE;
//            $this->clientId = 0;
//        }

        // Set the template root path
        if (empty($this->element))
        {
            throw new RuntimeException(
                JText::sprintf(
                    'JLIB_INSTALLER_ABORT_MOD_INSTALL_NOFILE',
                    JText::_('JLIB_INSTALLER_' . strtoupper($this->route))
                )
            );
        }

        $this->parent->setPath('extension_root', COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH . '/' . $this->element);
    }

    protected function storeExtension()
    {
        // Discover installs are stored a little differently
        if ($this->route == 'discover_install')
        {
            $manifest_details = JInstaller::parseXMLInstallFile($this->parent->getPath('manifest'));

            $this->extension->manifest_cache = json_encode($manifest_details);
            $this->extension->state = 0;
            $this->extension->name = $manifest_details['name'];
            $this->extension->published = 1;
            $this->extension->params = $this->parent->getParams();

            if (!$this->extension->store())
            {
                // Install failed, roll back changes
                throw new RuntimeException(JText::_('JLIB_INSTALLER_ERROR_TPL_DISCOVER_STORE_DETAILS'));
            }

            return;
        }

        // Was there a template already installed with the same name?
        if ($this->currentExtensionId)
        {
            if (!$this->parent->isOverwrite())
            {
                // Install failed, roll back changes
                throw new RuntimeException(
                    JText::_('JLIB_INSTALLER_ABORT_TPL_INSTALL_ALREADY_INSTALLED')
                );
            }

            // Load the entry and update the manifest_cache
            $this->extension->load($this->currentExtensionId);
        }
        else
        {
            $this->extension->type = 'tz_portfolio_plus-template';
            $this->extension->element = $this->element;

            // There is no folder for templates
            $this->extension->folder = '';
            $this->extension->published = 1;
            $this->extension->protected = 0;
            $this->extension->access = 1;
            $this->extension->params = $this->parent->getParams();
        }

        // Name might change in an update
        $this->extension->name = $this->name;
        $this->extension->manifest_cache = $this->parent->generateManifestCache();

        unset($this -> extension -> extension_id);

        if (!$this->extension->store())
        {
            // Install failed, roll back changes
            throw new RuntimeException(
                JText::sprintf(
                    'JLIB_INSTALLER_ABORT_ROLLBACK',
                    JText::_('JLIB_INSTALLER_' . strtoupper($this->route)),
                    $this->extension->getError()
                )
            );
        }

        // Set extension_id = id because table extension of joomla with key is "extension_id" so plus is "id"
        $this -> extension -> extension_id  = $this -> extension -> id;
    }


    protected function parseQueries()
    {
        if (in_array($this->route, array('install', 'discover_install')))
        {
            $db    = $this->db;
            $lang  = JFactory::getLanguage();
            $debug = $lang->setDebug(false);

            $columns = array($db->quoteName('template'),
                $db->quoteName('home'),
                $db->quoteName('title'),
                $db->quoteName('params')
            );

            $values = array(
                $db->quote($this->extension->element), $db->quote(0),
                $db->quote(JText::sprintf('JLIB_INSTALLER_DEFAULT_STYLE', JText::_($this->extension->name))),
                $db->quote($this->extension->params));

            $lang->setDebug($debug);

            // Insert record in #__template_styles
            $query = $db->getQuery(true);
            $query->insert($db->quoteName('#__tz_portfolio_plus_templates'))
                ->columns($columns)
                ->values(implode(',', $values));

            // There is a chance this could fail but we don't care...
            $db->setQuery($query)->execute();
        }
    }
}