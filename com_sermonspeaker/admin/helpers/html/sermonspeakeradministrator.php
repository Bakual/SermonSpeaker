<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

JLoader::register('SermonspeakerHelper', JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/helpers/sermonspeaker.php');

abstract class JHtmlSermonspeakerAdministrator
{
	/**
	 * Get the associated language flags
	 *
	 * @param   int  $itemid The item id
	 *
	 * @param string $type
	 *
	 * @return string The language HTML
	 *
	 * @throws \Exception
	 * @since ?
	 */
	public static function association($itemid, $type = 'sermon')
	{
		switch ($type)
		{
			case 'sermon':
			default:
				$type  = 'sermon';
				$table = '#__sermon_sermons';
				break;
			case 'serie':
				$table = '#__sermon_series';
				break;
			case 'speaker':
				$table = '#__sermon_speakers';
				break;
		}

		// Defaults
		$html = '';

		// Get the associations
		if ($associations = Associations::getAssociations('com_sermonspeaker.' . $type . 's', $table, 'com_sermonspeaker.' . $type, $itemid))
		{

			foreach ($associations as $tag => $associated)
			{
				$associations[$tag] = (int) $associated->id;
			}

			// Get the associated menu items
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('a.*')
				->from($table . ' as a')
				->select('cat.title as category_title')
				->join('LEFT', '#__categories as cat ON cat.id = a.catid')
				->where('a.id IN (' . implode(',', array_values($associations)) . ')')
				->select('l.image, l.sef as lang_sef, l.lang_code')
				->select('l.title as language_title')
				->join('LEFT', '#__languages as l ON a.language = l.lang_code');
			$db->setQuery($query);

			try
			{
				$items = $db->loadObjectList('id');
			}
			catch (RuntimeException $e)
			{
				throw new Exception($e->getMessage(), 500);
			}

			if ($items)
			{
				foreach ($items as $item)
				{
					$text    = $item->lang_sef ? strtoupper($item->lang_sef) : 'XX';
					$url     = JRoute::_('index.php?option=com_sermonspeaker&task=' . $type . '.edit&id=' . (int) $item->id);

					$tooltip = htmlspecialchars($item->title, ENT_QUOTES) . '<br />' . Text::sprintf('JCATEGORY_SPRINTF', $item->category_title);
					$classes = 'hasPopover badge badge-association badge-' . $item->lang_sef;

					$item->link = '<a href="' . $url . '" title="' . $item->language_title . '" class="' . $classes
						. '" data-bs-content="' . $tooltip . '" data-bs-placement="top">'
						. $text . '</a>';
				}
			}

			HTMLHelper::_('bootstrap.popover', '.hasPopover', ['trigger' => 'hover focus']);

			$html = LayoutHelper::render('joomla.content.associations', $items);
		}

		return $html;
	}

	/**
	 * Show the podcast links, based on JHtmlContentAdministrator::featured
	 *
	 * @param   int     $value     The state value
	 * @param   int     $i         Row number
	 * @param   boolean $canChange Is user allowed to change?
	 *
	 * @return  string   HTML code
	 *
	 * @since ?
	 */
	public static function podcasted($value, $i, $canChange = true)
	{
		HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');

		$states = array(
			0 => array(
				'task'           => 'sermons.podcast_publish',
				'active_title'   => 'COM_SERMONSPEAKER_TOGGLE_PODCASTED',
				'inactive_title' => 'COM_SERMONSPEAKER_UNPODCASTED',
				'active_class'   => 'feed',
				'inactive_class' => 'feed',
			),
			1 => array(
				'task'           => 'sermons.podcast_unpublish',
				'active_title'   => 'COM_SERMONSPEAKER_TOGGLE_PODCASTED',
				'inactive_title' => 'COM_SERMONSPEAKER_PODCASTED',
				'active_class'   => 'feed text-success',
				'inactive_class' => 'feed text-success',
			),
		);

		$value = (int) $value;

		if ($value != 0 && $value != 1)
		{
			$value = 0;
		}

		$state = $states[$value];

		if ($canChange)
		{
			$html = '<a class="tbody-icon' . ($value == 1 ? ' active' : '') . ' hasTooltip"'
				. ' href="#" onclick="return Joomla.listItemTask(\'cb' . $i . '\',\'' . $state['task'] . '\')"'
				. ' title="' . HTMLHelper::_('tooltipText', $state['active_title']) . '">'
				. '<span class="icon-' . $state['active_class'] . '"></span>'
				. '</a>';
		}
		else
		{
			$html = '<a class="tbody-icon' . ($value == 1 ? ' active' : '') . ' hasTooltip disabled"'
				. ' title="' . HTMLHelper::_('tooltipText', $state['inactive_title']) . '">'
				. '<span class="icon-' . $state['inactive_class'] . '"></span>'
				. '</a>';
		}

		return $html;
	}
}
