<?php
defined('_JEXEC') or die;
class SermonspeakerViewSermons extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$layout = $this->getLayout();
		if ($layout !== 'modal')
		{
			SermonspeakerHelper::addSubmenu('sermons');
		}

		// Switch Layout if in Joomla 3.0
		$version		= new JVersion;
		$this->joomla30	= $version->isCompatible(3.0);
		if ($this->joomla30)
		{
			$this->setLayout($layout.'30');
		}

		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->speakers		= $this->get('Speakers');
		$this->series		= $this->get('Series');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// We don't need toolbar in the modal window.
		if ($layout !== 'modal') {
			$this->addToolbar();
			if ($this->joomla30)
			{
				$this->sidebar = JHtmlSidebar::render();
			}
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		$canDo 	= SermonspeakerHelper::getActions();

		JToolBarHelper::title(JText::_('COM_SERMONSPEAKER_SERMONS_TITLE'), 'sermons');
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('sermon.add','JTOOLBAR_NEW');
		}
		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) {
			JToolBarHelper::editList('sermon.edit','JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::custom('sermons.publish', 'publish', '','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('sermons.unpublish', 'unpublish', '', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();
			if ($this->state->get('filter.state') != 2) {
				JToolBarHelper::archiveList('sermons.archive','JTOOLBAR_ARCHIVE');
			} else {
				JToolBarHelper::unarchiveList('sermons.publish', 'JTOOLBAR_UNARCHIVE');
			}
			JToolBarHelper::checkin('sermons.checkin');
		}
		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'sermons.delete','JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} else if ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('sermons.trash','JTOOLBAR_TRASH');
			JToolBarHelper::divider();
		}
		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::custom('tools.order', 'purge icon-loop', '','COM_SERMONSPEAKER_TOOLS_ORDER', false);
			JToolBarHelper::divider();
		}

		if ($this->joomla30)
		{
			// Get the toolbar object instance
			$bar = JToolBar::getInstance('toolbar');

			// Add a batch button
			if ($canDo->get('core.edit'))
			{
				$title = JText::_('JTOOLBAR_BATCH');
				$dhtml = "<button data-toggle=\"modal\" data-target=\"#collapseModal\" class=\"btn btn-small\">
							<i class=\"icon-checkbox-partial\" title=\"$title\"></i>
							$title</button>";
				$bar->appendButton('Custom', $dhtml, 'batch');
			}
		}

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_sermonspeaker', 650, 900);
		}

		if ($this->joomla30)
		{
			$this->addFilters();
		}
	}

	/**
	 * Add the filters.
	 */
	protected function addFilters()
	{
		JHtmlSidebar::setAction('index.php?option=com_sermonspeaker&view=sermons');

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
		);

		JHtmlSidebar::addFilter(
			JText::_('COM_SERMONSPEAKER_SELECT_PCAST'),
			'filter_podcast',
			JHtml::_('select.options', array('0'=>JText::_('JUNPUBLISHED'), '1'=>JText::_('JPUBLISHED')), 'value', 'text', $this->state->get('filter.podcast'), true)
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_CATEGORY'),
			'filter_category_id',
			JHtml::_('select.options', JHtml::_('category.options', 'com_sermonspeaker'), 'value', 'text', $this->state->get('filter.category_id'))
		);

		JHtmlSidebar::addFilter(
			JText::_('COM_SERMONSPEAKER_SELECT_SPEAKER'),
			'filter_speaker',
			JHtml::_('select.options', $this->speakers, 'value', 'text', $this->state->get('filter.speaker'))
		);

		JHtmlSidebar::addFilter(
			JText::_('COM_SERMONSPEAKER_SELECT_SERIES'),
			'filter_series',
			JHtml::_('select.options', $this->series, 'value', 'text', $this->state->get('filter.series'))
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_LANGUAGE'),
			'filter_language',
			JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'))
		);
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
			'sermons.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'sermons.state' => JText::_('JSTATUS'),
			'sermons.podcast' => JText::_('COM_SERMONSPEAKER_FIELD_SERMONCAST_LABEL'),
			'sermons.sermon_title' => JText::_('JGLOBAL_TITLE'),
			'category_title' => JText::_('JCATEGORY'),
			'name' => JText::_('COM_SERMONSPEAKER_SPEAKER'),
			'scripture' => JText::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'),
			'series_title' => JText::_('COM_SERMONSPEAKER_SERIE'),
			'sermons.sermon_date' => JText::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'),
			'sermons.hits' => JText::_('JGLOBAL_HITS'),
			'language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'sermons.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}