<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\File;use Joomla\Utilities\ArrayHelper;

/**
 * Controller class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerControllerFrontendupload extends FormController
{
	protected $view_item = 'frontendupload';

	protected $view_list = 'sermons';

	/**
	 * Method to add a new record
	 *
	 * @return  boolean  True if the article can be added, false if not
	 *
	 * @since ?
	 */
	public function add()
	{
		$return = parent::add();

		if (!$return)
		{
			// Redirect to the return page.
			$this->setRedirect($this->getReturnPage());
		}

		return $return;
	}

	/**
	 * Method override to check if you can add a new record
	 *
	 * @param   array $data An array of input data
	 *
	 * @return  boolean
	 *
	 * @since ?
	 */
	protected function allowAdd($data = array())
	{
		$user       = Factory::getApplication()->getIdentity();
		$categoryId = ArrayHelper::getValue($data, 'catid', Factory::getApplication()->input->get('filter_category_id'), 'int');
		$allow      = null;

		if ($categoryId)
		{
			// If the category has been passed in the data or URL check it
			$allow = $user->authorise('core.create', $this->option . '.category.' . $categoryId);
		}

		if ($allow === null)
		{
			// In the absense of better information, revert to the component permissions
			return parent::allowAdd($data);
		}
		else
		{
			return $allow;
		}
	}

	/**
	 * Method to check if you can add a new record
	 *
	 * @param   array  $data An array of input data
	 * @param   string $key  The name of the key for the primary key
	 *
	 * @return  boolean
	 *
	 * @since ?
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;

		if (!$recordId)
		{
			return false;
		}

		// Need to do a lookup from the model.
		$record     = $this->getModel()->getItem($recordId);
		$categoryId = (int) $record->catid;

		if ($categoryId)
		{
			$user = Factory::getUser();

			// The category has been set. Check the category permissions.
			if ($user->authorise('core.edit', $this->option . '.category.' . $categoryId))
			{
				return true;
			}

			// Fallback on edit.own.
			if ($user->authorise('core.edit.own', $this->option . '.category.' . $categoryId))
			{
				return ($record->created_by == $user->id);
			}
		}
		else
		{
			// Since there is no asset tracking, revert to the component permissions.
			return parent::allowEdit($data, $key);
		}

		return false;
	}

	/**
	 * Method to cancel an edit
	 *
	 * @param   string $key The name of the primary key of the URL variable
	 *
	 * @return  Boolean  True if access level checks pass, false otherwise
	 *
	 * @since ?
	 */
	public function cancel($key = 's_id')
	{
		$return = parent::cancel($key);

		// Redirect to the return page.
		$this->setRedirect($this->getReturnPage());

		return $return;
	}

	/**
	 * Method to edit an existing record
	 *
	 * @param   string $key    The name of the primary key of the URL variable
	 * @param   string $urlVar The name of the URL variable if different from the primary key (sometimes required to
	 *                         avoid router collisions)
	 *
	 * @return  Boolean  True if access level check and checkout passes, false otherwise
	 *
	 * @since ?
	 */
	public function edit($key = null, $urlVar = 's_id')
	{
		return parent::edit($key, $urlVar);
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string $name   The model name. Optional
	 * @param   string $prefix The class prefix. Optional
	 * @param   array  $config Configuration array for model. Optional
	 *
	 * @return  object  The model
	 *
	 * @since ?
	 */
	public function getModel($name = 'frontendupload', $prefix = '', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Gets the URL arguments to append to an item redirect
	 *
	 * @param   int    $recordId The primary key id for the item.
	 * @param   string $urlVar   The name of the URL variable for the id
	 *
	 * @return  string  The arguments to append to the redirect URL
	 *
	 * @since ?
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 's_id')
	{
		$jinput = Factory::getApplication()->input;
		$jinput->set('layout', 'default');
		$append = parent::getRedirectToItemAppend($recordId, 's_id');
		$itemId = $jinput->get('Itemid', 0, 'int');
		$catId  = $jinput->get('catid', 0, 'int');

		if ($itemId)
		{
			$append .= '&Itemid=' . $itemId;
		}

		if ($catId)
		{
			$append .= '&catid=' . $catId;
		}

		return $append;
	}

	/**
	 * Get the return URL
	 *
	 * If a "return" variable has been passed in the request
	 *
	 * @return  string  The return URL
	 *
	 * @since ?
	 */
	protected function getReturnPage()
	{
		$return = Factory::getApplication()->input->get('return', '', 'base64');

		if (empty($return) || !Uri::isInternal(base64_decode($return)))
		{
			return Uri::base();
		}
		else
		{
			return base64_decode($return);
		}
	}

	/**
	 * Function that allows child controller access to model data after the data has been saved.
	 *
	 * @param   BaseDatabaseModel  $model      The data model object.
	 * @param   array              $validData  The validated data.
	 *
	 * @since ?
	 */
	protected function postSaveHook(BaseDatabaseModel $model, $validData = array())
	{
		$task = $this->getTask();

		$recordId = (int) $model->getState($this->context . '.id');
		$params   = ComponentHelper::getParams('com_sermonspeaker');

		$app = Factory::getApplication();
		$db  = Factory::getDbo();

		// Check filenames and show a warning if one isn't save to use in an URL. Store anyway.
		if ($params->get('sanitise_filename', 1))
		{
			$files = array('audiofile', 'videofile', 'addfile');

			foreach ($files as $file)
			{
				$filename = File::stripExt(basename($validData[$file]));

				// Remove query part (eg for YouTube URLs
				if ($pos = strpos($filename, '?'))
				{
					$filename = substr($filename, 0, $pos);
				}

				if ($filename != ApplicationHelper::stringURLSafe($filename))
				{
					$text = Text::_('COM_SERMONSPEAKER_FILENAME_NOT_IDEAL') . ': ' . $validData[$file];
					$app->enqueueMessage($text, 'warning');
				}
			}
		}

		// Scriptures
		$query = "DELETE FROM #__sermon_scriptures \n"
			. "WHERE sermon_id = " . $recordId;
		$db->setQuery($query);
		$db->execute();
		$i = 1;

		if (!empty($validData['scripture']))
		{
			foreach ($validData['scripture'] as $scripture)
			{
				$item  = explode('|', $scripture);
				$query = "INSERT INTO #__sermon_scriptures \n"
					. "(`book`,`cap1`,`vers1`,`cap2`,`vers2`,`text`,`ordering`,`sermon_id`) \n"
					. "VALUES ('" . (int) $item[0] . "','" . (int) $item[1] . "','" . (int) $item[2] . "','" . (int) $item[3] . "','" . (int) $item[4]
					. "'," . $db->quote($item[5]) . ",'" . $i . "','" . $recordId . "')";
				$db->setQuery($query);
				$db->execute();
				$i++;
			}
		}

		// ID3
		if ($params->get('write_id3', 0))
		{
			$app = Factory::getApplication();
			$app->enqueueMessage($this->setMessage(''));

			$this->write_id3($recordId);
		}
	}

	/**
	 * Method to save a record
	 *
	 * @param   string $key    The name of the primary key of the URL variable
	 * @param   string $urlVar The name of the URL variable if different from the primary key (sometimes required to
	 *                         avoid router collisions)
	 *
	 * @return  Boolean  True if successful, false otherwise
	 *
	 * @since ?
	 */
	public function save($key = null, $urlVar = 's_id')
	{
		return parent::save($key, $urlVar);
	}

	/**
	 * Method to write the ID3 tags to the file
	 *
	 * @param   int $id The id of the record
	 *
	 * @return  Boolean  True if successful, false otherwise
	 *
	 * @since ?
	 */
	public function write_id3($id)
	{
		$app = Factory::getApplication();

		if (!$id)
		{
			$app->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			$app->redirect('index.php?option=com_sermonspeaker&view=frontendupload');

			return false;
		}

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('audiofile, videofile, sermons.created_by, sermons.catid, sermons.title, speakers.title as speaker_title');
		$query->select('series.title AS series_title, notes, sermon_number, picture');
		$query->select('YEAR(sermon_date) AS year, DATE_FORMAT(sermon_date, "%H%i") AS time, DATE_FORMAT(sermon_date, "%d%m") AS date');
		$query->from('#__sermon_sermons AS sermons');
		$query->join('LEFT', '#__sermon_speakers AS speakers ON speaker_id = speakers.id');
		$query->join('LEFT', '#__sermon_series AS series ON series_id = series.id');
		$query->where('sermons.id = ' . $id);
		$db->setQuery($query);
		$item       = $db->loadObject();
		$user       = Factory::getUser();
		$canEdit    = $user->authorise('core.edit', 'com_sermonspeaker.category.' . $item->catid);
		$canEditOwn = $user->authorise('core.edit.own', 'com_sermonspeaker.category.' . $item->catid) && $item->created_by == $user->id;

		if ($canEdit || $canEditOwn)
		{
			$files[] = $item->audiofile;
			$files[] = $item->videofile;
			$getID3 = new getID3;
			$getID3->setOption(array('encoding' => 'UTF-8'));
			$writer             = new getid3_writetags;
			$writer->tagformats = array('id3v2.3');

			// False would merge, but is currently known to be buggy and throws an exception
			$writer->overwrite_tags    = true;
			$writer->remove_other_tags = false;
			$writer->tag_encoding      = 'UTF-8';
			$TagData                   = array(
				'title'  => array($item->title),
				'artist' => array($item->speaker_title),
				'album'  => array($item->series_title),
				'track'  => array($item->sermon_number),
				'year'   => array($item->year),
				'date'   => array($item->date),
				'time'   => array($item->time),
			);
			$TagData['comment']        = array(strip_tags(HTMLHelper::_('content.prepare', $item->notes)));

			// Adding the picture to the id3 tags, taken from getID3 Demos -> demo.write.php
			if ($item->picture && !parse_url($item->picture, PHP_URL_SCHEME))
			{
				ob_start();
				$pic = JPATH_ROOT . '/' . trim($item->picture, ' /');

				if ($fd = fopen($pic, 'rb'))
				{
					ob_end_clean();
					$APICdata = fread($fd, filesize($pic));
					fclose($fd);
					$image = getimagesize($pic);

					// 1 = gif, 2 = jpg, 3 = png
					if (($image[2] > 0) && ($image[2] < 4))
					{
						$TagData['attached_picture'][0]['data']          = $APICdata;
						$TagData['attached_picture'][0]['picturetypeid'] = 0;
						$TagData['attached_picture'][0]['description']   = basename($pic);
						$TagData['attached_picture'][0]['mime']          = $image['mime'];
					}
				}
				else
				{
					ob_end_clean();
					$app->enqueueMessage('Couldn\'t open the picture: ' . $pic, 'notice');
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
					$app->enqueueMessage('Successfully wrote id3 tags to "' . $file . '"');

					if (!empty($writer->warnings))
					{
						$app->enqueueMessage('There were some warnings:<br>' . implode(', ', $writer->warnings), 'notice');
					}
				}
				else
				{
					$app->enqueueMessage('Failed to write id3 tags to "' . $file . '"! ' . implode(', ', $writer->errors), 'notice');
				}
			}

			return true;
		}
		else
		{
			$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->redirect('index.php?option=com_sermonspeaker&view=sermons');

			return false;
		}
	}
}
