<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Plugin.Finder
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Table\Table;
use Joomla\Component\Finder\Administrator\Indexer\Adapter;
use Joomla\Component\Finder\Administrator\Indexer\Helper;
use Joomla\Component\Finder\Administrator\Indexer\Indexer;
use Joomla\Component\Finder\Administrator\Indexer\Result;
use Joomla\Database\DatabaseQuery;
use Joomla\Registry\Registry;

/**
 * Finder adapter for SermonSpeaker.
 *
 * @since  1.0
 */
class PlgFinderSermonspeaker extends Adapter
{
	/**
	 * The plugin identifier.
	 *
	 * @var  string
	 *
	 * @since ?
	 */
	protected $context = 'Sermonspeaker';

	/**
	 * The extension name.
	 *
	 * @var  string
	 *
	 * @since ?
	 */
	protected $extension = 'com_sermonspeaker';

	/**
	 * The sublayout to use when rendering the results.
	 *
	 * @var  string
	 *
	 * @since ?
	 */
	protected $layout = 'sermon';

	/**
	 * The type of content that the adapter indexes.
	 *
	 * @var  string
	 *
	 * @since ?
	 */
	protected $type_title = 'SermonSpeaker';

	/**
	 * The table name.
	 *
	 * @var  string
	 *
	 * @since ?
	 */
	protected $table = '#__sermon_sermons';

	/**
	 * The category of an item before save.
	 *
	 * @var    integer
	 * @since  6.0.1
	 */
	protected $old_category;

	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  6.0.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * valid SermonSpeaker Contexts
	 *
	 * @var    array
	 * @since  6.0.1
	 */
	private $validContexts = [
		'com_sermonspeaker.sermon',
		'com_sermonspeaker.frontendupload',
		'com_sermonspeaker.serie',
		'com_sermonspeaker.serieform',
		'com_sermonspeaker.speaker',
		'com_sermonspeaker.speakerform',
	];

	/**
	 * SermonSpeaker Types
	 *
	 * @var    array
	 * @since  6.0.1
	 */
	private $sermonspeakerTypes = [
		'Sermons'  => 'sermon',
		'Series'   => 'serie',
		'Speakers' => 'speaker',
	];

	/**
	 * Method to update the item link information when the item category is
	 * changed. This is fired when the item category is published or unpublished
	 * from the list view.
	 *
	 * @param   string   $extension  The extension whose category has been updated.
	 * @param   array    $pks        A list of primary key ids of the content that has changed state.
	 * @param   integer  $value      The value of the state that the content has been changed to.
	 *
	 * @return  void
	 *
	 * @since ?
	 */
	public function onFinderCategoryChangeState($extension, $pks, $value)
	{
		// Make sure we're handling com_sermonspeaker categories
		if ($extension == 'com_sermonspeaker')
		{
			$this->categoryStateChange($pks, $value);
		}
	}

	/**
	 * Method to remove the link information for items that have been deleted.
	 *
	 * @param   string  $context  The context of the action being performed.
	 * @param   JTable  $table    A JTable object containing the record to be deleted
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  Exception on database error.
	 *
	 * @since ?
	 */
	public function onFinderAfterDelete($context, $table)
	{
		if ($context == 'com_sermonspeaker.sermon')
		{
			$id = $table->id;
		}
		elseif ($context == 'com_finder.index')
		{
			$id = $table->link_id;
		}
		else
		{
			return true;
		}

		// Remove the items.
		return $this->remove($id);
	}

	/**
	 * Method to determine if the access level of an item changed.
	 *
	 * @param   string   $context  The context of the content passed to the plugin.
	 * @param   Table    $row      A JTable object
	 * @param   boolean  $isNew    If the content has just been created
	 *
	 * @return  void
	 *
	 * @throws  Exception on database error.
	 * @since   2.5
	 */
	public function onFinderAfterSave($context, $row, $isNew): void
	{
		// We only want to handle sermons here. We need to handle front end and back end editing.
		if (in_array($context, $this->validContexts))
		{
			// Check if the access levels are different.
			if (!$isNew && $this->old_category != $row->catid)
			{
				// Process the change.
				$this->itemAccessChange($row);
			}

			// Reindex the item
			$this->reindex($row->id);
		}

		// Check for access changes in the category
		if ($context == 'com_categories.category')
		{
			// Check if the access levels are different
			if (!$isNew && $this->old_cataccess != $row->access)
			{
				$this->categoryAccessChange($row);
			}
		}

		return;
	}

