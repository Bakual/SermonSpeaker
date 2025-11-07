<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonUpload
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Module\Sermonupload\Site\Dispatcher;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();


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
	 * @return array|false
	 *
	 * @since   7.0.0
	 */
	protected function getLayoutData(): array|false
	{
		$data = parent::getLayoutData();

		// Bail out if user isn't allowed to create a sermon.
		if (!$this->app->getIdentity()->authorise('core.create', 'com_sermonspeaker'))
		{
			return false;
		}

		// Prepare Upload Script
		HTMLHelper::_('jquery.framework');
		HTMLHelper::_('script', 'com_sermonspeaker/plupload/plupload.full.min.js', array('relative' => true));

		// Load localisation
		$tag  = str_replace('-', '_', $this->app->getLanguage()->getTag());
		$path = 'com_sermonspeaker/plupload/i18n/';
		$file = $tag . '.js';

		if (!HTMLHelper::_('script', $path . $file, array('relative' => true, 'pathOnly' => true)))
		{
			$tag_array = explode('_', $tag);
			$file      = $tag_array[0] . '.js';

		}

		HTMLHelper::_('script', $path . $file, array('relative' => true));

		$data['identifier'] = 'SermonUpload_' . $this->module->id . '_';
		$c_params   = ComponentHelper::getParams('com_sermonspeaker');

		$types = $data['params']->get('types');

		if (!$types)
		{
			$types = array('audio', 'video', 'addfile');
		}

		foreach ($types as $type)
		{
			$this->getHelperFactory()->getHelper('SermonuploadHelper')->loadUploaderScript($data['identifier'], $type, $c_params);
		}

		$data['types'] = $types;

		return $data;
	}
}