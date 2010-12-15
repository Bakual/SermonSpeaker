<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewSeries extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		$state	= $this->get('State');

		JToolBarHelper::title(JText::_('COM_SERMONSPEAKER_SERIES_TITLE'), 'series');
		JToolBarHelper::addNew('serie.add','JTOOLBAR_NEW');
		JToolBarHelper::editList('serie.edit','JTOOLBAR_EDIT');
		JToolBarHelper::divider();
		JToolBarHelper::custom('series.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
		JToolBarHelper::custom('series.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
		if ($state->get('filter.state') != -1 ) {
			JToolBarHelper::divider();
			if ($state->get('filter.state') != 2) {
				JToolBarHelper::archiveList('series.archive','JTOOLBAR_ARCHIVE');
			}
			else if ($state->get('filter.state') == 2) {
				JToolBarHelper::unarchiveList('series.publish', 'JTOOLBAR_UNARCHIVE');
			}
		}
		if ($state->get('filter.state') == -2) {
			JToolBarHelper::deleteList('', 'series.delete','JTOOLBAR_EMPTY_TRASH');
		} else {
			JToolBarHelper::trash('series.trash','JTOOLBAR_TRASH');
		}
		JToolBarHelper::divider();
		JToolBarHelper::preferences('com_sermonspeaker');
	}
}