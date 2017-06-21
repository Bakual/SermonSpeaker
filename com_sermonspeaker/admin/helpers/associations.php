<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Association\AssociationExtensionHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Table\Table;

Table::addIncludePath(__DIR__ . '/../tables');

/**
 * Content associations helper.
 *
 * @since  5.6.0
 */
class SermonSpeakerAssociationsHelper extends AssociationExtensionHelper
{
	/**
	 * var       array   $extension  The extension name
	 *
	 * @since    5.6.0
	 */
	protected $extension = 'com_sermonspeaker';

	/**
	 * var       array   $itemTypes  Array of item types
	 *
	 * @since    5.6.0
	 */
	protected $itemTypes = array('sermon', 'serie', 'speaker', 'sermons.category', 'series.category', 'speakers.category');

	/**
	 * var       boolean   $associationsSupport  Has the extension association support
	 *
	 * @since    5.6.0
	 */
	protected $associationsSupport = true;

	/**
	 * Get the associated items for an item
	 *
	 * @param   string $typeName The item type
	 * @param   int    $id       The id of item for which we need the associated items
	 *
	 * @return  array
	 *
	 * @since    5.6.0
	 */

	public function getAssociations($typeName, $id)
	{
		if (!in_array($typeName, $this->itemTypes))
		{
			return array();
		}

		$type = $this->getType($typeName);

		$context    = $this->extension . '.' . $typeName;
		$extension  = $context . 's';
		$catidField = 'catid';

		$categories = array('sermons.category', 'series.category', 'speakers.category');

		if (in_array($typeName, $categories))
		{
			$extension = 'com_sermonspeaker.' . str_replace('.category', '', $typeName);
			$context = 'com_categories.item';
			$catidField = '';
		}

		// Get the associations.
		$associations = Associations::getAssociations(
			$extension,
			$type['tables']['a'],
			$context,
			$id,
			'id',
			'alias',
			$catidField
		);

		return $associations;
	}

	/**
	 * Get information about the type
	 *
	 * @param   string $typeName The item type
	 *
	 * @return  array  Array of item types
	 *
	 * @since  5.6.0
	 */
	public function getType($typeName = '')
	{
		$fields = $this->getFieldsTemplate();
		$tables = array();
		$joins = array();
		$support = $this->getSupportTemplate();
		$title = '';

		// Setting some default values
		$fields['access'] = '';
		$support['state'] = true;
		$support['acl'] = false;
		$support['checkout'] = true;
		$support['save2copy'] = true;

		if (in_array($typeName, $this->itemTypes))
		{
			switch ($typeName)
			{
				case 'serie':
					$support['category'] = true;

					$tables = array(
						'a' => '#__sermon_series',
					);
					$title = 'serie';
					break;

				case 'sermon':
					$support['category'] = true;

					$tables = array(
						'a' => '#__sermon_sermons',
					);
					$title = 'sermon';
					break;

				case 'speaker':
					$support['category'] = true;

					$tables = array(
						'a' => '#__sermon_speakers',
					);
					$title = 'speaker';
					break;

				case 'sermons.category':
				case 'series.category':
				case 'speakers.category':
					$fields['created_user_id'] = 'a.created_user_id';
					$fields['ordering'] = 'a.lft';
					$fields['level'] = 'a.level';
					$fields['catid'] = '';
					$fields['state'] = 'a.published';

					$support['state'] = true;
					$support['acl'] = true;
					$support['checkout'] = true;
					$support['level'] = true;

					$tables = array(
						'a' => '#__categories',
					);
					$title = str_replace('.', '_', $typeName);
					break;
			}
		}

		return array(
			'fields'  => $fields,
			'support' => $support,
			'tables'  => $tables,
			'joins'   => $joins,
			'title'   => $title,
		);
	}

	/**
	 * Get item information
	 *
	 * @param   string $typeName The item type
	 * @param   int    $id       The id of item for which we need the associated items
	 *
	 * @return  Table|null
	 *
	 * @since    5.6.0
	 */
	public function getItem($typeName, $id)
	{
		if (empty($id))
		{
			return null;
		}

		$table = null;

		switch ($typeName)
		{
			case 'serie':
				$table = Table::getInstance('Serie', 'SermonspeakerTable');
				break;

			case 'sermon':
				$table = Table::getInstance('Sermon', 'SermonspeakerTable');
				break;

			case 'speaker':
				$table = Table::getInstance('Speaker', 'SermonspeakerTable');
				break;

			case 'sermons.category':
			case 'series.category':
			case 'speakers.category':
				$table = Table::getInstance('Category');
				break;
		}

		if (empty($table))
		{
			return null;
		}

		$table->load($id);

		return $table;
	}
}
