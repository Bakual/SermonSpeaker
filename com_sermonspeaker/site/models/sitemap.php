<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2015 - Thomas Hunziker
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
	 */
	public function getSermons()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('id, title, sermon_date, created, catid, language');
		$query->select("CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(':', id, alias) ELSE id END as slug");
		$query->from('#__sermon_sermons');
		$query->where('state = 1');
		$query->order('sermon_date DESC');

		// Filter by start and end dates.
		$nullDate = $db->quote($db->getNullDate());
		$nowDate  = $db->quote(JFactory::getDate()->toSql());

		$query->where('(publish_up = ' . $nullDate . ' OR publish_up <= ' . $nowDate . ')');
		$query->where('(publish_down = ' . $nullDate . ' OR publish_down >= ' . $nowDate . ')');

		// Filter by cat if set
		$app    = JFactory::getApplication();
		$params = $app->getParams();
		$cat    = (int) $params->get('cat', 0);

		if ($cat)
		{
			$query->where('catid = ' . $cat);
		}

		$rows = $this->_getList($query);

		return $rows;
	}
}
