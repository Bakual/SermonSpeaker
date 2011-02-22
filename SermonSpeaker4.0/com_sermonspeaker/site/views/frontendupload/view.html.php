<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewFrontendupload extends JView
{
	function display($tpl = null)
	{
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		// Initialise variables.
		$app		= JFactory::getApplication();
		$params		= $app->getParams();

		// Get the log in credentials.
		$credentials = array();
		$credentials['username'] = JRequest::getVar('username', '', 'get', 'username');
		$credentials['password'] = JRequest::getString('password', '', 'get', JREQUEST_ALLOWRAW);

		// Perform the log in.
		if ($credentials['username'] && $credentials['password']){
			$app->login($credentials);
		}

		$user		= JFactory::getUser();

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));

			return false;
		}

		if (!$params->get('fu_enable') || !$user->authorise('core.create', 'com_sermonspeaker')){
			JError::raiseWarning(403, JText::_('JGLOBAL_AUTH_ACCESS_DENIED'));
			return;
		} else {
			if (!JRequest::getBool('submitted', false, 'POST')){
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

				// Reading ID3 Tags if the Lookup Button was pressed
				if ($id3_file = JRequest::getString('file')){
					if (JRequest::getCmd('type') == 'video'){
						$data['videofile'] = $id3_file;
					} else {
						$data['audiofile'] = $id3_file;
					}
					require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'id3.php';

					$id3 = SermonspeakerHelperId3::getID3($id3_file, $params);
					foreach ($id3 as $key => $value){
						$data[$key] = $value;
					}
				} else {
					$data = array();
					$data['audiofile'] 		= '';
					$data['videofile'] 		= '';
					$data['speaker_id'] 	= 0;
					$data['series_id'] 		= 0;
					$data['sermon_time']	= '';
					$data['sermon_title']	= '';
					$data['alias'] 			= '';
					$data['sermon_number']	= '';
					$data['notes'] 			= '';
					$data['sermon_scripture'] = '';
					$data['series_id'] 		= '';
					$data['speaker_id']		= '';
				}

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

				$lists['speaker_id']	= JHTML::_('select.genericlist', $speaker_names, 'speaker_id', '', 'id', 'name', $data['speaker_id']);
				$lists['series_id']		= JHTML::_('select.genericlist', $series_title, 'series_id', '', 'id', 'series_title', $data['series_id']);
				$lists['state']			= JHTML::_('select.booleanlist', 'state', 'class="inputbox"', '1');
				$lists['podcast']		= JHTML::_('select.booleanlist', 'podcast', 'class="inputbox"', '1');
				$lists['catid']			= JHTML::_('list.category', 'catid', 'com_sermonspeaker');
				$lists['addfile_choice'] = JHTML::_('select.genericlist', $addfiles, 'addfile_choice', 'disabled="disabled"', 'file', 'file');
				
				// Prepare Flashuploader
				JHTML::_('stylesheet','media/mediamanager.css', array(), true);

				$displayTypes = '*.aac, *.m4a, *.mp3, *.mp4, *.mov, *.f4v, *.flv, *.3gp, *.3g2';
				$filterTypes = '*.aac; *.m4a; *.mp3; *.mp4; *.mov; *.f4v; *.flv; *.3gp; *.3g2';
				$typeString = "{ '".JText::_('COM_SERMONSPEAKER_FU_FILES', 'true').' ('.$displayTypes.")': '".$filterTypes."' }";
				$session	= JFactory::getSession();
				
				$targetURL = JURI::base().'index.php?option=com_sermonspeaker&amp;task=file.upload&amp;tmpl=component&amp;'.$session->getName().'='.$session->getId().'&amp;'.JUtility::getToken().'=1&amp;format=json';

				$js =  "function(file, response) {
							var json = new Hash(JSON.decode(response, true) || {});
							if (json.get('status') == '1') {
								document.fu_createsermon.audiofile.value = json.get('path');
								file.element.addClass('file-success');
								file.info.set('html', '<strong>' + Joomla.JText._('JLIB_HTML_BEHAVIOR_UPLOADER_FILE_SUCCESSFULLY_UPLOADED') + '</strong>');
							} else {
								file.element.addClass('file-failed');
								file.info.set('html', '<strong>' + Joomla.JText._('JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_OCCURRED', 'An Error Occurred').substitute({ error: json.get('error') }) + '</strong>');
								test.info.set('html', '<strong>' + Joomla.JText._('JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_OCCURRED', 'An Error Occurred').substitute({ error: json.get('error') }) + '</strong>');
							}
						}";
				JHtml::_('behavior.uploader', 'upload-flash',
					array(
						'onFileSuccess'	=> $js,
						'onStart'		=> "function() {
												document.getElementById('status-bar').style.display='block';
												document.getElementById('upload-queue').style.display='block';
												}",
						'onBeforeStart'	=> "function() {
												document.getElementById('upload-browse').style.display='block';
												}",
						'targetURL' 	=> $targetURL,
						'typeFilter' 	=> $typeString,
						'fileSizeMax'	=> 0,
						'multiple'		=> false,
						'queued'		=> false,
						'instantStart'	=> true,
					)
				);

				// Push the Data to the Template
				$this->assignRef('lists',		$lists);
				$this->assignRef('filename',	$filename);
				$this->assignRef('path',		$path);
				$this->assignRef('editor',		$editor);
				$this->assignRef('data',		$data);
				$this->assignRef('params',		$params);
				$this->assignRef('session', $session);

				parent::display($tpl);
			} else {
				// Form was submitted, let's save it!
				$db =& JFactory::getDBO();
				$sql['speaker_id'] 			= JRequest::getInt('speaker_id', '', 'POST');
				$sql['series_id'] 			= JRequest::getInt('series_id', '', 'POST');
				$file 						= JRequest::getString('filename', '', 'POST');
				if ($params->get('fu_destdir')){
					$fu_destdir = '/'.trim($params->get('fu_destdir'),' /').'/';
				} else {
					$fu_destdir = '/';
				}
				$sql['audiofile']			= $db->getEscaped('/'.$params->get('path').$fu_destdir.$file);
				$sql['sermon_title']		= $db->getEscaped(JRequest::getString('sermon_title', '', 'POST'));
				$sql['alias']				= JRequest::getString('alias', $sql['sermon_title'], 'POST');
				$sql['alias']				= $db->getEscaped(JFilterOutput::stringURLSafe($sql['alias']));
				$sql['sermon_number']		= JRequest::getInt('sermon_number', '', 'POST');
				$sql['sermon_scripture']	= $db->getEscaped(JRequest::getString('sermon_scripture', '', 'POST'));
				$sql['sermon_date']			= $db->getEscaped(JRequest::getString('sermon_date', '', 'POST'));
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
				$sql['notes']		= $db->getEscaped(JRequest::getVar('notes', '', '', 'STRING', JREQUEST_ALLOWHTML));
				$sql['state']		= JRequest::getInt('state', '0', 'POST');
				$sql['podcast']		= JRequest::getInt('podcast', '0', 'POST');
				$user =& JFactory::getUser();
				$sql['created_by']	= $user->id;
				$sql['created']		= date('Y-m-d');
				$sql['catid']		= JRequest::getInt('catid', '0', 'POST');
				$sql['addfile']		= $db->getEscaped(JRequest::getString('addfile_choice', JRequest::getString('addfile_text', '', 'POST'), 'POST'));
				$sql['addfileDesc']	= $db->getEscaped(JRequest::getString('addfileDesc', '', 'POST'));

				$keys	= implode('`,`', array_keys($sql));
				$values = implode("','", $sql);
				$query	= "INSERT INTO #__sermon_sermons \n"
						. "(`".$keys."`) \n"
						. "VALUES ('".$values."')";
				$db->setQuery($query);
				if (!$db->query()) { die("SQL error".$db->stderr(true)); }
				
				$app->redirect(JRoute::_('index.php?view=frontendupload'), JText::_('COM_SERMONSPEAKER_FU_UPSAVEDOK'));
				return;
			}
		}
	}
}