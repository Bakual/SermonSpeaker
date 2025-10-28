<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Associations;
use Joomla\Component\Categories\Administrator\Helper\CategoryAssociationHelper;

JLoader::register('SermonspeakerHelper', JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/helpers/sermonspeaker.php');

/**
 * SermonSpeaker Association Helper
 *
 * @since  5
 */
abstract class SermonspeakerHelperAssociation extends CategoryAssociationHelper
{
	/**
	 * Method to get the associations for a given item
	 *
	 * @param   integer $id   Id of the item
	 * @param   string  $view Name of the view
	 *
	 * @return  array   Array of associations for the item
	 *
	 * @since ?
	 */
	public static function getAssociations($id = 0, $view = null)
	{
		jimport('helper.route', JPATH_COMPONENT_SITE);

		$jinput = Factory::getApplication()->input;
		$view = is_null($view) ? $jinput->get('view') : $view;
		$id = empty($id) ? $jinput->getInt('id') : $id;

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
	 * @param   int    $id        ID to find
	 * @param   string $view      Viewname
	 * @param   string $tablename The name of the table.
	 *
	 * @return  array  Matching items
	 *
	 * @since ?
	 */
	protected static function _findItems($id, $view, $tablename)
	{
		if ($id)
		{
			$associations = Associations::getAssociations('com_sermonspeaker.' . $view . 's', $tablename, 'com_sermonspeaker.' . $view, $id);
			$function = 'get' . $view . 'Route';

			$return = array();

			foreach ($associations as $tag => $item)
			{
				$return[$tag] = Sermonspeaker\Component\Sermonspeaker\Site\Helper\RouteHelper::$function($item->id, $item->catid, $item->language);
			}

			return $return;
		}

		return array();
	}
}
