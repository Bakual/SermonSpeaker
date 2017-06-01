<?php
/**
 * Scriptfile for the SermonSpeaker installation
 *
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Installer\InstallerScript;
use Joomla\CMS\Table\Table;

/**
 * Class Com_SermonspeakerInstallerScript
 *
 * @since  4.x
 */
class Com_SermonspeakerInstallerScript extends InstallerScript
{
	/**
	 * The extension name. This should be set in the installer script.
	 *
	 * @var    string
	 * @since  5.4.0
	 */
	protected $extension = 'com_sermonspeaker';
	/**
	 * Minimum PHP version required to install the extension
	 *
	 * @var    string
	 * @since  5.4.0
	 */
	protected $minimumPhp = '5.6.0';
	/**
	 * Minimum Joomla! version required to install the extension
	 *
	 * @var    string
	 * @since  6.0.0
	 */
	protected $minimumJoomla = '4.0.0-dev';
	/**
	 * @var  Joomla\CMS\Application\CMSApplication  Holds the application object
	 *
	 * @since ?
	 */
	private $app;
	/**
	 * @var  string  During an update, it will be populated with the old release version
	 *
	 * @since ?
	 */
	private $oldRelease;

	/**
	 *  Constructor
	 *
	 * @since ?
	 */
	public function __construct()
	{
		$this->app = JFactory::getApplication();
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @param   string                                        $type   'install', 'update' or 'discover_install'
	 * @param   Joomla\CMS\Installer\Adapter\ComponentAdapter $parent Installerobject
	 *
	 * @return  boolean  false will terminate the installation
	 *
	 * @since ?
	 */
	public function preflight($type, $parent)
	{
		// Storing old release number for process in postflight
		if (strtolower($type) == 'update')
		{
			$manifest         = $this->getItemArray('manifest_cache', '#__extensions', 'element', JFactory::getDbo()->quote($this->extension));
			$this->oldRelease = $manifest['version'];

			// Check if update is allowed (only update from 5.6.0 and higher)
			if (version_compare($this->oldRelease, '5.6.0', '<'))
			{
				$this->app->enqueueMessage(JText::sprintf('COM_SERMONSPEAKER_UPDATE_UNSUPPORTED', $this->oldRelease, '5.6.0'), 'error');

				return false;
			}
		}

		return parent::preflight($type, $parent);
	}

	/**
	 * Method to install the component
	 *
	 * @param   Joomla\CMS\Installer\Adapter\ComponentAdapter $parent Installerobject
	 *
	 * @return void
	 *
	 * @since ?
	 */
	public function install($parent)
	{
		// Notice $parent->getParent() returns JInstaller object
		$parent->getParent()->setRedirectUrl('index.php?option=com_sermonspeaker');
	}

	/**
	 * Method to uninstall the component
	 *
	 * @param   Joomla\CMS\Installer\Adapter\ComponentAdapter $parent Installerobject
	 *
	 * @return void
	 *
	 * @since ?
	 */
	public function uninstall($parent)
	{
	}

	/**
	 * method to update the component
	 *
	 * @param   Joomla\CMS\Installer\Adapter\ComponentAdapter $parent Installerobject
	 *
	 * @return void
	 *
	 * @since ?
	 */
	public function update($parent)
	{
		if (version_compare($this->oldRelease, '6.0.0', '<'))
		{
			// Remove integrated player classes
			$this->deleteFiles[]   = '/administrator/components/com_sermonspeaker/models/fields/player.php';
			$this->deleteFolders[] = '/components/com_sermonspeaker/helpers/player';

			// Remove old SQL files
			$this->deleteFiles[]   = '/administrator/components/com_sermonspeaker/sql/updates/mysql/4.5.0.sql';
			$this->deleteFiles[]   = '/administrator/components/com_sermonspeaker/sql/updates/mysql/4.5.1.sql';
			$this->deleteFiles[]   = '/administrator/components/com_sermonspeaker/sql/updates/mysql/4.5.2.sql';
			$this->deleteFiles[]   = '/administrator/components/com_sermonspeaker/sql/updates/mysql/4.5.3.sql';
			$this->deleteFiles[]   = '/administrator/components/com_sermonspeaker/sql/updates/mysql/4.5.4.sql';
			$this->deleteFiles[]   = '/administrator/components/com_sermonspeaker/sql/updates/mysql/5.0.0.sql';
			$this->deleteFiles[]   = '/administrator/components/com_sermonspeaker/sql/updates/mysql/5.0.1.sql';
			$this->deleteFiles[]   = '/administrator/components/com_sermonspeaker/sql/updates/mysql/5.0.2.sql';
			$this->deleteFiles[]   = '/administrator/components/com_sermonspeaker/sql/updates/mysql/5.0.3.sql';
			$this->deleteFiles[]   = '/administrator/components/com_sermonspeaker/sql/updates/mysql/5.0.4.sql';
			$this->deleteFiles[]   = '/administrator/components/com_sermonspeaker/sql/updates/mysql/5.4.0.sql';
			$this->deleteFiles[]   = '/administrator/components/com_sermonspeaker/sql/updates/mysql/5.5.0.sql';
		}
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @param   string                                        $type   'install', 'update' or 'discover_install'
	 * @param   Joomla\CMS\Installer\Adapter\ComponentAdapter $parent Installerobject
	 *
	 * @return void
	 *
	 * @since ?
	 */
	public function postflight($type, $parent)
	{
		$type = strtolower($type);

		if ($type == 'install' || $type == 'discover_install')
		{
			// Adding Category "Uncategorised" if installing or discovering.
			$this->addCategory();

			// Adding ContentTypes
			$this->saveContentTypes();

			// Setting some default values for columns on install
			$params                = array();
			$params['col']         = '"col":['
				. '"sermons:scripture","sermons:speaker","sermons:date","sermons:series","sermons:player"'
				. ',"sermon:scripture","sermon:speaker","sermon:date","sermon:series","sermon:player","sermon:notes","sermon:addfile"'
				. ',"serie:scripture","serie:speaker","serie:date","serie:player"'
				. ',"speaker:scripture","speaker:date","speaker:series","speaker:player"'
				. ',"seriessermon:scripture","seriessermon:speaker","seriessermon:date"'
				. ']';
			$params['col_serie']   = '"col_serie":['
				. '"series:speaker"'
				. ',"serie:description","serie:speaker"'
				. ',"speaker:description"'
				. ',"seriessermon:description","seriessermon:speaker"'
				. ']';
			$params['col_speaker'] = '"col_speaker":["speakers:bio","speaker:bio","speaker:intro"]';

			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__extensions'));
			$query->set($db->quoteName('params') . ' = ' . $db->quote('{' . implode(',', $params) . '}'));
			$query->where($db->quoteName('name') . ' = ' . $db->quote('com_sermonspeaker'));
			$db->setQuery($query);
			$db->execute();
		}

		if ($type == 'update')
		{
			// Migrate categories to new section based ones
			$this->moveCategories();
		}

		$this->removeFiles();
		$this->app->enqueueMessage(JText::_('COM_SERMONSPEAKER_POSTFLIGHT'), 'warning');
	}


	/**
	 * Method to add a default category "Uncategorised"
	 *
	 * @return void
	 *
	 * @since ?
	 */
	private function addCategory()
	{
		$db         = JFactory::getDbo();
		$catFactory = new Joomla\CMS\Mvc\Factory\MvcFactory('Joomla\Component\Categories', $this->app);
		$catModel   = new Joomla\Component\Categories\Administrator\Model\Category(array(), $catFactory);
		$sections   = array('sermons', 'series', 'speakers');

		foreach ($sections as $section)
		{
			$catData = array(
				'id'          => 0,
				'parent_id'   => 0,
				'level'       => 1,
				'path'        => 'uncategorised',
				'extension'   => 'com_sermonspeaker.' . $section,
				'title'       => 'Uncategorised',
				'alias'       => 'uncategorised',
				'description' => '',
				'published'   => 1,
				'language'    => '*',
				'params'      => '',
			);
			$catModel->save($catData);
			$id = $catModel->getItem()->id;

			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__sermon_' . $section));
			$query->set($db->quoteName('catid') . ' = ' . (int) $id);
			$query->where($db->quoteName('catid') . ' = 0');
			$db->setQuery($query);
			$db->execute();
		}

		return;
	}

	private function saveContentTypes()
	{
		// Adding content_type for tags
		$table = Table::getInstance('Contenttype', 'JTable');

		// Generic FieldMappings
		$common                       = new stdClass;
		$common->core_content_item_id = 'id';
		$common->core_title           = 'title';
		$common->core_state           = 'state';
		$common->core_alias           = 'alias';
		$common->core_created_time    = 'created';
		$common->core_modified_time   = 'modified';
		$common->core_body            = 'notes';
		$common->core_hits            = 'hits';
		$common->core_publish_up      = null;
		$common->core_publish_down    = null;
		$common->core_access          = null;
		$common->core_params          = null;
		$common->core_featured        = null;
		$common->core_metadata        = null;
		$common->core_language        = 'language';

		// Does this one work?
		$common->core_images          = 'picture';
		$common->core_urls            = null;
		$common->core_version         = 'version';
		$common->core_ordering        = 'ordering';
		$common->core_metakey         = 'metakey';
		$common->core_metadesc        = 'metadesc';
		$common->core_catid           = 'catid';
		$common->core_xreference      = null;
		$common->asset_id             = null;
		$field_mappings               = new stdClass;
		$field_mappings->common       = $common;
		$field_mappings->special      = new stdClass;
		$history                      = new stdClass;
		$history->form_file           = 'administrator/components/com_sermonspeaker/models/forms/sermon.xml';
		$history->hide_fields         = array('checked_out', 'checked_out_time', 'version');
		$history->display_lookup      = array();
		$source_user1                 = new stdClass;
		$source_user1->source_column  = 'created_by';
		$source_user1->target_table   = '#__users';
		$source_user1->target_column  = 'id';
		$source_user1->display_column = 'name';
		$source_user2                 = clone $source_user1;
		$source_user2->source_column  = 'modified_by';
		$source_catid                 = clone $source_user1;
		$source_catid->source_column  = 'catid';
		$source_catid->target_table   = '#__categories';
		$source_catid->display_column = 'title';
		$history->display_lookup[]    = $source_user1;
		$history->display_lookup[]    = $source_user2;
		$history->display_lookup[]    = $source_catid;

		// Create Sermon Type
		$table->load(array('type_alias' => 'com_sermonspeaker.sermon'));

		$special          = new stdClass;
		$special->dbtable = '#__sermon_sermons';
		$special->key     = 'id';
		$special->type    = 'Sermon';
		$special->prefix  = 'SermonspeakerTable';
		$special->config  = 'array()';

		$source_speaker                = clone $source_catid;
		$source_speaker->source_column = 'speaker_id';
		$source_speaker->target_table  = '#__sermon_speakers';
		$source_serie                  = clone $source_catid;
		$source_serie->source_column   = 'series_id';
		$source_serie->target_table    = '#__sermon_series';

		$history->form_file        = 'administrator/components/com_sermonspeaker/models/forms/sermon.xml';
		$history->display_lookup[] = $source_speaker;
		$history->display_lookup[] = $source_serie;

		$table_object          = new stdClass;
		$table_object->special = $special;

		$contenttype['type_id']                 = ($table->type_id) ? $table->type_id : 0;
		$contenttype['type_title']              = 'Sermon';
		$contenttype['type_alias']              = 'com_sermonspeaker.sermon';
		$contenttype['table']                   = json_encode($table_object);
		$contenttype['rules']                   = '';
		$contenttype['router']                  = 'SermonspeakerHelperRoute::getSermonRoute';
		$contenttype['field_mappings']          = json_encode($field_mappings);
		$contenttype['content_history_options'] = json_encode($history);

		$table->save($contenttype);

		// Create Speaker Type
		$table->type_id = 0;
		$table->load(array('type_alias' => 'com_sermonspeaker.speaker'));

		$field_mappings->common->core_body   = 'bio';
		$field_mappings->common->core_images = 'pic';

		$special          = new stdClass;
		$special->dbtable = '#__sermon_speakers';
		$special->key     = 'id';
		$special->type    = 'Speaker';
		$special->prefix  = 'SermonspeakerTable';
		$special->config  = 'array()';

		$history->form_file = 'administrator/components/com_sermonspeaker/models/forms/speaker.xml';

		$table_object          = new stdClass;
		$table_object->special = $special;

		$contenttype['type_id']                 = ($table->type_id) ? $table->type_id : 0;
		$contenttype['type_title']              = 'Speaker';
		$contenttype['type_alias']              = 'com_sermonspeaker.speaker';
		$contenttype['table']                   = json_encode($table_object);
		$contenttype['rules']                   = '';
		$contenttype['router']                  = 'SermonspeakerHelperRoute::getSpeakerRoute';
		$contenttype['field_mappings']          = json_encode($field_mappings);
		$contenttype['content_history_options'] = json_encode($history);

		$table->save($contenttype);

		// Create Series Type
		$table->type_id = 0;
		$table->load(array('type_alias' => 'com_sermonspeaker.serie'));

		$field_mappings->common->core_body   = 'series_description';
		$field_mappings->common->core_images = 'avatar';

		$special          = new stdClass;
		$special->dbtable = '#__sermon_series';
		$special->key     = 'id';
		$special->type    = 'Serie';
		$special->prefix  = 'SermonspeakerTable';
		$special->config  = 'array()';

		$history->form_file = 'administrator/components/com_sermonspeaker/models/forms/serie.xml';

		$table_object          = new stdClass;
		$table_object->special = $special;

		$contenttype['type_id']                 = ($table->type_id) ? $table->type_id : 0;
		$contenttype['type_title']              = 'Serie';
		$contenttype['type_alias']              = 'com_sermonspeaker.serie';
		$contenttype['table']                   = json_encode($table_object);
		$contenttype['rules']                   = '';
		$contenttype['router']                  = 'SermonspeakerHelperRoute::getSerieRoute';
		$contenttype['field_mappings']          = json_encode($field_mappings);
		$contenttype['content_history_options'] = json_encode($history);

		$table->save($contenttype);

		// Create/Update Category Type
		$table->type_id = 0;
		$table->load(array('type_alias' => 'com_sermonspeaker.category'));

		$field_mappings->common->core_state         = 'published';
		$field_mappings->common->core_created_time  = 'created_time';
		$field_mappings->common->core_modified_time = 'modified_time';
		$field_mappings->common->core_body          = 'description';
		$field_mappings->common->core_images        = null;
		$field_mappings->common->core_access        = 'access';
		$field_mappings->common->core_params        = 'params';
		$field_mappings->common->core_metadata      = 'metadata';
		$field_mappings->common->core_ordering      = null;
		$field_mappings->common->core_catid         = 'parent_id';
		$field_mappings->common->asset_id           = 'asset_id';
		$field_mappings->special->parent_id         = 'parent_id';
		$field_mappings->special->lft               = 'lft';
		$field_mappings->special->rgt               = 'rgt';
		$field_mappings->special->level             = 'level';
		$field_mappings->special->path              = 'path';
		$field_mappings->special->extension         = 'extension';
		$field_mappings->special->note              = 'note';

		$special          = new stdClass;
		$special->dbtable = '#__categories';
		$special->key     = 'id';
		$special->type    = 'Category';
		$special->prefix  = 'JTable';
		$special->config  = 'array()';

		$history                = new stdClass;
		$history->form_file     = 'administrator/components/com_categories/models/forms/category.xml';
		$history->hideFields    = array('asset_id', 'checked_out', 'checked_out_time', 'version', 'lft', 'rgt', 'level', 'path', 'extension');
		$history->ignoreChanges = array('modified_user_id', 'modified_time', 'checked_out', 'checked_out_time', 'version', 'hits', 'path');
		$history->convertToInt  = array('publish_up', 'publish_down');

		$displayLookup1                = new stdClass;
		$displayLookup1->sourceColumn  = 'created_user_id';
		$displayLookup1->targetTable   = '#__users';
		$displayLookup1->targetColumn  = 'id';
		$displayLookup1->displayColumn = 'name';
		$history->displayLookup[]      = $displayLookup1;

		$displayLookup2               = clone $displayLookup1;
		$displayLookup2->sourceColumn = 'modified_user_id';
		$displayLookup2->targetTable  = '#__users';
		$history->displayLookup[]     = $displayLookup2;

		$displayLookup3                = clone $displayLookup1;
		$displayLookup3->sourceColumn  = 'access';
		$displayLookup3->targetTable   = '#__viewlevels';
		$displayLookup3->displayColumn = 'title';
		$history->displayLookup[]      = $displayLookup3;

		$displayLookup4               = clone $displayLookup1;
		$displayLookup4->sourceColumn = 'parent_id';
		$displayLookup4->targetTable  = '#__categories';
		$history->displayLookup[]     = $displayLookup4;

		$table_object          = new stdClass;
		$table_object->special = $special;

		$contenttype['type_id']                 = ($table->type_id) ? $table->type_id : 0;
		$contenttype['type_title']              = 'SermonSpeaker Category';
		$contenttype['type_alias']              = 'com_sermonspeaker.category';
		$contenttype['table']                   = json_encode($table_object);
		$contenttype['rules']                   = '';
		$contenttype['router']                  = 'SermonspeakerHelperRoute::getSermonsRoute';
		$contenttype['field_mappings']          = json_encode($field_mappings);
		$contenttype['content_history_options'] = json_encode($history);

		$table->save($contenttype);

		return;
	}

	/**
	 * Method to move the categories to new section based ones.
	 *
	 * @return void
	 *
	 * @since 6.0.0
	 */
	private function moveCategories()
	{
		$db         = JFactory::getDbo();
		$catFactory = new Joomla\CMS\Mvc\Factory\MvcFactory('Joomla\Component\Categories', $this->app);
		$catModel   = new Joomla\Component\Categories\Administrator\Model\Category(array(), $catFactory);
		$sections   = array('sermons', 'series', 'speakers');

		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__categories')
			->where($db->quoteName('extension') . ' = ' . $db->quote('com_sermonspeaker'));
		$db->setQuery($query);
		$categories = $db->loadAssocList();

		foreach ($categories as $category)
		{
			$oldId     = $category['id'];
			$parentIds = array('sermons', 'series', 'speakers');

			foreach ($sections as $section)
			{
				if (isset($parentIds[$section][$category['parent_id']]))
				{
					$category['parent_id'] = $parentIds[$section][$oldId];
				}

				$category['id']        = 0;
				$category['extension'] = 'com_sermonspeaker.' . $section;

				try
				{
					$catModel->save($category);
				}
				catch (Exception $e)
				{
					$this->app->enqueueMessage($e->getMessage(), 'ERROR');
				}

				$newId = $catModel->getItem()->id;

				$parentIds[$section][$oldId] = $newId;

				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__sermon_' . $section));
				$query->set($db->quoteName('catid') . ' = ' . (int) $newId);
				$query->where($db->quoteName('catid') . ' = ' . $oldId);
				$db->setQuery($query);
				$db->execute();
			}

			try
			{
				// Trash the old category so it can be deleted
				$category['id']        = $oldId;
				$category['published'] = -2;
				$category['extension'] = 'com_sermonspeaker';
				$catModel->save($category);
				$catModel->delete($oldId);
			}
			catch (Exception $e)
			{
				$this->app->enqueueMessage($e->getMessage(), 'ERROR');
			}
		}

		return;
	}
}
