<?php
// No direct access
defined('_JEXEC') or die;
/**
 * View to edit a tag.
 *
 * @package		Sermonspeaker.Administrator
 */
class SermonspeakerViewTag extends JViewLegacy
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
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$canDo		= SermonspeakerHelper::getActions();
		JToolBarHelper::title(JText::_('COM_SERMONSPEAKER_TAGS_TITLE'), 'tags');
		// Built the actions for new and existing records.
		if ($isNew)  {
			// For new records, check the create permission.
			if ($canDo->get('core.create')) {
				JToolBarHelper::apply('tag.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('tag.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::custom('tag.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::cancel('tag.cancel', 'JTOOLBAR_CANCEL');
		} else {
			// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
			if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId)) {
				JToolBarHelper::apply('tag.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('tag.save', 'JTOOLBAR_SAVE');
				// We can save this record as copy, but check the create permission first.
				if ($canDo->get('core.create')) {
					JToolBarHelper::custom('tag.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
					JToolBarHelper::custom('tag.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
				}
			}
			JToolBarHelper::cancel('tag.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}