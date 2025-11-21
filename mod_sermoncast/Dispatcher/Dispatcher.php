<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.Sermoncast
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Module\Sermoncast\Site\Dispatcher;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;

defined('_JEXEC') or die;

/**
 * Dispatcher class for mod_sermoncast
 *
 * @since  7.0.0
 */
class Dispatcher extends AbstractModuleDispatcher
{
	/**
	 * Returns the layout data.
	 *
	 * @return  array
	 *
	 * @since   7.0.0
	 */
	protected function getLayoutData(): array
	{
		$data = parent::getLayoutData();

		$type     = $data['params']->get('sc_type');
		$menuitem = (int) $data['params']->get('sc_menuitem');
		$feedFile = 'index.php?option=com_sermonspeaker&view=feed&format=raw';

		if ($type)
		{
			$feedFile .= '&type=' . $type;
		}

		if ($menuitem)
		{
			$feedFile .= '&Itemid=' . $menuitem;
		}

		$data['feedFile'] = $feedFile;

		return $data;
	}
}
