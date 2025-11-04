<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.LatestSermons
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Module\Latestsermons\Site\Dispatcher;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;

defined('_JEXEC') or die;

/**
 * Dispatcher class for mod_latestsermons
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

		$data['list'] = $this->getHelperFactory()->getHelper('LatestsermonsHelper')->getSermons($data['params'], $this->getApplication());

		$data['itemid'] = (int) $data['params']->get('ls_mo_menuitem');

		return $data;
	}
}
