<?php
// No direct access to this file
defined('_JEXEC') or die;

class Com_SermonspeakerInstallerScript
{
	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent)
	{
		$min_version = (string)$parent->get('manifest')->attributes()->version;

		$jversion = new JVersion();
		if (!$jversion->isCompatible($min_version))
		{
			JError::raiseWarning(1, JText::sprintf('COM_SERMONSPEAKER_VERSION_UNSUPPORTED', $min_version));
			return false;
		}

		// Storing old release number for process in postflight
		if ($type == 'update')
		{
			$this->oldRelease = $this->getParam('version');

			// Check if update is allowed (only update from 4.5.0 and higher)
			if (version_compare($this->oldRelease, '4.5.0', '<'))
			{
				JError::raiseWarning(1, JText::sprintf('COM_SERMONSPEAKER_UPDATE_UNSUPPORTED', $this->oldRelease));
				return false;
			}
		}
	}

	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent)
	{
		$parent->getParent()->setRedirectURL('index.php?option=com_sermonspeaker');
	}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent)
	{
		echo '<p>'.JText::_('COM_SERMONSPEAKER_UNINSTALL_TEXT').'</p>';
	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent)
	{
		if (version_compare($this->oldRelease, '5.0.0', '<'))
		{
			jimport('joomla.filesystem.file');
			jimport('joomla.filesystem.folder');
			// Cleanup non-bootstrap layout files from old installations
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/views/files/tmpl/modal30.php';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/views/help/tmpl/default30.php';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/views/languages/tmpl/default30.php';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/views/main/tmpl/default30.php';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/views/scripture/tmpl/default30.php';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/views/serie/tmpl/edit30.php';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/views/serie/tmpl/modal30.php';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/views/series/tmpl/default_batch30.php';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/views/series/tmpl/default30.php';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/views/sermon/tmpl/edit30.php';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/views/sermons/tmpl/default_batch30.php';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/views/sermons/tmpl/default30.php';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/views/sermons/tmpl/modal30.php';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/views/speaker/tmpl/edit30.php';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/views/speaker/tmpl/modal30.php';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/views/speakers/tmpl/default_batch30.php';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/views/speakers/tmpl/default30.php';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/views/tools/tmpl/default30.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/frontendupload/tmpl/default30.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/scripture/tmpl/default30.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/serieform/tmpl/edit30.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/serieform/tmpl/modal30.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speakerform/tmpl/edit30.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speakerform/tmpl/modal30.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/serie/tmpl/default_filters30.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/serie/tmpl/default_filtersorder30.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/serie/tmpl/protostar-table.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/serie/tmpl/protostar-table.xml';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/serie/tmpl/protostar-list.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/serie/tmpl/protostar-list.xml';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/serie/tmpl/protostar-blog.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/serie/tmpl/protostar-blog.xml';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/series/tmpl/default_children30.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/series/tmpl/normal.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/series/tmpl/normal.xml';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/series/tmpl/protostar-table.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/series/tmpl/protostar-table.xml';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/series/tmpl/protostar-list.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/series/tmpl/protostar-list.xml';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/series/tmpl/protostar-blog.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/series/tmpl/protostar-blog.xml';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/sermons/tmpl/default_children30.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/sermons/tmpl/default_filters30.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/sermons/tmpl/default_filtersorder30.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/sermons/tmpl/tableless.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/sermons/tmpl/tableless.xml';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/sermons/tmpl/protostar-table.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/sermons/tmpl/protostar-table.xml';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/sermons/tmpl/protostar-list.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/sermons/tmpl/protostar-list.xml';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/sermons/tmpl/protostar-blog.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/sermons/tmpl/protostar-blog.xml';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speaker/tmpl/default_filters30.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speaker/tmpl/default_filtersorder30.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speaker/tmpl/series.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speaker/tmpl/series.xml';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speaker/tmpl/sermons.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speaker/tmpl/sermons.xml';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speaker/tmpl/popup30.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speaker/tmpl/protostar-table.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speaker/tmpl/protostar-table.xml';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speaker/tmpl/protostar-list.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speaker/tmpl/protostar-list.xml';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speaker/tmpl/protostar-blog.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speaker/tmpl/protostar-blog.xml';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speakers/tmpl/default_children30.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speakers/tmpl/normal.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speakers/tmpl/normal.xml';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speakers/tmpl/protostar-table.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speakers/tmpl/protostar-table.xml';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speakers/tmpl/protostar-list.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speakers/tmpl/protostar-list.xml';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speakers/tmpl/protostar-blog.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speakers/tmpl/protostar-blog.xml';

			// Cleanup tag view as we're now using the core tags in J!3.1
			JFolder::delete(JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/views/tags');
			JFolder::delete(JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/views/tag');
			JFolder::delete(JPATH_SITE.'/components/com_sermonspeaker/views/tagform');
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/models/tags.php';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/models/tag.php';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/models/forms/tag.xml';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/models/fields/tag.php'; // renamed to plugintag.php
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/models/fields/tagslist.php';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/tables/tag.php';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/controllers/tags.php';
			$files[]	= JPATH_ADMINISTRATOR.'/components/com_sermonspeaker/controllers/tag.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/models/tagform.php';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/models/forms/tag.xml';
			$files[]	= JPATH_SITE.'/components/com_sermonspeaker/controllers/tagform.php';

			JFile::delete($files);
		}
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
 	function postflight($type, $parent)
	{
		// Adding Category "uncategorized" if installing or discovering.
		if ($type != 'update')
		{
			$this->_addCategory();
		}

		// Adding content_type for tags
		$table = JTable::getInstance('Contenttype', 'JTable');
		if(!$table->load(array('type_alias' => 'com_sermonspeaker.sermon')))
		{
			$common	= new stdClass;
			$common->core_content_item_id	= 'id';
			$common->core_title				= 'sermon_title';
			$common->core_state				= 'state';
			$common->core_alias				= 'alias';
			$common->core_created_time		= 'created';
			$common->core_modified_time		= 'modified';
			$common->core_body				= 'notes';
			$common->core_hits				= 'hits';
			$common->core_publish_up		= null;
			$common->core_publish_down		= null;
			$common->core_access			= null;
			$common->core_params			= null;
			$common->core_featured			= null;
			$common->core_metadata			= null;
			$common->core_language			= 'language';
			$common->core_images			= 'picture'; // Does this work?
			$common->core_urls				= null;
			$common->core_version			= null;
			$common->core_ordering			= 'ordering';
			$common->core_metakey			= 'metakey';
			$common->core_metadesc			= 'metadesc';
			$common->core_catid				= 'catid';
			$common->core_xreference		= null;
			$common->asset_id				= null;

			$field_mappings	= new stdClass;
			$field_mappings->common[]		= $common;
			$field_mappings->special		= array();

			$special	= new stdClass;
			$special->dbtable		= '#__sermon_sermons';
			$special->key			= 'id';
			$special->type			= 'Sermon';
			$special->prefix		= 'SermonspeakerTable';
			$special->config		= 'array()';

			$table_object	= new stdClass;
			$table_object->special	= $special;

			$contenttype['type_title']		= 'Sermon';
			$contenttype['type_alias']		= 'com_sermonspeaker.sermon';
			$contenttype['table']			= json_encode($table_object);
			$contenttype['rules']			= '';
			$contenttype['router']			= 'SermonspeakerHelperRoute::getSermonRoute';
			$contenttype['field_mappings']	= json_encode($field_mappings);

			$table->save($contenttype);
		}

		// Setting some default values for columns on install
		if ($type == 'install')
		{
			$params	= array();
			$params['col']			='"col":['
					.'"sermons:scripture","sermons:speaker","sermons:date","sermons:series","sermons:player"'
					.',"sermon:scripture","sermon:speaker","sermon:date","sermon:series","sermon:player","sermon:notes","sermon:addfile"'
					.',"serie:scripture","serie:speaker","serie:date","serie:player"'
					.',"speaker:scripture","speaker:date","speaker:series","speaker:player"'
					.',"seriessermon:scripture","seriessermon:speaker","seriessermon:date"'
				.']';
			$params['col_serie']	= '"col_serie":['
					.'"series:speaker"'
					.',"serie:description","serie:speaker"'
					.',"speaker:description"'
					.',"seriessermon:description","seriessermon:speaker"'
				.']';
			$params['col_speaker']	= '"col_speaker":["speakers:bio","speaker:bio","speaker:intro"]';

			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__extensions'));
			$query->set($db->quoteName('params').' = '.$db->quote('{'.implode(',', $params).'}'));
			$query->where($db->quoteName('name').' = '.$db->quote('com_sermonspeaker'));
			$db->setQuery($query);
			$db->execute();
		}

		echo '<p>'.JText::sprintf('COM_SERMONSPEAKER_POSTFLIGHT', $type).'</p>';
	}


	/**
	 * method to add a default category "uncategorized"
	 *
	 * @return id of the created category
	 */
	function _addCategory()
	{
		// Create categories for our component
		$basePath = JPATH_ADMINISTRATOR.'/components/com_categories';
		require_once $basePath.'/models/category.php';
		$config		= array('table_path' => $basePath.'/tables');
		$catmodel	= new CategoriesModelCategory($config);
		$catData	= array('id' => 0, 'parent_id' => 0, 'level' => 1, 'path' => 'uncategorized', 'extension' => 'com_sermonspeaker',
						'title' => 'Uncategorized', 'alias' => 'uncategorized', 'description' => '', 'published' => 1, 'language' => '*');
		$catmodel->save($catData);
		$id = $catmodel->getItem()->id;

		$db = JFactory::getDBO();
		// Updating the example data with 'Uncategorized'
		$query	= $db->getQuery(true);
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