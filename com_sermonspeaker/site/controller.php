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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\File;

/**
 * SermonSpeaker Component Controller
 * @since  1.0
 */
class SermonspeakerController extends BaseController
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
	 * @param   array $config An optional associative array of configuration settings.
	 *                        Recognized key values include 'name', 'default_task', 'model_path', and
	 *                        'view_path' (this list is not meant to be comprehensive).
	 *
	 * @since   4.0
	 */
	public function __construct($config = array())
	{
		$this->input = Factory::getApplication()->input;
		$view        = $this->input->get('view');

		// Frontpage Editor sermons proxying:
		if (($view === 'sermons' || $view === 'series' || $view === 'speakers') && $this->input->get('layout') === 'modal')
		{
			HTMLHelper::_('stylesheet', 'system/adminlist.css', array('version' => 'auto', 'relative' => true));
			$config['base_path'] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker';
		}

		parent::__construct($config);
	}

	/**
	 * View method.
	 *
	 * @param   boolean $cachable  If true, the view output will be cached
	 * @param   array   $urlparams An array of safe url parameters and their variable types, for valid values see
	 *                             {@link InputFilter::clean()}.
	 *
	 * @return  false|BaseController  A JControllerLegacy object to support chaining.
	 *
	 * @since   4.0
	 */
	public function display($cachable = false, $urlparams = array())
	{
		$cachable = Factory::getUser()->id ? false : true;
		$viewName = $this->input->get('view', $this->default_view);
		$id       = $this->input->getInt('id');
		$views    = array('frontendupload', 'serieform', 'speakerform');

		// Check for edit form.
		if (in_array($viewName, $views) && !$this->checkEditId('com_sermonspeaker.edit.' . $viewName, $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
			$this->setRedirect(Route::_('index.php?option=com_sermonspeaker&view=main', false));

			return false;
		}

		$app    = Factory::getApplication();
		$params = $app->getParams();

		if ($params->get('css_fontawesome'))
		{
			HTMLHelper::_('stylesheet', 'system/joomla-fontawesome.min.css', ['relative' => true]);
		}

		// Make sure the format is raw for feed and sitemap view
		if (($viewName == 'feed' || $viewName == 'sitemap') && $app->getDocument()->getType() != 'raw')
		{
			$uri = Uri::getInstance();
			$uri->setVar('format', 'raw');
			$url = $uri->toString();
			$app->redirect($url, 301);
		}

		$safeurlparams = array(
			'id'               => 'INT',
			'catid'            => 'INT',
			'limit'            => 'INT',
			'limitstart'       => 'INT',
			'filter_order'     => 'CMD',
			'filter_order_Dir' => 'CMD',
			'lang'             => 'CMD',
			'year'             => 'INT',
			'month'            => 'INT',
			'filter-search'    => 'STRING',
			'return'           => 'BASE64',
			'book'             => 'INT',
			'type'             => 'STRING',
			'Itemid'           => 'INT',
		);

		switch ($viewName)
		{
			case 'speaker':
				$viewLayout   = $this->input->get('layout', 'default');
				$view         = $this->getView($viewName, 'html', '', array('base_path' => $this->basePath, 'layout' => $viewLayout));
				$series_model = $this->getModel('series');
				$view->setModel($series_model);
				$sermons_model = $this->getModel('sermons');
				$view->setModel($sermons_model);
				break;
			case 'serie':
				$viewLayout    = $this->input->get('layout', 'default');
				$view          = $this->getView($viewName, 'html', '', array('base_path' => $this->basePath, 'layout' => $viewLayout));
				$sermons_model = $this->getModel('sermons');
				$view->setModel($sermons_model);
				break;
		}

		return parent::display($cachable, $safeurlparams);
	}

	public function download()
	{
		$this->input = Factory::getApplication()->input;
		$id          = $this->input->get('id', 0, 'int');

		if (!$id)
		{
			die("<html><body onload=\"alert('I have no clue what you want to download...');history.back();\"></body></html>");
		}

		$db       = Factory::getDbo();
		$nullDate = $db->quote($db->getNullDate());
		$nowDate  = $db->quote(Factory::getDate()->toSql());
		$query    = $db->getQuery(true);

		if ($this->input->get('type', 'audio', 'word') == 'video')
		{
			$query->select($db->quoteName('videofile'));
		}
		else
		{
			$query->select($db->quoteName('audiofile'));
		}

		$query->from('#__sermon_sermons');
		$query->where($db->quoteName('id') . ' = ' . $id);
		$query->where($db->quoteName('state') . ' = 1');
		$query->where('(publish_up = ' . $nullDate . ' OR publish_up <= ' . $nowDate . ')');
		$query->where('(publish_down = ' . $nullDate . ' OR publish_down >= ' . $nowDate . ')');

		$db->setQuery($query);
		$result = $db->loadResult() or die ("<html><body onload=\"alert('I haven\'t found a valid file');
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
		$mime = SermonspeakerHelperSermonspeaker::getMime(File::getExt($file));

		if (ini_get('zlib.output_compression'))
		{
			ini_set('zlib.output_compression', 'Off');
		}

		if (file_exists($file))
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
