<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\Field;

use Joomla\CMS\Form\Field\TextField;
use Joomla\Component\Media\Administrator\Model\MediaModel;
use stdClass;

defined('_JEXEC') or die();

/**
 * Creates the filelist dropdown for sermon file select
 *
 * @since ?
 */
class MediamanagerfolderField extends TextField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since ?
	 */
	public $type = 'Mediamanagerfolder';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since ?
	 */
	protected function getOptions()
	{
		$options = parent::getOptions();

		$model     = new MediaModel;
		$providers = $model->getProviders();

		foreach ($providers as $provider)
		{
			foreach ($provider->adapterNames as $apapter)
			{
				$option        = new stdClass;
				$option->value = $provider->name . '-' . $apapter . ':/';
				$option->text  = '[' . $provider->displayName . '] /' . $apapter . '/';
				$options[]     = $option;
			}
		}

		return $options;
	}
}
