<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\Controller;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Table\Table;
use Joomla\Component\Categories\Administrator\Model\CategoryModel;
use Joomla\Filesystem\File;
use Sermonspeaker\Component\Sermonspeaker\Site\Helper\Id3Helper;

defined('_JEXEC') or die;

/**
 * Tools Sermonspeaker Controller
 *
 * @since ?
 */
class ToolsController extends BaseController
{
	/**
	 * Reorder the sermons by date
	 *
	 * @since ?
	 */
	public function order()
	{
		// Check for request forgeries
		Session::checkToken('request') or jexit(Text::_('JINVALID_TOKEN'));
		$db    = Factory::getDbo();
		$query = "SET @c := 0";
		$db->setQuery($query);
		$db->execute();
		/** @noinspection SqlResolve */
		$query = "UPDATE #__sermon_sermons SET `ordering` = ( SELECT @c := @c + 1 ) ORDER BY `sermon_date` ASC, `id` ASC;";
		$db->setQuery($query);

		try
		{
			$db->execute();
			$this->setMessage('Successfully reordered the sermons');
		}
		catch (\Exception $e)
		{
			$this->setMessage('Error: ' . $e->getMessage(), 'error');
		}

		$this->setRedirect('index.php?option=com_sermonspeaker&view=sermons');
	}

	/**
	 * Reorder the series by title
	 *
	 * @since ?
	 */
	public function seriesorder()
	{
		// Check for request forgeries
		Session::checkToken('request') or jexit(Text::_('JINVALID_TOKEN'));
		$db    = Factory::getDbo();
		$query = "SET @c := 0";
		$db->setQuery($query);
		$db->execute();
		$query = "UPDATE #__sermon_series SET ordering = ( SELECT @c := @c + 1 ) ORDER BY title ASC, id ASC;";
		$db->setQuery($query);

		try
		{
			$db->execute();
			$this->setMessage('Successfully reordered the series');
		}
		catch (\Exception $e)
		{
			$this->setMessage('Error: ' . $e->getMessage(), 'error');
		}

		$this->setRedirect('index.php?option=com_sermonspeaker&view=series');
	}

	/**
	 * Reorder the speakers by title
	 *
	 * @since ?
	 */
	public function speakersorder()
	{
		// Check for request forgeries
		Session::checkToken('request') or jexit(Text::_('JINVALID_TOKEN'));
		$db    = Factory::getDbo();
		$query = "SET @c := 0";
		$db->setQuery($query);
		$db->execute();
		$query = "UPDATE #__sermon_speakers SET ordering = ( SELECT @c := @c + 1 ) ORDER BY title ASC, id ASC;";
		$db->setQuery($query);

		try
		{
			$db->execute();
			$this->setMessage('Successfully reordered the speakers');
		}
		catch (\Exception $e)
		{
			$this->setMessage('Error: ' . $e->getMessage(), 'error');
		}

		$this->setRedirect('index.php?option=com_sermonspeaker&view=speakers');
	}

	// Function to adjust the sermon time
	public function time()
	{
		// Check for request forgeries
		Session::checkToken('request') or jexit(Text::_('JINVALID_TOKEN'));
		$app    = Factory::getApplication();
		$jinput = $app->input;
		$db     = Factory::getDbo();
		$mode   = $jinput->get('submit');

		if (isset($mode['diff']))
		{
			$diff  = $jinput->get('diff', 0, 'float');
			$mins  = abs(($diff - intval($diff)) * 60);
			$hrs   = abs(intval($diff));
			$minus = ($diff < 0) ? '-' : '';
			$query = "UPDATE #__sermon_sermons \n"
				. "SET sermon_date = DATE_ADD(sermon_date, INTERVAL '" . $minus . $hrs . ":" . $mins . "' HOUR_MINUTE) \n"
				. "WHERE sermon_date != '0000-00-00 00:00:00' \n"
				. "AND state = 1";
			$db->setQuery($query);

			try
			{
				$db->execute();

				if ($minus)
				{
					$app->enqueueMessage('Successfully substracted ' . $hrs . ' hours and ' . $mins . ' minutes from the sermon date!');
				}
				else
				{
					$app->enqueueMessage('Successfully added ' . $hrs . ' hours and ' . $mins . ' minutes to the sermon date!');
				}
			}
			catch (\Exception $e)
			{
				$this->setMessage($e->getMessage(), 'error');
			}

		}
		elseif (isset($mode['time']))
		{
			$time   = $jinput->get('time', '', 'string');
			$config = Factory::getConfig();
			$user   = Factory::getApplication()->getIdentity();
			$date   = Factory::getDate($time, $user->getParam('timezone', $config->get('offset')));
			$date->setTimezone(new \DateTimeZone('UTC'));
			$t_utc = $date->format('H:i:s', true);
			$query = "UPDATE #__sermon_sermons \n"
				. "SET sermon_date = CONCAT(DATE(sermon_date), ' " . $t_utc . "') \n"
				. "WHERE sermon_date != '0000-00-00 00:00:00' \n"
				. "AND state = 1";
			$db->setQuery($query);

			try
			{
				$db->execute();
				$app->enqueueMessage('Successfully set time to ' . $time . ' for each sermon date!');
			}
			catch (\Exception $e)
			{
				$this->setMessage($e->getMessage(), 'error');
			}
		}

		$app->redirect('index.php?option=com_sermonspeaker&view=tools');
	}

