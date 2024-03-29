<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerViewFrontendupload extends HtmlView
{
	/**
	 * Injected from the controller
	 *
	 * @var    JDocument
	 *
	 * @since ?
	 */
	public $document;
	protected $form;
	protected $item;
	protected $user;
	protected $pageclass_sfx;
	/**
	 * AmazonS3 information
	 *
	 * @var    string
	 *
	 * @since ?
	 */
	protected $s3audio;
	/**
	 * AmazonS3 information
	 *
	 * @var    string
	 *
	 * @since ?
	 */
	protected $s3video;
	/**
	 * A params object
	 *
	 * @var    Joomla\Registry\Registry
	 *
	 * @since ?
	 */
	protected $params;
	/**
	 * The URL to return to
	 *
	 * @var    string
	 *
	 * @since ?
	 */
	protected $return_page;
	/**
	 * A state object
	 *
	 * @var    JObject
	 *
	 * @since ?
	 */
	protected $state;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since ?
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$app    = Factory::getApplication();
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

		$this->user = Factory::getUser();

		// Get model data.
		$this->state       = $this->get('State');
		$this->item        = $this->get('Item');
		$this->form        = $this->get('Form');
		$this->return_page = $this->get('ReturnPage');

		// Create a shortcut to the parameters.
		$this->params = &$this->state->params;

		if (!$this->params->get('fu_enable', 0))
		{
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		if ($this->user->guest)
		{
			$redirectUrl = urlencode(base64_encode(Uri::getInstance()->toString()));
			$app->enqueueMessage(Text::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'), 'error');
			$app->redirect(Route::_('index.php?option=com_users&view=login&return=' . $redirectUrl));

			return false;
		}

		if (empty($this->item->id))
		{
			$authorised = ($this->user->authorise('core.create', 'com_sermonspeaker'));
		}
		else
		{
			$authorised = ($this->user->authorise('core.edit', 'com_sermonspeaker.category.' . $this->item->catid));

			if (!$authorised && ($this->item->created_by == $this->user->id))
			{
				$authorised = ($this->user->authorise('core.edit.own', 'com_sermonspeaker.category.' . $this->item->catid));
			}
		}

		if ($authorised !== true)
		{
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
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
				document.getElementById(element + "_text_icon").className = "icon-radio-unchecked text-danger";
				document.getElementById(element + "_icon").className = "icon-radio-checked text-success";
				document.getElementById("jform_" + element + "_text").disabled = true;
				document.getElementById("jform_" + element).disabled = false;
				if(document.getElementById("jform_" + element + "_chzn")){
					jQuery("#jform_" + element).trigger("liszt:updated");
				}
			} else {
				document.getElementById(element + "_text_icon").className = "icon-radio-checked text-success";
				document.getElementById(element + "_icon").className = "icon-radio-unchecked text-danger";
				document.getElementById("jform_" + element + "_text").disabled = false;
				document.getElementById("jform_" + element).disabled = true;
				if(document.getElementById("jform_" + element + "_chzn")){
					jQuery("#jform_" + element).trigger("liszt:updated");
				}
			}
		}';

		// Push translation to Javascript
		Text::script('COM_SERMONSPEAKER_SERIE');
		Text::script('COM_SERMONSPEAKER_SPEAKER');
		Text::script('COM_SERMONSPEAKER_ID3_NO_MATCH_FOUND');

		// Add Javascript for active tab selection (based on menu item param)
		if ($tab = $this->params->get('active_tab'))
		{
			$this->getDocument()->addScriptDeclaration('jQuery(function() {
					jQuery(\'#sermonEditTab a[href="#' . $tab . '"]\').tab(\'show\');
				})');
		}

		$this->getDocument()->addScriptDeclaration($enElem);
		$this->getDocument()->addScriptDeclaration($toggle);
		$this->getDocument()->getWebAssetManager()->useScript('com_sermonspeaker.id3-lookup');

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
			$this->getDocument()->addScriptDeclaration($picker);
			$this->getDocument()->addScript('https://apis.google.com/js/api.js?onload=onApiLoad');
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
			$region       = $this->params->get('s3_region');
			$bucket       = $this->params->get('s3_bucket');
			$folder       = $this->params->get('s3_folder') ? '/' . $this->params->get('s3_folder') : '';

			if (!$awsAccessKey || !$awsSecretKey || !$region || !$bucket)
			{
				Factory::getApplication()->enqueueMessage(Text::_('COM_SERMONSPEAKER_S3_MISSING_PARAMETER'), 'warning');
			}

			$prefix       = ($region === 'us-east-1') ? 's3' : 's3-' . $region;
			$this->domain = $prefix . '.amazonaws.com/' . $bucket . $folder;
		}

		// Calculate destination path to show
		if ($this->params->get('append_path_user', 0))
		{
			$this->append_user = Factory::getApplication()->getIdentity()->id . '/';
		}
		else
		{
			$this->append_user = '';
		}

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

		$this->getDocument()->addScriptDeclaration($changedate);

		if ($this->params->get('append_path_lang', 0))
		{
			$changelang = "function changelang(language) {
					if(!language || language == '*'){
						language = '" . Factory::getLanguage()->getTag() . "'
					}";

			if (!$this->s3audio)
			{
				$changelang .= "document.id('audiopathlang').innerHTML = language+'/';";
			}

			if (!$this->s3video)
			{
				$changelang .= "document.id('videopathlang').innerHTML = language+'/';";
			}

			$changelang        .= "document.id('addfilepathlang').innerHTML = language+'/';
				}";
			$lang              = ($this->item->language && $this->item->language == '*') ? $this->item->language : Factory::getLanguage()->getTag();
			$this->append_lang = $lang . '/';
		}
		else
		{
			$changelang        = "function changelang(language) {}";
			$this->append_lang = '';
		}

		$this->getDocument()->addScriptDeclaration($changelang);

		// Add javascript validation script
		Text::script('COM_SERMONSPEAKER_JS_CHECK_KEYWORDS', false, true);
		Text::script('COM_SERMONSPEAKER_JS_CHECK_CHARS', false, true);
		$valscript = 'function check(string, count, mode){
					if(mode){
						split = string.split(",");
						if(split.length > count){
							message = Joomla.Text._("COM_SERMONSPEAKER_JS_CHECK_KEYWORDS");
							message = message.replace("{0}", split.length);
							message = message.replace("{1}", count);
							alert(message);
						}
					}else{
						if(string.length > count){
							message = Joomla.Text._("COM_SERMONSPEAKER_JS_CHECK_CHARS");
							message = message.replace("{0}", string.length);
							message = message.replace("{1}", count);
							alert(message);
						}
					}
				}';
		$this->getDocument()->addScriptDeclaration($valscript);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx', ''));

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Function to return bytes from the PHP settings. Taken from the ini_get() manual
	 *
	 * @param   string  $val  Value from the PHP setting
	 *
	 * @return  int  $val  Value in bytes
	 *
	 * @since ?
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

	/**
	 * Prepares the document
	 *
	 * @return  void
	 *
	 * @since ?
	 */
	protected function _prepareDocument()
	{
		$app   = Factory::getApplication();
		$menus = $app->getMenu();

		// Because the application sets a default page title, we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', Text::_('COM_SERMONSPEAKER_FU_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		$this->setDocumentTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->getDocument()->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->getDocument()->setMetaData('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->getDocument()->setMetaData('robots', $this->params->get('robots'));
		}
	}
}
