<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewSermons extends JView
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
		$this->speakers		= $this->get('Speakers');
		$this->series		= $this->get('Series');

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
		$canDo 	= SermonspeakerHelper::getActions();
		$state	= $this->get('State');

		JToolBarHelper::title(JText::_('COM_SERMONSPEAKER_SERMON_TITLE'), 'sermons');
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('sermon.add','JTOOLBAR_NEW');
		}
		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('sermon.edit','JTOOLBAR_EDIT');
		}
		JToolBarHelper::divider();
		JToolBarHelper::custom('sermons.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
		JToolBarHelper::custom('sermons.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
		if ($state->get('filter.state') != -1 ) {
			JToolBarHelper::divider();
			if ($state->get('filter.state') != 2) {
				JToolBarHelper::archiveList('sermons.archive','JTOOLBAR_ARCHIVE');
			}
			else if ($state->get('filter.state') == 2) {
				JToolBarHelper::unarchiveList('sermons.publish', 'JTOOLBAR_UNARCHIVE');
			}
		}
		if ($canDo->get('core.delete')) {
			if ($state->get('filter.state') == -2) {
				JToolBarHelper::deleteList('', 'sermons.delete','JTOOLBAR_EMPTY_TRASH');
			} else {
				JToolBarHelper::trash('sermons.trash','JTOOLBAR_TRASH');
			}
		}
		if ($canDo->get('core.admin')) {
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_sermonspeaker');
		}
	}
}