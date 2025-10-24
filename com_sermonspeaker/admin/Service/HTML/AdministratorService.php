<?php

/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\Service\HTML;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

\defined('_JEXEC') or die;

/**
 * Sermonspeaker HTML helper
 *
 * @since  7.0.0
 */
class AdministratorService
{
	/**
	 * Get the associated language flags
	 *
	 * @param   int     $itemid  The item id
	 *
	 * @param   string  $type
	 *
	 * @return string The language HTML
	 *
	 * @throws \Exception
	 *
	 * @since 7.0.0
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
			$db    = Factory::getDbo();
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
			catch (\RuntimeException $e)
			{
				throw new \Exception($e->getMessage(), 500);
			}

			if ($items)
			{
				foreach ($items as $item)
				{
					$text = $item->lang_sef ? strtoupper($item->lang_sef) : 'XX';
					$url  = Route::_('index.php?option=com_sermonspeaker&task=' . $type . '.edit&id=' . (int) $item->id);

					$tooltip = htmlspecialchars($item->title, ENT_QUOTES) . '<br />' . Text::sprintf('JCATEGORY_SPRINTF', $item->category_title);
					$classes = 'hasPopover badge badge-association badge-' . $item->lang_sef;

					$item->link = '<a href="' . $url . '" title="' . $item->language_title . '" class="' . $classes
						. '" data-bs-content="' . $tooltip . '" data-bs-placement="top">'
						. $text . '</a>';
				}
			}

			\HTMLHelper::_('bootstrap.popover', '.hasPopover', ['trigger' => 'hover focus']);

			$html = LayoutHelper::render('joomla.content.associations', $items);
		}

		return $html;
	}
}
