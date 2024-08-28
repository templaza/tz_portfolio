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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\MVC\Controller\AdminController;

class TZ_PortfolioSetupControllerLegacy extends AdminController {

    private $result = array();

    public function __construct($config = array())
    {
        parent::__construct($config);

        $this -> app    = Factory::getApplication();
        $this -> input  = $this -> app -> input;
    }

    public function output($data = array())
    {
        header('Content-type: text/x-json; UTF-8');

        if (empty($data)) {
            $data = $this->result;
        }

        echo json_encode($data);
        exit;
    }

    public function setInfo($message, $state = true, $args = array())
    {
        $result = new stdClass();
        $result->state = $state;
        $result->message = Text::_($message);

        if (!empty($args)) {
            foreach ($args as $key => $val) {
                $result->$key = $val;
            }
        }

        $this->result = $result;
    }

    /**
     * method to extract zip file in installation part
     *
     * @since	2.2.7
     * @access	public
     */
    public function tppExtract($destination, $extracted)
    {
        $archive = new Joomla\Archive\Archive();
        $state = $archive->extract($destination, $extracted);

        return $state;
    }

    /**
     * Allows caller to set the data
     *
     * @since	2.2.7
     * @access	public
     */
    public function getResultObj($message, $state, $stateMessage = '')
    {
        $obj = new stdClass();
        $obj->state = $state;
        $obj->stateMessage = $stateMessage;
        $obj->message = Text::_($message);

        return $obj;
    }


    /**
     * Determine if database is set to mysql or not.
     *
     * @since	2.2.7
     * @access	public
     */
    public function isMySQL()
    {
        $jConfig = Factory::getConfig();
        $dbType = $jConfig->get('dbtype');

        return $dbType == 'mysql' || $dbType == 'mysqli';
    }

    /**
     * Determine if mysql can support utf8mb4 or not.
     *
     * @since	2.2.7
     * @access	public
     */
    public function hasUTF8mb4Support()
    {
        static $_cache = null;

        if (is_null($_cache)) {

            $db = Factory::getDBO();

            if (method_exists($db, 'hasUTF8mb4Support')) {
                $_cache = $db->hasUTF8mb4Support();
                return $_cache;
            }

            // we check the server version 1st
            $server_version = $db->getVersion();
            if (version_compare($server_version, '5.5.3', '<')) {
                $_cache = false;
                return $_cache;
            }

            $client_version = '5.0.0';

            if (function_exists('mysqli_get_client_info')) {
                $client_version = mysqli_get_client_info();
            } else if (function_exists('mysql_get_client_info')) {
                $client_version = mysql_get_client_info();
            }

            if (strpos($client_version, 'mysqlnd') !== false) {
                $client_version = preg_replace('/^\D+([\d.]+).*/', '$1', $client_version);
                $_cache = version_compare($client_version, '5.0.9', '>=');
            } else {
                $_cache = version_compare($client_version, '5.5.3', '>=');
            }

        }

        return $_cache;
    }

    /**
     * Convert utf8mb4 to utf8
     *
     * @since	2.2.7
     * @access	public
     */
    public function convertUtf8mb4QueryToUtf8($query)
    {
        if ($this->hasUTF8mb4Support())
        {
            return $query;
        }

        // If it's not an ALTER TABLE or CREATE TABLE command there's nothing to convert
        $beginningOfQuery = substr($query, 0, 12);
        $beginningOfQuery = strtoupper($beginningOfQuery);

        if (!in_array($beginningOfQuery, array('ALTER TABLE ', 'CREATE TABLE')))
        {
            return $query;
        }

        // Replace utf8mb4 with utf8
        return str_replace('utf8mb4', 'utf8', $query);
    }


    /**
     * Splits a string of multiple queries into an array of individual queries.
     *
     * @since	2.2.7
     * @access	public
     */
    public function splitSql($contents)
    {
        $queries = DatabaseDriver::splitSql($contents);

        return $queries;
    }
}