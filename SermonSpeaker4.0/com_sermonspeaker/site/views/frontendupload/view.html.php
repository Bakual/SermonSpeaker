<?php
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');
/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewFrontendupload extends JView
{
	protected $form;
	protected $item;
	protected $return_page;
	protected $state;

	function display($tpl = null)
	{
		JHTML::stylesheet('frontendupload.css', 'media/com_sermonspeaker/css/');

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

		if (empty($this->item->id)) {
			$authorised = ($params->get('fu_enable') && $user->authorise('core.create', 'com_sermonspeaker'));
		} else {
			$authorised = ($params->get('fu_enable') && $user->authorise('core.edit', 'com_sermonspeaker'));
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
		// add Javascript for Scripture Links buttons
		$sendText = 'function sendText(elem, open, close) {
			document.getElementById(elem).value = open+document.getElementById(elem).value+close;
		}';

		$document = JFactory::getDocument();
		$document->addScriptDeclaration($enElem);
		$document->addScriptDeclaration($sendText);

		$session	= JFactory::getSession();
		if($params->get('enable_flash')){
			// Prepare Flashuploader
			$audioTypes = '*.aac; *.m4a; *.mp3; *.wma';
			$videoTypes = '*.mp4; *.mov; *.f4v; *.flv; *.3gp; *.3g2; *.wmv';
			$lang	= JRequest::getWord('lang');
			$lang	= ($lang) ? '&lang='.$lang : '';
			$targetURL 	= JURI::root().'index.php?option=com_sermonspeaker&task=file.upload&'.$session->getName().'='.$session->getId().'&'.JUtility::getToken().'=1&format=json'.$lang;

			// SWFUpload
			JHTML::Script('media/com_sermonspeaker/swfupload/swfupload.js');
			JHTML::Script('media/com_sermonspeaker/swfupload/swfupload.queue.js');
			JHTML::Script('media/com_sermonspeaker/swfupload/fileprogress.js');
			JHTML::Script('media/com_sermonspeaker/swfupload/handlers.js', true);
			$uploader_script = '
				window.onload = function() {
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
							document.id(\'btnCancel1\').removeClass(\'ss-hide\');
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
							document.id(\'btnCancel2\').removeClass(\'ss-hide\');
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
							document.id(\'upload-noflash\').destroy();
							document.id(\'btnCancel3\').removeClass(\'ss-hide\');
							document.id(\'loading\').destroy();
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
			';
			$document->addScriptDeclaration($uploader_script);
		}

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

		// Set Page Header if not already set in the menu entry
		$menus	= $app->getMenu();
		$menu 	= $menus->getActive();
		if ($menu){
			$this->params->def('page_heading', $menu->title);
		} else {
			$this->params->def('page_heading', JText::_('COM_SERMONSPEAKER_FU_TITLE'));
		}

		// Set Pagetitle
		if (!$menu) {
			$title = JText::_('COM_SERMONSPEAKER_FU_TITLE');
		} else {
			$title = $this->params->get('page_title', '');
		}
		if ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		$this->document->setTitle($title);

		// Set MetaData from menu entry if available
		if ($this->params->get('menu-meta_description')){
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
		if ($this->params->get('menu-meta_keywords')){
			$this->document->setMetaData('keywords', $this->params->get('menu-meta_keywords'));
		}
	}
}