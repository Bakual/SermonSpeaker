<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

/**
 * Item Model for a Sermon.
 *
 * @since  3.4
 */
class SermonspeakerModelSermon extends JModelAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 */
	protected $text_prefix = 'COM_SERMONSPEAKER';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			if ($record->state != -2)
			{
				return false;
			}

			$user = JFactory::getUser();

			if ($record->catid)
			{
				return $user->authorise('core.delete', 'com_sermonspeaker.category.' . (int) $record->catid);
			}
			else
			{
				return parent::canDelete($record);
			}
		}

		return false;
	}

	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   12.2
	 */
	public function delete(&$pks)
	{
		// Initialise variables.
		$pks   = (array) $pks;
		$table = $this->getTable();
		$db    = $this->getDbo();

		// Iterate the items to delete the files.
		foreach ($pks as $pk)
		{
			if (!$table->load($pk))
			{
				$this->setError($table->getError());

				return false;
			}

			if (!$this->canDelete($table))
			{
				continue;
			}

			if ($table->audiofile && file_exists(JPATH_ROOT . $table->audiofile))
			{
				$query = $db->getQuery(true);
				$query->select('count(1)');
				$query->from($db->quoteName('#__sermon_sermons'));
				$query->where(
					$db->quoteName('audiofile') . ' = ' . $db->quote($table->audiofile)
					. ' OR ' . $db->quoteName('videofile') . ' = ' . $db->quote($table->audiofile)
				);
				$db->setQuery($query);

				// Only delete file if it's not used in another sermon.
				if ($db->loadResult() == 1)
				{
					JFile::delete(JPATH_ROOT . $table->audiofile);
				}
			}

			if ($table->videofile && file_exists(JPATH_ROOT . $table->videofile))
			{
				$query = $db->getQuery(true);
				$query->select('count(1)');
				$query->from($db->quoteName('#__sermon_sermons'));
				$query->where(
					$db->quoteName('audiofile') . ' = ' . $db->quote($table->videofile)
					. ' OR ' . $db->quoteName('videofile') . ' = ' . $db->quote($table->videofile)
				);
				$db->setQuery($query);

				// Only delete file if it's not used in another sermon.
				if ($db->loadResult() == 1)
				{
					JFile::delete(JPATH_ROOT . $table->videofile);
				}
			}
		}

		// Call parent function to delete the database records
		parent::delete($pks);

		return true;
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		if (!empty($record->catid))
		{
			return $user->authorise('core.edit.state', 'com_sermonspeaker.category.' . (int) $record->catid);
		}
		else
		{
			return parent::canEditState($record);
		}
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable    A database object
	 */
	public function getTable($type = 'Sermon', $prefix = 'SermonspeakerTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_sermonspeaker.sermon', 'sermon', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		// Determine correct permissions to check.
		if ((int) $this->getState('sermon.id'))
		{
			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit');

			// Existing record. Can only edit own articles in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit.own');
		}
		else
		{
			// New record. Can only create in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.create');
		}

		// Modify the form based on Edit State access controls.
		if (!$this->canEditState((object) $data))
		{
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');
			$form->setFieldAttribute('podcast', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is an article you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('state', 'filter', 'unset');
			$form->setFieldAttribute('podcast', 'filter', 'unset');
		}

		// Modify the form to allow resetting the hits counter.
		if (isset($data['hits']) && $data['hits'] == 0)
		{
			$form->setFieldAttribute('hits', 'filter', '');
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data   = JFactory::getApplication()->getUserState('com_sermonspeaker.edit.sermon.data', array());
		$jinput = JFactory::getApplication()->input;

		if (empty($data))
		{
			$data = $this->getItem();
		}
		else
		{
			// Catch scriptures from database again because the values in UserState can't be used due to formatting.
			$data['scripture'] = array();

			if ($data['id'])
			{
				$db    = JFactory::getDBO();
				$query = $db->getQuery(true)
					->select($db->quoteName(array('book', 'cap1', 'vers1', 'cap2', 'vers2', 'text')))
					->from($db->quoteName('#__sermon_scriptures'))
					->where($db->quoteName('sermon_id') . ' = ' . (int) $data['id'])
					->order('ordering ASC');

				$db->setQuery($query);
				$data['scripture'] = $db->loadAssocList();
			}
		}

		// Deprecated with SermonSpeaker 4.4.4. Using Ajax now for Lookup. Still used for tools function files to create sermon from file.
		if ($id3_file = $jinput->get('file', '', 'string'))
		{
			if ($jinput->get('type') == 'video')
			{
				$data->videofile = $id3_file;
			}
			else
			{
				$data->audiofile = $id3_file;
			}

			require_once JPATH_COMPONENT_SITE . '/helpers/id3.php';

			$params = JComponentHelper::getParams('com_sermonspeaker');

			$id3 = SermonspeakerHelperId3::getID3($id3_file, $params);

			if ($id3)
			{
				foreach ($id3 as $key => $value)
				{
					if ($value)
					{
						$data->$key = $value;
					}
				}
			}
			else
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_SERMONSPEAKER_ERROR_ID3'), 'notice');
			}
		}

		$this->preprocessData('com_sermonspeaker.sermon', $data);

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		$item->scripture = array();

		if ($item->id)
		{
			$db    = JFactory::getDBO();
			$query = $db->getQuery(true)
				->select($db->quoteName(array('book', 'cap1', 'vers1', 'cap2', 'vers2', 'text')))
				->from($db->quoteName('#__sermon_scriptures'))
				->where($db->quoteName('sermon_id') . ' = ' . (int) $item->id)
				->order('ordering ASC');

			$db->setQuery($query);
			$item->scripture = $db->loadAssocList();

			// Convert the metadata field to an array.
			$registry = new Joomla\Registry\Registry;
			$registry->loadString($item->metadata);
			$item->metadata = $registry->toArray();

			$item->tags = new JHelperTags;
			$item->tags->getTagIds($item->id, 'com_sermonspeaker.sermon');
		}

		// Load associated items
		if (JLanguageAssociations::isEnabled())
		{
			$item->associations = array();

			if ($item->id != null)
			{
				$associations = JLanguageAssociations::getAssociations('com_sermonspeaker', '#__sermon_sermons', 'com_sermonspeaker.sermon', $item->id);

				foreach ($associations as $tag => $association)
				{
					$item->associations[$tag] = $association->id;
				}
			}
		}

		return $item;
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A JTable object.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareTable($table)
	{
		$table->title = htmlspecialchars_decode($table->title, ENT_QUOTES);
		$table->alias = JApplicationHelper::stringURLSafe($table->alias);

		if (empty($table->alias))
		{
			$table->alias = JApplicationHelper::stringURLSafe($table->title);

			if (empty($table->alias))
			{
				$table->alias = JFactory::getDate()->format("Y-m-d-H-i-s");
			}
		}

		if (!$table->audiofilesize && $table->audiofile)
		{
			if (!parse_url($table->audiofile, PHP_URL_SCHEME))
			{
				$table->audiofilesize = filesize(JPATH_SITE . $table->audiofile);
			}
		}

		if (!$table->videofilesize && $table->videofile)
		{
			if (!parse_url($table->videofile, PHP_URL_SCHEME))
			{
				$table->videofilesize = filesize(JPATH_SITE . $table->videofile);
			}
		}

		$time_arr = explode(':', $table->sermon_time);

		foreach ($time_arr as &$time_int)
		{
			$time_int = (int) $time_int;
			$time_int = str_pad($time_int, 2, '0', STR_PAD_LEFT);
		}

		if (count($time_arr) == 2)
		{
			$table->sermon_time = '00:' . $time_arr[0] . ':' . $time_arr[1];
		}
		elseif (count($time_arr) == 3)
		{
			$table->sermon_time = $time_arr[0] . ':' . $time_arr[1] . ':' . $time_arr[2];
		}

		// Only process if not empty
		if (!empty($table->metakey))
		{
			// Array of characters to remove
			$bad_characters = array("\n", "\r", '"', '<', '>');

			// Remove bad characters
			$after_clean = Joomla\String\String::str_ireplace($bad_characters, '', $table->metakey);

			// Create array using commas as delimiter
			$keys       = explode(',', $after_clean);
			$clean_keys = array();

			foreach ($keys as $key)
			{
				// Ignore blank keywords
				if ($key = trim($key))
				{
					$clean_keys[] = $key;
				}
			}

			// Put array back together delimited by ", "
			$table->metakey = implode(', ', $clean_keys);
		}

		// Reorder the articles within the category so the new sermon is first
		if (empty($table->id))
		{
			$table->reorder('catid = ' . (int) $table->catid . ' AND state >= 0');
		}

		// Increment the content version number.
		$table->version++;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @since   12.2
	 */
	public function save($data)
	{
		if (parent::save($data))
		{
			if (JLanguageAssociations::isEnabled())
			{
				$id   = (int) $this->getState($this->getName() . '.id');
				$item = $this->getItem($id);

				// Adding self to the association
				$associations = $data['associations'];

				foreach ($associations as $tag => $id)
				{
					if (empty($id))
					{
						unset($associations[$tag]);
					}
				}

				// Detecting all item menus
				$all_language = $item->language == '*';

				if ($all_language && !empty($associations))
				{
					JFactory::getApplication()->enqueueMessage(JText::_('COM_SERMONSPEAKER_ERROR_ALL_LANGUAGE_ASSOCIATED'), 'notice');
				}

				$associations[$item->language] = $item->id;

				// Deleting old association for these items
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true)
					->delete('#__associations')
					->where('context=' . $db->quote('com_sermonspeaker.sermon'))
					->where('id IN (' . implode(',', $associations) . ')');
				$db->setQuery($query);
				$db->execute();

				if ($error = $db->getErrorMsg())
				{
					$this->setError($error);

					return false;
				}

				if (!$all_language && count($associations))
				{
					// Adding new association for these items
					$key = md5(json_encode($associations));
					$query->clear()
						->insert('#__associations');

					foreach ($associations as $tag => $id)
					{
						$query->values($id . ',' . $db->quote('com_sermonspeaker.sermon') . ',' . $db->quote($key));
					}

					$db->setQuery($query);
					$db->execute();

					if ($error = $db->getErrorMsg())
					{
						$this->setError($error);

						return false;
					}
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * Method to allow derived classes to preprocess the form.
	 *
	 * @param   JForm   $form   A JForm object.
	 * @param   mixed   $data   The data expected for the form.
	 * @param   string  $group  The name of the plugin group to import (defaults to "content").
	 *
	 * @return  void
	 *
	 * @see     JFormField
	 * @since   12.2
	 * @throws  Exception if there is an error in the form event.
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'sermonspeaker')
	{
		// Association items
		if (JLanguageAssociations::isEnabled())
		{
			$languages = JLanguageHelper::getLanguages('lang_code');

			// Force to array (perhaps move to $this->loadFormData())
			$data = (array) $data;

			$addform = new SimpleXMLElement('<form />');
			$fields  = $addform->addChild('fields');
			$fields->addAttribute('name', 'associations');
			$fieldset = $fields->addChild('fieldset');
			$fieldset->addAttribute('name', 'item_associations');
			$fieldset->addAttribute('description', 'COM_SERMONSPEAKER_ITEM_ASSOCIATIONS_FIELDSET_DESC');
			$add = false;

			foreach ($languages as $tag => $language)
			{
				if (empty($data['language']) || $tag != $data['language'])
				{
					$add   = true;
					$field = $fieldset->addChild('field');
					$field->addAttribute('name', $tag);
					$field->addAttribute('type', 'modal_sermon');
					$field->addAttribute('language', $tag);
					$field->addAttribute('label', $language->title);
					$field->addAttribute('translate_label', 'false');
				}
			}

			if ($add)
			{
				$form->load($addform, false);
			}
		}

		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   object  $table  A record object.
	 *
	 * @return  array  An array of conditions to add to add to ordering queries.
	 *
	 * @since   1.6
	 */
	protected function getReorderConditions($table = null)
	{
		$condition = array();
		$condition[] = 'catid = ' . (int) $table->catid;

		return $condition;
	}

	/**
	 * Method to change the podcast state of one or more records.
	 *
	 * @param   array    &$pks   A list of the primary keys to change.
	 * @param   integer  $value  The value of the published state.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   12.2
	 * @throws  Exception
	 */
	public function podcast(&$pks, $value = 1)
	{
		// Initialise variables.
		$user  = JFactory::getUser();
		$table = $this->getTable();
		$pks   = (array) $pks;

		// Access checks.
		foreach ($pks as $i => $pk)
		{
			$table->reset();

			if ($table->load($pk))
			{
				if (!$this->canEditState($table))
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					throw new Exception(JText::_('JLIB_APPLICATION_ERROR_EDIT_STATE_NOT_PERMITTED'), 403);
				}
			}
		}

		// Attempt to change the state of the records.
		if (!$table->podcast($pks, $value, $user->get('id')))
		{
			$this->setError($table->getError());

			return false;
		}

		return true;
	}

	/**
	 * Method to perform batch operations on an item or a set of items.
	 * Copy from modeladmin with added commands for speaker and series.
	 *
	 * @param   array  $commands  An array of commands to perform.
	 * @param   array  $pks       An array of item ids.
	 * @param   array  $contexts  An array of item contexts.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 *
	 * @since   11.1
	 */
	public function batch($commands, $pks, $contexts)
	{
		// Sanitize user ids.
		$pks = array_unique($pks);
		$pks = Joomla\Utilities\ArrayHelper::toInteger($pks);

		// Remove any values of zero.
		if (array_search(0, $pks, true))
		{
			unset($pks[array_search(0, $pks, true)]);
		}

		if (empty($pks))
		{
			$this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));

			return false;
		}

		$done = false;

		if (!empty($commands['category_id']))
		{
			$cmd = Joomla\Utilities\ArrayHelper::getValue($commands, 'move_copy', 'c');

			if ($cmd == 'c')
			{
				$result = $this->batchCopy($commands['category_id'], $pks, $contexts);

				if (is_array($result))
				{
					$pks = $result;
				}
				else
				{
					return false;
				}
			}
			elseif ($cmd == 'm' && !$this->batchMove($commands['category_id'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if (!empty($commands['language_id']))
		{
			if (!$this->batchLanguage($commands['language_id'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if (!empty($commands['tag']))
		{
			if (!$this->batchTag($commands['tag'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if (!empty($commands['speaker_id']))
		{
			if (!$this->batchSpeaker($commands['speaker_id'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if (!empty($commands['serie_id']))
		{
			if (!$this->batchSerie($commands['serie_id'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if (!$done)
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));

			return false;
		}

		// Clear the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Batch copy items to a new category or current.
	 * Override from modeladmin to adjust title field.
	 *
	 * @param   integer  $value     The new category.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  mixed  An array of new IDs on success, boolean false on failure.
	 *
	 * @since  11.1
	 */
	protected function batchCopy($value, $pks, $contexts)
	{
		$categoryId = (int) $value;

		$table = $this->getTable();
		$i = 0;

		// Check that the category exists
		if ($categoryId)
		{
			$categoryTable = JTable::getInstance('Category');

			if (!$categoryTable->load($categoryId))
			{
				if ($error = $categoryTable->getError())
				{
					// Fatal error
					$this->setError($error);

					return false;
				}
				else
				{
					$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));

					return false;
				}
			}
		}

		if (empty($categoryId))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));

			return false;
		}

		// Check that the user has create permission for the component
		$extension = JFactory::getApplication()->input->get('option', '');
		$user = JFactory::getUser();

		if (!$user->authorise('core.create', $extension . '.category.' . $categoryId))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'));

			return false;
		}

		// Parent exists so we let's proceed
		while (!empty($pks))
		{
			// Pop the first ID off the stack
			$pk = array_shift($pks);

			$table->reset();

			// Check that the row actually exists
			if (!$table->load($pk))
			{
				if ($error = $table->getError())
				{
					// Fatal error
					$this->setError($error);

					return false;
				}
				else
				{
					// Not fatal error
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));

					continue;
				}
			}

			// Alter the title & alias
			// Custom: defining the title
			$data         = $this->generateNewTitle($categoryId, $table->alias, $table->title);
			$table->title = $data['0'];
			$table->alias = $data['1'];

			// Reset the ID because we are making a copy
			$table->id = 0;

			// New category ID
			$table->catid = $categoryId;

			// TODO: Deal with ordering?
			// $table->ordering	= 1;

			// Check the row.
			if (!$table->check())
			{
				$this->setError($table->getError());

				return false;
			}

			// Store the row.
			if (!$table->store())
			{
				$this->setError($table->getError());

				return false;
			}

			// Get the new item ID
			$newId = $table->get('id');

			// Add the new ID to the array
			$newIds[$i] = $newId;
			$i++;
		}

		// Clean the cache
		$this->cleanCache();

		return $newIds;
	}

	/**
	 * Batch speaker changes for a group of rows.
	 *
	 * @param   string  $value     The new value matching a speaker.
	 * @param   array   $pks       An array of row IDs.
	 * @param   array   $contexts  An array of item contexts.
	 *
	 * @return  boolean  True if successful, false otherwise and internal error is set.
	 *
	 * @since   11.3
	 */
	protected function batchSpeaker($value, $pks, $contexts)
	{
		// Set the variables
		$user  = JFactory::getUser();
		$table = $this->getTable();

		foreach ($pks as $pk)
		{
			if ($user->authorise('core.edit', $contexts[$pk]))
			{
				$table->reset();
				$table->load($pk);
				$table->speaker_id = $value;

				if (!$table->store())
				{
					$this->setError($table->getError());

					return false;
				}
			}
			else
			{
				$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));

				return false;
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Batch serie changes for a group of rows.
	 *
	 * @param   string  $value     The new value matching a serie.
	 * @param   array   $pks       An array of row IDs.
	 * @param   array   $contexts  An array of item contexts.
	 *
	 * @return  boolean  True if successful, false otherwise and internal error is set.
	 *
	 * @since   11.3
	 */
	protected function batchSerie($value, $pks, $contexts)
	{
		// Set the variables
		$user  = JFactory::getUser();
		$table = $this->getTable();

		foreach ($pks as $pk)
		{
			if ($user->authorise('core.edit', $contexts[$pk]))
			{
				$table->reset();
				$table->load($pk);
				$table->series_id = $value;

				if (!$table->store())
				{
					$this->setError($table->getError());

					return false;
				}
			}
			else
			{
				$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));

				return false;
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}
}
