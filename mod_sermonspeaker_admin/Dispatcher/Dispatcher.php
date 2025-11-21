<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Administrator.Module.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Module\Sermonspeaker\Administrator\Dispatcher;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;

defined('_JEXEC') or die;

/**
 * Dispatcher class for mod_sermonspeaker
 *
 * @since  7.0.0
 */
class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
	use HelperFactoryAwareTrait;

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

		$types        = $data['params']->get('types');
		$data['list'] = array();
		$helper       = $this->getHelperFactory()->getHelper('SermonspeakerHelper');

		if (!$types)
		{
			$types = array('sermons', 'series', 'speakers');
		}

		foreach ($types as $type)
		{
			$items = $helper->getItems($data['params'], $this->getApplication(), $type);

			if ($items)
			{
				$data['list'][$type] = $items;
			}
		}

		return $data;
	}
}