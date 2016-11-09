<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Controller class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerControllerFrontendupload extends JControllerForm
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
		$user       = JFactory::getUser();
		$categoryId = Joomla\Utilities\ArrayHelper::getValue($data, 'catid', JFactory::getApplication()->input->get('filter_category_id'), 'int');
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
			$user = JFactory::getUser();

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
		$result = parent::edit($key, $urlVar);

		return $result;
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
		$model = parent::getModel($name, $prefix, $config);

		return $model;
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
		$jinput = JFactory::getApplication()->input;
		$jinput->set('layout', 'default');
		$append = parent::getRedirectToItemAppend($recordId, 's_id');
		$itemId = $jinput->get('Itemid', 0, 'int');
		$return = $this->getReturnPage();

		if ($itemId)
		{
			$append .= '&Itemid=' . $itemId;
		}

		if ($return)
		{
			$append .= '&return=' . base64_encode($return);
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
		$return = JFactory::getApplication()->input->get('return', '', 'base64');

		if (empty($return) || !JUri::isInternal(base64_decode($return)))
		{
			return JUri::base();
		}
		else
		{
			return base64_decode($return);
		}
	}

	/**
	 * Function that allows child controller access to model data after the data has been saved.
	 *
	 * @param \JModel|\JModelLegacy $model     The data model object.
	 * @param   array               $validData The validated data.
	 *
	 * @since ?
	 */
	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
		$task = $this->getTask();

		if ($task == 'save')
		{
			$this->setRedirect(JRoute::_('index.php?option=com_sermonspeaker&view=sermons', false));
		}

		$recordId = (int) $model->getState($this->context . '.id');
		$params   = JComponentHelper::getParams('com_sermonspeaker');

		$app = JFactory::getApplication();
		$db  = JFactory::getDbo();

		// Check filenames and show a warning if one isn't save to use in an URL. Store anyway.
		$files = array('audiofile', 'videofile', 'addfile');

		foreach ($files as $file)
		{
			$filename = JFile::stripExt(basename($validData[$file]));

			if ($filename != JApplicationHelper::stringURLSafe($filename))
			{
				$text = JText::_('COM_SERMONSPEAKER_FILENAME_NOT_IDEAL') . ': ' . $validData[$file];
				$app->enqueueMessage($text, 'warning');
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
			$app = JFactory::getApplication();
			$app->enqueueMessage($this->setMessage(''));

			$this->write_id3($recordId);
		}

		return;
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
		$result = parent::save($key, $urlVar);

		return $result;
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
		$app = JFactory::getApplication();

		if (!$id)
		{
			$app->redirect('index.php?option=com_sermonspeaker&view=frontendupload', JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');

			return false;
		}

		$db    = JFactory::getDbo();
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
		$user       = JFactory::getUser();
		$canEdit    = $user->authorise('core.edit', 'com_sermonspeaker.category.' . $item->catid);
		$canEditOwn = $user->authorise('core.edit.own', 'com_sermonspeaker.category.' . $item->catid) && $item->created_by == $user->id;

		if ($canEdit || $canEditOwn)
		{
			$files[] = $item->audiofile;
			$files[] = $item->videofile;
			require_once JPATH_COMPONENT_SITE . '/id3/getid3/getid3.php';
			$getID3 = new getID3;
			$getID3->setOption(array('encoding' => 'UTF-8'));
			require_once JPATH_COMPONENT_SITE . '/id3/getid3/write.php';
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
			$TagData['comment']        = array(strip_tags(JHtml::_('content.prepare', $item->notes)));

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
			$app->redirect('index.php?option=com_sermonspeaker&view=sermons', JText::_('JERROR_ALERTNOAUTHOR'), 'error');

			return false;
		}
	}
}
