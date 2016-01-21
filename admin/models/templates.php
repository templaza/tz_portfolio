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

//no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
jimport('joomla.filesystem.folder');

class TZ_Portfolio_PlusModelTemplates extends JModelList
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 't.id',
                'name', 't.name',
                'published', 't.published',
                'type', 't.type'
            );
        }

        parent::__construct($config);
    }

    function populateState($ordering = null, $direction = null){

        parent::populateState($ordering,$direction);

        $search  = $this -> getUserStateFromRequest('com_tz_portfolio_plus.templates.filter_search','filter_search',null,'string');
        $this -> setState('filter_search',$search);

        $status = $this->getUserStateFromRequest($this->context.'.filter.status', 'filter_status', '');
        $this->setState('filter.status', $status);

        $order  = $this -> getUserStateFromRequest('com_tz_portfolio_plus.templates.filter_order','filter_order',null,'string');
        $this -> setState('filter_order',$order);

        $orderDir  = $this -> getUserStateFromRequest('com_tz_portfolio_plus.templates.filter_order_Dir','filter_order_Dir','asc','string');
        $this -> setState('filter_order_Dir',$orderDir);
    }

    function getForm($data = array(), $loadData = true){
        $form = $this->loadForm('com_tz_portfolio_plus.template', 'template', array('control' => ''));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    public function getTemplates(){
        $items  = array();
        $tpl_path   = COM_TZ_PORTFOLIO_PLUS_PATH_SITE.DIRECTORY_SEPARATOR.'templates';
        if(!JFolder::exists($tpl_path)){
            return false;
        }

        if($folders    = JFolder::folders($tpl_path)){
            if(count($folders)){
                foreach($folders as $i => $folder){
                    $xmlFile    = $tpl_path.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.'template.xml';
                    if(JFile::exists($xmlFile)){
                        $installer  = JInstaller::getInstance($tpl_path.DIRECTORY_SEPARATOR.$folder);
                        if($manifest = $installer ->isManifest($xmlFile)){


                            $lang   = JFactory::getLanguage();
                            $lang -> load('tpl_'.((string) $manifest -> name),$tpl_path.DIRECTORY_SEPARATOR.$folder);

                            $item                   = new stdClass();
                            $item -> id             = $i;
                            $item -> name           = (string) $manifest -> name;
                            $item -> type           = (string) $manifest -> type;
                            $item -> version        = (string) $manifest -> version;
                            $item -> creationDate   = (string) $manifest -> creationDate;
                            $item -> author         = (string) $manifest -> author;
                            $item -> authorEmail    = (string) $manifest -> authorEmail;
                            $item -> description    = JText::_((string) $manifest -> description);
                            $items[]    = $item;
                        }
                    }
                }
            }
        }
        return $items;
//        var_dump(JFolder::folders($tpl_path)); die();
    }

    function getListQuery(){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('t.*');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_extensions').' AS t');

        $query -> where('type = '.$db -> quote('tz_portfolio_plus-template'));

        // Add the list ordering clause.
        $orderCol = $this->getState('list.ordering','t.id');
        $orderDirn = $this->getState('list.direction','desc');
        if ($orderCol == 't.ordering')
        {
            $orderCol = 't.name ' . $orderDirn . ', a.ordering';
        }

        // Filter by published state
        $status = $this->getState('filter.status');
        if ($status != '')
        {
            if ($status == '2')
            {
                $query->where('protected = 1');
            }
            elseif ($status == '3')
            {
                $query->where('protected = 0');
            }
            else
            {
                $query->where('protected = 0')
                    ->where('published=' . (int) $status);
            }
        }

        if(!empty($orderCol) && !empty($orderDirn)){
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

    public function getItems(){
        if($items = parent::getItems()){
            foreach($items as $item){
                if (strlen($item -> manifest_cache))
                {
                    $data = json_decode($item -> manifest_cache);

                    if ($data)
                    {
                        foreach ($data as $key => $value)
                        {
                            if ($key == 'type')
                            {
                                // Ignore the type field
                                continue;
                            }

                            $item -> $key = $value;
                        }
                    }
                }

//                $item -> author_info = @$item -> authorEmail . '<br />' . @$item -> authorUrl;
            }

            return $items;
        }
        return false;
    }

}