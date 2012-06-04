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
						if(data.sermon_time && document.getElementById("jform_sermon_time")){
							document.getElementById("jform_sermon_time").value = data.sermon_time;
						}
						if(data.notes && document.getElementById("jform_notes")){
							jInsertEditorText(data.notes, "jform_notes");
						}
						if(data.series_id && document.getElementById("jform_series_id")){
							document.getElementById("jform_series_id").value = data.series_id;
						}
						if(data.speaker_id && document.getElementById("jform_speaker_id")){
							document.getElementById("jform_speaker_id").value = data.speaker_id;
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

		$this->params	= JComponentHelper::getParams('com_sermonspeaker');

		$document = JFactory::getDocument();
		$document->addScriptDeclaration($enElem);
		$document->addScriptDeclaration($lookup);

		$session	= JFactory::getSession();
		// Prepare Flashuploader
		$audioTypes = '*.aac; *.m4a; *.mp3; *.wma';
		$videoTypes = '*.mp4; *.mov; *.f4v; *.flv; *.3gp; *.3g2; *.wmv';
		$targetURL 	= JURI::root().'administrator/index.php?option=com_sermonspeaker&task=file.upload&'.$session->getName().'='.$session->getId().'&'.JUtility::getToken().'=1&format=json';
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

		// Calculate destination path to show
		$this->append_date	= '';
		if ($this->params->get('append_path', 0)) {
			$script	= "function changedate(datestring) {
					alert(datestring);
					n = datestring.replace('-',',');
					alert(n);
					var dateobj = new Date(n);
					alert(dateobj.getFullYear());
				}";
			$document->addScriptDeclaration($script);
			$time	= ($this->item->sermon_date && $this->item->sermon_date != '0000-00-00 00:00:00') ? strtotime($this->item->sermon_date) : time();
			$this->append_date	= date('Y', $time).'/'.date('m', $time).'/';
		}
		$this->append_lang	= '';
		if ($this->params->get('append_path_lang', 0)) {
			$lang	= ($this->item->language && $this->item->language == '*') ? $this->item->language : JFactory::getLanguage()->getTag();
			$this->append_lang	= $lang.'/';
		}

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
				JToolBarHelper::custom('sermon.id3', 'export.png', 'export_f2.png', 'Write ID3', false);
				JToolBarHelper::divider();
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