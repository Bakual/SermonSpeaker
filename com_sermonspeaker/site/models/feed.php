<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Model class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerModelFeed extends JModelLegacy
{
	/**
	 * Method to get an list of object
	 *
	 * @return  mixed  array of objects
	 */
	function getData()
	{
		$app    = JFactory::getApplication();
		$params = $app->getParams();
		$jinput = $app->input;
		$user   = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());

		$db = $this->getDbo();

		// Force utf8 connection
		$query = "SET character_set_results ='utf8';";
		$db->setQuery($query);

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select required fields from the table.
		$query->select('sermons.sermon_date, sermons.title, sermons.audiofile, sermons.videofile, sermons.notes');
		$query->select('sermons.sermon_time, sermons.id, sermons.picture, sermons.custom1, sermons.custom2');
		$query->select('sermons.catid, sermons.language');
		$query->select('CASE WHEN CHAR_LENGTH(sermons.alias) THEN CONCAT_WS(\':\', sermons.id, sermons.alias) ELSE sermons.id END as slug');
		$query->from('`#__sermon_sermons` AS sermons');

		// Join over the scriptures.
		$query->select('GROUP_CONCAT(script.book,"|",script.cap1,"|",script.vers1,"|",script.cap2,"|",script.vers2,"|",script.text '
			. 'ORDER BY script.ordering ASC SEPARATOR "!") AS scripture');
		$query->join('LEFT', '#__sermon_scriptures AS script ON script.sermon_id = sermons.id');
		$query->group('sermons.id');

		// Join over Speaker
		$query->select('speakers.title AS speaker_title, speakers.pic AS pic');
		$query->join('LEFT', '#__sermon_speakers AS speakers ON speakers.id = sermons.speaker_id');

		// Join over Series
		$query->select('series.title AS series_title, series.avatar');
		$query->join('LEFT', '#__sermon_series AS series ON series.id = sermons.series_id');

		// Join over Sermons Category.
		$query->join('LEFT', '#__categories AS c_sermons ON c_sermons.id = sermons.catid');
		$query->where('(sermons.catid = 0 OR (c_sermons.access IN (' . $groups . ') AND c_sermons.published = 1))');

		// Join over Speakers Category.
		$query->join('LEFT', '#__categories AS c_speaker ON c_speaker.id = speakers.catid');
		$query->where('(sermons.speaker_id = 0 OR speakers.catid = 0 OR (c_speaker.access IN (' . $groups . ') AND c_speaker.published = 1))');

		// Join over Series Category.
		$query->join('LEFT', '#__categories AS c_series ON c_series.id = series.catid');
		$query->where('(sermons.series_id = 0 OR series.catid = 0 OR (c_series.access IN (' . $groups . ') AND c_series.published = 1))');

		// Category filter
		if ($categoryId = $jinput->get('catid', $params->get('catid', 0), 'int'))
		{
			$type = $params->get('count_items_type', 'sermons');

			// Check if we have to include sermons from subcategories
			if ($levels = (int) $params->get('show_subcategory_content', 0))
			{
				// Create a subquery for the subcategory list
				$subQuery = $db->getQuery(true);
				$subQuery->select('sub.id');
				$subQuery->from('#__categories as sub');
				$subQuery->join('INNER', '#__categories as this ON sub.lft > this.lft AND sub.rgt < this.rgt');
				$subQuery->where('this.id = ' . (int) $categoryId);

				if ($levels > 0)
				{
					$subQuery->where('sub.level <= this.level + ' . $levels);
				}

				// Add the subquery to the main query
				$query->where('(' . $type . '.catid = ' . (int) $categoryId
					. ' OR ' . $type . '.catid IN (' . $subQuery->__toString() . '))');
			}
			else
			{
				$query->where($type . '.catid = ' . (int) $categoryId);
			}
		}

		// Define null and now dates
		$nullDate = $db->quote($db->getNullDate());
		$nowDate  = $db->quote(JFactory::getDate()->toSql());

		// Filter by start and end dates.
		$query->where('(sermons.publish_up = ' . $nullDate . ' OR sermons.publish_up <= ' . $nowDate . ')');
		$query->where('(sermons.publish_down = ' . $nullDate . ' OR sermons.publish_down >= ' . $nowDate . ')');

		// Filter by type
		$feedtype = $jinput->get('type', 'auto');

		if ($feedtype == 'video')
		{
			$query->where('sermons.videofile != ""');
		}
		elseif ($feedtype == 'audio')
		{
			$query->where('sermons.audiofile != ""');
		}

		// Filter by state
		$query->where('sermons.podcast = 1');
		$query->where('sermons.state = 1');

		// Grouping
		$query->order('sermons.sermon_date DESC');

		$db->setQuery($query, '0', $app->get('feed_limit'));
		$rows = $db->loadObjectList();

		return $rows;
	}
}
