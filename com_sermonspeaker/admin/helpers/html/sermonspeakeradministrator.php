<?php
/**
 * Copy from contentadministrator
 */

defined('_JEXEC') or die;

JLoader::register('SermonspeakerHelper', JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/helpers/sermonspeaker.php');

abstract class JHtmlSermonspeakerAdministrator
{
	/**
	 * Get the associated language flags
	 *
	 * @param   int  $itemid  The item id
	 *
	 * @return  string  The language HTML
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
			catch (runtimeException $e)
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
}
