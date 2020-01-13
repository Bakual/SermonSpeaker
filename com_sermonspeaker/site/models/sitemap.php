<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2019 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Model class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerModelSitemap extends JModelLegacy
{
	/**
	 * Method to get sermons
	 *
	 * @return  array
	 *
	 * @since ?
	 */
	public function getSermons()
	{
		// Create a new query object.
		$db     = $this->getDbo();
		$query  = $db->getQuery(true);
		$groups = JFactory::getUser()->getAuthorisedViewLevels();

		$query->select('a.id, a.title, a.sermon_date, a.created, a.catid, a.language');
		$query->select("CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(':', a.id, a.alias) ELSE a.id END as slug");
		$query->from('#__sermon_sermons AS a');
		$query->where('a.state = 1');
		$query->order('sermon_date DESC');

		// Join over categories.
		$query->join('LEFT', '#__categories AS c ON a.catid = c.id');
		$query->where('c.published = 1');
		$query->where('c.access IN (' . implode(',', $groups) . ')');

		// Filter by start and end dates.
		$nullDate = $db->quote($db->getNullDate());
		$nowDate  = $db->quote(JFactory::getDate()->toSql());

		$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
		$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

		// Filter by cat if set
		/** @var JApplicationSite $app */
		$app    = JFactory::getApplication();
		$params = $app->getParams();
		$cat    = (int) $params->get('cat', 0);

		if ($cat)
		{
			$query->where('a.catid = ' . $cat);
		}
$string = (string) $query;
		$rows = $this->_getList($query);

		return $rows;
	}
}
