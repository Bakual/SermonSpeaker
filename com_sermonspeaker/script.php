<?php
/**
 * Scriptfile for the SermonSpeaker installation
 *
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   2014 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Class Com_SermonspeakerInstallerScript
 *
 * @since  4.x
 */
class Com_SermonspeakerInstallerScript
{
	/**
	 * @var  JApplicationCms  Holds the application object
	 */
	private $app;

	/**
	 * @var  string  During an update, it will be populated with the old release version
	 */
	private $oldRelease;

	/**
	 *  Constructor
	 */
	public function __construct()
	{
		$this->app = JFactory::getApplication();
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @param   string                      $type    'install', 'update' or 'discover_install'
	 * @param   JInstallerAdapterComponent  $parent  Installerobject
	 *
	 * @return  boolean  false will terminate the installation
	 */
	public function preflight($type, $parent)
	{
		$min_version = (string) $parent->get('manifest')->attributes()->version;

		$jversion = new JVersion;

		if (!$jversion->isCompatible($min_version))
		{
			$this->app->enqueueMessage(JText::sprintf('COM_SERMONSPEAKER_VERSION_UNSUPPORTED', $min_version), 'error');

			return false;
		}

		// Storing old release number for process in postflight
		if ($type == 'update')
		{
			$this->oldRelease = $this->getParam('version');

			// Check if update is allowed (only update from 4.5.0 and higher)
			if (version_compare($this->oldRelease, '4.5.0', '<'))
			{
				$this->app->enqueueMessage(JText::sprintf('COM_SERMONSPEAKER_UPDATE_UNSUPPORTED', $this->oldRelease, '4.5.0'), 'error');
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to install the component
	 *
	 * @param   JInstallerAdapterComponent  $parent  Installerobject
	 *
	 * @return void
	 */
	public function install($parent)
	{
		// Notice $parent->getParent() returns JInstaller object
		/** @noinspection PhpUndefinedMethodInspection */
		$parent->getParent()->setRedirectURL('index.php?option=com_sermonspeaker');
	}

	/**
	 * Method to uninstall the component
	 *
	 * @param   JInstallerAdapterComponent  $parent  Installerobject
	 *
	 * @return void
	 */
	public function uninstall($parent)
	{
	}

	/**
	 * method to update the component
	 *
	 * @param   JInstallerAdapterComponent  $parent  Installerobject
	 *
	 * @return void
	 */
	public function update($parent)
	{
		if (version_compare($this->oldRelease, '5.0.0', '<'))
		{
			// Cleanup non-bootstrap layout files from old installations
			$files   = array();
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/views/files/tmpl/modal30.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/views/help/tmpl/default30.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/views/languages/tmpl/default30.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/views/main/tmpl/default30.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/views/scripture/tmpl/default30.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/views/serie/tmpl/edit30.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/views/serie/tmpl/modal30.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/views/series/tmpl/default_batch30.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/views/series/tmpl/default30.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/views/sermon/tmpl/edit30.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/views/sermons/tmpl/default_batch30.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/views/sermons/tmpl/default30.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/views/sermons/tmpl/modal30.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/views/speaker/tmpl/edit30.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/views/speaker/tmpl/modal30.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/views/speakers/tmpl/default_batch30.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/views/speakers/tmpl/default30.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/views/tools/tmpl/default30.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/frontendupload/tmpl/default30.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/scripture/tmpl/default30.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/serieform/tmpl/edit30.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/serieform/tmpl/modal30.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speakerform/tmpl/edit30.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speakerform/tmpl/modal30.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/serie/tmpl/default_filters30.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/serie/tmpl/default_filtersorder30.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/serie/tmpl/protostar-table.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/serie/tmpl/protostar-table.xml';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/serie/tmpl/protostar-list.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/serie/tmpl/protostar-list.xml';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/serie/tmpl/protostar-blog.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/serie/tmpl/protostar-blog.xml';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/series/tmpl/default_children30.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/series/tmpl/normal.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/series/tmpl/normal.xml';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/series/tmpl/protostar-table.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/series/tmpl/protostar-table.xml';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/series/tmpl/protostar-list.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/series/tmpl/protostar-list.xml';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/series/tmpl/protostar-blog.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/series/tmpl/protostar-blog.xml';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/sermons/tmpl/default_children30.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/sermons/tmpl/default_filters30.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/sermons/tmpl/default_filtersorder30.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/sermons/tmpl/tableless.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/sermons/tmpl/tableless.xml';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/sermons/tmpl/protostar-table.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/sermons/tmpl/protostar-table.xml';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/sermons/tmpl/protostar-list.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/sermons/tmpl/protostar-list.xml';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/sermons/tmpl/protostar-blog.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/sermons/tmpl/protostar-blog.xml';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speaker/tmpl/default_filters30.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speaker/tmpl/default_filtersorder30.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speaker/tmpl/series.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speaker/tmpl/series.xml';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speaker/tmpl/sermons.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speaker/tmpl/sermons.xml';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speaker/tmpl/popup30.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speaker/tmpl/protostar-table.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speaker/tmpl/protostar-table.xml';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speaker/tmpl/protostar-list.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speaker/tmpl/protostar-list.xml';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speaker/tmpl/protostar-blog.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speaker/tmpl/protostar-blog.xml';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speakers/tmpl/default_children30.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speakers/tmpl/normal.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speakers/tmpl/normal.xml';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speakers/tmpl/protostar-table.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speakers/tmpl/protostar-table.xml';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speakers/tmpl/protostar-list.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speakers/tmpl/protostar-list.xml';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speakers/tmpl/protostar-blog.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/views/speakers/tmpl/protostar-blog.xml';

			// Cleanup tag view as we're now using the core tags in J!3.1
			JFolder::delete(JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/views/tags');
			JFolder::delete(JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/views/tag');
			JFolder::delete(JPATH_SITE . '/components/com_sermonspeaker/views/tagform');
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/models/tags.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/models/tag.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/models/forms/tag.xml';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/models/fields/tag.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/models/fields/tagslist.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/tables/tag.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/controllers/tags.php';
			$files[] = JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/controllers/tag.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/models/tagform.php';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/models/forms/tag.xml';
			$files[] = JPATH_SITE . '/components/com_sermonspeaker/controllers/tagform.php';

			if (version_compare($this->oldRelease, '5.4.3', '<'))
			{
				JFolder::delete(JPATH_SITE . '/media/com_sermonspeaker/plupload');
			}

			JFile::delete($files);
		}
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @param   string                      $type    'install', 'update' or 'discover_install'
	 * @param   JInstallerAdapterComponent  $parent  Installerobject
	 *
	 * @return void
	 */
 	public function postflight($type, $parent)
	{
		// Adding Category "uncategorized" if installing or discovering.
		if ($type != 'update')
		{
			$this->_addCategory();
		}

		/* Adding ContentTypes
		 * needed in all cases for 5.0.4 to add content_history stuff.
		 * Only needs to run on install and updates from "< 5.0.4" afterwards.
		 * However no harm done when running always. */
		$this->_saveContentTypes();

		// Setting some default values for columns on install
		if ($type == 'install')
		{
			$params = array();
			$params['col'] = '"col":['
					. '"sermons:scripture","sermons:speaker","sermons:date","sermons:series","sermons:player"'
					. ',"sermon:scripture","sermon:speaker","sermon:date","sermon:series","sermon:player","sermon:notes","sermon:addfile"'
					. ',"serie:scripture","serie:speaker","serie:date","serie:player"'
					. ',"speaker:scripture","speaker:date","speaker:series","speaker:player"'
					. ',"seriessermon:scripture","seriessermon:speaker","seriessermon:date"'
				. ']';
			$params['col_serie'] = '"col_serie":['
					. '"series:speaker"'
					. ',"serie:description","serie:speaker"'
					. ',"speaker:description"'
					. ',"seriessermon:description","seriessermon:speaker"'
				. ']';
			$params['col_speaker'] = '"col_speaker":["speakers:bio","speaker:bio","speaker:intro"]';

			$db    = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__extensions'));
			$query->set($db->quoteName('params') . ' = ' . $db->quote('{' . implode(',', $params) . '}'));
			$query->where($db->quoteName('name') . ' = ' . $db->quote('com_sermonspeaker'));
			$db->setQuery($query);
			$db->execute();
		}

		// Migrate tags on update if table exists
		if ($type == 'update')
		{
			$db     = JFactory::getDBO();
			$tables = $db->getTableList();
			$prefix = $db->getPrefix();

			if (in_array($prefix . 'sermon_tags', $tables))
			{
				require_once(JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/tables/sermon.php');
				$sermontable = new SermonspeakerTableSermon($db);
				$query       = $db->getQuery(true);
				$query->select($db->quoteName('sermon_id'));
				$query->select('GROUP_CONCAT(CONCAT(' . $db->quote('#new#') . ',' . $db->quoteName('t.title') . ') SEPARATOR \',\') AS tagtitles');
				$query->from($db->quoteName('#__sermon_sermons_tags') . ' AS s');
				$query->join('LEFT', $db->quoteName('#__sermon_tags') . ' AS t ON ' . $db->quoteName('s.tag_id') . ' = ' . $db->quoteName('t.id'));
				$query->group($db->quoteName('sermon_id'));
				$db->setQuery($query);
				$result = $db->loadObjectList('sermon_id');

				foreach ($result as $sermon)
				{
					$sermontable->load($sermon->sermon_id);
					$sermontable->newTags = explode (',', $sermon->tagtitles);
					$sermontable->store();
				}

				$db->dropTable('#__sermon_tags');
				$db->dropTable('#__sermon_sermons_tags');
				$this->app->enqueueMessage(JText::sprintf('COM_SERMONSPEAKER_TAGS_MIGRATED', count($result)), 'notice');
			}
		}

		$this->app->enqueueMessage(JText::_('COM_SERMONSPEAKER_POSTFLIGHT'), 'warning');
	}


	/**
	 * Method to add a default category "uncategorized"
	 *
	 * @return void
	 */
	function _addCategory()
	{
		// Create categories for our component
		$basePath = JPATH_ADMINISTRATOR . '/components/com_categories';
		require_once $basePath . '/models/category.php';
		$config   = array('table_path' => $basePath.'/tables');
		$catmodel = new CategoriesModelCategory($config);
		$catData  = array('id' => 0, 'parent_id' => 0, 'level' => 1, 'path' => 'uncategorized', 'extension' => 'com_sermonspeaker',
					'title' => 'Uncategorized', 'alias' => 'uncategorized', 'description' => '', 'published' => 1, 'language' => '*');
		$catmodel->save($catData);
		$id = $catmodel->getItem()->id;

		$db = JFactory::getDBO();

		// Updating the example data with 'Uncategorized'
		$query = $db->getQuery(true);
		$query->update('#__sermon_sermons');
		$query->set('catid = '.(int)$id);
		$query->where('catid = 0');
		$db->setQuery($query);
		$db->execute();

		// Speakers
		$query->update('#__sermon_speakers');
		$db->setQuery($query);
		$db->execute();

		// Series
		$query->update('#__sermon_series');
		$db->setQuery($query);
		$db->execute();

		return;
	}

	function _saveContentTypes()
	{
		// Adding content_type for tags
		$table = JTable::getInstance('Contenttype', 'JTable');

		// Generic FieldMappings
		$common = new stdClass;
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
		$field_mappings          = new stdClass;
		$field_mappings->common  = $common;
		$field_mappings->special = new stdClass;
		$history                   = new stdClass;
		$history->form_file        = 'administrator/components/com_sermonspeaker/models/forms/sermon.xml';
		$history->hide_fields      = array('checked_out','checked_out_time','version');
		$history->display_lookup   = array();
		$source_user1                 = new stdClass;
		$source_user1->source_column  = 'created_by';
		$source_user1->target_table   = '#__users';
		$source_user1->target_column  = 'id';
		$source_user1->display_column = 'name';
		$source_user2                = clone $source_user1;
		$source_user2->source_column = 'modified_by';
		$source_catid                 = clone $source_user1;
		$source_catid->source_column  = 'catid';
		$source_catid->target_table   = '#__categories';
		$source_catid->display_column = 'title';
		$history->display_lookup[] = $source_user1;
		$history->display_lookup[] = $source_user2;
		$history->display_lookup[] = $source_catid;

		// Create/Update Sermon Type
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
		$source_serie                = clone $source_catid;
		$source_serie->source_column = 'series_id';
		$source_serie->target_table  = '#__sermon_series';

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

		// Create/Update Speaker Type
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

		$table_object = new stdClass;
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

		// Create/Update Series Type
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

		$table_object = new stdClass;
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

		$history = new stdClass;
		$history->form_file     = 'administrator/components/com_categories/models/forms/category.xml';
		$history->hideFields    = array('asset_id', 'checked_out', 'checked_out_time', 'version', 'lft', 'rgt', 'level', 'path', 'extension');
		$history->ignoreChanges = array('modified_user_id', 'modified_time', 'checked_out', 'checked_out_time', 'version', 'hits', 'path');
		$history->convertToInt  = array('publish_up', 'publish_down');

		$displayLookup1 = new stdClass;
		$displayLookup1->sourceColumn  = 'created_user_id';
		$displayLookup1->targetTable   = '#__users';
		$displayLookup1->targetColumn  = 'id';
		$displayLookup1->displayColumn = 'name';
		$history->displayLookup[]      = $displayLookup1;

		$displayLookup2 = clone $displayLookup1;
		$displayLookup2->sourceColumn = 'modified_user_id';
		$displayLookup2->targetTable  = '#__users';
		$history->displayLookup[]     = $displayLookup2;

		$displayLookup3 = clone $displayLookup1;
		$displayLookup3->sourceColumn  = 'access';
		$displayLookup3->targetTable   = '#__viewlevels';
		$displayLookup3->displayColumn = 'title';
		$history->displayLookup[]      = $displayLookup3;

		$displayLookup4 = clone $displayLookup1;
		$displayLookup4->sourceColumn = 'parent_id';
		$displayLookup4->targetTable  = '#__categories';
		$history->displayLookup[]     = $displayLookup4;

		$table_object = new stdClass;
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

	/*
	 * get a variable from the manifest file (actually, from the manifest cache).
	 */
	function getParam($name)
	{
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_sermonspeaker"');
		$manifest = json_decode($db->loadResult(), true);

		return $manifest[$name];
	}
}
