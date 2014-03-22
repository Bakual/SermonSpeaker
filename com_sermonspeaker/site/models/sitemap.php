<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2014 - Thomas Hunziker
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
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$query->select('id, title, sermon_date, created');
		$query->select("CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(':', id, alias) ELSE id END as slug");
		$query->from('#__sermon_sermons');
		$query->where('state = 1');
		$query->order('sermon_date DESC');

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
