<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Speakerlist Field class for the SermonSpeaker.
 * Based on the Bannerlist field from com_banners
 *
 * @package        SermonSpeaker
 * @since          4.0
 */
class JFormFieldSpeakerlist extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'Speakerlist';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$html   = array();
		$html[] = parent::getInput();

		if (!$this->element['hidebutton'])
		{
			$app = JFactory::getApplication();

			if ($app->isClient('administrator'))
			{
				$returnpage = base64_encode('index.php?option=com_sermonspeaker&view=close&tmpl=component');
				$url        = 'index.php?option=com_sermonspeaker&task=speaker.add&layout=modal&tmpl=component&return=' . $returnpage;
				$string     = 'COM_SERMONSPEAKER_NEW_SPEAKER';
			}
			else
			{
				$returnpage = base64_encode(JRoute::_('index.php?view=close&tmpl=component'));
				$url        = JRoute::_('index.php?task=speakerform.add&layout=modal&tmpl=component&return=' . $returnpage);
				$string     = 'COM_SERMONSPEAKER_BUTTON_NEW_SPEAKER';
			}

			array_unshift($html, '<div class="input-append">');
			$html[] = '<a class="modal" href="' . $url . '" rel="{handler: \'iframe\', size: {x: 950, y: 650}}">';
			$html[] = '<div class="btn add-on icon-plus-2" rel="tooltip" title="' . JText::_($string) . '"> </div>';
			$html[] = '</a></div>';
		}

		return implode('', $html);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return array The field option objects.
	 * @throws \Exception
	 *
	 * @since    1.6
	 */
	public function getOptions()
	{
		$db     = JFactory::getDbo();
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
			$catids = implode(',', JFactory::getUser()->getAuthorisedCategories('com_sermonspeaker', $action));
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

		$published = $db->loadObjectList();

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

		$unpublished = $db->loadObjectList();

		if (count($unpublished))
		{
			if (count($published))
			{
				array_unshift($published, JHtml::_('select.optgroup', JText::_('JPUBLISHED')));
				array_push($published, JHtml::_('select.optgroup', JText::_('JPUBLISHED')));
			}

			array_unshift($unpublished, JHtml::_('select.optgroup', JText::_('JUNPUBLISHED')));
			array_push($unpublished, JHtml::_('select.optgroup', JText::_('JUNPUBLISHED')));
		}

		// Check for a database error.
		if ($db->getErrorNum())
		{
			throw new Exception($db->getErrorMsg(), 500);
		}

		$options = array_merge(parent::getOptions(), $published, $unpublished);

		return $options;
	}
}
