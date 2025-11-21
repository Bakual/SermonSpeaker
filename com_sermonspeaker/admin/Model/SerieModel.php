<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\Model;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Versioning\VersionableModelTrait;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Sermonspeaker\Component\Sermonspeaker\Administrator\Table\SerieTable;

defined('_JEXEC') or die;

/**
 * Serie model.
 *
 * @package   Sermonspeaker.Administrator
 *
 * @since     ?
 */
class SerieModel extends AdminModel
{
	use VersionableModelTrait;

	/**
	 * The type alias for this content type.
	 *
	 * @var    string
	 * @since  5.8.0
	 */
	public $typeAlias = 'com_sermonspeaker.serie';

	/**
	 * @var   string  The prefix to use with controller messages.
	 *
	 * @since ?
	 */
	protected $text_prefix = 'COM_SERMONSPEAKER';

	/**
	 * The context used for the associations table
	 *
	 * @var     string
	 * @since   3.4.4
	 */
	protected $associationsContext = 'com_sermonspeaker.serie';

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return    bool|Form    A Form object on success, false on failure
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_sermonspeaker.serie', 'serie', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		// Determine correct permissions to check.
		if ($this->getState('serie.id'))
		{
			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit');
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

			// Disable fields while saving.
			// The controller has already verified this is an article you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('state', 'filter', 'unset');
		}

		// Modify the form to allow resetting the hits counter.
		if (isset($data['hits']) && $data['hits'] == 0)
		{
			$form->setFieldAttribute('hits', 'filter', '');
		}

