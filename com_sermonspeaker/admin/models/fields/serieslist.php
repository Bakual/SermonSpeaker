<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2015 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Serieslist Field class for the SermonSpeaker.
 * Based on the Bannerlist field from com_banners
 *
 * @package		SermonSpeaker
 * @since		4.0
 */
class JFormFieldSerieslist extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Serieslist';
	protected $translateLabel = false;

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
		$html = array();
		$html[] = parent::getInput();

		if (!$this->element['hidebutton'])
		{
			$app = JFactory::getApplication();

			if ($app->isAdmin())
			{
				$returnpage = base64_encode('index.php?option=com_sermonspeaker&view=close&tmpl=component');
				$url        = 'index.php?option=com_sermonspeaker&task=serie.add&layout=modal&tmpl=component&return=' . $returnpage;
			}
			else
			{
				$returnpage = base64_encode(JRoute::_('index.php?view=close&tmpl=component'));
				$url        = JRoute::_('index.php?task=serieform.edit&layout=modal&tmpl=component&return=' . $returnpage);
			}

			array_unshift($html, '<div class="input-append">');
			$html[] = '<a class="modal" href="'.$url.'" rel="{handler: \'iframe\', size: {x: 950, y: 650}}">';
			$html[] = '<div class="btn add-on icon-plus-2" rel="tooltip" title="'.JText::_('COM_SERMONSPEAKER_NEW_SERIE').'"> </div>';
			$html[] = '</a></div>';
		}

		return implode('', $html);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	public function getOptions()
	{
		$db     = JFactory::getDbo();
		$params = JComponentHelper::getParams('com_sermonspeaker');

		if ($catfilter = $params->get('catfilter_lists', 0))
		{
			$action = ($this->value === '') ? 'core.create' : 'core.edit.state';
			$catids = implode(',', JFactory::getUser()->getAuthorisedCategories('com_sermonspeaker', $action));
		}

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

		if ($catfilter)
		{
			if ($catids)
			{
				$query->where('(series.catid IN ('.$catids.') OR series.id = '.$db->quote($this->value).')');
			}
			else
			{
				$query->where('series.id = '.$db->quote($this->value));
			}
		}

		$query->order('series.title');

		// Get the options.
		$db->setQuery($query);

		$published = $db->loadObjectList();

		$query	= $db->getQuery(true);
		$query->select('series.id As value, home');
		$query->select('CASE WHEN CHAR_LENGTH(c_series.title) THEN CONCAT(series.title, " (", c_series.title, ")") ELSE series.title END AS text');
		$query->from('#__sermon_series AS series');
		$query->join('LEFT', '#__categories AS c_series ON c_series.id = series.catid');
		$query->where('series.state = 0');

		if ($catfilter)
		{
			if ($catids)
			{
				$query->where('(series.catid IN ('.$catids.') OR series.id = '.$db->quote($this->value).')');
			}
			else
			{
				$query->where('series.id = '.$db->quote($this->value));
			}
		}

		$query->order('series.title');

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

		return $options;
	}
}
