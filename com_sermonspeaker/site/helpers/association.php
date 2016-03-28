<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

JLoader::register('SermonspeakerHelper', JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/helpers/sermonspeaker.php');
JLoader::register('CategoryHelperAssociation', JPATH_ADMINISTRATOR . '/components/com_categories/helpers/association.php');

/**
 * SermonSpeaker Association Helper
 *
 * @since  5
 */
abstract class SermonspeakerHelperAssociation extends CategoryHelperAssociation
{
	/**
	 * Method to get the associations for a given item
	 *
	 * @param   integer  $id    Id of the item
	 * @param   string   $view  Name of the view
	 *
	 * @return  array   Array of associations for the item
	 */
	public static function getAssociations($id = 0, $view = null)
	{
		jimport('helper.route', JPATH_COMPONENT_SITE);

		$jinput = JFactory::getApplication()->input;
		$view   = is_null($view) ? $jinput->get('view') : $view;
		$id     = empty($id) ? $jinput->getInt('id') : $id;

		switch ($view)
		{
			case 'sermon':
				return self::_findItems($id, $view, '#__sermon_sermons');
			case 'serie':
				return self::_findItems($id, $view, '#__sermon_series');
			case 'speaker':
				return self::_findItems($id, $view, '#__sermon_speakers');
			case 'category':
			case 'categories':
				return self::getCategoryAssociations($id, 'com_sermonspeaker');
		}

		return array();
	}

	/**
	 * Search items
	 *
	 * @param   int     $id          ID to find
	 * @param   string  $view        Viewname
	 * @param   string  $tablename   The name of the table.
	 *
	 * @return  array  Matching items
	 */
	protected static function _findItems($id, $view, $tablename)
	{
		if ($id)
		{
			$associations = JLanguageAssociations::getAssociations('com_sermonspeaker', $tablename, 'com_sermonspeaker.' . $view, $id);
			$function = 'get' . $view . 'Route';

			$return = array();

			foreach ($associations as $tag => $item)
			{
				$return[$tag] = SermonspeakerHelperRoute::$function($item->id, $item->catid, $item->language);
			}

			return $return;
		}

		return array();
	}
}
