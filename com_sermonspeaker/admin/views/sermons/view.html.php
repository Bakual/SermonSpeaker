<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerViewSermons extends JViewLegacy
{
	/**
	 * Holds an array of item objects
	 *
	 * @var    array
	 *
	 * @since  ?
	 */
	protected $items;

	protected $pagination;

	/**
	 * A state object
	 *
	 * @var    JObject
	 *
	 * @since  ?
	 */
	protected $state;

	protected $speakers;

	protected $series;

	public $filterForm;

	public $activeFilters;

	protected $sidebar;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return mixed A string if successful, otherwise a Error object.
	 *
	 * @throws Exception
	 *
	 * @since  ?
	 */
	public function display($tpl = null)
	{
		$layout = $this->getLayout();

		$this->state         = $this->get('State');
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->speakers      = $this->get('Speakers');
		$this->series        = $this->get('Series');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// We don't need toolbar and sidebar in the modal window.
		if ($layout !== 'modal')
		{
			$this->addToolbar();
			SermonspeakerHelper::addSubmenu('sermons');
			$this->sidebar = JHtmlSidebar::render();
		}
		else
		{
			// In sermon associations modal we need to remove language filter if forcing a language.
			if ($forcedLanguage = JFactory::getApplication()->input->get('forcedLanguage', '', 'CMD'))
			{
				// If the language is forced we can't allow to select the language, so transform the language selector filter into an hidden field.
				$languageXml = new SimpleXMLElement('<field name="language" type="hidden" default="' . $forcedLanguage . '" />');
				$this->filterForm->setField($languageXml, 'filter', true);

				// Also, unset the active language filter so the search tools is not open by default with this filter.
				unset($this->activeFilters['language']);

				// One last changes needed is to change the category filter to just show categories with All language or with the forced language.
				$this->filterForm->setFieldAttribute('category_id', 'language', '*,' . $forcedLanguage, 'filter');
			}
		}

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since  ?
	 */
	protected function addToolbar()
	{
		$canDo = SermonspeakerHelper::getActions();

		// Get the toolbar object instance
		$bar = JToolbar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_SERMONSPEAKER_SERMONS_TITLE'), 'quote-3 sermons');

		if ($canDo->get('core.create'))
		{
			JToolbarHelper::addNew('sermon.add', 'JTOOLBAR_NEW');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))
		{
			JToolbarHelper::editList('sermon.edit', 'JTOOLBAR_EDIT');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::divider();
			JToolbarHelper::custom('sermons.publish', 'publish', '', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::custom('sermons.unpublish', 'unpublish', '', 'JTOOLBAR_UNPUBLISH', true);

			if ($this->state->get('filter.state') != 2)
			{
				JToolbarHelper::archiveList('sermons.archive', 'JTOOLBAR_ARCHIVE');
			}
			else
			{
				JToolbarHelper::unarchiveList('sermons.publish', 'JTOOLBAR_UNARCHIVE');
			}

			JToolbarHelper::checkin('sermons.checkin');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::custom('tools.order', 'lightning', '', 'COM_SERMONSPEAKER_TOOLS_ORDER', false);
		}

		// Add a batch button
		if ($canDo->get('core.edit'))
		{
			$title = JText::_('JTOOLBAR_BATCH');

			// Instantiate a new JLayoutFile instance and render the batch button
			$layout = new JLayoutFile('joomla.toolbar.batch');

			$dhtml = $layout->render(array('title' => $title));
			$bar->appendButton('Custom', $dhtml, 'batch');
		}

		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'sermons.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('sermons.trash', 'JTOOLBAR_TRASH');
		}

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			JToolbarHelper::preferences('com_sermonspeaker');
		}
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'sermons.ordering'    => JText::_('JGRID_HEADING_ORDERING'),
			'sermons.state'       => JText::_('JSTATUS'),
			'sermons.podcast'     => JText::_('COM_SERMONSPEAKER_FIELD_SERMONCAST_LABEL'),
			'sermons.title'       => JText::_('JGLOBAL_TITLE'),
			'category_title'      => JText::_('JCATEGORY'),
			'speaker_title'       => JText::_('COM_SERMONSPEAKER_SPEAKER'),
			'scripture'           => JText::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'),
			'series_title'        => JText::_('COM_SERMONSPEAKER_SERIE'),
			'sermons.sermon_date' => JText::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'),
			'sermons.hits'        => JText::_('JGLOBAL_HITS'),
			'language'            => JText::_('JGRID_HEADING_LANGUAGE'),
			'sermons.id'          => JText::_('JGRID_HEADING_ID'),
		);
	}
}
