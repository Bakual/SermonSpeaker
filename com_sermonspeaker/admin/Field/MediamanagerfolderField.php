<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\Field;

use JFactory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Form\Field\TextField;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Media\Administrator\Model\MediaModel;
use stdClass;

defined('_JEXEC') or die();

/**
 * Creates the filelist dropdown for sermon file select
 *
 * @since 7.1.0
 */
class MediamanagerfolderField extends FormField
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
		$model     = new MediaModel;
		$providers = $model->getProviders();

		foreach ($providers as $provider)
		{
			foreach ($provider->adapterNames as $adapter)
			{
				$option        = new stdClass;
				$option->value = $provider->name . '-' . $adapter . ':/';
				$option->text  = '[' . $provider->displayName . '] /' . $adapter . '/';
				$options[]     = $option;
			}
		}

		return $options;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string   The field input markup.
	 *
	 * @since 7.1.4
	 */
	protected function getInput()
	{
		// Try converting legacy string values to new array
		if (is_string($this->value))
		{
			$oldValue = $this->value;
			$this->value = array();

			if (str_contains($oldValue, ':/'))
			{
				$this->value = explode(':/', $oldValue, 2);
				$this->value[0] .= ':/';
			}
			elseif (str_starts_with($oldValue, 'images'))
			{
				$this->value[0] = 'local-images:/';
				$this->value[1] = substr($oldValue, 7);
			}
			else
			{
				$this->value[0] = '';
				$this->value[1] = $oldValue;
			}
		}

		$listoptions = array();
		$listoptions['option.key'] = 'value';
		$listoptions['option.text'] = 'text';
		$listoptions['list.select'] = $this->value[0];
		$listoptions['id'] = $this->id . '_adapter';
		$listoptions['list.translate'] = false;
		$listoptions['list.attr'] = 'class="form-select d-inline w-25"';

		$html = HTMLHelper::_('select.genericlist', $this->getOptions(), $this->name . '[]', $listoptions);
		$html .= '<input type="text" name="' . $this->name . '[]" id="' . $this->id . '_dir" value="' . htmlspecialchars($this->value[1], ENT_COMPAT) . '" class="form-control d-inline w-50">';

		return $html;
	}
}
