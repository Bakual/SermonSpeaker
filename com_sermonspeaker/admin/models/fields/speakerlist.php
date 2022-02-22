<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\GroupedlistField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('groupedlist');

/**
 * Speakerlist Field class for the SermonSpeaker.
 *
 * @since  4.0
 */
class JFormFieldSpeakerlist extends GroupedlistField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  4.0
	 */
	protected $type = 'Speakerlist';

	/**
	 * True to translate the field label string.
	 *
	 * @var    boolean
	 * @since  4.0
	 */
	protected $translateLabel = false;

	/**
	 * Method to get the field options.
	 *
	 * @return array The field option objects.
	 * @throws \Exception
	 *
	 * @since  6.0.0
	 */
	public function getGroups()
	{
		$db     = Factory::getDbo();
		$params = JComponentHelper::getParams('com_sermonspeaker');

		$query = $db->getQuery(true);
		$query->select('speakers.id As value, home');

		if ($this->element['hidecategory'])
		{
			$query->select('speakers.title AS text');
		}
		else
		{
			$query->select('CASE WHEN CHAR_LENGTH(c_speakers.title) THEN CONCAT(speakers.title, " (", c_speakers.title, ")") ELSE speakers.title END AS text');
		}

		$query->from('#__sermon_speakers AS speakers');
		$query->join('LEFT', '#__categories AS c_speakers ON c_speakers.id = speakers.catid');
		$query->where('speakers.state = 1');

		if ($params->get('catfilter_lists', 0))
		{
			$action = ($this->value === '') ? 'core.create' : 'core.edit.state';
			$catids = implode(',', Factory::getUser()->getAuthorisedCategories('com_sermonspeaker', $action));
		}
		else
		{
			$catids = false;
		}

		if ($catids)
		{
			$query->where('(speakers.catid IN (' . $catids . ') OR speakers.id = ' . $db->quote($this->value) . ')');
		}

		$query->order('speakers.title');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$published = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'ERROR');

			return parent::getGroups();
		}

		$query = $db->getQuery(true);
		$query->select('speakers.id As value, home');

		if ($this->element['hidecategory'])
		{
			$query->select('speakers.title AS text');
		}
		else
		{
			$query->select('CASE WHEN CHAR_LENGTH(c_speakers.title) THEN CONCAT(speakers.title, " (", c_speakers.title, ")") ELSE speakers.title END AS text');
		}

		$query->from('#__sermon_speakers AS speakers');
		$query->join('LEFT', '#__categories AS c_speakers ON c_speakers.id = speakers.catid');
		$query->where('speakers.state = 0');

		if ($catids)
		{
			$query->where('(speakers.catid IN (' . $catids . ') OR speakers.id = ' . $db->quote($this->value) . ')');
		}

		$query->order('speakers.title');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$unpublished = $db->loadObjectList();

		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'ERROR');

			return parent::getGroups();
		}

		if (count($unpublished))
		{
			if (count($published))
			{
				$options[Text::_('JPUBLISHED')] = $published;
			}

			$options[Text::_('JUNPUBLISHED')] = $unpublished;

			$groups = array_merge(parent::getGroups(), $options);
		}
		else
		{
			$options = $published;

			// Fake a single group.
			$groups[] = array_merge(parent::getGroups()[0], $options);
		}

		if ($this->value === '' && !$this->element['ignoredefault'])
		{
			foreach ($options as $option)
			{
				if (isset($option->home) && $option->home)
				{
					$this->value = $option->value;
					break;
				}
			}
		}

		return $groups;
	}

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   4.0
	 */
	protected function getInput()
	{
		$html = parent::getInput();

		if (!$this->element['hidebutton'])
		{
			if (Factory::getApplication()->isClient('administrator'))
			{
				$returnpage = base64_encode('index.php?option=com_sermonspeaker&view=close&tmpl=component');
				$url        = 'index.php?option=com_sermonspeaker&task=speaker.add&layout=modal&tmpl=component&return=' . $returnpage;
				$string     = 'COM_SERMONSPEAKER_NEW_SPEAKER';
			}
			else
			{
				$returnpage = base64_encode(Route::_('index.php?view=close&tmpl=component'));
				$url        = Route::_('index.php?task=speakerform.add&layout=modal&tmpl=component&return=' . $returnpage);
				$string     = 'COM_SERMONSPEAKER_BUTTON_NEW_SPEAKER';
			}

			$html = '<div class="input-group">' . $html .
				'<span class="input-group-append">
							<a href="#speakerModal_' . $this->id . '"
								class="btn btn-secondary hasTooltip"
								title="' . Text::_($string) . '"
								data-bs-toggle="modal"
								role="button"
							>
								<span class="icon-new"></span>
							</a>
						</span>
					</div>';

			// Add the modal field script to the document head.
			HTMLHelper::_('jquery.framework');
			HTMLHelper::_('script', 'system/fields/modal-fields.js', array('version' => 'auto', 'relative' => true));

			$html .= HTMLHelper::_(
				'bootstrap.renderModal',
				'speakerModal_' . $this->id,
				array(
					'title'       => Text::_($string),
					'backdrop'    => 'static',
					'keyboard'    => false,
					'closeButton' => false,
					'url'         => $url,
					'height'      => '100%',
					'width'       => '100%',
					'bodyHeight'  => 70,
					'modalWidth'  => 80,
					'footer'      => '<a role="button" class="btn btn-secondary" aria-hidden="true"'
						. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'speaker\', \'cancel\', \'item-form\'); return false;">'
						. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
						. '<a role="button" class="btn btn-primary" aria-hidden="true"'
						. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'speaker\', \'save\', \'item-form\'); return false;">'
						. Text::_('JSAVE') . '</a>'
						. '<a role="button" class="btn btn-success" aria-hidden="true"'
						. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'speaker\', \'apply\', \'item-form\'); return false;">'
						. Text::_('JAPPLY') . '</a>',
				)
			);
		}

		return $html;
	}
}