	/**
	 * @throws \Exception
	 * @since ?
	 */
	public function createAutomatic()
	{
		$app    = Factory::getApplication();
		$jinput = $app->input;

		// Get the log in credentials.
		$credentials             = array();
		$credentials['username'] = $jinput->get->get('username', '', 'username');
		$credentials['password'] = $jinput->get->get('password', '', 'RAW');

		// Perform the log in.
		if ($credentials['username'] && $credentials['password'])
		{
			$app->login($credentials);
		}

		$user = Factory::getApplication()->getIdentity();

		if (!$user->authorise('core.create', 'com_sermonspeaker') || !$user->authorise('com_sermonspeaker.script', 'com_sermonspeaker'))
		{
			if ($credentials['username'] && $credentials['password'])
			{
				$app->logout($user->id);
			}
			$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');

			return false;
		}

		$file_model = $this->getModel('Files');
		$files      = $file_model->getItems();
		$catid      = $file_model->getCategory();
		$catTable   = Table::getInstance('Category');
		$state      = $user->authorise('core.edit.state', 'com_sermonspeaker') ? 1 : 0;

		$params = ComponentHelper::getParams('com_sermonspeaker');
		require_once JPATH_SITE . '/components/com_sermonspeaker/helpers/id3.php';

		$i       = 0;
		$missing = array();

		foreach ($files as $file)
		{
			$id3          = Id3Helper::getID3($file['file'], $params);
			$sermon_model = $this->getModel('Sermon', 'Administrator');
			$sermon       = $sermon_model->getItem();

			foreach ($id3 as $key => $value)
			{
				$sermon->$key = $value;
			}

			if ($file['type'] == 'audio')
			{
				$sermon->audiofile = $file['file'];
			}
			elseif ($file['type'] == 'video')
			{
				$sermon->videofile = $file['file'];
			}
			else
			{
				continue;
			}

			$sermon->state   = $state;
			$sermon->podcast = $state;

			// Check if folder is corresponding with a category
			$dirs = explode('/', str_replace('\\', '/', $file['file']));
			array_pop($dirs);
			$dir = array_pop($dirs);
			$catTable->load(array('alias' => $dir, 'extension' => 'com_sermonspeaker', 'published' => 1));
			$sermon->catid = $catTable->id ?: $catid;

			if (!$sermon->sermon_date)
			{
				$file_timestamp      = filemtime(JPATH_SITE . $file['file']);
				$sermon->sermon_date = date('Y-m-d H:i:s', $file_timestamp);
			}

			if (!$sermon_model->save($sermon->getProperties()))
			{
				$app->enqueueMessage(Text::sprintf('COM_SERMONSPEAKER_TOOLS_AUTOMATIC_FAILED', $file['file'], $sermon_model->getError()), 'error');
			}
			else
			{
				$i++;
			}

			if (isset($id3['not_found']))
			{
				foreach ($id3['not_found'] as $key => $value)
				{
					$missing[$key][] = $value;
				}
			}
		}

		$app->enqueueMessage(Text::sprintf('COM_SERMONSPEAKER_TOOLS_AUTOMATIC_CREATED', $i));

		if ($missing)
		{
			$message = '<div class="row-fluid">'
				. '<div class="span12">' . Text::_('COM_SERMONSPEAKER_ID3_NO_MATCH_FOUND') . '</div>';
			$span    = 'span' . 12 / count($missing);

			foreach ($missing as $key => $values)
			{
				$arrayCount = array_count_values($values);
				$message    .= '<div class="' . $span . '">'
					. '<h5>' . Text::_('COM_SERMONSPEAKER_' . strtoupper($key)) . '</h5>'
					. '<ul>';

				foreach ($arrayCount as $countKey => $countValue)
				{
					$message .= '<li>' . $countKey . ' <span class="badge bg-info">' . $countValue . '</span></li>';
				}

				$message .= '</ul></div>';
			}

			$message .= '</div>';

			$app->enqueueMessage($message, 'notice');
		}

		$this->setRedirect('index.php?option=com_sermonspeaker&view=tools');

		return true;
	}

