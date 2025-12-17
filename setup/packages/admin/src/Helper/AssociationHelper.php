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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\CategoriesHelper as TZ_PortfolioHelperCategories;

/**
 * Category Component Association Helper
 *
 */
abstract class AssociationHelper
{
	public static $category_association = true;

	/**
	 * Method to get the associations for a given category
	 *
	 * @param   integer  $id         Id of the item
	 * @param   string   $extension  Name of the component
	 *
	 * @return  array    Array of associations for the component categories
	 *
	 * @since  3.0
	 */

	public static function getCategoryAssociations($id = 0, $extension = 'com_tz_portfolio',$view = '')
	{
		$return = array();

		if ($id)
		{
            // Load route helper
            jimport('helper.route', JPATH_COMPONENT_SITE);

            $helperClassname = 'TZ_Portfolio_PlusHelperRoute';

            $associations = TZ_PortfolioHelperCategories::getAssociations($id, $extension);

            foreach ($associations as $tag => $item)
            {
                if (class_exists($helperClassname) && is_callable(array($helperClassname, 'getCategoryRoute')))
                {
                    $return[$tag] = $helperClassname::getCategoryRoute($item, $tag);
                }
                else
                {
                    $return[$tag] = 'index.php?option=com_tz_portfolio&amp;view=category&id=' . $item;
                }
            }
        }

		return $return;
	}

    public static function getArticleAssociations($id, $extension = 'com_tz_portfolio', $pk = 'id', $aliasField = 'alias', $catField = 'catid')
    {
        $associations = array();
        $db     = Factory::getDbo();
        $query  = $db->getQuery(true)
            ->select($db->quoteName('c2.language'))
            ->from($db->quoteName('#__tz_portfolio_plus_content', 'c'))
            ->join('INNER', $db->quoteName('#__associations', 'a') . ' ON a.id = c.' . $db->quoteName($pk) . ' AND a.context=' . $db->quote('com_tz_portfolio.article.item'))
            ->join('INNER', $db->quoteName('#__associations', 'a2') . ' ON a.key = a2.key')
            ->join('INNER', $db->quoteName('#__tz_portfolio_plus_content', 'c2') . ' ON a2.id = c2.' . $db->quoteName($pk));

        // Use alias field ?
        if (!empty($aliasField))
        {
            $query->select(
                $query->concatenate(
                    array(
                        $db->quoteName('c2.' . $pk),
                        $db->quoteName('c2.' . $aliasField)
                    ),
                    ':'
                ) . ' AS ' . $db->quoteName($pk)
            );
        }
        else
        {
            $query->select($db->quoteName('c2.' . $pk));
        }

        // Use catid field ?
        if (!empty($catField))
        {
            $query->join(
                'INNER',
                $db->quoteName('#__tz_portfolio_plus_content_category_map', 'm') . ' ON ' . $db->quoteName('c2.' . $pk)
                . ' = m.contentid'
            )
            -> join('INNER', $db -> quoteName('#__tz_portfolio_plus_categories','ca').' ON '. $db -> quoteName('m.'.$catField)
                .' = ca.id  AND ca.extension = ' . $db->quote($extension))
                ->select(
                    $query->concatenate(
                        array('ca.id', 'ca.alias'),
                        ':'
                    ) . ' AS ' . $db->quoteName($catField)
                );
        }

        $query->where('c.' . $pk . ' = ' . (int) $id);

        $db->setQuery($query);

        try
        {
            $items = $db->loadObjectList('language');
        }
        catch (\RuntimeException $e)
        {
            throw new \Exception($e->getMessage(), 500);
        }

        if ($items)
        {
            foreach ($items as $tag => $item)
            {
                // Do not return itself as result
                if ((int) $item->{$pk} != $id)
                {
                    $associations[$tag] = $item;
                }
            }
        }

        return $associations;
    }
}
