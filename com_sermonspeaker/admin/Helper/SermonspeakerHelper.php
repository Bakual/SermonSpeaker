<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\Helper;

use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\Database\DatabaseInterface;

defined('_JEXEC') or die;

/**
 * Sermonspeaker Helper
 *
 * @since  3.4
 */
class SermonspeakerHelper
{
	/**
	 * Get the actions for ACL
	 *
	 * @param int $categoryId
	 *
	 * @return \JObject
	 * @since ?
	 *
	 */
	public static function getActions($categoryId = 0)
	{
		$user   = Factory::getApplication()->getIdentity();
		$result = new CMSObject;

		if (empty($categoryId))
		{
			$assetName = 'com_sermonspeaker';
		}
		else
		{
			$assetName = 'com_sermonspeaker.category.' . (int) $categoryId;
		}

		$actions = Access::getActionsFromFile(
			JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/access.xml'
		);

		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}

	/**
	 * Returns valid contexts
	 *
	 * @return  array
	 *
	 * @since   5.6.0
	 */
	public static function getContexts()
	{
		$lang = Factory::getLanguage();
		$lang->load('com_sermonspeaker', JPATH_ADMINISTRATOR)
		|| $lang->load('com_sermonspeaker', JPATH_ADMINISTRATOR . '/components/com_sermonspeaker');

		return array(
			'com_sermonspeaker.sermon'              => Text::_('COM_SERMONSPEAKER_FIELDS_CONTEXT_SERMON'),
			'com_sermonspeaker.serie'               => Text::_('COM_SERMONSPEAKER_FIELDS_CONTEXT_SERIE'),
			'com_sermonspeaker.speaker'             => Text::_('COM_SERMONSPEAKER_FIELDS_CONTEXT_SPEAKER'),
			'com_sermonspeaker.sermons.categories'  => Text::_('COM_SERMONSPEAKER_FIELDS_CONTEXT_CATEGORY_SERMONS'),
			'com_sermonspeaker.series.categories'   => Text::_('COM_SERMONSPEAKER_FIELDS_CONTEXT_CATEGORY_SERIES'),
			'com_sermonspeaker.speakers.categories' => Text::_('COM_SERMONSPEAKER_FIELDS_CONTEXT_CATEGORY_SPEAKERS'),
		);
	}

	/**
	 * Map the section for custom fields.
	 *
	 * @param string $section The section to get the mapping for
	 *
	 * @return  string  The new section
	 *
	 * @since  5.6.0
	 */
	public static function validateSection($section)
	{
		$mapping = array(
			'sermon'         => 'sermon',
			'sermons'        => 'sermon',
			'frontendupload' => 'sermon',
			'intro'          => 'speaker',
			'bio'            => 'speaker',
			'speaker'        => 'speaker',
			'speakers'       => 'speaker',
			'speakerform'    => 'speaker',
			'serie'          => 'serie',
			'series'         => 'serie',
			'serieform'      => 'serie',
		);

		return $mapping[$section] ?? null;
	}


	/**
	 * @param array             $items Array of sermons
	 * @param DatabaseInterface $db
	 *
	 * @return void
	 * @since  7.1.0
	 */
	public static function attachScriptures(array $items, DatabaseInterface $db): void
	{
		if (!$items)
		{
			return;
		}

		$ids   = array_map('intval', array_column($items, 'id'));
		$query = $db->getQuery(true)
			->select($db->quoteName(['book', 'cap1', 'vers1', 'cap2', 'vers2', 'text', 'ordering', 'sermon_id']))
			->from($db->quoteName('#__sermon_scriptures'))
			->whereIn($db->quoteName('sermon_id'), $ids)
			->order($db->quoteName('ordering') . ' ASC');

		$bySermon = [];

		foreach ($db->setQuery($query)->loadObjectList() as $row)
		{
			$bySermon[$row->sermon_id][] = $row;
		}

		foreach ($items as $item)
		{
			$item->scripture = $bySermon[$item->id] ?? [];
		}
	}
}
