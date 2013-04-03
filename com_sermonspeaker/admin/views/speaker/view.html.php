<?php
// No direct access
defined('_JEXEC') or die;
/**
 * View to edit a speaker.
 *
 * @package		Sermonspeaker.Administrator
 */
class SermonspeakerViewSpeaker extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Switch Layout if in Joomla 3.0
		$version		= new JVersion;
		$this->joomla30	= $version->isCompatible(3.0);
		if ($this->joomla30)
		{
			$this->setLayout($this->getLayout().'30');
		}

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
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		$canDo		= SermonspeakerHelper::getActions();
		JToolbarHelper::title(JText::sprintf('COM_SERMONSPEAKER_PAGE_'.($checkedOut ? 'VIEW' : ($isNew ? 'ADD' : 'EDIT')), JText::_('COM_SERMONSPEAKER_SPEAKERS_TITLE'), JText::_('COM_SERMONSPEAKER_SPEAKER')), 'speakers');

		// Build the actions for new and existing records.
		if ($isNew)
		{
			// For new records, check the create permission.
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::apply('speaker.apply');
				JToolBarHelper::save('speaker.save');
				JToolbarHelper::save2new('speaker.save2new');
			}
			JToolbarHelper::cancel('speaker.cancel');
		}
		else
		{
			// Can't save the record if it's checked out.
			if (!$checkedOut)
			{
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId))
				{
					JToolBarHelper::apply('speaker.apply');
					JToolBarHelper::save('speaker.save');

					// We can save this record, but check the create permission to see if we can return to make a new one.
					if ($canDo->get('core.create'))
					{
						JToolbarHelper::save2new('speaker.save2new');
					}
				}
			}

			// If checked out, we can still save to copy
			if ($canDo->get('core.create'))
			{
				JToolbarHelper::save2copy('speaker.save2copy');
			}

			JToolbarHelper::cancel('speaker.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}