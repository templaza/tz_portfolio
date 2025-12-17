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

namespace TemPlaza\Component\TZ_Portfolio\Site\Model;

// no direct access
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Component\ComponentHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\QueryHelper;

class CategoriesModel extends ListModel
{

    /**
     * Model context string.
     *
     * @var		string
     */
    public $_context = 'com_tz_portfolio.categories';

    /**
     * The category context (allows other extensions to derived from this model).
     *
     * @var		string
     */
    protected $_extension = 'com_tz_portfolio';

    private $_parent = null;

    private $_items = null;

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState($ordering = null, $direction = null)
    {
        $app = Factory::getApplication();
        $this->setState('filter.extension', $this->_extension);

        // Get the parent id if defined.
        $parentId = $app -> input -> getInt('id');
        $this->setState('filter.parentId', $parentId);

        $params     = $app -> getParams();
        $this->setState('params', $params);

        $this->setState('filter.published',	1);
        $this->setState('filter.access',	true);
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param	string		$id	A prefix for the store id.
     *
     * @return	string		A store id.
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id	.= ':'.$this->getState('filter.extension');
        $id	.= ':'.$this->getState('filter.published');
        $id	.= ':'.$this->getState('filter.access');
        $id	.= ':'.$this->getState('filter.parentId');

        return parent::getStoreId($id);
    }

    /**
     * Redefine the function an add some properties to make the styling more easy
     *
     * @param	bool	$recursive	True if you want to return children recursively.
     *
     * @return	mixed	An array of data items on success, false on failure.
     * @since	1.6
     */
    public function getItems($recursive = false)
    {
        $store = $this->getStoreId();

        if (!isset($this->cache[$store]))
        {
            $app = Factory::getApplication();
            $menu = $app->getMenu();
            $active = $menu->getActive();
            $params = new Registry;

            if ($active)
            {
                $params->loadString($active->getParams());
            }

            $options = array();
            $options['countItems'] = $params->get('show_cat_num_articles_cat', 1) || !$params->get('show_empty_categories_cat', 0);
            $categories = Categories::getInstance('TZ_Portfolio', $options);
            $this->_parent = $categories->get($this->getState('filter.parentId', 'root'));

            if (is_object($this->_parent))
            {
                $this->cache[$store] = $this->_parent->getChildren($recursive);
            }
            else
            {
                $this->cache[$store] = false;
            }
        }

        return $this->cache[$store];
    }

    public function getParent()
    {
        if (!is_object($this->_parent)) {
            $this->getItems();
        }

        return $this->_parent;
    }
}
?>