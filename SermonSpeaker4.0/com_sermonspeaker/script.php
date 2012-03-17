<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class Com_SermonspeakerInstallerScript {

	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent) {
		$parent->getParent()->setRedirectURL('index.php?option=com_sermonspeaker');
	}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent) {
		echo '<div>'.JText::_('COM_SERMONSPEAKER_UNINSTALL_TEXT').'</div>';
	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent) {
		echo '<div>'.JText::_('COM_SERMONSPEAKER_UPDATE_TEXT').'</div>';

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
		if(JFile::exists($files[1])){
			JFile::delete($files);
		}
		if(JFile::exists(JPATH_SITE.'/components/com_sermonspeaker/models/archive.php')){
			JFile::delete(JPATH_SITE.'/components/com_sermonspeaker/models/archive.php');
			jimport('joomla.filesystem.folder');
			JFolder::delete(JPATH_SITE.'/components/com_sermonspeaker/views/archive');
		}

		$this->_migrate();
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent) {
		echo JText::sprintf('COM_SERMONSPEAKER_PREFLIGHT', $type);
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
 	function postflight($type, $parent) {
		echo JText::sprintf('COM_SERMONSPEAKER_POSTFLIGHT', $type);
	}

	/**
	 * method to run if tables are from SermonSpeaker 3.4.2. Will apply the needed changes for SermonSpeaker 4.0
	 *
	 * @return void
	 */
	function _migrate(){
		$db =& JFactory::getDBO();
		$fields = $db->getTableFields('#__sermon_sermons');
		$sermons = $fields['#__sermon_sermons'];
		if (array_key_exists('published', $sermons)){
			$sqlfile = dirname(__FILE__).DS.'migrate.sql';
			$buffer = file_get_contents($sqlfile);
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);
			if (count($queries)) {
				foreach ($queries as $query) {
					$query = trim($query);
					if ($query != '' && $query{0} != '#') {
						$db->setQuery($query);
						if (!$db->query()) {
							JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
							return;
						}
					}
				}
				if (array_key_exists('play', $sermons)){
					$query = "ALTER TABLE #__sermon_sermons DROP COLUMN `play`, DROP COLUMN `download`";
					$db->setQuery($query);
					if (!$db->query()) {
						JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
						return;
					}
				}
				echo '<div style="background-color:orange;">'.JText::_('COM_SERMONSPEAKER_MIGRATION_TEXT').'</div>';
			}
		}
		return;
	}
}