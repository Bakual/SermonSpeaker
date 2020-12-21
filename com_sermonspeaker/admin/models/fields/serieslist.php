<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('groupedlist');

/**
 * Serieslist Field class for the SermonSpeaker.
 *
 * @since  4.0
 */
class JFormFieldSerieslist extends JFormFieldGroupedList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  4.0
	 */
	protected $type = 'Serieslist';

	/**
	 * True to translate the field label string.
	 *
	 * @var    boolean
	 * @since  4.0
	 */
	protected $translateLabel = false;

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
			if (JFactory::getApplication()->isClient('administrator'))
			{
				$returnpage = base64_encode('index.php?option=com_sermonspeaker&view=close&tmpl=component');
				$url        = 'index.php?option=com_sermonspeaker&task=serie.add&layout=modal&tmpl=component&return=' . $returnpage;
				$string     = 'COM_SERMONSPEAKER_NEW_SERIE';
			}
			else
			{
				$returnpage = base64_encode(JRoute::_('index.php?view=close&tmpl=component'));
				$url        = JRoute::_('index.php?task=serieform.add&layout=modal&tmpl=component&return=' . $returnpage);
				$string     = 'COM_SERMONSPEAKER_BUTTON_NEW_SERIE';
			}

			$html = '<div class="input-group">' . $html .
						'<span class="input-group-append">
							<a href="#serieModal_' . $this->id .'"
								class="btn btn-secondary hasTooltip"
								title="' . JText::_($string) . '"
								data-toggle="modal"
								role="button"
							>
								<span class="icon-new"></span>
							</a>
						</span>
					</div>';

			// Add the modal field script to the document head.
			JHtml::_('jquery.framework');
			JHtml::_('script', 'system/fields/modal-fields.js', array('version' => 'auto', 'relative' => true));

			$html .= JHtml::_(
				'bootstrap.renderModal',
				'serieModal_' . $this->id,
				array(
					'title'       => JText::_($string),
					'backdrop'    => 'static',
					'keyboard'    => false,
					'closeButton' => false,
					'url'         => $url,
					'height'      => '100%',
					'width'       => '100%',
					'bodyHeight'  => 70,
					'modalWidth'  => 80,
					'footer'      => '<a role="button" class="btn btn-secondary" aria-hidden="true"'
						. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'serie\', \'cancel\', \'item-form\'); return false;">'
						. JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
						. '<a role="button" class="btn btn-primary" aria-hidden="true"'
						. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'serie\', \'save\', \'item-form\'); return false;">'
						. JText::_('JSAVE') . '</a>'
						. '<a role="button" class="btn btn-success" aria-hidden="true"'
						. ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'serie\', \'apply\', \'item-form\'); return false;">'
						. JText::_('JAPPLY') . '</a>',
				)
			);
		}

		return $html;
	}

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
		$db     = JFactory::getDbo();
		$params = ComponentHelper::getParams('com_sermonspeaker');

		$query = $db->getQuery(true);
		$query->select('series.id As value, home');

		if ($this->element['hidecategory'])
		{
			$query->select('series.title AS text');
		}
		else
		{
			$query->select('CASE WHEN CHAR_LENGTH(c_series.title) THEN CONCAT(series.title, " (", c_series.title, ")") ELSE series.title END AS text');
		}

		$query->from('#__sermon_series AS series');
		$query->join('LEFT', '#__categories AS c_series ON c_series.id = series.catid');
		$query->where('series.state = 1');

		if ($params->get('catfilter_lists', 0))
		{
			$action = ($this->value === '') ? 'core.create' : 'core.edit.state';
			$catids = implode(',', JFactory::getUser()->getAuthorisedCategories('com_sermonspeaker', $action));
		}
		else
		{
			$catids = false;
		}

		if ($catids)
		{
			$query->where('(series.catid IN (' . $catids . ') OR series.id = ' . $db->quote($this->value) . ')');
		}

		$query->order('series.title');

		// Get the options.
		$db->setQuery($query);

		$published = $db->loadObjectList();

		$query = $db->getQuery(true);
		$query->select('series.id As value, home');

		if ($this->element['hidecategory'])
		{
			$query->select('series.title AS text');
		}
		else
		{
			$query->select('CASE WHEN CHAR_LENGTH(c_series.title) THEN CONCAT(series.title, " (", c_series.title, ")") ELSE series.title END AS text');
		}

		$query->from('#__sermon_series AS series');
		$query->join('LEFT', '#__categories AS c_series ON c_series.id = series.catid');
		$query->where('series.state = 0');

		if ($catids)
		{
			$query->where('(series.catid IN (' . $catids . ') OR series.id = ' . $db->quote($this->value) . ')');
		}

		$query->order('series.title');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$unpublished = $db->loadObjectList();

		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'ERROR');

			return parent::getGroups();
		}

		if (count($unpublished))
		{
			if (count($published))
			{
				$options[JText::_('JPUBLISHED')] = $published;
			}

			$options[JText::_('JUNPUBLISHED')] = $unpublished;

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
}
