<?php
/**
 * @package         Joomla.Site
 * @subpackage      MOD_SERMONSPEAKER_categories
 *
 * @copyright   (C) 2010 Open Source Matters, Inc. <https://www.joomla.org>
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sermonspeaker\Module\SermonspeakerCategories\Site\Dispatcher;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;
use Joomla\CMS\Helper\ModuleHelper;

defined('_JEXEC') or die;

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
		$cacheparams->cachemode    = 'id';
		$cacheparams->class        = 'Sermonspeaker\Module\SermonspeakerCategories\Site\Helper\SermonspeakerCategoriesHelper';
		$cacheparams->method       = 'getCategories';
		$cacheparams->methodparams = [$params, $data['app']];
		$cacheparams->modeparams   = md5($this->module->id);

		$data['list']       = ModuleHelper::moduleCache($this->module, $params, $cacheparams);
		$data['startLevel'] = $data['list'] ? reset($data['list'])->getParent()->level : null;

		return $data;
	}
}