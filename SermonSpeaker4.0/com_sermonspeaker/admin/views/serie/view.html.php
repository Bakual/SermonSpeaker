<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit a series.
 *
 * @package		Sermonspeaker.Administrator
 */
class SermonspeakerViewSerie extends JView
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

		$this->avatarlist = $this->getFiles();
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

		JToolBarHelper::title(JText::_('COM_SERMONSPEAKER_SERIES_TITLE'), 'series');

		JToolBarHelper::apply('serie.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('serie.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::custom('serie.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		// If an existing item, can save to a copy.
		if (!$isNew) {
			JToolBarHelper::custom('serie.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id)) {
			JToolBarHelper::cancel('serie.cancel', 'JTOOLBAR_CANCEL');
		} else {
			JToolBarHelper::cancel('serie.cancel', 'JTOOLBAR_CLOSE');
		}
	}

	protected function getFiles()
	{
		// getting the files with extension $filters from $path and its subdirectories for avatars
		$params	=& JComponentHelper::getParams('com_sermonspeaker');
		$path = JPATH_ROOT.DS.$params->get('path_avatar');
		$path2 = JPATH_ROOT.DS.'components'.DS.'com_sermonspeaker'.DS.'media'.DS.'avatars';
		$filters = array('.jpg','.gif','.png','.bmp');
		$filesabs = array();
		foreach($filters as $filter) {
			$filesabs = array_merge(JFolder::files($path, $filter, true, true),$filesabs);
		}
		foreach($filters as $filter) {
			$filesabs = array_merge(JFolder::files($path2, $filter, true, true),$filesabs);
		}
		
		// changing the filepaths relativ to the joomla root
		$root = JPATH_ROOT;
		$lsdir = strlen($root);
		$avatars = array();
		$avatars[0]->name = JText::_('COM_SERMONSPEAKER_SELECT_NOAVATAR');
		$avatars[0]->file = '';
		$i = 1;
		foreach($filesabs as $file){
			$avatars[$i]->name = trim(strrchr($file,DS),DS);
			$avatars[$i]->file = str_replace('\\','/',substr($file,$lsdir));
			$i++;
		}
		return JHTML::_('select.genericlist', $avatars, 'jform[avatar]', '', 'file', 'name', $this->item->avatar, 'jform_avatar');
	}
}