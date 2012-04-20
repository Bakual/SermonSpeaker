<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewSpeakers extends JView
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
		$canDo 	= SermonspeakerHelper::getActions();

		JToolBarHelper::title(JText::_('COM_SERMONSPEAKER_SPEAKERS_TITLE'), 'speakers');

		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('speaker.add','JTOOLBAR_NEW');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) {
			JToolBarHelper::editList('speaker.edit','JTOOLBAR_EDIT');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::custom('speakers.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('speakers.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();
			if ($this->state->get('filter.state') != 2) {
				JToolBarHelper::archiveList('speakers.archive','JTOOLBAR_ARCHIVE');
			} else {
				JToolBarHelper::unarchiveList('speakers.publish', 'JTOOLBAR_UNARCHIVE');
			}
			JToolBarHelper::checkin('speakers.checkin');
		}

		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'speakers.delete','JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} else if ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('speakers.trash','JTOOLBAR_TRASH');
			JToolBarHelper::divider();
		}
		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::custom('tools.speakersorder', 'purge', '','COM_SERMONSPEAKER_TOOLS_ORDER', false);
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_sermonspeaker', 650, 900);
		}
	}
}