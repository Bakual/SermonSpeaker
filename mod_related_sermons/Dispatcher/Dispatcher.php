<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.RelatedSermons
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
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
	 * @return  array
	 *
	 * @since   7.0.0
	 */
	protected function getLayoutData(): array
	{
		$data   = parent::getLayoutData();
		$params = $data['params'];

		$cacheparams               = new \stdClass;
		$cacheparams->cachemode    = 'safeuri';
		$cacheparams->class        = $this->getHelperFactory()->getHelper('RelatedSermonsHelper');
		$cacheparams->method       = 'getRelatedSermons';
		$cacheparams->methodparams = [$params, $data['app']];
		$cacheparams->modeparams   = array('id' => 'int', 'Itemid' => 'int');

		$data['list']            = ModuleHelper::moduleCache($this->module, $params, $cacheparams);
		$data['moduleclass_sfx'] = htmlspecialchars($params->get('moduleclass_sfx'));
		$data['showDate']        = $params->get('showDate', 0);

		return $data;
	}
}