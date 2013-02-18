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

		if (get_magic_quotes_gpc())
		{
			JError::raiseWarning(1, JText::_('COM_SERMONSPEAKER_MAGIC_QUOTES'));
			return false;
		}

		// Storing old release number ot process in postflight
		if ($type == 'update')
		{
			$this->oldRelease = $this->getParam('version');
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
		// Cleanup unused layout files from old installations
		jimport('joomla.filesystem.file');
		$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speaker/tmpl/latest-sermons.php';
		$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speaker/tmpl/latest-sermons.xml';
		$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/archive/tmpl/default.php';
		$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/archive/tmpl/default.xml';
		$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/serie/tmpl/default.php';
		$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/serie/tmpl/default.xml';
		$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/series/tmpl/default.php';
		$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/series/tmpl/default.xml';
		$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/seriessermon/tmpl/default.php';
		$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/seriessermon/tmpl/default.xml';
		$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/sermons/tmpl/default.php';
		$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/sermons/tmpl/default.xml';
		$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speakers/tmpl/default.php';
		$files[]	= JPATH_SITE.'/components/com_sermonspeaker/views/speakers/tmpl/default.xml';
		if(JFile::exists($files[1]))
		{
			JFile::delete($files);
		}
		// Cleanup files no longer used in SS4.4
		if (JFile::exists(JPATH_SITE.'/components/com_sermonspeaker/models/archive.php'))
		{
			JFile::delete(JPATH_SITE.'/components/com_sermonspeaker/models/archive.php');
			JFile::delete(JPATH_SITE.'/components/com_sermonspeaker/models/seriessermon.php');
			jimport('joomla.filesystem.folder');
			JFolder::delete(JPATH_SITE.'/components/com_sermonspeaker/views/archive');
		}
		// Cleanup old Flowplayer files
		if (JFile::exists(JPATH_SITE.'/media/com_sermonspeaker/player/flowplayer/flowplayer-3.2.7.swf'))
		{
			JFile::delete(JPATH_SITE.'/media/com_sermonspeaker/player/flowplayer/flowplayer-3.2.7.swf');
			JFile::delete(JPATH_SITE.'/media/com_sermonspeaker/player/flowplayer/flowplayer-3.2.7.zip');
			JFile::delete(JPATH_SITE.'/media/com_sermonspeaker/player/flowplayer/flowplayer-3.2.6.min.js');
			JFile::delete(JPATH_SITE.'/media/com_sermonspeaker/player/flowplayer/flowplayer.controls-3.2.5.swf');
			JFile::delete(JPATH_SITE.'/media/com_sermonspeaker/player/flowplayer/flowplayer.audio-3.2.2.swf');
		}
		// Cleanup old close layouts
		if (JFile::exists(JPATH_SITE.'/components/com_sermonspeaker/views/speakerform/tmpl/close.php'))
		{
			JFile::delete(JPATH_SITE.'/components/com_sermonspeaker/views/speakerform/tmpl/close.php');
			JFile::delete(JPATH_SITE.'/components/com_sermonspeaker/views/serieform/tmpl/close.php');
			JFile::delete(JPATH_SITE.'/components/com_sermonspeaker/views/tagform/tmpl/close.php');
			JFile::delete(JPATH_SITE.'/administrator/components/com_sermonspeaker/views/speaker/tmpl/close.php');
			JFile::delete(JPATH_SITE.'/administrator/components/com_sermonspeaker/views/serie/tmpl/close.php');
			JFile::delete(JPATH_SITE.'/administrator/components/com_sermonspeaker/views/tag/tmpl/close.php');
		}

		$this->_migrate();
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
 	function postflight($type, $parent)
	{
		// Adding Category "uncategorized" if installing or upgrading from older installations.
		if ($type == 'update')
		{
			if (version_compare($this->oldRelease, '4.4.4', '<'))
			{
				$this->_addCategory();
			}
		}
		else
		{
			$this->_addCategory();
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

			// Set Protostar layouts for Jooml 3.0 installations
			$jversion = new JVersion();
			if ($jversion->isCompatible('3.0'))
			{
				$params['sermonslayout']	= '"sermonslayout":"_:protostar-table"';
				$params['sermonlayout']		= '"sermonlayout":"_:protostar"';
				$params['serieslayout']		= '"serieslayout":"_:protostar-table"';
				$params['serielayout']		= '"serielayout":"_:protostar-table"';
				$params['speakerslayout']	= '"speakerslayout":"_:protostar-table"';
				$params['speakerlayout']	= '"speakerlayout":"_:protostar-table"';
			}

			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__extensions'));
			$query->set($db->quoteName('params').' = '.$db->quote('{'.implode(',', $params).'}'));
			$query->where($db->quoteName('name').' = '.$db->quote('com_sermonspeaker'));
			$db->setQuery($query);
			$db->query();
		}

		echo '<p>'.JText::sprintf('COM_SERMONSPEAKER_POSTFLIGHT', $type).'</p>';
	}

	/**
	 * method to run if tables are from SermonSpeaker 3.4.2. Will apply the needed changes for SermonSpeaker 4.0
	 *
	 * @return void
	 */
	function _migrate()
	{
		$db = JFactory::getDBO();
		$fields = $db->getTableFields('#__sermon_sermons');
		$sermons = $fields['#__sermon_sermons'];
		if ($sermons && array_key_exists('published', $sermons))
		{
			$sqlfile = dirname(__FILE__).'/migrate.sql';
			$buffer = file_get_contents($sqlfile);
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);
			if (count($queries))
			{
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '' && $query{0} != '#')
					{
						$db->setQuery($query);
						if (!$db->query())
						{
							JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
							return;
						}
					}
				}
				if (array_key_exists('play', $sermons))
				{
					$query = "ALTER TABLE #__sermon_sermons DROP COLUMN `play`, DROP COLUMN `download`";
					$db->setQuery($query);
					if (!$db->query())
					{
						JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
						return;
					}
				}
				echo '<div style="background-color:orange;">'.JText::_('COM_SERMONSPEAKER_MIGRATION_TEXT').'</div>';
			}
		}
		return;
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
		// Updating all sermons without category to have this new one
		$query	= $db->getQuery(true);
		$query->update('#__sermon_sermons');
		$query->set('catid = '.(int)$id);
		$query->where('catid = 0');
		$db->setQuery($query);
		$db->query();
		// Speakers
		$query->update('#__sermon_speakers');
		$db->setQuery($query);
		$db->query();
		// Series
		$query->update('#__sermon_series');
		$db->setQuery($query);
		$db->query();

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