	/**
	 * Method to reindex the link information for an item that has been saved.
	 * This event is fired before the data is actually saved so we are going
	 * to queue the item to be indexed later.
	 *
	 * @param   string   $context  The context of the content passed to the plugin.
	 * @param   Table    $row      A Table object
	 * @param   boolean  $isNew    If the content is just about to be created
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  Exception on database error.
	 * @since ?
	 */
	public function onFinderBeforeSave($context, $row, $isNew)
	{
		// Check for access levels from the category
		if (in_array($context, $this->validContexts))
		{
			$this->adjustType($context);

			// Query the database for the old access level if the item isn't new.
			if (!$isNew)
			{
				$this->checkCategory($row);
			}
		}

		// Check for access levels from the category.
		if ($context === 'com_categories.category')
		{
			// Query the database for the old access level if the item isn't new.
			if (!$isNew)
			{
				$this->checkCategoryAccess($row);
			}
		}

		return true;
	}

	/**
	 * @param   string  $context
	 *
	 *
	 * @since version
	 */
	private function adjustType(string $context): void
	{
		// Adjust to context
		if ($context == 'com_sermonspeaker.sermon' || $context == 'com_sermonspeaker.frontendupload')
		{
			$this->layout     = 'sermon';
			$this->table      = '#__sermon_sermons';
			$this->type_title = 'Sermons';
		}

		if ($context == 'com_sermonspeaker.serie' || $context == 'com_sermonspeaker.serieform')
		{
			$this->layout     = 'serie';
			$this->table      = '#__sermon_series';
			$this->type_title = 'Series';
		}

		if ($context == 'com_sermonspeaker.speaker' || $context == 'com_sermonspeaker.speakerform')
		{
			$this->layout     = 'speaker';
			$this->table      = '#__sermon_speakers';
			$this->type_title = 'Speakers';
		}
	}

	/**
	 * Method to check the existing category (cause of its access level) for items
	 *
	 * @param   Table  $row  A Table object
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	protected function checkCategory($row)
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('catid'))
			->from($this->db->quoteName($this->table))
			->where($this->db->quoteName('id') . ' = ' . (int) $row->id);
		$this->db->setQuery($query);

		// Store the access level to determine if it changes
		$this->old_category = $this->db->loadResult();
	}

	/**
	 * Method to update the link information for items that have been changed
	 * from outside the edit screen. This is fired when the item is published,
	 * unpublished, archived, or unarchived from the list view.
	 *
	 * @param   string   $context  The context for the content passed to the plugin.
	 * @param   array    $pks      A list of primary key ids of the content that has changed state.
	 * @param   integer  $value    The value of the state that the content has been changed to.
	 *
	 * @return  void
	 * @since ?
	 */
	public function onFinderChangeState($context, $pks, $value)
	{
		// We only want to handle SermonSpeaker items here
		if (in_array($context, $this->validContexts))
		{
			$this->adjustType($context);

			$this->itemStateChange($pks, $value);
		}

		// Handle when the plugin is disabled
		if ($context == 'com_plugins.plugin' && $value === 0)
		{
			$this->pluginDisable($pks);
		}
	}

	/**
	 * Method to get the adapter state and push it into the indexer.
	 *
	 * @return  void
	 *
	 * @throws  Exception on error.
	 * @since   2.5
	 */
	public function onStartIndex()
	{
		// Get the indexer state.
		$iState = Indexer::getState();

		foreach ($this->sermonspeakerTypes as $title => $type)
		{
			$this->adjustType('com_sermonspeaker.' . $type);

			// Get the number of content items.
			$total = (int) $this->getContentCount();

			// Add the content count to the total number of items.
			$iState->totalItems += $total;

			// Populate the indexer state information for the adapter.
			$iState->pluginState[$this->context . '.' . $title]['total']  = $total;
			$iState->pluginState[$this->context . '.' . $title]['offset'] = 0;
		}

		// Set the indexer state.
		Indexer::setState($iState);
	}

