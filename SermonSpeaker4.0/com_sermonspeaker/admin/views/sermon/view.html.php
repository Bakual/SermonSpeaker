<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit a sermon.
 *
 * @package		Sermonspeaker.Administrator
 */
class SermonspeakerViewSermon extends JView
{
//	protected $state;
	protected $item;
	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		// add Javascript for Form Elements enable and disable
		$enElem = 'function enableElement(ena_elem, dis_elem) {
			ena_elem.disabled = false;
			dis_elem.disabled = true;
		}';
		// add Javascript for Scripture Links buttons
		$sendText = 'function sendText(elem, open, close) {
			elem.value = open+elem.value+close;
		}';

		$params	= &JComponentHelper::getParams('com_sermonspeaker');

		$document =& JFactory::getDocument();
		$document->addScriptDeclaration($enElem);
		$document->addScriptDeclaration($sendText);

		// getting the files with extension $filters from $path and its subdirectories for sermons
		$path = JPATH_ROOT.DS.$params->get('path');
		$filters = array('.mp3','.m4a','.flv','.mp4','.wmv');
		$filesabs = array();
		foreach($filters as $filter) {
			$filesabs = array_merge(JFolder::files($path, $filter, true, true),$filesabs);
		}
		
		// changing the filepaths relativ to the joomla root
		$root = JPATH_ROOT;
		$lsdir = strlen($root);
		$sermons = array();
		foreach($filesabs as $file) {
			$sermons[]->file = str_replace('\\','/',substr($file,$lsdir));
		}

		// getting the files with extension $filters from $path and its subdirectories for addfiles
		$path_addfile = JPATH_ROOT.DS.$params->get('path_addfile');
		$filters = array('.pdf','.bmp','.png','.jpg','.gif','.txt','.doc');
		$filesabs = array();
		foreach($filters as $filter) {
			$filesabs = array_merge(JFolder::files($path_addfile, $filter, true, true),$filesabs);
		}
		// changing the filepaths relativ to the joomla root
		$addfiles = array();
		foreach($filesabs as $file) {
			$addfiles[]->file = str_replace('\\','/',substr($file,$lsdir));
		}

		$this->sermon_files	= JHTML::_('select.genericlist', $sermons, 'jform[sermon_path]', 'disabled="disabled"', 'file', 'file', $this->item->sermon_path, 'jform_sermon_path_choice');
		$this->addfiles = JHTML::_('select.genericlist', $addfiles, 'jform[addfile]', 'disabled="disabled"', 'file', 'file', $this->item->addfile, 'jform_addfile_choice');

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

		JToolBarHelper::title(JText::_('COM_SERMONSPEAKER_SERMONS_TITLE'), 'sermons');

		// Build the actions for new and existing records.
		if ($isNew)  {
			// For new records, check the create permission.
			if ($canDo->get('core.create')) {
				JToolBarHelper::apply('sermon.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('sermon.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::custom('sermon.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}

			JToolBarHelper::cancel('sermon.cancel', 'JTOOLBAR_CANCEL');
		} else {
			// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
			if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId)) {
				JToolBarHelper::apply('sermon.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('sermon.save', 'JTOOLBAR_SAVE');

				// We can save this record as copy, but check the create permission first.
				if ($canDo->get('core.create')) {
					JToolBarHelper::custom('sermon.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
					JToolBarHelper::custom('sermon.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
				}
			}

			JToolBarHelper::cancel('sermon.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}