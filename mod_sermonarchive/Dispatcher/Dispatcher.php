<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonArchive
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Module\Sermonarchive\Site\Dispatcher;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;

defined('_JEXEC') or die();

/**
 * Dispatcher class for mod_sermonarchive
 *
 * @since  7.0.0
 */
class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
	use HelperFactoryAwareTrait;

	/**
	 * Returns the layout data.
	 *
	 * @return  array|false
	 *
	 * @since   7.0.0
	 */
	protected function getLayoutData(): array|false
	{
		$data = parent::getLayoutData();

		$data['list'] = $this->getHelperFactory()->getHelper('SermonarchiveHelper')->getSermons($data['params'], $this->getApplication());

		if (!$data['list'])
		{
			return false;
		}

		return $data;
	}
}