	/**
	 * Method to prepare for the indexer to be run. This method will often
	 * be used to include dependencies and things of that nature.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  Exception on error.
	 * @since   2.5
	 */
	public function onBeforeIndex()
	{
		// Get the indexer and adapter state.
		$iState = Indexer::getState();
		$count  = 0;

		foreach ($this->sermonspeakerTypes as $title => $type)
		{
			$aState = $iState->pluginState[$this->context . '.' . $title];

			// Check the progress of the indexer and the adapter.
			if ($iState->batchOffset == $iState->batchSize || $aState['offset'] == $aState['total'])
			{
				$count++;
			}
		}

		if ($count === 3)
		{
			return true;
		}

		// Run the setup method.
		return $this->setup();
	}

	/**
	 * Method to setup the indexer to be run.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since ?
	 */
	protected function setup()
	{
		return true;
	}

	/**
	 * Method to index a batch of content items. This method can be called by
	 * the indexer many times throughout the indexing process depending on how
	 * much content is available for indexing. It is important to track the
	 * progress correctly so we can display it to the user.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  Exception on error.
	 * @since   2.5
	 */
	public function onBuildIndex()
	{
		// Get the indexer and adapter state.
		$iState = Indexer::getState();

		foreach ($this->sermonspeakerTypes as $title => $type)
		{
			$aState = $iState->pluginState[$this->context . '.' . $title];

			// Check the progress of the indexer and the adapter.
			if ($iState->batchOffset == $iState->batchSize || $aState['offset'] == $aState['total'])
			{
				continue;
			}

			// Get the batch offset and size.
			$offset = (int) $aState['offset'];
			$limit  = (int) ($iState->batchSize - $iState->batchOffset);

			// Get the content items to index.
			$this->adjustType('com_sermonspeaker.' . $type);
			$items = $this->getItems($offset, $limit);

			// Iterate through the items and index them.
			for ($i = 0, $n = count($items); $i < $n; $i++)
			{
				// Index the item.
				$this->index($items[$i]);

				// Adjust the offsets.
				$offset++;
				$iState->batchOffset++;
				$iState->totalItems--;
			}

			// Update the indexer state.
			$aState['offset']                                   = $offset;
			$iState->pluginState[$this->context . '.' . $title] = $aState;
			Indexer::setState($iState);
		}

		return true;
	}

	/**
	 * Method to index an item. The item must be a FinderIndexerResult object.
	 *
	 * @param   FinderIndexerResult  $item  The item to index as an FinderIndexerResult object.
	 *
	 * @return  void
	 *
	 * @throws  Exception on database error.
	 * @since ?
	 */
	protected function index(Result $item)
	{
		// Check if the extension is enabled
		if (ComponentHelper::isEnabled($this->extension) == false)
		{
			return;
		}

		// Load dependent classes.
		require_once JPATH_SITE . '/components/com_sermonspeaker/helpers/route.php';

		$item->context = 'com_sermonspeaker.' . $this->layout;

		$item->metadata = new Registry($item->metadata);

		// Trigger the onContentPrepare event.
		$item->summary = Helper::prepareContent($item->summary);

		// Create a URL as identifier to recognise items again.
		$item->url = $this->getURL($item->id, $this->extension, $this->layout);

		// Build the necessary route and path information.
		$method      = 'get' . ucfirst($this->layout) . 'Route';
		$item->route = SermonspeakerHelperRoute::$method($item->slug, $item->catid, $item->language);

		// Add the metadata processing instructions.
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metakey');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metadesc');

		// Add the type taxonomy data.
		$item->addTaxonomy('Type', $this->type_title);

		// Add the author taxonomy data.
		if (!empty($item->author))
		{
			$item->addTaxonomy('Author', $item->author, $item->state);
		}

