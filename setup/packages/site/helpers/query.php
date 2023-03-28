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

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/**
 * Content Component Query Helper.
 */
class TZ_Portfolio_PlusHelperQuery
{
	/**
	 * Translate an order code to a field for primary category ordering.
	 *
	 * @param	string	$orderby	The ordering code.
	 *
	 * @return	string	The SQL field(s) to order by.
	 * @since	1.5
	 */
	public static function orderbyPrimary($orderby, $tblprefix = 'cc')
	{
		switch ($orderby)
		{
			case 'alpha' :
				$orderby = $tblprefix.'.path, ';
				break;

			case 'ralpha' :
				$orderby = $tblprefix.'.path DESC, ';
				break;

			case 'order' :
				$orderby = $tblprefix.'.lft, ';
				break;

			default :
				$orderby = '';
				break;
		}

		return $orderby;
	}

	/**
	 * Translate an order code to a field for secondary category ordering.
	 *
	 * @param	string	$orderby	The ordering code.
	 * @param	string	$orderDate	The ordering code for the date.
	 *
	 * @return	string	The SQL field(s) to order by.
	 * @since	1.5
	 */
	public static function orderbySecondary($orderby, $orderDate = 'created', $tblprefix = 'c')
	{
        $db = Factory::getDbo();

		$queryDate  = self::getQueryDate($orderDate, $tblprefix);

		switch ($orderby)
		{
			case 'date' :
				$orderby = $queryDate;
				break;

			case 'rdate' :
				$orderby = $queryDate . ' DESC ';
				break;

			case 'alpha' :
				$orderby = $tblprefix.'.title';
				break;

			case 'ralpha' :
				$orderby = $tblprefix.'.title DESC';
				break;

			case 'hits' :
				$orderby = $tblprefix.'.hits DESC';
				break;

			case 'rhits' :
				$orderby = $tblprefix.'.hits';
				break;

            case 'random':
                $orderby = $db->getQuery(true)->rand();
                break;

			case 'order' :
				$orderby = $tblprefix.'.ordering';
				break;

            case 'rorder' :
                $orderby = $tblprefix.'.ordering DESC';
                break;

			case 'author' :
				$orderby = 'author';
				break;

			case 'rauthor' :
				$orderby = 'author DESC';
				break;

			case 'front' :
				$orderby = 'fp.ordering';
				break;

			case 'priority' :
				$orderby = $tblprefix.'.priority';
				break;
			case 'rpriority' :
				$orderby = $tblprefix.'.priority DESC';
				break;

			default :
				$orderby = $tblprefix.'.ordering';
				break;
		}

		return $orderby;
	}

	/**
	 * Translate an order code to a field for primary category ordering.
	 *
	 * @param	string	$orderDate	The ordering code.
	 *
	 * @return	string	The SQL field(s) to order by.
	 * @since	1.6
	 */
	public static function getQueryDate($orderDate, $tblprefix = 'c') {

		switch ($orderDate)
		{
            case 'modified' :
                $queryDate = ' CASE WHEN '.$tblprefix.'.modified IS NULL THEN '.$tblprefix.'.created ELSE '.$tblprefix.'.modified END';
                break;

            // use created if publish_up is not set
            case 'published' :
                $queryDate = ' CASE WHEN '.$tblprefix.'.publish_up IS NULL THEN '.$tblprefix.'.created ELSE '.$tblprefix.'.publish_up END ';
                break;

            case 'unpublished':
                $queryDate = ' CASE WHEN '.$tblprefix.'.publish_down IS NULL THEN '.$tblprefix.'.created ELSE '.$tblprefix.'.publish_down END ';
                break;

			case 'created' :
			default :
				$queryDate = ' '.$tblprefix.'.created ';
				break;
		}

		return $queryDate;
	}

	/**
	 * Get join information for the voting query.
	 *
	 * @param	JRegistry	$param	An options object for the article.
	 *
	 * @return	array		A named array with "select" and "join" keys.
	 * @since	1.5
	 */
	public static function buildVotingQuery($params=null)
	{
		if (!$params) {
			$params = JComponentHelper::getParams('com_tz_portfolio_plus');
		}

		$voting = $params->get('show_vote');

		if ($voting) {
			// calculate voting count
			$select = ' , ROUND(v.rating_sum / v.rating_count) AS rating, v.rating_count';
			$join = ' LEFT JOIN #__content_rating AS v ON a.id = v.content_id';
		}
		else {
			$select = '';
			$join = '';
		}

		$results = array ('select' => $select, 'join' => $join);

		return $results;
	}

	/**
	 * Method to order the intro articles array for ordering
	 * down the columns instead of across.
	 * The layout always lays the introtext articles out across columns.
	 * Array is reordered so that, when articles are displayed in index order
	 * across columns in the layout, the result is that the
	 * desired article ordering is achieved down the columns.
	 *
	 * @param	array	$articles	Array of intro text articles
	 * @param	integer	$numColumns	Number of columns in the layout
	 *
	 * @return	array	Reordered array to achieve desired ordering down columns
	 * @since	1.6
	 */
	public static function orderDownColumns(&$articles, $numColumns = 1)
	{
		$count = count($articles);

		// just return the same array if there is nothing to change
		if ($numColumns == 1 || !is_array($articles) || $count <= $numColumns) {
			$return = $articles;
		}
		// we need to re-order the intro articles array
		else {
			// we need to preserve the original array keys
			$keys = array_keys($articles);

			$maxRows = ceil($count / $numColumns);
			$numCells = $maxRows * $numColumns;
			$numEmpty = $numCells - $count;
			$index = array();

			// calculate number of empty cells in the array


			// fill in all cells of the array
			// put -1 in empty cells so we can skip later

			for ($row = 1, $i = 1; $row <= $maxRows; $row++)
			{
				for ($col = 1; $col <= $numColumns; $col++)
				{
					if ($numEmpty > ($numCells - $i)) {
						// put -1 in empty cells
						$index[$row][$col] = -1;
					}
					else {
						// put in zero as placeholder
						$index[$row][$col] = 0;
					}
					$i++;
				}
			}

			// layout the articles in column order, skipping empty cells
			$i = 0;
			for ($col = 1; ($col <= $numColumns) && ($i < $count); $col++)
			{
				for ($row = 1; ($row <= $maxRows) && ($i < $count); $row++)
				{
					if ($index[$row][$col] != - 1) {
						$index[$row][$col] = $keys[$i];
						$i++;
					}
				}
			}

			// now read the $index back row by row to get articles in right row/col
			// so that they will actually be ordered down the columns (when read by row in the layout)
			$return = array();
			$i = 0;
			for ($row = 1; ($row <= $maxRows) && ($i < $count); $row++)
			{
				for ($col = 1; ($col <= $numColumns) && ($i < $count); $col++)
				{
					$return[$keys[$i]] = $articles[$index[$row][$col]];
					$i++;
				}
			}
		}
		return $return;
	}
}
