<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
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
				$type	= 'sermon';
				$table	= '#__sermon_sermons';
				break;
			case 'serie':
				$table	= '#__sermon_series';
				break;
			case 'speaker':
				$table	= '#__sermon_speakers';
				break;
		}

		// Defaults
		$html = '';

		// Get the associations
		if ($associations = JLanguageAssociations::getAssociations('com_sermonspeaker', $table, 'com_sermonspeaker.'.$type, $itemid))
		{

			foreach ($associations as $tag => $associated)
			{
				$associations[$tag] = (int) $associated->id;
			}

			// Get the associated menu items
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('c.*')
				->from($table.' as c')
				->select('cat.title as category_title')
				->join('LEFT', '#__categories as cat ON cat.id=c.catid')
				->where('c.id IN (' . implode(',', array_values($associations)) . ')')
				->join('LEFT', '#__languages as l ON c.language=l.lang_code')
				->select('l.image')
				->select('l.title as language_title');
			$db->setQuery($query);

			try
			{
				$items = $db->loadObjectList('id');
			}
			catch (RuntimeException $e)
			{
				throw new Exception($e->getMessage(), 500);
			}

			$flags = array();

			// Construct html
			foreach ($associations as $associated)
			{
				if ($associated != $itemid)
				{
					$flags[] = JText::sprintf(
						'COM_SERMONSPEAKER_TIP_ASSOCIATED_LANGUAGE',
						JHtml::_('image', 'mod_languages/' . $items[$associated]->image . '.gif',
							$items[$associated]->language_title,
							array('title' => $items[$associated]->language_title),
							true
						),
						$items[$associated]->title, $items[$associated]->category_title
					);
				}
			}

			$html = JHtml::_('tooltip', implode('<br />', $flags), JText::_('COM_SERMONSPEAKER_TIP_ASSOCIATION'), 'admin/icon-16-links.png');

		}

		return $html;
	}

	/**
	 * Show the podcast links, based on JHtmlContentAdministrator::featured
	 *
	 * @param   int      $value      The state value
	 * @param   int      $i          Row number
	 * @param   boolean  $canChange  Is user allowed to change?
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
				'task' => 'sermons.podcast_publish',
				'active_title' => 'COM_SERMONSPEAKER_TOGGLE_PODCASTED',
				'inactive_title' => 'COM_SERMONSPEAKER_UNPODCASTED',
				'active_class' => 'feed',
				'inactive_class' => 'feed',
			),
			1 => array(
				'task' => 'sermons.podcast_unpublish',
				'active_title' => 'COM_SERMONSPEAKER_TOGGLE_PODCASTED',
				'inactive_title' => 'COM_SERMONSPEAKER_PODCASTED',
				'active_class' => 'feed',
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
