<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\View\Sermon;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Sermonspeaker\Component\Sermonspeaker\Administrator\Helper\SermonspeakerHelper;
use Sermonspeaker\Component\Sermonspeaker\Site\Helper\RouteHelper;

defined('_JEXEC') or die;

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * A state object
	 *
	 * @var    \JObject
	 *
	 * @since  ?
	 */
	protected $state;

	protected $item;

	protected $form;

	protected $upload_limit;

	protected $append_user;

	protected $append_date;

	protected $append_lang;

	/**
	 * AmazonS3 information
	 *
	 * @var    string
	 *
	 * @since  ?
	 */
	protected string $s3audio;

	/**
	 * AmazonS3 information
	 *
	 * @var    string
	 *
	 * @since  ?
	 */
	protected string $s3video;

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
	 * @var    \Joomla\Registry\Registry
	 *
	 * @since  ?
	 */
	protected $params;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since  ?
	 */
	public function display($tpl = null): void
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
		Text::script('COM_SERMONSPEAKER_ID3_NO_MATCH_FOUND');
		Text::script('COM_SERMONSPEAKER_SERIE');
		Text::script('COM_SERMONSPEAKER_SPEAKER');
		Text::script('NOTICE');
		$this->params = ComponentHelper::getParams('com_sermonspeaker');

		$document = Factory::getApplication()->getDocument();
		$document->addScriptDeclaration($enElem);
		$document->addScriptDeclaration($toggle);
		$document->getWebAssetManager()->useScript('com_sermonspeaker.id3-lookup');

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
			$document->addScript('https://apis.google.com/js/api.js?onload=onApiLoad');
		}

		// Destination folder based on mode
		$this->s3audio = ($this->params->get('path_mode_audio', 0) == 2) ? 1 : 0;
		$this->s3video = ($this->params->get('path_mode_video', 0) == 2) ? 1 : 0;

		if ($this->s3audio || $this->s3video)
		{
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

		$document->addScriptDeclaration($changedate);

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

		$document->addScriptDeclaration($changelang);

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
		$document->addScriptDeclaration($valscript);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// If we are forcing a language in modal (used for associations).
		if ($this->getLayout() === 'modal' && $forcedLanguage = Factory::getApplication()->input->get('forcedLanguage', ''))
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

		parent::display($tpl);
	}

	/**
	 * Function to return bytes from the PHP settings. Taken from the ini_get() manual.
	 *
	 * @param   string  $val  PHP setting (eg 2M)
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

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since  ?
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);
		$user       = Factory::getApplication()->getIdentity();
		$isNew      = ($this->item->id == 0);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->id);
		$canDo      = SermonspeakerHelper::getActions();
		$toolbar    = Toolbar::getInstance();

		ToolbarHelper::title(
			Text::sprintf('COM_SERMONSPEAKER_PAGE_' . ($checkedOut ? 'VIEW' : ($isNew ? 'ADD' : 'EDIT')),
				Text::_('COM_SERMONSPEAKER_SERMONS_TITLE')),
			'pencil-2 sermons'
		);

		// Build the actions for new and existing records.
		if ($isNew)
		{
			// For new records, check the create permission.
			if ($canDo->get('core.create'))
			{
				$toolbar->apply('sermon.apply');

				$saveGroup = $toolbar->dropdownButton('save-group');

				$saveGroup->configure(
					function (Toolbar $childBar) use ($user) {
						$childBar->save('sermon.save');

						if ($user->authorise('core.create', 'com_menus.menu'))
						{
							$childBar->save('sermon.save2menu', Text::_('JTOOLBAR_SAVE_TO_MENU'));
						}

						$childBar->save2new('sermon.save2new');
					}
				);
			}
		}
		else
		{
			// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
			$itemEditable = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $user->id);

			if (!$checkedOut && $itemEditable)
			{
				$toolbar->apply('sermon.apply');
			}

			$saveGroup = $toolbar->dropdownButton('save-group');

			$saveGroup->configure(
				function (Toolbar $childBar) use ($checkedOut, $itemEditable, $canDo, $user) {
					// Can't save the record if it's checked out and editable
					if (!$checkedOut && $itemEditable)
					{
						$childBar->save('sermon.save');

						// We can save this record, but check the create permission to see if we can return to make a new one.
						if ($canDo->get('core.create'))
						{
							$childBar->save2new('sermon.save2new');
						}
					}

					// If checked out, we can still save2menu
					if ($user->authorise('core.create', 'com_menus.menu'))
					{
						$childBar->save('sermon.save2menu', Text::_('JTOOLBAR_SAVE_TO_MENU'));
					}

					// If checked out, we can still save2copy
					if ($canDo->get('core.create'))
					{
						$childBar->save2copy('sermon.save2copy');
					}
				}
			);

			if (ComponentHelper::isEnabled('com_contenthistory') && $this->state->params->get('save_history') && $itemEditable)
			{
				$toolbar->versions('com_sermonspeaker.sermon', $this->item->id);
			}

			$url = Route::link(
				'site',
				RouteHelper::getSermonRoute($this->item->id . ':' . $this->item->alias, $this->item->catid, $this->item->language),
				true
			);

			$toolbar->preview($url)
				->bodyHeight(80)
				->modalWidth(90);
		}

		$toolbar->cancel('sermon.cancel');
	}
}
