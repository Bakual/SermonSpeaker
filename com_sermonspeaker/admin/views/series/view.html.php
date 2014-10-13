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

		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// We don't need toolbar in the modal window.
		if ($layout !== 'modal') {
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
		}

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		$canDo 	= SermonspeakerHelper::getActions();
		JToolBarHelper::title(JText::_('COM_SERMONSPEAKER_SERIES_TITLE'), 'drawer-2 series');
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
			JToolBarHelper::custom('tools.seriesorder', 'purge icon-lightning', '','COM_SERMONSPEAKER_TOOLS_ORDER', false);
			JToolBarHelper::divider();
		}

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

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_sermonspeaker', 650, 900);
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
			'series.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'series.state' => JText::_('JSTATUS'),
			'series.title' => JText::_('JGLOBAL_TITLE'),
			'category_title' => JText::_('JCATEGORY'),
			'series.avatar' => JText::_('COM_SERMONSPEAKER_FIELD_PICTURE_LABEL'),
			'series.home' => JText::_('JDEFAULT'),
			'series.hits' => JText::_('JGLOBAL_HITS'),
			'language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'series.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
