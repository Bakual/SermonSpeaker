<?php
/**
 * jUpgrade
 *
 * @version		$Id: 
 * @package		MatWare
 * @subpackage	com_jupgrade
 * @copyright	Copyright 2006 - 2011 Matias Aguirre. All rights reserved.
 * @license		GNU General Public License version 2 or later.
 * @author		Matias Aguirre <maguirre@matware.com.ar>
 * @link		http://www.matware.com.ar
 */

defined ( '_JEXEC' ) or die ();

/**
 * @package		MatWare
 * @subpackage	com_jupgrade
 * @since		1.1.0
 */
class jUpgradeComponentSermonspeaker extends jUpgradeExtensions {
	/**
	 * Check if extension migration is supported.
	 *
	 * @return	boolean
	 * @since	1.1.0
	 */
	protected function detectExtension()
	{
		$version = $this->getExtensionVersion('administrator/components/com_sermonspeaker/sermonspeaker.xml');
		return $version && version_compare($version, '3.4.2', '>=');
	}

	function getSourceData(){
		$rows = parent::getSourceData();

		// Getting the categories id's
		$categories = $this->getMapList('categories', 'com_sermonspeaker');

		// Do some custom post processing on the list.
		foreach ($rows as &$row)
		{
			$cid = $row['catid'];
			$row['catid'] = &$categories[$cid]->new;
		}

		return $rows;
	}

	/**
	 * Migrate tables
	 *
	 * @return	boolean
	 * @since	1.1.0
	 */
	public function migrateExtensionCustom()
	{
		return true;
	}
}
