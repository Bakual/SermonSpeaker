<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2014 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * SermonSpeaker Component Controller
 */
class SermonspeakerController extends JControllerLegacy
{
	/**
	 * The default view for the display method.
	 *
	 * @var    string
	 * @since  4.0
	 */
	protected $default_view = 'sermons';

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 * Recognized key values include 'name', 'default_task', 'model_path', and
	 * 'view_path' (this list is not meant to be comprehensive).
	 *
	 * @since   4.0
	 */
	public function __construct($config = array())
	{
		$this->input = JFactory::getApplication()->input;

		// Frontpage Editor sermons proxying:
		if ($this->input->get('view') === 'sermons' && $this->input->get('layout') === 'modal')
		{
			$config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
		}

		parent::__construct($config);
	}

	/**
	 * View method.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JControllerLegacy  A JControllerLegacy object to support chaining.
	 *
	 * @since   4.0
	 */
	public function display($cachable = false, $urlparams = array())
	{
		$cachable = JFactory::getUser()->get('id') ? false : true;
		$viewName = $this->input->get('view', $this->default_view);

		$params   = JFactory::getApplication()->getParams();

		if ($params->get('css_icomoon'))
		{
			JHtml::_('stylesheet', 'jui/icomoon.css', array(), true);
		}

		// Make sure the format is raw for feed and sitemap view
		// Bug: Doesn't take into account additional filters (type, cat)
		if (($viewName == 'feed' || $viewName == 'sitemap') && $this->input->get('format') != 'raw')
		{
			// JFactory::$document = JDocument::getInstance('raw');
			header('HTTP/1.1 301 Moved Permanently');
			header('Location: ' . JURI::root() . 'index.php?option=com_sermonspeaker&view=' . $viewName . '&format=raw');

			return $this;
		}

		$safeurlparams = array(
							'id' => 'INT',
							'catid' => 'INT',
							'limit' => 'INT',
							'limitstart' => 'INT',
							'filter_order' => 'CMD',
							'filter_order_Dir' => 'CMD',
							'lang' => 'CMD',
							'year' => 'INT',
							'month' => 'INT',
							'filter-search' => 'STRING',
							'return' => 'BASE64',
							'book' => 'INT',
							'Itemid' => 'INT'
						);

		switch ($viewName)
		{
			case 'speaker':
				$viewLayout = $this->input->get('layout', 'default');
				$view = $this->getView($viewName, 'html', '', array('base_path' => $this->basePath, 'layout' => $viewLayout));
				$series_model = $this->getModel('series');
				$view->setModel($series_model);
				$sermons_model = $this->getModel('sermons');
				$view->setModel($sermons_model);
				break;
			case 'serie':
				$viewLayout = $this->input->get('layout', 'default');
				$view = $this->getView($viewName, 'html', '', array('base_path' => $this->basePath, 'layout' => $viewLayout));
				$sermons_model = $this->getModel('sermons');
				$view->setModel($sermons_model);
				break;
			case 'seriessermon':
				$viewLayout = $this->input->get('layout', 'default');
				$view = $this->getView($viewName, 'html', '', array('base_path' => $this->basePath, 'layout' => $viewLayout));
				$series_model = $this->getModel('series');
				$view->setModel($series_model);
				$sermons_model = $this->getModel('sermons');
				$view->setModel($sermons_model);
				break;
		}

		return parent::display($cachable, $safeurlparams);
	}

	public function download()
	{
		$this->input = JFactory::getApplication()->input;
		$id          = $this->input->get('id', 0, 'int');

		if (!$id)
		{
			die("<html><body onload=\"alert('I have no clue what you want to download...');history.back();\"></body></html>");
		}

		$db = JFactory::getDBO();

		if ($this->input->get('type', 'audio', 'word') == 'video')
		{
			$query = "SELECT videofile FROM #__sermon_sermons WHERE id = " . $id;
		}
		else
		{
			$query = "SELECT audiofile FROM #__sermon_sermons WHERE id = " . $id;
		}

		$db->setQuery($query);
		$result = $db->loadResult() or die ("<html><body onload=\"alert('Encountered an error while accessing the database');
			history.back();\"></body></html>");
		$result = rtrim($result);

		// Redirect if link goes to an external source
		if (parse_url($result, PHP_URL_SCHEME))
		{
			$result = str_replace('http://player.vimeo.com/video/', 'http://vimeo.com/', $result);
			$this->setRedirect($result);

			return;
		}

		// Replace \ with /
		$result = str_replace('\\', '/', $result);

		// Add a leading slash to the sermonpath if not present
		if (substr($result, 0, 1) != '/')
		{
			$result = '/' . $result;
		}

		$file = JPATH_ROOT . $result;
		$mime = SermonspeakerHelperSermonspeaker::getMime(JFile::getExt($file));

		if (ini_get('zlib.output_compression'))
		{
			ini_set('zlib.output_compression', 'Off');
		}

		if (JFile::exists($file))
		{
			// If present overriding the memory_limit for php so big files can be downloaded
			if (ini_get('memory_limit'))
			{
				ini_set('memory_limit', '-1');
			}

			header('Pragma: public');
			header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Cache-Control: private', false);
			header('Content-Type: ' . $mime);
			header('Content-Disposition: attachment; filename="' . basename($file) . '"');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: ' . @filesize($file));
			set_time_limit(0);
			$fSize = @filesize($file);

			// How many bytes per chunk
			$chunksize = 3 * (1024 * 1024);

			if ($fSize > $chunksize)
			{
				$handle = fopen($file, 'rb');

				if (!$handle)
				{
					die("Can't open the file!");
				}

				while (!feof($handle))
				{
					$buffer = fread($handle, $chunksize);
					echo $buffer;
					ob_flush();
					flush();
				}

				fclose($handle);
			}
			else
			{
				@readfile($file) or die('Unable to read file!');
			}

			exit;
		}
		else
		{
			die("<html><body onload=\"alert('File not found!');history.back();\"></body></html>");
		}
	}
}
