<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewFu_details extends JView
{
	function display($tpl = null)
	{
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		// Initialise variables.
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$userId		= $user->get('id');

		$params		= $app->getParams();

		if (!$params->get('fu_enable') || !$user->authorise('core.create', 'com_sermonspeaker')){
			JError::raiseWarning(403, JText::_('JGLOBAL_AUTH_ACCESS_DENIED'));
			return;
		} else {
			$filename 	= JRequest::getString('filename', '', 'GET');
			if (!$filename && !JRequest::getBool('submitted', false, 'POST')) {
				JError::raiseWarning(403, JText::_('JGLOBAL_AUTH_ACCESS_DENIED'));
				return;
			} elseif ($filename && !JRequest::getBool('submitted', false, 'POST')){
				// Form wasn't submitted, let's get some additional informations!
				
				// add Javascript for Form Elements enable and disable
				$enElem = 'function enableElement(ena_elem, dis_elem) {
					ena_elem.disabled = false;
					dis_elem.disabled = true;
				}';
				// add Javascript for Scripture Links buttons
				$sendText = 'function sendText(elem, open, close) {
					elem.value = open+elem.value+close;
				}';
				
				$document =& JFactory::getDocument();
				$document->addScriptDeclaration($enElem);
				$document->addScriptDeclaration($sendText);

				JHTML::_('behavior.calendar'); 
				JHTML::_('behavior.modal', 'a.modal-button');

				$editor	=& JFactory::getEditor();

				// Reading ID3 Tags
				$path 	= DS.$params->get('path').DS.$params->get('fu_destdir').DS.$filename;
				require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'id3.php';
				$id3 = SermonspeakerHelperId3::getID3($path, $params);

				// Preparing Dropdown Lists
				// todo: probably move Databasestuff to Model
				$db =& JFactory::getDBO();
				
				$query = "SELECT name, id FROM #__sermon_speakers";
				$db->setQuery($query);
				$speaker_names = $db->loadObjectList();

				$query = "SELECT series_title, id FROM #__sermon_series";
				$db->setQuery($query);
				$series_title = $db->loadObjectList();
				
				// getting the files with extension $filters from $path and its subdirectories for addfiles
				$path_addfile = JPATH_ROOT.DS.$params->get('path_addfile');
				$filters = array('.pdf', '.bmp', '.png', '.jpg', '.gif', '.txt', '.doc');
				$filesabs = array();
				foreach($filters as $filter) {
					$filesabs = array_merge(JFolder::files($path_addfile, $filter, true, true), $filesabs);
				}
				// changing the filepaths relativ to the joomla root
				$lsdir = strlen(JPATH_ROOT);
				$addfiles = array();
				foreach($filesabs as $file) {
					$addfiles[]->file = str_replace('\\', '/', substr($file, $lsdir));
				}

				$lists['speaker_id']	= JHTML::_('select.genericlist', $speaker_names, 'speaker_id', '', 'id', 'name', $id3['speaker_id']);
				$lists['series_id']		= JHTML::_('select.genericlist', $series_title, 'series_id', '', 'id', 'series_title', $id3['series_id']);
				$lists['state']			= JHTML::_('select.booleanlist', 'state', 'class="inputbox"', '1');
				$lists['podcast']		= JHTML::_('select.booleanlist', 'podcast', 'class="inputbox"', '1');
				$lists['catid']			= JHTML::_('list.category', 'catid', 'com_sermonspeaker');
				$lists['addfile_choice'] = JHTML::_('select.genericlist', $addfiles, 'addfile_choice', 'disabled="disabled"', 'file', 'file');
				
				// Push the Data to the Template
				$this->assignRef('lists',		$lists);
				$this->assignRef('filename',	$filename);
				$this->assignRef('editor',		$editor);
				$this->assignRef('id3',			$id3);

				parent::display($tpl);
			} else {
				// Form was submitted, let's save it!
				$sql['speaker_id'] 			= JRequest::getInt('speaker_id', '', 'POST');
				$sql['series_id'] 			= JRequest::getInt('series_id', '', 'POST');
				$file 						= JRequest::getString('filename', '', 'POST');
				if ($params->get('fu_destdir')){
					$fu_destdir = '/'.trim($params->get('fu_destdir'),' /').'/';
				} else {
					$fu_destdir = '/';
				}
				$sql['sermon_path']			= '/'.$params->get('path').$fu_destdir.$file;
				$sql['sermon_title']		= JRequest::getString('sermon_title', '', 'POST');
				$sql['alias']				= JRequest::getString('alias', $sql['sermon_title'], 'POST');
				$sql['alias']				= JFilterOutput::stringURLSafe($sql['alias']);
				$sql['sermon_number']		= JRequest::getInt('sermon_number', '', 'POST');
				$sql['sermon_scripture']	= JRequest::getString('sermon_scripture', '', 'POST');
				$sql['sermon_date']			= JRequest::getString('sermon_date', '', 'POST');
				// making sure that the time is valid formatted
				$tarr = explode(':',JRequest::getString('sermon_time', '', 'POST'));
				foreach ($tarr as $tar){
					$tar = (int)$tar;
					$tar = str_pad($tar, 2, '0', STR_PAD_LEFT);
				}
				if (count($tarr) == 2) {
					$sql['sermon_time'] = '00:'.$tarr[0].':'.$tarr[1];
				} elseif (count($tarr) == 3) {
					$sql['sermon_time'] = $tarr[0].':'.$tarr[1].':'.$tarr[2];
				}
				$sql['notes']		= JRequest::getVar('notes', '', '', 'STRING', JREQUEST_ALLOWHTML);
				$sql['state']	= JRequest::getInt('state', '0', 'POST');
				$sql['podcast']		= JRequest::getInt('podcast', '0', 'POST');
				$user =& JFactory::getUser();
				$sql['created_by']	= $user->id;
				$sql['created']	= date('Y-m-d');
				$sql['catid']		= JRequest::getInt('catid', '0', 'POST');
				$sql['addfile']		= JRequest::getString('addfile_choice', JRequest::getString('addfile_text', '', 'POST'), 'POST');
				$sql['addfileDesc']	= JRequest::getString('addfileDesc', '', 'POST');

				$keys	= implode('`,`', array_keys($sql));
				$values = implode("','", $sql);
				$query	= "INSERT INTO #__sermon_sermons \n"
						. "(`".$keys."`) \n"
						. "VALUES ('".$values."')";
				$db =& JFactory::getDBO();
				$db->setQuery($query);
				if (!$db->query()) { die("SQL error".$db->stderr(true)); }
				
				// Change Template to "Saved" (Step 3)
				$this->setLayout('saved');
				parent::display($tpl);
			}
		}
	}
}