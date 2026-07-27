<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\Controller;

use Joomla\CMS\Client\ClientHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Filesystem\Path;
use Joomla\Filesystem\File;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Session\Session;
use Sermonspeaker\Component\Sermonspeaker\Site\Helper\Id3Helper;

defined('_JEXEC') or die;

/**
 * File Sermonspeaker Controller
 *
 * @since  3.4
 */
class FileController extends BaseController
{
	/**
	 * ID3 Lookup
	 *
	 * @since ?
	 */
	public function lookup()
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
