<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2015 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerViewFrontendupload extends JViewLegacy
{
	protected $form;

	protected $item;

	protected $user;

	protected $pageclass_sfx;

	/**
	 * AmazonS3 information
	 *
	 * @var    string
	 */
	protected $s3audio;

	/**
	 * AmazonS3 information
	 *
	 * @var    string
	 */
	protected $s3video;

	/**
	 * AmazonS3 information
	 *
	 * @var    string
	 */
	protected $bucket;

	/**
	 * AmazonS3 information
	 *
	 * @var    string
	 */
	protected $prefix;

	/**
	 * A params object
	 *
	 * @var    Joomla\Registry\Registry
	 */

	protected $params;
	/**
	 * The URL to return to
	 *
	 * @var    string
	 */
	protected $return_page;

	/**
	 * Injected from the controller
	 *
	 * @var    JDocument
	 */
	public $document;

	/**
	 * A state object
	 *
	 * @var    JObject
	 */
	protected $state;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$app    = JFactory::getApplication();
		$jinput = $app->input;

		// Get the log in credentials.
		$credentials              = array();
		$credentials['username']  = $jinput->get->get('username', '', 'USERNAME');
		$credentials['password']  = $jinput->get->get('password', '', 'RAW');
		$credentials['secretkey'] = $jinput->get->get('secretkey', '', 'RAW');

		// Perform the log in.
		if ($credentials['username'] && $credentials['password'])
		{
			$app->login($credentials);
		}

		$this->user = JFactory::getUser();

		// Get model data.
		$this->state       = $this->get('State');
		$this->item        = $this->get('Item');
		$this->form        = $this->get('Form');
		$this->return_page = $this->get('ReturnPage');

		// Create a shortcut to the parameters.
		$this->params = &$this->state->params;

		if (!$this->params->get('fu_enable', 0))
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		if ($this->user->guest)
		{
			$redirectUrl = urlencode(base64_encode(JUri::getInstance()->toString()));
			$app->redirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . $redirectUrl),
				JText::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'),
				'error'
			);

			return false;
		}

		if (empty($this->item->id))
		{
			$authorised = ($this->user->authorise('core.create', 'com_sermonspeaker'));
		}
		else
		{
			$authorised = ($this->user->authorise('core.edit', 'com_sermonspeaker'));
		}

		if ($authorised !== true)
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
		}

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
				document.getElementById(element + "_text_icon").className = "btn add-on icon-radio-unchecked";
				document.getElementById(element + "_icon").className = "btn add-on icon-radio-checked";
				document.getElementById("jform_" + element + "_text").disabled = true;
				document.getElementById("jform_" + element).disabled = false;
				if(document.getElementById("jform_" + element + "_chzn")){
					jQuery("#jform_" + element).trigger("liszt:updated");
				}
			} else {
				document.getElementById(element + "_text_icon").className = "btn add-on icon-radio-checked";
				document.getElementById(element + "_icon").className = "btn add-on icon-radio-unchecked";
				document.getElementById("jform_" + element + "_text").disabled = false;
				document.getElementById("jform_" + element).disabled = true;
				if(document.getElementById("jform_" + element + "_chzn")){
					jQuery("#jform_" + element).trigger("liszt:updated");
				}
			}
		}';

		// Add Javascript for ID3 Lookup (ajax)
		$lookup = 'function lookup(elem) {
			xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange=function(){
				if (xmlhttp.readyState==4 && xmlhttp.status==200){
					var data = JSON.decode(xmlhttp.responseText);
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
			xmlhttp.open("POST","' . JURI::root() . 'index.php?option=com_sermonspeaker&task=file.lookup&format=json",true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
 			xmlhttp.send("file="+elem.value);
		}';

		// Add Javascript for active tab selection (based on menu item param)
		if ($tab = $this->params->get('active_tab'))
		{
			$this->document->addScriptDeclaration('jQuery(function() {
					jQuery(\'#sermonEditTab a[href="#' . $tab . '"]\').tab(\'show\');
				})');
		}

		$this->document->addScriptDeclaration($enElem);
		$this->document->addScriptDeclaration($toggle);
		$this->document->addScriptDeclaration($lookup);

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
			$this->document->addScriptDeclaration($picker);
			$this->document->addScript('https://apis.google.com/js/api.js?onload=onApiLoad', 'text/javascript', true);
		}

		// Destination folder based on mode
		$this->s3audio = ($this->params->get('path_mode_audio', 0) == 2) ? 1 : 0;
		$this->s3video = ($this->params->get('path_mode_video', 0) == 2) ? 1 : 0;

		if ($this->s3audio || $this->s3video)
		{
			// Include the S3 class
			require_once JPATH_COMPONENT_ADMINISTRATOR . '/s3/S3.php';

			// AWS access info
			$awsAccessKey = $this->params->get('s3_access_key');
			$awsSecretKey = $this->params->get('s3_secret_key');
			$this->bucket = $this->params->get('s3_bucket');

			// Instantiate the class
			$s3           = new S3($awsAccessKey, $awsSecretKey);
			$region       = $s3->getBucketLocation($this->bucket);
			$this->prefix = ($region == 'US') ? 's3' : 's3-' . $region;
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
					}";

			if (!$this->s3audio)
			{
				$changedate .= "document.id('audiopathdate').innerHTML = year+'/'+month+'/';";
			}

			if (!$this->s3video)
			{
				$changedate .= "document.id('videopathdate').innerHTML = year+'/'+month+'/';";
			}

			$changedate .= "document.id('addfilepathdate').innerHTML = year+'/'+month+'/';
				}";
			$time = ($this->item->sermon_date && $this->item->sermon_date != '0000-00-00 00:00:00') ? strtotime($this->item->sermon_date) : time();
			$this->append_date = date('Y', $time) . '/' . date('m', $time) . '/';
		}
		else
		{
			$changedate = "function changedate(datestring) {}";
			$this->append_date = '';
		}

		$this->document->addScriptDeclaration($changedate);

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
			$lang = ($this->item->language && $this->item->language == '*') ? $this->item->language : JFactory::getLanguage()->getTag();
			$this->append_lang = $lang . '/';
		}
		else
		{
			$changelang = "function changelang(language) {}";
			$this->append_lang = '';
		}

		$this->document->addScriptDeclaration($changelang);

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
		$this->document->addScriptDeclaration($valscript);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

		$this->_prepareDocument();

		return parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 */
	protected function _prepareDocument()
	{
		$app   = JFactory::getApplication();
		$menus = $app->getMenu();

		// Because the application sets a default page title, we need to get it from the menu item itself
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
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
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

	/**
	 * Function to return bytes from the PHP settings. Taken from the ini_get() manual
	 *
	 * @param   string  $val  Value from the PHP setting
	 *
	 * @return  int  $val  Value in bytes
	 */
	protected function return_bytes($val)
	{
		$val  = trim($val);
		$last = strtolower($val[strlen($val) - 1]);

		switch ($last)
		{
			// The 'G' modifier is available since PHP 5.1.0
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}

		return $val;
	}
}
