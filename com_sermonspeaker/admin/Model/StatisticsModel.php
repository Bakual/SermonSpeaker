<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\Model;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;

/**
 * Class SermonspeakerModelStatistics
 *
 * @since ?
 */
class StatisticsModel extends BaseDatabaseModel
{
	/**
	 * @return \object[]
	 *
	 * @since ?
	 */
	public function getSpeakers()
	{
		$query = "SELECT id, title, hits FROM #__sermon_speakers \n"
			. "ORDER BY id";

		return $this->_getList($query);
	}

	/**
	 * @return \object[]
	 *
	 * @since ?
	 */
	public function getSeries()
	{
		$query = "SELECT id, title, hits FROM #__sermon_series \n"
			. "ORDER BY id";

		return $this->_getList($query);
	}

	/**
	 * @return \object[]
	 *
	 * @since ?
	 */
	public function getSermons()
	{
		$query = "SELECT id, title, hits FROM #__sermon_sermons \n"
			. "ORDER BY id";

		return $this->_getList($query);
	}
}