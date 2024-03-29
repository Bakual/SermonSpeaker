<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Tag\TaggableTableInterface;
use Joomla\CMS\Tag\TaggableTableTrait;
use Joomla\CMS\Versioning\VersionableTableInterface;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;

/**
 * Sermon Table class
 *
 * @package  Sermonspeaker.Administrator
 *
 * @since    ?
 */
class SermonspeakerTableSermon extends Table implements VersionableTableInterface, TaggableTableInterface
{
	use TaggableTableTrait;

	/**
	 * The UCM type alias. Used for tags, content versioning etc. Leave blank to effectively disable these features.
	 *
	 * @var    string
	 * @since  6.0.0
	 */
	public $typeAlias = 'com_sermonspeaker.sermon';
	/**
	 * Array with alias for "special" columns such as ordering, hits etc etc
	 *
	 * @var    array
	 * @since  6.0.0
	 */
	protected $_columnAlias = array('published' => 'state');

	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  JDatabaseDriver object.
	 *
	 * @since ?
	 */
	public function __construct($db)
	{
		parent::__construct('#__sermon_sermons', 'id', $db);
	}

	/**
	 * Overloaded check function
	 *
	 * @return  boolean
	 *
	 * @see     Table::check
	 * @since   6.0.0
	 */
	public function check()
	{
		try
		{
			parent::check();
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		// Set name
		$this->title = htmlspecialchars_decode($this->title, ENT_QUOTES);

		// Set alias
		if (trim($this->alias) == '')
		{
			$this->alias = $this->title;
		}

		$this->alias = ApplicationHelper::stringURLSafe($this->alias, $this->language);

		if (trim(str_replace('-', '', $this->alias)) == '')
		{
			$this->alias = Factory::getDate()->format('Y-m-d-H-i-s');
		}

		// Set modified to null date if not set
		if (!$this->modified)
		{
			$this->modified = $this->getDbo()->getNullDate();
		}

		// Set publish_up to null date if not set
		if (!$this->publish_up)
		{
			$this->publish_up = $this->getDbo()->getNullDate();
		}

		// Set publish_down to null date if not set
		if (!$this->publish_down)
		{
			$this->publish_down = $this->getDbo()->getNullDate();
		}

		// Check the publish down date is not earlier than publish up.
		if ($this->publish_down > $this->getDbo()->getNullDate() && $this->publish_down < $this->publish_up)
		{
			// Swap the dates.
			$temp               = $this->publish_up;
			$this->publish_up   = $this->publish_down;
			$this->publish_down = $temp;
		}

		// Make sure audio- and videofilesize are integers.
		$this->audiofilesize = (int) $this->audiofilesize;
		$this->videofilesize = (int) $this->videofilesize;

		// Clean up keywords -- eliminate extra spaces between phrases
		// and cr (\r) and lf (\n) characters from string
		if (!empty($this->metakey))
		{
			// Only process if not empty

			// Array of characters to remove
			$bad_characters = array("\n", "\r", "\"", '<', '>');

			// Remove bad characters
			$after_clean = StringHelper::str_ireplace($bad_characters, '', $this->metakey);

			// Create array using commas as delimiter
			$keys = explode(',', $after_clean);

			$clean_keys = array();

			foreach ($keys as $key)
			{
				if (trim($key))
				{
					// Ignore blank keywords
					$clean_keys[] = trim($key);
				}
			}

			// Put array back together delimited by ", "
			$this->metakey = implode(', ', $clean_keys);
		}

		return true;
	}

	/**
	 * Overloaded bind function to pre-process the params.
	 *
	 * @param   array   $array
	 * @param   string  $ignore
	 *
	 * @return null|string null is operation was satisfactory, otherwise returns an error
	 *
	 * @see      Table::bind
	 *
	 * @since    1.5
	 */
	public function bind($array, $ignore = '')
	{
		if (isset($array['metadata']) && is_array($array['metadata']))
		{
			$registry          = new Registry($array['metadata']);
			$array['metadata'] = (string) $registry;
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Method to store a row in the database from the Table instance properties.
	 *
	 * If a primary key value is set the row with that primary key value will be updated with the instance property
	 * values. If no primary key value is set a new row will be inserted into the database with the properties from the
	 * Table instance.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return bool True on success.
	 *
	 * @throws \Exception
	 * @since   ?
	 */
	public function store($updateNulls = false)
	{
		$date = Factory::getDate();
		$user = Factory::getUser();

		if ($this->id)
		{
			$this->modified    = $date->toSql();
			$this->modified_by = $user->id;
		}
		else
		{
			if (!(int) $this->created)
			{
				if ($this->sermon_date == '')
				{
					$this->sermon_date = HTMLHelper::date($date, 'Y-m-d H:i:s', 'UTC');
				}

				$this->created = $date->toSql();
			}

			if (empty($this->created_by))
			{
				$this->created_by = $user->id;
			}
		}

		if (empty($this->speaker_id))
		{
			$this->speaker_id = 0;
		}

		if (empty($this->series_id))
		{
			$this->series_id = 0;
		}

		// Verify that the alias is unique
		$table = Table::getInstance('Sermon', 'SermonspeakerTable');

		if ($table->load(array('alias' => $this->alias, 'catid' => $this->catid)) && ($table->id != $this->id || $this->id == 0))
		{
			$this->setError(Text::_('COM_SERMONSPEAKER_ERROR_ALIAS'));

			return false;
		}

		return parent::store($updateNulls);
	}

	/**
	 * Method to set the podcast state for a row or list of rows in the database table.
	 *
	 * The method respects checked out rows by other users and will attempt to checkin rows that it can after
	 * adjustments are made.
	 *
	 * @param   mixed    $pks     An optional array of primary key values to update. If not set the instance property
	 *                            value is used.
	 * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param   integer  $userId  The user ID of the user performing the operation.
	 *
	 * @return  boolean  True on success; false if $pks is empty.
	 *
	 * @since   ?
	 */
	public function podcast($pks = null, $state = 1, $userId = 0)
	{
		// Initialise variables.
		$k = $this->_tbl_key;
		// Sanitize input.
		$pks    = ArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state  = (int) $state;
		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k)
			{
				$pks = array($this->$k);
			}
			// Nothing to set podcasting state on, return false.
			else
			{
				$this->setError(Text::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));

				return false;
			}
		}
		// Build the WHERE clause for the primary keys.
		$where = $k . '=' . implode(' OR ' . $k . '=', $pks);
		// Determine if there is checkin support for the table.
		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time'))
		{
			$checkin = '';
		}
		else
		{
			$checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
		}
		// Update the podcasting state for rows with the given primary keys.
		$this->getDbo()->setQuery(
			'UPDATE `' . $this->_tbl . '`' .
			' SET `podcast` = ' . (int) $state .
			' WHERE (' . $where . ')' .
			$checkin
		);

		$this->getDbo()->execute();

		try
		{
			$this->getDbo()->execute();

		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'ERROR');

			return false;
		}

		// If checkin is supported and all rows were adjusted, check them in.
		if ($checkin && (count($pks) == $this->getDbo()->getAffectedRows()))
		{
			// Checkin the rows.
			foreach ($pks as $pk)
			{
				$this->checkIn($pk);
			}
		}
		// If the Table instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks))
		{
			$this->state = $state;
		}
		$this->setError('');

		return true;
	}

	/**
	 * Get the type alias for UCM features
	 *
	 * @return  string  The alias as described above
	 *
	 * @since   4.0.0
	 */
	public function getTypeAlias()
	{
		return $this->typeAlias;
	}
}
