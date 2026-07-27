<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Site\Controller;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Session\Session;
use Joomla\Filesystem\File;
use Sermonspeaker\Component\Sermonspeaker\Site\Helper\Id3Helper;

defined('_JEXEC') or die();

/**
 * Controller class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class FileController extends BaseController
{
	/**
	 * Upload a file
	 *
	 * @return  void  Echoes an AJAX response
	 *
	 * @throws \Exception
	 * @since ?
	 */
	public function upload(): void
	{
		// Check for request forgeries
		if (!Session::checkToken('request'))
		{
			$response = array(
				'status' => '0',
				'error'  => Text::_('JINVALID_TOKEN'),
			);
			echo json_encode($response);

			return;
		}

		// Authorize User
		$user = Factory::getApplication()->getIdentity();

		if (!$user->authorise('core.create', 'com_sermonspeaker'))
		{
			$response = array(
				'status' => '0',
				'error'  => Text::_('JGLOBAL_AUTH_ACCESS_DENIED'),
			);
			echo json_encode($response);

			return;
		}

		// Initialise variables.
		$app    = Factory::getApplication();
		$params = $app->getParams();
		$jinput = $app->input;

		// Get some data from the request
		$file = $jinput->files->get('file');
		$type = $jinput->get('type', 'audio', 'word');
		$type = (in_array($type, array('audio', 'video', 'addfile'))) ? $type : 'audio';

		if (!$file['name'])
		{
			$response = array(
				'status' => '0',
				'error'  => Text::_('COM_SERMONSPEAKER_FU_FAILED'),
			);
			echo json_encode($response);

			return;
		}

		// Get file extension
		$ext = File::getExt($file['name']);

		// Optionally sanitising filenames
		if ($params->get('sanitise_filename', 1))
		{
			// Make filename URL safe. Eg replaces ä with ae.
			$file['name'] = OutputFilter::stringURLSafe(File::stripExt($file['name'])) . '.' . $ext;

			// Make the filename safe
			$file['name'] = File::makeSafe($file['name']);

			// Replace spaces in filename as long as makeSafe doesn't do this.
			$file['name'] = str_replace(' ', '_', $file['name']);

			// Check if filename has more chars than only dashes, making a new filename based on current date/time if not
			if (count_chars(File::stripExt($file['name']), 3) == '-')
			{
				$file['name'] = Factory::getDate()->format("Y-m-d-H-i-s") . '.' . $ext;
			}
		}

		// Check for file extension
		$types = strtolower($params->get($type . '_filetypes'));
		$types = array_map('trim', explode(',', $types));

		if (!in_array(strtolower($ext), $types))
		{
			$response = array(
				'status' => '0',
				'error'  => Text::sprintf('COM_SERMONSPEAKER_FILETYPE_NOT_ALLOWED', $ext),
			);
			echo json_encode($response);

			return;
		}
	}

	/**
	 * Lookup the ID3 tags
	 *
	 * @return  void  Echoes an AJAX response
	 *
	 * @throws \Exception
	 * @since ?
	 */
	public function lookup(): void
	{
		$file = Factory::getApplication()->input->get('file', '', 'string');

		if (!$file)
		{
			$response = array(
				'status' => '0',
				'msg'    => Text::_('COM_SERMONSPEAKER_ERROR_ID3'),
			);
			echo json_encode($response);

			return;
		}

		$params = ComponentHelper::getParams('com_sermonspeaker');
		$id3    = Id3Helper::getID3($file, $params);

		// Format the date to the language specific format
		if ($id3['sermon_date'])
		{
			$id3['sermon_date'] = HTMLHelper::date($id3['sermon_date'], Text::_('DATE_FORMAT_FILTER_DATETIME'));
		}

		if ($id3)
		{
			$response           = $id3;
			$response['status'] = 1;
		}
		else
		{
			$response = array(
				'status' => '0',
				'msg'    => Text::_('COM_SERMONSPEAKER_ERROR_ID3'),
			);
		}

		echo json_encode($response);
	}
}
