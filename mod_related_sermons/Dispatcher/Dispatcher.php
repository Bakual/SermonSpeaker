<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.RelatedSermons
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Module\RelatedSermons\Site\Dispatcher;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;
use Joomla\CMS\Helper\ModuleHelper;

defined('_JEXEC') or die();

/**
 * Dispatcher class for mod_related_sermons
 *
 * @since  7.0.0
 */
class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
	use HelperFactoryAwareTrait;

	/**
	 * Returns the layout data.
	 *
	 * @return  array|bool
	 *
	 * @since   7.0.0
	 */
	protected function getLayoutData(): array|bool
	{
		$data   = parent::getLayoutData();
		$params = $data['params'];

		$cacheparams               = new \stdClass;
		$cacheparams->cachemode    = 'safeuri';
		$cacheparams->class        = $this->getHelperFactory()->getHelper('RelatedSermonsHelper');
		$cacheparams->method       = 'getRelatedSermons';
		$cacheparams->methodparams = [$params, $data['app']];
		$cacheparams->modeparams   = array('id' => 'int', 'Itemid' => 'int');

		$data['showDate'] = $params->get('showDate', 0);
		$data['list']     = ModuleHelper::moduleCache($this->module, $params, $cacheparams);

		if (!$data['list'])
		{
			return false;
		}

		return $data;
	}
}