	public function write_id3()
	{
		// Load Composer Autoloader
		require_once(JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/vendor/autoload.php');

		// Check for request forgeries
		Session::checkToken('request') or jexit(Text::_('JINVALID_TOKEN'));
		$app   = Factory::getApplication();
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('audiofile, videofile, sermons.created_by, sermons.catid, sermons.title, speakers.title as speaker_title');
		$query->select('series.title AS series_title, notes, sermon_number, picture');
		$query->select('YEAR(sermon_date) AS year, DATE_FORMAT(sermon_date, "%H%i") AS time, DATE_FORMAT(sermon_date, "%d%m") AS date');
		$query->from('#__sermon_sermons AS sermons');
		$query->join('LEFT', '#__sermon_speakers AS speakers ON speaker_id = speakers.id');
		$query->join('LEFT', '#__sermon_series AS series ON series_id = series.id');
		$db->setQuery($query);
		$items = $db->loadObjectList();
		$user  = Factory::getApplication()->getIdentity();

		$getID3 = new \getID3;
		$getID3->setOption(array('encoding' => 'UTF-8'));

		$writer                 = new \getid3_writetags;
		$writer->tagformats     = array('id3v2.3');
		$writer->overwrite_tags = true;
		$writer->tag_encoding   = 'UTF-8';

		foreach ($items as $item)
		{
			$canEdit    = $user->authorise('core.edit', 'com_sermonspeaker.category.' . $item->catid);
			$canEditOwn = $user->authorise('core.edit.own', 'com_sermonspeaker.category.' . $item->catid) && $item->created_by == $user->id;
			if ($canEdit || $canEditOwn)
			{
				$files              = array();
				$files[]            = $item->audiofile;
				$files[]            = $item->videofile;
				$TagData            = array(
					'title'  => array($item->title),
					'artist' => array($item->speaker_title),
					'album'  => array($item->series_title),
					'track'  => array($item->sermon_number),
					'year'   => array($item->year),
					'date'   => array($item->date),
					'time'   => array($item->time),
				);
				$TagData['comment'] = array(strip_tags(HTMLHelper::_('content.prepare', $item->notes)));

				// Adding the picture to the id3 tags, taken from getID3 Demos -> demo.write.php
				if ($item->picture && !parse_url($item->picture, PHP_URL_SCHEME))
				{
					ob_start();
					$pic = $item->picture;

					if (str_starts_with($pic, '/'))
					{
						$pic = substr($pic, 1);
					}

					$pic = JPATH_ROOT . '/' . $pic;

					ob_end_clean();

					if ($fd = fopen($pic, 'rb'))
					{
						$APICdata = fread($fd, filesize($pic));
						fclose($fd);
						$image = getimagesize($pic);
						if (($image[2] > 0) && ($image[2] < 4))
						{ // 1 = gif, 2 = jpg, 3 = png
							$TagData['attached_picture'][0]['data']          = $APICdata;
							$TagData['attached_picture'][0]['picturetypeid'] = 0;
							$TagData['attached_picture'][0]['description']   = basename($pic);
							$TagData['attached_picture'][0]['mime']          = $image['mime'];
						}
					}
					else
					{
						$app->enqueueMessage("Couldn't open the picture: $pic", 'notice');
					}
				}
				$writer->tag_data = $TagData;
				foreach ($files as $file)
				{
					if (!$file)
					{
						continue;
					}
					$path = JPATH_SITE . $file;
					$path = str_replace('//', '/', $path);
					if (!is_writable($path))
					{
						continue;
					}
					$writer->filename = $path;
					if ($writer->WriteTags())
					{
						$app->enqueueMessage('Successfully wrote tags to "' . $file . '"');
						if (!empty($writer->warnings))
						{
							$app->enqueueMessage('There were some warnings: ' . implode(', ', $writer->errors), 'notice');
							$writer->warnings = array();
						}
					}
					else
					{
						$app->enqueueMessage('Failed to write tags to "' . $file . '"! ' . implode(', ', $writer->errors), 'error');
						$writer->errors = array();
					}
				}
			}
			else
			{
				$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR') . ' - ' . $item->title, 'error');
			}
		}
		$app->redirect('index.php?option=com_sermonspeaker&view=tools');
	}

	public function delete()
	{
		// Check for request forgeries
		Session::checkToken('request') or jexit(Text::_('JINVALID_TOKEN'));
		$app  = Factory::getApplication();
		$file = $app->input->get('file', '', 'string');
		$file = JPATH_SITE . $file;

		if (file_exists($file))
		{
			File::delete($file);
			$app->enqueueMessage($file . ' deleted!');
		}
		else
		{
			$app->enqueueMessage($file . ' not found!', 'error');
		}

		$app->redirect('index.php?option=com_sermonspeaker&view=tools');
	}


	/**
	 * Imports data from Preach It 4.1
	 *
	 * @throws \Exception
	 *
	 * @since ?
	 */
	public function piimport()
	{
		// Check for request forgeries
		Session::checkToken('request') or jexit(Text::_('JINVALID_TOKEN'));
		$app = Factory::getApplication();
		$db  = Factory::getDbo();

		// Get Ministries and create Categories for it
		$query = $db->getQuery(true);
		$query->select('id, name, alias, description, published, access, language, metakey, metadesc');
		$query->from('`#__piministry`');
		$db->setQuery($query);
		$ministries = $db->loadObjectList();

		// Create categories for our component
		$catConversion = array();

		$catData = array(
			'id'          => 0,
			'parent_id'   => 0,
			'level'       => 1,
			'path'        => 'preachitmigration',
			'extension'   => 'com_sermonspeaker.sermons',
			'title'       => 'Preach It Migration',
			'alias'       => 'preachitmigration',
			'description' => 'Items migrated from Preach It',
			'published'   => 1,
			'language'    => '*',
		);

		/** @var \Joomla\Component\Categories\Administrator\Model\CategoryModel $catmodel */
		$catmodel = Factory::getApplication()->bootComponent('com_categories')
			->getMVCFactory()->createModel('Category', 'Administrator', ['ignore_request' => true]);

		// Get the Category ID.
		$catmodel->save($catData);
		$catConversion[0] = $catmodel->getItem()->id;

		foreach ($ministries as $ministry)
		{
			$catData = array(
				'id'          => 0,
				'parent_id'   => 0,
				'level'       => 1,
				'path'        => $ministry->alias,
				'extension'   => 'com_sermonspeaker.sermons',
				'title'       => $ministry->name,
				'alias'       => $ministry->alias,
				'description' => $ministry->description,
				'published'   => $ministry->published,
				'language'    => $ministry->language,
				'metakey'     => $ministry->metakey,
				'metadesc'    => $ministry->metadesc,
				'access'      => $ministry->access,
			);
			$catmodel->save($catData);
			$catConversion[$ministry->id] = $catmodel->getItem()->id;
		}

		$app->enqueueMessage(count($catConversion) . ' categories created!');

		// Check version of table structure (changed somewhere with PI 4)
		$v4 = array_key_exists('date', $db->getTableColumns('#__pistudies'));

		// Get Studies
		$query = $db->getQuery(true);
		$query->from('`#__pistudies` AS a');

		if ($v4)
		{
			$query->select('a.date as study_date, a.name as study_name, a.alias as study_alias, CONCAT(a.description, a.study_text) as study_description, a.ministry');
		}
		else
		{
			$query->select('a.study_date, a.study_name, a.study_alias, a.study_description');
		}

		$query->select('a.study_book, a.ref_ch_beg, a.ref_ch_end, a.ref_vs_beg, a.ref_vs_end');
		$query->select('a.study_book2, a.ref_ch_beg2, a.ref_ch_end2, a.ref_vs_beg2, a.ref_vs_end2');
		$query->select('CONCAT_WS(":", a.dur_hrs, a.dur_mins, a.dur_secs) AS duration');
		$query->select('a.published, a.hits, a.user');

		// Join over the series.
		if ($v4)
		{
			$query->select('b.name as series_name');
		}
		else
		{
			$query->select('b.series_name');
		}

		$query->join('LEFT', '#__piseries AS b ON b.id = a.series');

		// Join over the teachers. This fails on newer PI versions because it stores the teachers as json_encoded array
		$query->select('a.teacher');

		if ($v4)
		{
			$query->select('c.name as teacher_name');
		}
		else
		{
			$query->select('c.teacher_name');
		}

		$query->join('LEFT', '#__piteachers AS c ON c.id = a.teacher');

		// Join over the audio path.
		$query->select("IF (d.server != '', CONCAT('http://', CONCAT_WS('/', d.server, d.folder, a.audio_link)), "
			. "IF (LEFT(d.folder, 7) = 'http://', CONCAT(d.folder, '/', a.audio_link), "
			. "IF (d.folder != '', CONCAT(d.folder, '/', a.audio_link), a.audio_link))) AS audiofile");
		$query->join('LEFT', '#__pifilepath AS d ON d.id = a.audio_folder');

		// Join over the video path.
		$query->select("IF (e.server != '', CONCAT('http://', CONCAT_WS('/', e.server, e.folder, a.video_link)), "
			. "IF (LEFT(e.folder, 7) = 'http://', CONCAT(e.folder, '/', a.video_link), "
			. "IF (e.folder != '', CONCAT(e.folder, '/', a.video_link), a.video_link))) AS videofile");
		$query->join('LEFT', '#__pifilepath AS e ON e.id = a.video_folder');

		// Join over the study pic path.
		$query->select("IF (f.server != '', CONCAT('http://', CONCAT_WS('/', f.server, f.folder, a.imagelrg)), "
			. "IF (LEFT(f.folder, 7) = 'http://', CONCAT(f.folder, '/', a.imagelrg), "
			. "IF (f.folder != '', CONCAT(f.folder, '/', a.imagelrg), a.imagelrg))) AS study_pic");
		$query->join('LEFT', '#__pifilepath AS f ON f.id = a.image_folderlrg');

		// Join over the study notes path.
		$query->select("IF (g.server != '', CONCAT('http://', CONCAT_WS('/', g.server, g.folder, a.notes_link)), "
			. "IF (LEFT(g.folder, 7) = 'http://', CONCAT(g.folder, '/', a.notes_link), "
			. "IF (g.folder != '', CONCAT(g.folder, '/', a.notes_link), a.notes_link))) AS addfile");
		$query->join('LEFT', '#__pifilepath AS g ON g.id = a.notes_folder');
		$db->setQuery($query);

		try
		{
			$studies = $db->loadObjectList();
		}
		catch (\Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
			$studies = array();
		}

		// Get the speakers if the teacher is stored as json string.
		if ($studies[0]->teacher[0] == '{')
		{
			$query = $db->getQuery(true);

			if ($v4)
			{
				$query->select("id, CONCAT(name, ' ', lastname) AS name");
			}
			else
			{
				$query->select("id, CONCAT(teacher_name, ' ', lastname) AS name");
			}

			$query->from('#__piteachers');
			$db->setQuery($query);

			$piteachers = $db->loadObjectList('id');

			foreach ($studies as $study)
			{
				$teacher             = json_decode($study->teacher, true);
				$study->teacher_name = $piteachers[$teacher[0]]->name;
			}
		}

		// Store the Series
		$query = "INSERT INTO #__sermon_series \n"
			. "(title, alias, series_description, state, ordering, created_by, created, catid, avatar) \n";

		if ($v4)
		{
			$query .= "SELECT a.name, a.alias, a.description, ";
		}
		else
		{
			$query .= "SELECT a.series_name, a.series_alias, a.series_description, ";
		}


		$query .= "a.published, a.ordering, a.user, NOW(), '" . $catConversion[0] . "', \n"
			. "IF (b.server != '', CONCAT('http://', CONCAT_WS('/', b.server, b.folder, a.series_image_lrg)), "
			. "IF (LEFT(b.folder, 7) = 'http://', CONCAT(b.folder, '/', a.series_image_lrg), CONCAT('/', b.folder, '/', a.series_image_lrg))) \n"
			. "FROM #__piseries AS a \n"
			. "LEFT JOIN #__pifilepath AS b ON b.id = a.image_folderlrg \n";

		$db->setQuery($query);

		try
		{
			$db->execute();
			$app->enqueueMessage($db->getAffectedRows() . ' series migrated!');
		}
		catch (\Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');

		}

		// Store the Speakers
		/** @noinspection SqlResolve */
		$query = "INSERT INTO #__sermon_speakers \n"
			. "(title, alias, website, intro, state, ordering, created_by, created, catid, pic) \n";

		if ($v4)
		{
			$query .= "SELECT CONCAT(a.name, ' ', a.lastname), a.alias, a.website, a.description, ";
		}
		else
		{
			$query .= "SELECT a.series_name, a.series_alias, a.series_description, ";
		}

		$query .= "a.published, a.ordering, a.user, NOW(), '" . $catConversion[0] . "', \n"
			. "IF (b.server != '', CONCAT('http://', CONCAT_WS('/', b.server, b.folder, a.teacher_image_lrg)), "
			. "IF (LEFT(b.folder, 7) = 'http://', CONCAT(b.folder, '/', a.teacher_image_lrg), CONCAT('/', b.folder, '/', a.teacher_image_lrg))) \n"
			. "FROM #__piteachers AS a \n"
			. "LEFT JOIN #__pifilepath AS b ON b.id = a.image_folderlrg \n";

		$db->setQuery($query);

		try
		{
			$db->execute();
			$app->enqueueMessage($db->getAffectedRows() . ' speakers migrated!');
		}
		catch (\Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		// Prepare and Store the Sermons for SermonSpeaker
		$count = 0;

		foreach ($studies as $study)
		{
			// Prepare Scripture
			$scripture = array();

			if ($study->study_book)
			{
				$bible['book']     = (int) $study->study_book;
				$bible['cap1']     = (int) $study->ref_ch_beg;
				$bible['vers1']    = (int) $study->ref_vs_beg;
				$bible['cap2']     = (int) $study->ref_ch_end;
				$bible['vers2']    = (int) $study->ref_vs_end;
				$bible['ordering'] = 1;
				$scripture[]       = $bible;
			}

			if ($study->study_book2)
			{
				$bible['book']     = (int) $study->study_book2;
				$bible['cap1']     = (int) $study->ref_ch_beg2;
				$bible['vers1']    = (int) $study->ref_vs_beg2;
				$bible['cap2']     = (int) $study->ref_ch_end2;
				$bible['vers2']    = (int) $study->ref_vs_end2;
				$bible['ordering'] = 2;
				$scripture[]       = $bible;
			}

			if ($study->ministry)
			{
				$ministry     = json_decode($study->ministry, true);
				$study->catid = $catConversion[$ministry[0]];
			}

			/** @noinspection SqlResolve */
			$query = "INSERT INTO #__sermon_sermons \n"
				. "(`audiofile`, `videofile`, `picture`, `title`, `alias`, `sermon_date`, `sermon_time`, `notes`, `state`, `hits`, `created_by`, `addfile`, `podcast`, `created`, `catid`) \n"
				. 'VALUES (' . $db->quote($study->audiofile) . ',' . $db->quote($study->videofile) . ',' . $db->quote($study->study_pic) . ',' . $db->quote($study->study_name) . ',' . $db->quote($study->study_alias) . ',' . $db->quote($study->study_date) . ',' . $db->quote($study->duration) . ',' . $db->quote($study->study_description) . ',' . $db->quote($study->published) . ',' . $db->quote($study->hits) . ',' . $db->quote($study->user) . ',' . $db->quote($study->addfile) . ', 1, NOW(), ' . $db->quote($study->catid) . ')';
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (\Exception $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
				break;
			}

			$id = $db->insertid();

			foreach ($scripture as $passage)
			{
				// Insert Scriptures
				/** @noinspection SqlResolve */
				$query = "INSERT INTO #__sermon_scriptures \n"
					. "(`book`, `cap1`, `vers1`, `cap2`, `vers2`, `text`, `ordering`, `sermon_id`) \n"
					. "VALUES ('" . $passage['book'] . "','" . $passage['cap1'] . "','" . $passage['vers1'] . "','" . $passage['cap2'] . "','" . $passage['vers2'] . "','','" . $passage['ordering'] . "','" . $id . "')";

				$db->setQuery($query);

				try
				{
					$db->execute();
				}
				catch (\Exception $e)
				{
					$app->enqueueMessage($e->getMessage(), 'error');
					break;
				}
			}

			// Update Speaker
			if ($study->teacher_name)
			{
				$query = "UPDATE #__sermon_sermons \n"
					. "SET `speaker_id` = (SELECT `id` FROM #__sermon_speakers WHERE `title` = " . $db->quote($study->teacher_name) . " LIMIT 1) \n"
					. "WHERE `id` = " . $db->quote($id);

				$db->setQuery($query);

				try
				{
					$db->execute();
				}
				catch (\Exception $e)
				{
					$app->enqueueMessage($e->getMessage(), 'error');
				}
			}

			// Update Series
			if ($study->series_name)
			{
				$query = "UPDATE #__sermon_sermons \n"
					. "SET `series_id` = (SELECT `id` FROM #__sermon_series WHERE `title` = " . $db->quote($study->series_name) . " LIMIT 1) \n"
					. "WHERE `id` = " . $db->quote($id);

				$db->setQuery($query);
				try
				{
					$db->execute();
				}
				catch (\Exception $e)
				{
					$app->enqueueMessage($e->getMessage(), 'error');
				}
			}

			$count++;
		}

		$app->enqueueMessage($count . ' sermons migrated!');

		$app->redirect('index.php?option=com_sermonspeaker&view=tools');
	}

	/**
	 * Imports data from Proclaim / ex Bible Study
	 *
	 * @throws \Exception
	 *
	 * @since ?
	 */
	public function bsimport()
	{
		// Check for request forgeries
		Session::checkToken('request') or jexit(Text::_('JINVALID_TOKEN'));
		$app  = Factory::getApplication();
		$user = Factory::getApplication()->getIdentity();
		$db   = Factory::getDbo();

		$params         = ComponentHelper::getParams('com_sermonspeaker');
		$audioFiletypes = $params->get('audio_filetypes');
		$audioFiletypes = array_map('trim', explode(',', $audioFiletypes));
		$videoFiletypes = $params->get('video_filetypes');
		$videoFiletypes = array_map('trim', explode(',', $videoFiletypes));

		// Get Studies
		$query = $db->getQuery(true);
		$query->from('`#__bsms_studies` AS a');
		$query->select('a.id, a.studydate, a.studytitle, a.alias, a.studytext, a.studyintro');
		$query->select('a.booknumber, a.chapter_begin, a.chapter_end, a.verse_begin, a.verse_end');
		$query->select('a.booknumber2, a.chapter_begin2, a.chapter_end2, a.verse_begin2, a.verse_end2');
		$query->select('a.published, a.hits, a.user_id');

		// Join over the series.
		$query->select('a.series_id, b.series_text');
		$query->join('LEFT', '#__bsms_series AS b ON b.id = a.series_id');

		// Join over the teachers.
		$query->select('a.teacher_id, c.teachername');
		$query->join('LEFT', '#__bsms_teachers AS c ON c.id = a.teacher_id');

		// Join over the media path.
		$query->select("e.type AS server_type, e.params AS server_params, e.media AS server_media, d.params AS media_params");
		$query->join('LEFT', '#__bsms_mediafiles AS d ON d.study_id = a.id');
		$query->join('LEFT', '#__bsms_servers AS e ON e.id = d.server_id');

		$db->setQuery($query);

		$studies = $db->loadObjectList();

		// Store the Series
		/** @noinspection SqlResolve */
		$query = "INSERT INTO #__sermon_series \n"
			. "(title, alias, series_description, state, ordering, created_by, created, avatar) \n"
			. "SELECT a.series_text, a.alias, a.description, a.published, a.ordering, \n"
			. '"' . $user->id . '"' . ", NOW(), a.series_thumbnail \n"
			. "FROM #__bsms_series AS a";

		$db->setQuery($query);
		$db->execute();

		$app->enqueueMessage($db->getAffectedRows() . ' series migrated!');

		// Store the Speakers
		/** @noinspection SqlResolve */
		$query = "INSERT INTO #__sermon_speakers \n"
			. "(title, alias, website, intro, bio, state, ordering, created_by, created, pic) \n"
			. "SELECT a.teachername, a.alias, a.website, a.short, a.information, a.published, a.ordering, \n"
			. '"' . $user->id . '"' . ", NOW(), a.image \n"
			. "FROM #__bsms_teachers AS a \n";

		$db->setQuery($query);
		$db->execute();

		$app->enqueueMessage($db->getAffectedRows() . ' speakers migrated!');

		// Prepare and store the Sermons for SermonSpeaker
		$count   = 0;
		$studyId = 0;

		foreach ($studies as $study)
		{
			// Prepare Mediafile
			$server_params = json_decode($study->server_params);
			$server_media  = json_decode($study->server_media);
			$media_params  = json_decode($study->media_params);

			switch ($study->server_type)
			{
				case 'youtube':
					$type  = 'video';
					$file  = $media_params->filename;
					$image = $media_params->media_image;
					break;
				case 'local':
				default:
					if ($media_params)
					{
						$file  = $media_params->filename;
						$image = $media_params->media_image;
						$ext   = pathinfo($file, PATHINFO_EXTENSION);
					}
					else
					{
						$file  = '';
						$image = '';
						$ext   = '';
					}

					if (in_array($ext, $audioFiletypes))
					{
						$type = 'audio';
					}
					elseif (in_array($ext, $videoFiletypes))
					{
						$type = 'video';
					}
					else
					{
						$host = parse_url($file, PHP_URL_HOST);

						if ($host == 'youtube.com' || $host == 'www.youtube.com' || $host == 'youtu.be')
						{
							$type = 'video';
						}
						else
						{
							$type = 'add';
						}
					}
			}

			// Check if it's the same study as in the previous iteration and add the mediafile instead of creating new sermon.
			if ($studyId === $study->id)
			{
				$query = "UPDATE #__sermon_sermons \n"
					. "SET `" . $type . "file` = " . $db->quote($file) . " \n"
					. "WHERE `id` = " . $db->quote($id);

				$db->setQuery($query);
				$db->execute();

				continue;
			}

			$studyId = $study->id;

			// Prepare Scripture
			$scripture = array();

			if ($study->booknumber && ($study->booknumber != -1))
			{
				$bible['book']     = (int) $study->booknumber - 100;
				$bible['cap1']     = (int) $study->chapter_begin;
				$bible['vers1']    = (int) $study->verse_begin;
				$bible['cap2']     = (int) $study->chapter_end;
				$bible['vers2']    = (int) $study->verse_end;
				$bible['ordering'] = 1;
				$scripture[]       = $bible;
			}

			if ($study->booknumber2 && ($study->booknumber2 != -1))
			{
				$bible['book']     = (int) $study->booknumber2 - 100;
				$bible['cap1']     = (int) $study->chapter_begin2;
				$bible['vers1']    = (int) $study->verse_begin2;
				$bible['cap2']     = (int) $study->chapter_end2;
				$bible['vers2']    = (int) $study->verse_end2;
				$bible['ordering'] = 2;
				$scripture[]       = $bible;
			}

			// Use studytext if available, otherwise studyintro.
			$notes = ($study->studytext) ?: $study->studyintro;

			// Make sure user_id for created_by isn't NULL
			$study->user_id = $study->user_id ?? '0';

			/** @noinspection SqlResolve */
			$query = "INSERT INTO #__sermon_sermons \n"
				. "(`" . $type . "file`, `picture`, `title`, `alias`, `sermon_date`, `notes`, `state`, `hits`, `created_by`, `podcast`, `created`) \n"
				. 'VALUES (' . $db->quote($file) . ',' . $db->quote($image) . ',' . $db->quote($study->studytitle) . ',' . $db->quote($study->alias) . ','
				. $db->quote($study->studydate) . ',' . $db->quote($notes) . ',' . $db->quote($study->published) . ','
				. $db->quote($study->hits) . ',' . $db->quote($study->user_id) . ', 1, NOW())';

			$db->setQuery($query);
			$db->execute();

			$id = $db->insertid();

			foreach ($scripture as $passage)
			{
				// Insert Scriptures
				/** @noinspection SqlResolve */
				$query = "INSERT INTO #__sermon_scriptures \n"
					. "(`book`, `cap1`, `vers1`, `cap2`, `vers2`, `text`, `ordering`, `sermon_id`) \n"
					. "VALUES ('" . $passage['book'] . "','" . $passage['cap1'] . "','" . $passage['vers1'] . "','"
					. $passage['cap2'] . "','" . $passage['vers2'] . "','','" . $passage['ordering'] . "','" . $id . "')";

				$db->setQuery($query);
				$db->execute();
			}

			// Update Speaker
			if ($study->teachername)
			{
				$query = "UPDATE #__sermon_sermons \n"
					. "SET `speaker_id` = (SELECT `id` FROM #__sermon_speakers WHERE `title` = " . $db->quote($study->teachername) . " LIMIT 1) \n"
					. "WHERE `id` = " . $db->quote($id);

				$db->setQuery($query);
				$db->execute();
			}

			// Update Series
			if ($study->series_text)
			{
				$query = "UPDATE #__sermon_sermons \n"
					. "SET `series_id` = (SELECT `id` FROM #__sermon_series WHERE `title` = " . $db->quote($study->series_text) . " LIMIT 1) \n"
					. "WHERE `id` = " . $db->quote($id);

				$db->setQuery($query);
				$db->execute();
			}

			$count++;
		}

		$app->enqueueMessage($count . ' sermons migrated!');

		$app->redirect('index.php?option=com_sermonspeaker&view=tools');
	}
}
