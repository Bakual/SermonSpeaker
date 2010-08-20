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
		$speakers		= $this->get('Speakers');
		$series			= $this->get('Series');

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
		
		// Reading ID3 Tags if the Lookup Button was pressed
		if ($id3_file = JRequest::getString('file')){ // TODO: Does this belong to the view?
			$this->item->sermon_path = $id3_file;
			require_once(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'id3'.DS.'getid3'.DS.'getid3.php');
			$getID3 	= new getID3;
			$path		= JPATH_SITE.str_replace('/',DS,$id3_file);
			$FileInfo	= $getID3->analyze($path);
			getid3_lib::CopyTagsToComments($FileInfo);
			$id3 = array();
			if (array_key_exists('playtime_string', $FileInfo)){
				$id3['sermon_time']		= $FileInfo['playtime_string'];
			}
			if (array_key_exists('comments_html', $FileInfo)){
				if (array_key_exists('title', $FileInfo['comments_html'])){
					$id3['sermon_title']	= $FileInfo['comments_html']['title'][0];
				}
				if (array_key_exists('track_number', $FileInfo['comments_html'])){
					$id3['sermon_number']	= $FileInfo['comments_html']['track_number'][0]; // ID3v2 Tag
				} elseif (array_key_exists('track', $FileInfo['comments_html'])) {
					$id3['sermon_number']	= $FileInfo['comments_html']['track'][0]; // ID3v1 Tag
				}

				if (array_key_exists('comments', $FileInfo['comments_html'])){
					if ($params->get('fu_id3_comments') == 'ref'){
						if ($FileInfo['comments_html']['comments'][0] != ""){
							$id3['sermon_scripture'] = $FileInfo['comments_html']['comments'][0]; // ID3v2 Tag
						} else {
							$id3['sermon_scripture'] = $FileInfo['comments_html']['comment'][0]; // ID3v1 Tag
						}
					} else {
						if ($FileInfo['comments_html']['comments'][0] != ""){
							$id3['notes'] = $FileInfo['comments_html']['comments'][0]; // ID3v2 Tag
						} else {
							$id3['notes'] = $FileInfo['comments_html']['comment'][0]; // ID3v1 Tag
						}
					}
				}
				$db =& JFactory::getDBO();
				if (array_key_exists('album', $FileInfo['comments_html'])){
					$query = "SELECT id FROM #__sermon_series WHERE series_title like '".$FileInfo['comments_html']['album'][0]."';";
					$db->setQuery($query);
					$id3['series_id'] 	= $db->loadRow();
				}
				if (array_key_exists('artist', $FileInfo['comments_html'])){
					$query = "SELECT id FROM #__sermon_speakers WHERE name like '".$FileInfo['comments_html']['artist'][0]."';";
					$db->setQuery($query);
					$id3['speaker_id']	= $db->loadRow();
				}
			}

			foreach ($id3 as $key => $value){
				if ($value){
					$this->item->$key = $value;
				}
			}
		}

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

		$this->sermon_files	= JHTML::_('select.genericlist', $sermons, 'sermon_path_choice', 'disabled="disabled"', 'file', 'file', $this->item->sermon_path);
		$this->addfiles = JHTML::_('select.genericlist', $addfiles, 'addfile_choice', 'disabled="disabled"', 'file', 'file', $this->item->addfile);
		$this->speakers = JHTML::_('select.genericlist', $speakers, 'jform[speaker_id]', '', 'id', 'name', $this->item->speaker_id, 'jform_speaker_id');
		$this->series	= JHTML::_('select.genericlist', $series, 'jform[series_id]', '', 'id', 'series_title', $this->item->series_id, 'jform_series_id');

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

		JToolBarHelper::title(JText::_('COM_SERMONSPEAKER_SERMON_TITLE'), 'sermons');

		JToolBarHelper::apply('sermon.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('sermon.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::custom('sermon.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		// If an existing item, can save to a copy.
		if (!$isNew) {
			JToolBarHelper::custom('sermon.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id)) {
			JToolBarHelper::cancel('sermon.cancel', 'JTOOLBAR_CANCEL');
		} else {
			JToolBarHelper::cancel('sermon.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}