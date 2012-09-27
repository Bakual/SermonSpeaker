<?php
defined('_JEXEC') or die;
class SermonspeakerViewSeries extends JViewLegacy
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
			SermonspeakerHelper::addSubmenu('series');
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
		JToolBarHelper::title(JText::_('COM_SERMONSPEAKER_SERIES_TITLE'), 'series');
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('serie.add','JTOOLBAR_NEW');
		}
		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) {
			JToolBarHelper::editList('serie.edit','JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::custom('series.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('series.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();
			if ($this->state->get('filter.state') != 2) {
				JToolBarHelper::archiveList('series.archive','JTOOLBAR_ARCHIVE');
			} else {
				JToolBarHelper::unarchiveList('series.publish', 'JTOOLBAR_UNARCHIVE');
			}
			JToolBarHelper::checkin('series.checkin');
		}
		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'series.delete','JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} else if ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('series.trash','JTOOLBAR_TRASH');
			JToolBarHelper::divider();
		}
		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::custom('tools.seriesorder', 'purge icon-loop', '','COM_SERMONSPEAKER_TOOLS_ORDER', false);
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
		JHtmlSidebar::setAction('index.php?option=com_sermonspeaker&view=series');

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_CATEGORY'),
			'filter_category_id',
			JHtml::_('select.options', JHtml::_('category.options', 'com_sermonspeaker'), 'value', 'text', $this->state->get('filter.category_id'))
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
			'series.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'series.state' => JText::_('JSTATUS'),
			'series.series_title' => JText::_('JGLOBAL_TITLE'),
			'category_title' => JText::_('JCATEGORY'),
			'series.avatar' => JText::_('COM_SERMONSPEAKER_FIELD_PICTURE_LABEL'),
			'series.home' => JText::_('JDEFAULT'),
			'series.hits' => JText::_('JGLOBAL_HITS'),
			'language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'series.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}