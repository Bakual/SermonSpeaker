<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

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
		if ($associations = JLanguageAssociations::getAssociations('com_sermonspeaker', $table, 'com_sermonspeaker.' . $type, $itemid))
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
				foreach ($items as &$item)
				{
					$text    = $item->lang_sef ? strtoupper($item->lang_sef) : 'XX';
					$url     = JRoute::_('index.php?option=com_sermonspeaker&task=' . $type . '.edit&id=' . (int) $item->id);

					$tooltip = htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8') . '<br />' . JText::sprintf('JCATEGORY_SPRINTF', $item->category_title);
					$classes = 'hasPopover label label-association label-' . $item->lang_sef;

					$item->link = '<a href="' . $url . '" title="' . $item->language_title . '" class="' . $classes
						. '" data-content="' . $tooltip . '" data-placement="top">'
						. $text . '</a>';
				}
			}

			JHtml::_('bootstrap.popover');

			$html = JLayoutHelper::render('joomla.content.associations', $items);
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
	public static function podcasted($value = 0, $i, $canChange = true)
	{
		JHtml::_('bootstrap.tooltip');

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
				'active_class'   => 'feed',
				'inactive_class' => 'feed',
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
			$html = '<a class="btn btn-micro' . ($value == 1 ? ' active' : '') . ' hasTooltip"'
				. ' href="javascript:void(0);" onclick="return listItemTask(\'cb' . $i . '\',\'' . $state['task'] . '\')"'
				. ' title="' . JHtml::tooltipText($state['active_title']) . '">'
				. '<i class="icon-' . $state['active_class'] . '"></i>'
				. '</a>';
		}
		else
		{
			$html = '<a class="btn btn-micro hasTooltip disabled' . ($value == 1 ? ' active' : '')
				. '" title="' . JHtml::tooltipText($state['inactive_title']) . '">'
				. '<i class="icon-' . $state['inactive_class'] . '"></i>'
				. '</a>';
		}

		return $html;
	}
}
