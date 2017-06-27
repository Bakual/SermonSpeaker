<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerViewSermon extends JViewLegacy
{
	/**
	 * A state object
	 *
	 * @var    JObject
	 *
	 * @since  ?
	 */
	protected $state;

	protected $item;

	protected $form;

	protected $upload_limit;

	protected $append_date;

	protected $append_lang;

	/**
	 * AmazonS3 information
	 *
	 * @var    string
	 *
	 * @since  ?
	 */
	protected $s3audio;

	/**
	 * AmazonS3 information
	 *
	 * @var    string
	 *
	 * @since  ?
	 */
	protected $s3video;

	/**
	 * Amazon S3 domain name
	 *
	 * @var    string
	 *
	 * @since  ?
	 */
	protected $domain;

	/**
	 * A params object
	 *
	 * @var    Joomla\Registry\Registry
	 *
	 * @since  ?
	 */
	protected $params;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @throws Exception
	 *
	 * @since  ?
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');

		// Check some PHP settings for upload limit so I can show it as an info
		$post_max_size       = ini_get('post_max_size');
		$upload_max_filesize = ini_get('upload_max_filesize');
		$this->upload_limit  = ($this->return_bytes($post_max_size) < $this->return_bytes($upload_max_filesize)) ? $post_max_size : $upload_max_filesize;

		// Add Javascript for Form Elements enable and disable
		$enElem = 'function enableElement(ena_elem, dis_elem) {
			ena_elem.disabled = false;
			dis_elem.disabled = true;
		}';

		// Add Javascript for Form Elements enable and disable (J30)
		$toggle = 'function toggleElement(element, state) {
			if (state) {
				document.getElementById(element + "_text_icon").className = "icon-radio-unchecked";
				document.getElementById(element + "_icon").className = "icon-radio-checked";
				document.getElementById("jform_" + element + "_text").disabled = true;
				document.getElementById("jform_" + element).disabled = false;
				if(document.getElementById("jform_" + element + "_chzn")){
					jQuery("#jform_" + element).trigger("liszt:updated");
				}
			} else {
				document.getElementById(element + "_text_icon").className = "icon-radio-checked";
				document.getElementById(element + "_icon").className = "icon-radio-unchecked";
				document.getElementById("jform_" + element + "_text").disabled = false;
				document.getElementById("jform_" + element).disabled = true;
				if(document.getElementById("jform_" + element + "_chzn")){
					jQuery("#jform_" + element).trigger("liszt:updated");
				}
			}
		}';

		// Add Javascript for ID3 Lookup (ajax)
		JText::script('COM_SERMONSPEAKER_ID3_NO_MATCH_FOUND');
		JText::script('COM_SERMONSPEAKER_SERIE');
		JText::script('COM_SERMONSPEAKER_SPEAKER');
		JText::script('NOTICE');
		$lookup = 'function lookup(elem) {
			xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange=function(){
				if (xmlhttp.readyState==4 && xmlhttp.status==200){
					var data = jQuery.parseJSON(xmlhttp.responseText);
					if (data.status==1){
						if(data.filename_title==false || document.getElementById("jform_title").value==""){
							document.getElementById("jform_title").value = data.title;
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
							if(document.getElementById("jform_series_id_chzn")){
								jQuery("#jform_series_id").trigger("liszt:updated");
							}
						}
						if(data.speaker_id && document.getElementById("jform_speaker_id")){
							document.getElementById("jform_speaker_id").value = data.speaker_id;
							if(document.getElementById("jform_speaker_id_chzn")){
								jQuery("#jform_speaker_id").trigger("liszt:updated");
							}
						}
						if(data.notes && document.getElementById("jform_notes")){
							window.parent.Joomla.editors.instances["jform_notes"].replaceSelection(data.notes);
						}
						var splits = elem.id.split("_");
						var field = splits[0]+"_"+splits[1];
						if(data.filesize){
							if(document.getElementById(field+"size")){
								document.getElementById(field+"size").value = data.filesize;
							}
						}
						if(data.audio){
							var info;
							info = "<div class=\"clearfix\"><dl class=\"row id3-info\">";
							jQuery.each(data.audio, function(key,val){
								info += "<dt class=\"col-sm-3\">"+key+"</dt><dd class=\"col-sm-9\">"+val+"</dd>";
							})
							info += "</dl></div>";
							jQuery(elem).parents(".controls").children(".id3-info").remove();
							jQuery(elem).parents(".controls").prepend(info);
						}
						if(data.not_found){
							var notice = new Array();
							if (data.not_found.series){
								notice.push(Joomla.JText._("COM_SERMONSPEAKER_SERIE") + ": " + data.not_found.series);
							}
							if (data.not_found.speakers){
								notice.push(Joomla.JText._("COM_SERMONSPEAKER_SPEAKER") + ": " + data.not_found.speakers);
							}
							notice.push(Joomla.JText._("COM_SERMONSPEAKER_ID3_NO_MATCH_FOUND"));
							var messages = {"notice":notice};
							Joomla.renderMessages(messages);
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

		$this->params = JComponentHelper::getParams('com_sermonspeaker');

		$document = JFactory::getDocument();
		$document->addScriptDeclaration($enElem);
		$document->addScriptDeclaration($toggle);
		$document->addScriptDeclaration($lookup);

		// Google Picker
		if ($this->params->get('googlepicker'))
		{
			$picker = "
				var developerKey = '" . $this->params->get('gapi_developerKey') . "';
				var clientId = '" . $this->params->get('gapi_clientId') . "';
				var scope = [
					'https://www.googleapis.com/auth/drive',
					'https://www.googleapis.com/auth/photos',
					'https://www.googleapis.com/auth/youtube'
				];
				var pickerApiLoaded = false;
				var oauthToken;
				function onApiLoad() {
					gapi.load('auth', {'callback': onAuthApiLoad});
					gapi.load('picker', {'callback': onPickerApiLoad});
				}
				function onAuthApiLoad() {
					window.gapi.auth.authorize(
						{
							'client_id': clientId,
							'scope': scope,
							'immediate': false
						},
						handleAuthResult
					);
				}
				function onPickerApiLoad() {
					pickerApiLoaded = true;
				}
				function handleAuthResult(authResult) {
					if (authResult && !authResult.error) {
						oauthToken = authResult.access_token;
					}
				}

				function createVideoPicker() {
					if (pickerApiLoaded && oauthToken) {
						var picker = new google.picker.PickerBuilder().
							addView(google.picker.ViewId.DOCS_VIDEOS).
							addView(google.picker.ViewId.YOUTUBE).
							addView(google.picker.ViewId.VIDEO_SEARCH).
							addView(google.picker.ViewId.RECENTLY_PICKED).
							setOAuthToken(oauthToken).
							setDeveloperKey(developerKey).
							setCallback(pickerCallbackVideo).
							build();
						picker.setVisible(true);
					}
				}
				function createAddfilePicker() {
					if (pickerApiLoaded && oauthToken) {
						var picker = new google.picker.PickerBuilder().
							addView(google.picker.ViewId.DOCS).
							addView(google.picker.ViewId.PHOTOS).
							addView(google.picker.ViewId.YOUTUBE).
							addView(google.picker.ViewId.IMAGE_SEARCH).
							addView(google.picker.ViewId.VIDEO_SEARCH).
							addView(google.picker.ViewId.RECENTLY_PICKED).
							setOAuthToken(oauthToken).
							setDeveloperKey(developerKey).
							setCallback(pickerCallbackAddfile).
							build();
						picker.setVisible(true);
					}
				}
				function pickerCallbackVideo(data) {
					if (data[google.picker.Response.ACTION] == google.picker.Action.PICKED) {
						var doc = data[google.picker.Response.DOCUMENTS][0];
						document.getElementById('jform_videofile_text').value = doc[google.picker.Document.URL];
					}
				}
				function pickerCallbackAddfile(data) {
					if (data[google.picker.Response.ACTION] == google.picker.Action.PICKED) {
						var doc = data[google.picker.Response.DOCUMENTS][0];
						var value = doc[google.picker.Document.URL];
						if (data.docs[0].iconUrl){
							if (data.docs[0].url.indexOf('?') == -1){
								value += '?icon=' + data.docs[0].iconUrl;
							} else {
								value += '&icon=' + data.docs[0].iconUrl;
							}
						}
						document.getElementById('jform_addfile_text').value = value;
					}
				}
			";
			$document->addScriptDeclaration($picker);
			$document->addScript('https://apis.google.com/js/api.js?onload=onApiLoad', 'text/javascript', true);
		}

		// Destination folder based on mode
		$this->s3audio = ($this->params->get('path_mode_audio', 0) == 2) ? 1 : 0;
		$this->s3video = ($this->params->get('path_mode_video', 0) == 2) ? 1 : 0;

		if ($this->s3audio || $this->s3video)
		{
			// Add missing constant in PHP < 5.5
			defined('CURL_SSLVERSION_TLSv1') or define('CURL_SSLVERSION_TLSv1', 1);

			// AWS access info
			$awsAccessKey = $this->params->get('s3_access_key');
			$awsSecretKey = $this->params->get('s3_secret_key');
			$bucket       = $this->params->get('s3_bucket');

			// Instantiate the class
			$s3 = (new \Aws\Sdk)->createMultiRegionS3([
				'version'     => '2006-03-01',
				'credentials' => [
					'key'    => $awsAccessKey,
					'secret' => $awsSecretKey,
				],
			]);

			if ($this->params->get('s3_custom_bucket'))
			{
				$this->domain = $bucket;
			}
			else
			{
				$region       = $s3->getBucketLocation(['Bucket' => $bucket]);
				$prefix       = ($region == 'US') ? 's3' : 's3-' . $region;
				$this->domain = $prefix . '.amazonaws.com/' . $bucket;
			}
		}

		// Calculate destination path to show
		if ($this->params->get('append_path', 0))
		{
			$changedate = "function changedate(datestring) {
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
					}
					document.id('audiopathdate').innerHTML = year+'/'+month+'/';
					document.id('videopathdate').innerHTML = year+'/'+month+'/';
					document.id('addfilepathdate').innerHTML = year+'/'+month+'/';
				}";

			$time              = ($this->item->sermon_date && $this->item->sermon_date != '0000-00-00 00:00:00') ? strtotime($this->item->sermon_date) : time();
			$this->append_date = date('Y', $time) . '/' . date('m', $time) . '/';
		}
		else
		{
			$changedate        = "function changedate(datestring) {}";
			$this->append_date = '';
		}

		$document->addScriptDeclaration($changedate);

		if ($this->params->get('append_path_lang', 0))
		{
			$changelang = "function changelang(language) {
					if(!language || language == '*'){
						language = '" . JFactory::getLanguage()->getTag() . "'
					}";

			if (!$this->s3audio)
			{
				$changelang .= "document.id('audiopathlang').innerHTML = language+'/';";
			}

			if (!$this->s3video)
			{
				$changelang .= "document.id('videopathlang').innerHTML = language+'/';";
			}

			$changelang .= "document.id('addfilepathlang').innerHTML = language+'/';
				}";
			$lang              = ($this->item->language && $this->item->language == '*') ? $this->item->language : JFactory::getLanguage()->getTag();
			$this->append_lang = $lang . '/';
		}
		else
		{
			$changelang        = "function changelang(language) {}";
			$this->append_lang = '';
		}

		$document->addScriptDeclaration($changelang);

		// Add javascript validation script
		JText::script('COM_SERMONSPEAKER_JS_CHECK_KEYWORDS', false, true);
		JText::script('COM_SERMONSPEAKER_JS_CHECK_CHARS', false, true);
		$valscript = 'function check(string, count, mode){
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
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// If we are forcing a language in modal (used for associations).
		if ($this->getLayout() === 'modal' && $forcedLanguage = JFactory::getApplication()->input->get('forcedLanguage', '', 'cmd'))
		{
			// Set the language field to the forcedLanguage and disable changing it.
			$this->form->setValue('language', null, $forcedLanguage);
			$this->form->setFieldAttribute('language', 'readonly', 'true');

			// Only allow to select categories with All language or with the forced language.
			$this->form->setFieldAttribute('catid', 'language', '*,' . $forcedLanguage);

			// Only allow to select tags with All language or with the forced language.
			$this->form->setFieldAttribute('tags', 'language', '*,' . $forcedLanguage);
		}

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since  ?
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$user       = JFactory::getUser();
		$isNew      = ($this->item->id == 0);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->id);
		$canDo      = SermonspeakerHelper::getActions();
		JToolbarHelper::title(
			JText::sprintf('COM_SERMONSPEAKER_PAGE_' . ($checkedOut ? 'VIEW' : ($isNew ? 'ADD' : 'EDIT')),
				JText::_('COM_SERMONSPEAKER_SERMONS_TITLE'), JText::_('COM_SERMONSPEAKER_SERMON')
			), 'pencil-2 sermons'
		);

		// Build the actions for new and existing records.
		if ($isNew)
		{
			// For new records, check the create permission.
			if ($canDo->get('core.create'))
			{
				JToolbarHelper::apply('sermon.apply');
				JToolbarHelper::save('sermon.save');
				JToolbarHelper::save2new('sermon.save2new');
			}

			JToolbarHelper::cancel('sermon.cancel');
		}
		else
		{
			// Can't save the record if it's checked out.
			if (!$checkedOut)
			{
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $user->id))
				{
					JToolbarHelper::apply('sermon.apply');
					JToolbarHelper::save('sermon.save');

					// We can save this record, but check the create permission to see if we can return to make a new one.
					if ($canDo->get('core.create'))
					{
						JToolbarHelper::save2new('sermon.save2new');
					}
				}
			}

			// If checked out, we can still save to copy
			if ($canDo->get('core.create'))
			{
				JToolbarHelper::save2copy('sermon.save2copy');
			}

			JToolbarHelper::cancel('sermon.cancel', 'JTOOLBAR_CLOSE');
		}

		if ($this->state->params->get('save_history') && $user->authorise('core.edit'))
		{
			JToolbarHelper::versions('com_sermonspeaker.sermon', $this->item->id);
		}
	}

	/**
	 * Function to return bytes from the PHP settings. Taken from the ini_get() manual.
	 *
	 * @param   string $val PHP setting (eg 2M)
	 *
	 * @return  integer
	 *
	 * @since  ?
	 */
	protected function return_bytes($val)
	{
		$val  = trim($val);
		$last = strtolower($val[strlen($val) - 1]);
		$val  = (int) $val;

		switch ($last)
		{
			// The 'G' modifier is available since PHP 5.1.0
			/** @noinspection PhpMissingBreakStatementInspection */
			case 'g':
				$val *= 1024;
			/** @noinspection PhpMissingBreakStatementInspection */
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}

		return $val;
	}
}