		// Add the category taxonomy data.
		$categories = Categories::getInstance('com_sermonspeaker.' . $this->layout . 's', ['published' => false, 'access' => false]);
		$category   = $categories->get($item->catid);
		$item->addNestedTaxonomy('Category', $category, $category->published, $category->access, $category->language);

		// Add the language taxonomy data.
		$item->addTaxonomy('Language', $item->language);

		// Get content extras.
		Helper::getContentExtras($item);

		// Index the item.
		$this->indexer->index($item);
	}

	/**
	 * Method to get a SQL query to load the published and access states for
	 * an items and category.
	 * Needed because we don't use item access
	 *
	 * @return  DatabaseQuery  A database object.
	 *
	 * @since   5.0.3
	 */
	protected function getStateQuery()
	{
		$query = $this->db->getQuery(true);

		// Item ID
		$query->select('a.id');

		// Item and category published state
		$query->select('a.' . $this->state_field . ' AS state, c.published AS cat_state');

		// Item and category access levels
		$query->select('c.access AS access, c.access AS cat_access')
			->from($this->table . ' AS a')
			->join('LEFT', '#__categories AS c ON c.id = a.catid');

		return $query;
	}

	/**
	 * Method to get the SQL query used to retrieve the list of content items.
	 *
	 * @param   mixed  $query  A DatabaseQuery object or null.
	 *
	 * @return  DatabaseQuery  A database object.
	 *
	 * @since   2.5
	 */
	protected function getListQuery($query = null)
	{
		$params = ComponentHelper::getParams('com_sermonspeaker');
		$access = $params->get('access', 1);

		// Check if we can use the supplied SQL query.
		$query = $query instanceof DatabaseQuery ? $query : $this->db->getQuery(true)
			->select('a.id, a.title, a.alias')
			->select('a.state, a.catid, a.created AS start_date, a.created_by')
			->select('a.metakey, a.metadesc, ' . (int) $access . ' AS access, a.version, a.ordering')
			->select('a.publish_up AS publish_start_date, a.publish_down AS publish_end_date')
			->select('c.title AS category, c.published AS cat_state, c.access AS cat_access');

		switch ($this->layout)
		{
			case 'sermon':
				$query->select('a.picture AS image, a.notes AS summary');
				break;
			case 'serie':
				$query->select('a.avatar AS image, a.series_description AS summary');
				break;
			case 'speaker':
				$query->select('a.pic AS image, a.intro AS summary, a.bio AS body');
				break;
		}

		// Handle the alias CASE WHEN portion of the query
		$case_when_item_alias = ' CASE WHEN ';
		$case_when_item_alias .= $query->charLength('a.alias', '!=', '0');
		$case_when_item_alias .= ' THEN ';
		$a_id                 = $query->castAsChar('a.id');
		$case_when_item_alias .= $query->concatenate(array($a_id, 'a.alias'), ':');
		$case_when_item_alias .= ' ELSE ';
		$case_when_item_alias .= $a_id . ' END as slug';
		$query->select($case_when_item_alias);

		$case_when_category_alias = ' CASE WHEN ';
		$case_when_category_alias .= $query->charLength('c.alias', '!=', '0');
		$case_when_category_alias .= ' THEN ';
		$c_id                     = $query->castAsChar('c.id');
		$case_when_category_alias .= $query->concatenate(array($c_id, 'c.alias'), ':');
		$case_when_category_alias .= ' ELSE ';
		$case_when_category_alias .= $c_id . ' END as catslug';
		$query->select($case_when_category_alias);

		$query->select('u.name AS author')
			->from($this->db->quoteName($this->table) . ' AS a')
			->join('LEFT', '#__categories AS c ON c.id = a.catid')
			->join('LEFT', '#__users AS u ON u.id = a.created_by');

		return $query;
	}

	/**
	 * Method to get the query clause for getting items to update by time.
	 *
	 * @param   string  $time  The modified timestamp.
	 *
	 * @return  JDatabaseQuery  A database object.
	 *
	 * @since   2.5
	 */
	protected function getUpdateQueryByTime($time)
	{
		// Build an SQL query based on the modified time.
		// We don't have a modified time, so we just give the query back unchanged.
		return $this->db->getQuery(true);
	}
}
