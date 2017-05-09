<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;

/**
 * Serie Table class
 *
 * @package  Sermonspeaker.Administrator
 *
 * @since    ?
 */
class SermonspeakerTableSerie extends Table
{
	/**
	 * Array with alias for "special" columns such as ordering, hits etc etc
	 *
	 * @var    array
	 * @since  6.0.0
	 */
	protected $_columnAlias = array('published' => 'state');

	/**
	 * The UCM type alias. Used for tags, content versioning etc. Leave blank to effectively disable these features.
	 *
	 * @var    string
	 * @since  6.0.0
	 */
	public $typeAlias = 'com_sermonspeaker.serie';

	/**
	 * Constructor
	 *
	 * @param  JDatabaseDriver $db JDatabaseDriver object.
	 *
	 * @since ?
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__sermon_series', 'id', $db);
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
		catch (\Exception $e)
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
			$this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
		}

		// Set publish_up to null date if not set
		if (!$this->publish_up)
		{
			$this->publish_up = $this->_db->getNullDate();
		}

		// Set publish_down to null date if not set
		if (!$this->publish_down)
		{
			$this->publish_down = $this->_db->getNullDate();
		}

		// Check the publish down date is not earlier than publish up.
		if ($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up)
		{
			// Swap the dates.
			$temp               = $this->publish_up;
			$this->publish_up   = $this->publish_down;
			$this->publish_down = $temp;
		}

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
	 * @param array  $array
	 * @param string $ignore
	 *
	 * @return null|string null is operation was satisfactory, otherwise returns an error
	 *
	 * @see      JTable::bind
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
	 * @param   boolean $updateNulls True to update fields even if they are null.
	 *
	 * @return bool True on success.
	 *
	 * @throws \Exception
	 * @since   ?
	 */
	public function store($updateNulls = false)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		if ($this->id)
		{
			$this->modified    = $date->toSql();
			$this->modified_by = $user->id;
		}
		else
		{
			if (!(int) ($this->created))
			{
				$this->created = $date->toSql();
			}

			if (empty($this->created_by))
			{
				$this->created_by = $user->id;
			}
		}

		// Verify that the alias is unique
		$table = Table::getInstance('serie', 'SermonspeakerTable');

		if ($table->load(array('alias' => $this->alias, 'catid' => $this->catid)) && ($table->id != $this->id || $this->id == 0))
		{
			throw new Exception(JText::_('COM_SERMONSPEAKER_ERROR_ALIAS'));
		}

		return parent::store($updateNulls);
	}
}
