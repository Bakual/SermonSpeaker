<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JTable::addIncludePath(__DIR__ . '/../tables');

/**
 * Content associations helper.
 *
 * @since  5.6.0
 */
class SermonSpeakerAssociationsHelper extends JAssociationExtensionHelper
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
	protected $itemTypes = array('serie', 'sermon', 'speaker', 'category');

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

		$context = $this->extension . '.' . $typeName;
		$catidField = 'catid';

		if ($typeName === 'category')
		{
			$context = 'com_categories.item';
			$catidField = '';
		}

		// Get the associations.
		$associations = JLanguageAssociations::getAssociations(
			$this->extension,
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

				case 'category':
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
					$title = 'category';
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
	 * @return  JTable|null
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
				$table = JTable::getInstance('Serie', 'SermonspeakerTable');
				break;

			case 'sermon':
				$table = JTable::getInstance('Sermon', 'SermonspeakerTable');
				break;

			case 'speaker':
				$table = JTable::getInstance('Speaker', 'SermonspeakerTable');
				break;

			case 'category':
				$table = JTable::getInstance('Category');
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