		return $form;
	}

	/**
	 * Method to test whether a records state can be changed.
	 *
	 * @param    $record  object    A record object.
	 *
	 * @return    boolean    True if allowed to change the state of the record. Defaults to the permission set in the
	 *                       component.
	 * @since    1.6
	 */
	protected function canEditState($record)
	{
		$user = Factory::getApplication()->getIdentity();

		// Check against the category.
		if (!empty($record->catid))
		{
			return $user->authorise('core.edit.state', 'com_sermonspeaker.category.' . (int) $record->catid);
		}
		// Default to component settings if neither article nor category known.
		else
		{
			return parent::canEditState($record);
		}
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @since   5.6.0
	 */
	public function save($data)
	{
		$jinput = Factory::getApplication()->input;

		// Alter the title for save as copy
		if ($jinput->get('task') == 'save2copy')
		{
			$origTable = clone $this->getTable();
			$origTable->load($jinput->getInt('id'));

			if ($data['title'] == $origTable->title)
			{
				list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
				$data['title'] = $title;
				$data['alias'] = $alias;
			}
			else
			{
				if ($data['alias'] == $origTable->alias)
				{
					$data['alias'] = '';
				}
			}

			$data['state'] = 0;
		}

		return parent::save($data);
	}

	/**
	 * Method to set a default series.
	 * Copied from template style.
	 *
	 * @param    $id int        The primary key ID for the series.
	 *
	 * @return    boolean    True if successful.
	 * @throws    \Exception
	 *
	 * @since ?
	 */
	public function setDefault($id = 0)
	{
		// Initialise variables.
		$user = Factory::getApplication()->getIdentity();
		$db   = $this->getDbo();

		// Access checks.
		if (!$user->authorise('core.edit.state', 'com_sermonspeaker'))
		{
			throw new \Exception(Text::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
		}

		// Reset the home fields for the client_id.
		$db->setQuery(
			'UPDATE #__sermon_series' .
			" SET home = '0'" .
			" WHERE home = '1'"
		);

		if (!$db->execute())
		{
			throw new \Exception($db->getErrorMsg());
		}

		// Set the new home style.
		$db->setQuery(
			'UPDATE #__sermon_series' .
			" SET home = '1'" .
			' WHERE id = ' . (int) $id
		);

		if (!$db->execute())
		{
			throw new \Exception($db->getErrorMsg());
		}

		// Clean the cache.
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to unset a default series.
	 * Copied from template style.
	 *
	 * @param   integer  $id  The primary key ID for the series.
	 *
	 * @return  boolean  True if successful.
	 * @throws  \Exception
	 *
	 * @since 5.8.0
	 */
	public function unsetDefault($id = 0)
	{
		$user = Factory::getApplication()->getIdentity();
		$db   = $this->getDbo();

		// Access checks.
		if (!$user->authorise('core.edit.state', 'com_sermonspeaker'))
		{
			throw new \Exception(Text::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
		}

		// Set the new home style.
		$db->setQuery(
			'UPDATE #__sermon_series' .
			" SET home = '0'" .
			' WHERE id = ' . (int) $id
		);
		$db->execute();

		// Clean the cache.
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param    $record  object      A record object.
	 *
	 * @return    boolean    True if allowed to delete the record. Defaults to the permission set in the component.
	 * @since    1.6
	 */
	protected function canDelete($record)
	{
		return true;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return    mixed    The data for the form.
	 * @since    1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app  = Factory::getApplication();
		$data = $app->getUserState('com_sermonspeaker.edit.serie.data', array());

		if (empty($data))
		{
			$data = $this->getItem();

			// Pre-select some filters (Status, Category, Language, Podcast) in edit form if those have been selected in Sermon Manager: Sermons
			if ($this->getState('serie.id') == 0)
			{
				$filters        = (array) $app->getUserState('com_sermonspeaker.series.filter');
				$data->state    = $app->input->getInt('state', ((isset($filters['state']) && $filters['state'] !== '') ? $filters['state'] : null));
				$data->catid    = $app->input->getInt('catid', (!empty($filters['category_id']) ? $filters['category_id'] : null));
				$data->language = $app->input->getString('language', (!empty($filters['language']) ? $filters['language'] : null));
			}
		}

		$this->preprocessData('com_sermonspeaker.serie', $data);

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param    $pk integer    The id of the primary key.
	 *
	 * @return    mixed    Object on success, false on failure.
	 * @since    1.6
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		if ($item->id)
		{
			if ($item->metadata)
			{
				// Convert the metadata field to an array.
				$registry = new Registry;
				$registry->loadString($item->metadata);
				$item->metadata = $registry->toArray();
			}

			$item->tags = new TagsHelper();
			$item->tags->getTagIds($item->id, 'com_sermonspeaker.serie');
		}

		// Load associated items
		if (Associations::isEnabled())
		{
			$item->associations = array();

			if ($item->id != null)
			{
				$associations = Associations::getAssociations('com_sermonspeaker.series', '#__sermon_series', $this->associationsContext, $item->id);

				foreach ($associations as $tag => $association)
				{
					$item->associations[$tag] = $association->id;
				}
			}
		}

		return $item;
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  Table  A Table object
	 *
	 * @since   3.0
	 * @throws  \Exception
	 */
	public function getTable($name = 'Serie', $prefix = 'Administrator', $options = array())
	{
		return parent::getTable($name, $prefix, $options);
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   SerieTable  $table
	 *
	 * @since    1.6
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		$table->title = htmlspecialchars_decode($table->title, ENT_QUOTES);
		$table->alias = ApplicationHelper::stringURLSafe($table->alias);
		if (empty($table->alias))
		{
			$table->alias = ApplicationHelper::stringURLSafe($table->title);
			if (empty($table->alias))
			{
				$table->alias = Factory::getDate()->format("Y-m-d-H-i-s");
			}
		}

		if (!empty($table->metakey))
		{
			// only process if not empty
			$bad_characters = array("\n", "\r", "\"", "<", ">"); // array of characters to remove
			$after_clean    = StringHelper::str_ireplace($bad_characters, "", $table->metakey); // remove bad characters
			$keys           = explode(',', $after_clean); // create array using commas as delimiter
			$clean_keys     = array();
			foreach ($keys as $key)
			{
				if (trim($key))
				{  // ignore blank keywords
					$clean_keys[] = trim($key);
				}
			}
			$table->metakey = implode(", ", $clean_keys); // put array back together delimited by ", "
		}

		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (empty($table->ordering))
			{
				$db = $this->getDatabase();
				$db->setQuery('SELECT MAX(ordering) FROM #__sermon_series');
				$max = $db->loadResult();

				$table->ordering = $max + 1;
			}
		}

		// Set the publish date to now
		if ($table->state == 1 && (int) $table->publish_up == 0)
		{
			$table->publish_up = Factory::getDate()->toSql();
		}

		if ($table->state == 1 && intval($table->publish_down) == 0)
		{
			$table->publish_down = $this->getDbo()->getNullDate();
		}

		// Increment the content version number.
		$table->version++;
	}

	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   Form    $form
	 * @param   mixed   $data
	 * @param   string  $group
	 *
	 * @since    3.0
	 */
	protected function preprocessForm(Form $form, $data, $group = 'sermonspeaker')
	{
		// Association items
		if (Associations::isEnabled())
		{
			$languages = LanguageHelper::getContentLanguages(false, true, null, 'ordering', 'asc');

			if ($languages)
			{
				$addform = new \SimpleXMLElement('<form />');
				$fields  = $addform->addChild('fields');
				$fields->addAttribute('name', 'associations');
				$fieldset = $fields->addChild('fieldset');
				$fieldset->addAttribute('name', 'item_associations');

				foreach ($languages as $language)
				{
					$field = $fieldset->addChild('field');
					$field->addAttribute('name', $language->lang_code);
					$field->addAttribute('type', 'modal_serie');
					$field->addAttribute('language', $language->lang_code);
					$field->addAttribute('label', $language->title);
					$field->addAttribute('translate_label', 'false');
					$field->addAttribute('select', 'true');
					$field->addAttribute('new', 'true');
					$field->addAttribute('edit', 'true');
					$field->addAttribute('clear', 'true');
					$field->addAttribute('ignoredefault', 'true');
				}

				$form->load($addform, false);
			}
		}

		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param    $table  Table    A record object.
	 *
	 * @return    array    An array of conditions to add to add to ordering queries.
	 * @since    1.6
	 */
	protected function getReorderConditions($table = null)
	{
		$condition   = array();
		$condition[] = 'catid = ' . (int) $table->catid;

		return $condition;
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
	 * @since    11.1
	 */
	protected function batchCopy($value, $pks, $contexts)
	{
		$categoryId = (int) $value;

		$table = $this->getTable();
		$i     = 0;

		// Check that the category exists
		if ($categoryId)
		{
			$categoryTable = Table::getInstance('Category');
			if (!$categoryTable->load($categoryId))
			{
				if ($error = $categoryTable->getError())
				{
					// Fatal error
					$this->setError($error);

				}
				else
				{
					$this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));

				}

				return false;
			}
		}

		if (empty($categoryId))
		{
			$this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));

			return false;
		}

		// Check that the user has create permission for the component
		$extension = Factory::getApplication()->input->get('option', '');
		$user      = Factory::getApplication()->getIdentity();
		if (!$user->authorise('core.create', $extension . '.category.' . $categoryId))
		{
			$this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'));

			return false;
		}

		$newIds = array();

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
					$this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// Alter the title & alias
			// Custom: defining the title and set "home" to 0
			$data         = $this->generateNewTitle($categoryId, $table->alias, $table->title);
			$table->title = $data['0'];
			$table->alias = $data['1'];
			$table->home  = 0;

			// Reset the ID because we are making a copy
			$table->id = 0;

			// New category ID
			$table->catid = $categoryId;

			// TODO: Deal with ordering?
			//$table->ordering	= 1;

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
}
