<?php
defined('_JEXEC') or die;
jimport( 'joomla.application.component.view');
/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewFrontendupload extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $return_page;
	protected $state;

	function display($tpl = null)
	{
		JHTML::stylesheet('media/com_sermonspeaker/css/frontendupload.css');

		// Initialise variables.
		$app		= JFactory::getApplication();

		// Get the log in credentials.
		$credentials = array();
		$credentials['username'] = JRequest::getVar('username', '', 'get', 'username');
		$credentials['password'] = JRequest::getString('password', '', 'get', JREQUEST_ALLOWRAW);

		// Perform the log in.
		if ($credentials['username'] && $credentials['password']){
			$app->login($credentials);
		}
		$user		= JFactory::getUser();

		// Get model data.
		$this->state		= $this->get('State');
		$this->item			= $this->get('Item');
		$this->form			= $this->get('Form');
		$this->return_page	= $this->get('ReturnPage');

		// Create a shortcut to the parameters.
		$params	= &$this->state->params;

		if (!$params->get('fu_enable', 0))
		{
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}
		if ($user->guest)
		{
			$redirectUrl = urlencode(base64_encode(JFactory::getURI()->toString())); 
			$app->redirect(JRoute::_('index.php?option=com_users&view=login&return='.$redirectUrl), JText::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'), 'error');
			return false;
		}

		if (empty($this->item->id))
		{
			$authorised = ($user->authorise('core.create', 'com_sermonspeaker'));
		}
		else
		{
			$authorised = ($user->authorise('core.edit', 'com_sermonspeaker'));
		}
		if ($authorised !== true) {
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}

		// add Javascript for Form Elements enable and disable
		$enElem = 'function enableElement(ena_elem, dis_elem) {
			ena_elem.disabled = false;
			dis_elem.disabled = true;
		}';
		// add Javascript for ID3 Lookup (ajax)
		$lookup	= 'function lookup(elem) {
			xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange=function(){
				if (xmlhttp.readyState==4 && xmlhttp.status==200){
					var data = JSON.decode(xmlhttp.responseText);
					if (data.status==1){
						if(data.filename_title==false || document.getElementById("jform_sermon_title").value==""){
							document.getElementById("jform_sermon_title").value = data.sermon_title;
							document.getElementById("jform_alias").value = data.alias;
						}
						if(data.sermon_number && document.getElementById("jform_sermon_number")){
							document.getElementById("jform_sermon_number").value = data.sermon_number;
						}
						if(data.sermon_date && document.getElementById("jform_sermon_date")){
							document.getElementById("jform_sermon_date").value = data.sermon_date;
						}
						if(data.sermon_time && document.getElementById("jform_sermon_time")){
							document.getElementById("jform_sermon_time").value = data.sermon_time;
						}
						if(data.series_id && document.getElementById("jform_series_id")){
							document.getElementById("jform_series_id").value = data.series_id;
						}
						if(data.speaker_id && document.getElementById("jform_speaker_id")){
							document.getElementById("jform_speaker_id").value = data.speaker_id;
						}
						if(data.notes && document.getElementById("jform_notes")){
							jInsertEditorText(data.notes, "jform_notes");
						}
					} else {
						alert(data.msg);
					}
				}
			}
			xmlhttp.open("POST","index.php?option=com_sermonspeaker&task=file.lookup&format=json",true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
 			xmlhttp.send("file="+elem.value);
		}';

		$document = JFactory::getDocument();
		$document->addScriptDeclaration($enElem);
		$document->addScriptDeclaration($lookup);

		$session	= JFactory::getSession();
		if($params->get('enable_flash')){
			// Prepare Flashuploader
			$audioTypes = '*.aac; *.m4a; *.mp3; *.wma';
			$videoTypes = '*.mp4; *.mov; *.f4v; *.flv; *.3gp; *.3g2; *.wmv';
			$lang	= JRequest::getWord('lang');
			$lang	= ($lang) ? '&lang='.$lang : '';
			$targetURL 	= JURI::root().'index.php?option=com_sermonspeaker&task=file.upload&'.$session->getName().'='.$session->getId().'&'.JSession::getFormToken().'=1&format=json'.$lang;

			// SWFUpload
			JHTML::Script('media/com_sermonspeaker/swfupload/swfupload.js');
			JHTML::Script('media/com_sermonspeaker/swfupload/swfupload.queue.js');
			JHTML::Script('media/com_sermonspeaker/swfupload/fileprogress.js');
			JHTML::Script('media/com_sermonspeaker/swfupload/handlers.js', true);
			$uploader_script = '
				window.onload = function() {
					if(document.id("jform_audiofile_text")){
						upload1 = new SWFUpload({
							upload_url: "'.$targetURL.'",
							flash_url : "'.JURI::root().'media/com_sermonspeaker/swfupload/swfupload.swf",
							file_size_limit : "100MB",
							file_types : "'.$audioTypes.'",
							file_types_description : "'.JText::_('COM_SERMONSPEAKER_FIELD_AUDIOFILE_LABEL', 'true').'",
							file_upload_limit : "0",
							file_queue_limit : "0",
							button_image_url : "'.JURI::root().'media/com_sermonspeaker/swfupload/XPButtonUploadText_61x22.png",
							button_placeholder_id : "btnUpload1",
							button_width: 61,
							button_height: 22,
							button_window_mode: "transparent",
							debug: false,
							swfupload_loaded_handler: function() {
								document.id("btnCancel1").removeClass("ss-hide");
								document.id("audiopathinfo").removeClass("ss-hide");
								if(document.id("upload-noflash")){
									document.id("upload-noflash").destroy();
									document.id("loading").destroy();
								}
							},
							file_dialog_start_handler : fileDialogStart,
							file_queued_handler : fileQueued,
							file_queue_error_handler : fileQueueError,
							file_dialog_complete_handler : fileDialogComplete,
							upload_start_handler : uploadStart,
							upload_progress_handler : uploadProgress,
							upload_error_handler : uploadError,
							upload_success_handler : function uploadSuccess(file, serverData) {
								try {
									var progress = new FileProgress(file, this.customSettings.progressTarget);
									var data = JSON.decode(serverData);
									if (data.status == "1") {
										progress.setComplete();
										progress.setStatus(data.error);
										document.id("jform_audiofile_text").value = data.path;
									} else {
										progress.setError();
										progress.setStatus(data.error);
									}
									progress.toggleCancel(false);
								} catch (ex) {
									this.debug(ex);
								}
							},
							upload_complete_handler : uploadComplete,
							custom_settings : {
								progressTarget : "infoUpload1",
								cancelButtonId : "btnCancel1"
							}
						});
					}
					if(document.id("jform_videofile_text")){
						upload2 = new SWFUpload({
							upload_url: "'.$targetURL.'",
							flash_url : "'.JURI::root().'media/com_sermonspeaker/swfupload/swfupload.swf",
							file_size_limit : "100MB",
							file_types : "'.$videoTypes.'",
							file_types_description : "'.JText::_('COM_SERMONSPEAKER_FIELD_VIDEOFILE_LABEL', 'true').'",
							file_upload_limit : "0",
							file_queue_limit : "0",
							button_image_url : "'.JURI::root().'media/com_sermonspeaker/swfupload/XPButtonUploadText_61x22.png",
							button_placeholder_id : "btnUpload2",
							button_width: 61,
							button_height: 22,
							button_window_mode: "transparent",
							debug: false,
							swfupload_loaded_handler: function() {
								document.id("btnCancel2").removeClass("ss-hide");
								document.id("videopathinfo").removeClass("ss-hide");
								if(document.id("upload-noflash")){
									document.id("upload-noflash").destroy();
									document.id("loading").destroy();
								}
							},
							file_dialog_start_handler : fileDialogStart,
							file_queued_handler : fileQueued,
							file_queue_error_handler : fileQueueError,
							file_dialog_complete_handler : fileDialogComplete,
							upload_start_handler : uploadStart,
							upload_progress_handler : uploadProgress,
							upload_error_handler : uploadError,
							upload_success_handler : function uploadSuccess(file, serverData) {
								try {
									var progress = new FileProgress(file, this.customSettings.progressTarget);
									var data = JSON.decode(serverData);
									if (data.status == "1") {
										progress.setComplete();
										progress.setStatus(data.error);
										document.id("jform_videofile_text").value = data.path;
									} else {
										progress.setError();
										progress.setStatus(data.error);
									}
									progress.toggleCancel(false);
								} catch (ex) {
									this.debug(ex);
								}
							},
							upload_complete_handler : uploadComplete,
							custom_settings : {
								progressTarget : "infoUpload2",
								cancelButtonId : "btnCancel2"
							}
						});
					}
					if(document.id("jform_addfile_text")){
						upload3 = new SWFUpload({
							upload_url: "'.$targetURL.'&addfile=true",
							flash_url : "'.JURI::root().'media/com_sermonspeaker/swfupload/swfupload.swf",
							file_size_limit : "100MB",
							file_upload_limit : "0",
							file_queue_limit : "0",
							button_image_url : "'.JURI::root().'media/com_sermonspeaker/swfupload/XPButtonUploadText_61x22.png",
							button_placeholder_id : "btnUpload3",
							button_width: 61,
							button_height: 22,
							button_window_mode: "transparent",
							debug: false,
							swfupload_loaded_handler: function() {
								document.id("btnCancel3").removeClass("ss-hide");
								document.id("addfilepathinfo").removeClass("ss-hide");
								if(document.id("upload-noflash")){
									document.id("upload-noflash").destroy();
									document.id("loading").destroy();
								}
							},
							file_dialog_start_handler : fileDialogStart,
							file_queued_handler : fileQueued,
							file_queue_error_handler : fileQueueError,
							file_dialog_complete_handler : fileDialogComplete,
							upload_start_handler : uploadStart,
							upload_progress_handler : uploadProgress,
							upload_error_handler : uploadError,
							upload_success_handler : function uploadSuccess(file, serverData) {
								try {
									var progress = new FileProgress(file, this.customSettings.progressTarget);
									var data = JSON.decode(serverData);
									if (data.status == "1") {
										progress.setComplete();
										progress.setStatus(data.error);
										document.id("jform_addfile_text").value = data.path;
									} else {
										progress.setError();
										progress.setStatus(data.error);
									}
									progress.toggleCancel(false);
								} catch (ex) {
									this.debug(ex);
								}
							},
							upload_complete_handler : uploadComplete,
							custom_settings : {
								progressTarget : "infoUpload3",
								cancelButtonId : "btnCancel3"
							}
						});
					}
				}
			';
			$document->addScriptDeclaration($uploader_script);
		}

		// Destination folder based on mode
		$this->s3audio	= ($params->get('path_mode_audio', 0) == 2) ? 1 : 0;
		$this->s3video	= ($params->get('path_mode_video', 0) == 2) ? 1 : 0;
		$params->get('s3_bucket', '');

		// Calculate destination path to show
		if ($params->get('append_path', 0)) {
			$changedate	= "function changedate(datestring) {
					if(datestring && datestring != '0000-00-00 00:00:00'){
						year = datestring.substr(0,4);
						month = datestring.substr(5,2);
					} else {
						now = new Date;
						year = now.getFullYear();
						month = now.getMonth()+1;
						if (month < 10){
							month = '0'+month;
						}
					}";
			if(!$this->s3audio){$changedate	.= "document.id('audiopathdate').innerHTML = year+'/'+month+'/';";}
			if(!$this->s3video){$changedate	.= "document.id('videopathdate').innerHTML = year+'/'+month+'/';";}
			$changedate	.= "document.id('addfilepathdate').innerHTML = year+'/'+month+'/';
				}";
			$time	= ($this->item->sermon_date && $this->item->sermon_date != '0000-00-00 00:00:00') ? strtotime($this->item->sermon_date) : time();
			$this->append_date	= date('Y', $time).'/'.date('m', $time).'/';
		} else {
			$changedate	= "function changedate(datestring) {}";
			$this->append_date	= '';
		}
		$document->addScriptDeclaration($changedate);
		if ($params->get('append_path_lang', 0)) {
			$changelang	= "function changelang(language) {
					if(!language || language == '*'){
						language = '".JFactory::getLanguage()->getTag()."'
					}";
			if(!$this->s3audio){$changelang	.= "document.id('audiopathlang').innerHTML = language+'/';";}
			if(!$this->s3video){$changelang	.= "document.id('videopathlang').innerHTML = language+'/';";}
			$changelang	.= "document.id('addfilepathlang').innerHTML = language+'/';
				}";
			$lang	= ($this->item->language && $this->item->language == '*') ? $this->item->language : JFactory::getLanguage()->getTag();
			$this->append_lang	= $lang.'/';
		} else {
			$changelang	= "function changelang(language) {}";
			$this->append_lang	= '';
		}
		$document->addScriptDeclaration($changelang);

		// Add javascript validation script
		JText::script('COM_SERMONSPEAKER_JS_CHECK_KEYWORDS', false, true);
		JText::script('COM_SERMONSPEAKER_JS_CHECK_CHARS', false, true);
		$valscript	= 'function check(string, count, mode){
					if(mode){
						split = string.split(",");
						if(split.length > count){
							message = Joomla.JText._("COM_SERMONSPEAKER_JS_CHECK_KEYWORDS");
							message = message.replace("{0}", split.length);
							message = message.replace("{1}", count);
							alert(message);
						}
					}else{
						if(string.length > count){
							message = Joomla.JText._("COM_SERMONSPEAKER_JS_CHECK_CHARS");
							message = message.replace("{0}", string.length);
							message = message.replace("{1}", count);
							alert(message);
						}
					}
				}';
		$document->addScriptDeclaration($valscript);

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->params	= $params;
		$this->user		= $user;
		$this->session	= $session;

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_SERMONSPEAKER_FU_TITLE'));
		}
		$title = $this->params->get('page_title', '');
		if (empty($title))
		{
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}