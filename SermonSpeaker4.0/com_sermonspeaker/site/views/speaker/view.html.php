<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit a speaker.
 *
 * @package		Sermonspeaker.Administrator
 */
class SermonspeakerViewSpeaker extends JView
{
	protected $state;
	protected $item;
	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

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
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);

		$isNew		= ($this->item->id == 0);

		JToolBarHelper::title(JText::_('COM_SERMONSPEAKER_SPEAKERS_TITLE'), 'speakers');

		JToolBarHelper::apply('speaker.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('speaker.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::custom('speaker.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		// If an existing item, can save to a copy.
		if (!$isNew) {
			JToolBarHelper::custom('speaker.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id)) {
			JToolBarHelper::cancel('speaker.cancel', 'JTOOLBAR_CANCEL');
		} else {
			JToolBarHelper::cancel('speaker.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}