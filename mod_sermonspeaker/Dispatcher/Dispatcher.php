<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.Sermonspeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Module\Sermonspeaker\Site\Dispatcher;

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

		$data['list'] = $this->getHelperFactory()->getHelper('SermonspeakerHelper')->getItems($data['params'], $this->getApplication());

		$mode                 = (int) $data['params']->get('mode');
		$data['helperMethod'] = $mode ? 'getSerieRoute' : 'getSpeakerRoute';

		return $data;
	}
}
