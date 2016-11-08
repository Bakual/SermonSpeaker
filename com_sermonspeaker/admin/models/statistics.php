<?php
defined('_JEXEC') or die;

/**
 * Class SermonspeakerModelStatistics
 *
 * @since ?
 */
class SermonspeakerModelStatistics extends JModelLegacy
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