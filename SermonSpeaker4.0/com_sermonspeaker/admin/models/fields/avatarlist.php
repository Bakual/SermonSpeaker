<?php
/**
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Avatarlist Field class for the SermonSpeaker.
 * Based on the Bannerlist field from com_banners
 *
 * @package		SermonSpeaker
 * @since		4.0
 */
class JFormFieldAvatarlist extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Avatarlist';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	public function getInput()
	{
		// getting the files with extension $filters from $path and its subdirectories for avatars
		$params	=& JComponentHelper::getParams('com_sermonspeaker');
		$path = JPATH_ROOT.DS.$params->get('path_avatar');
		$path2 = JPATH_ROOT.DS.'components'.DS.'com_sermonspeaker'.DS.'media'.DS.'avatars';
		$filters = array('.jpg','.gif','.png','.bmp');
		$filesabs = array();
		foreach($filters as $filter) {
			$filesabs = array_merge(JFolder::files($path, $filter, true, true),$filesabs);
		}
		foreach($filters as $filter) {
			$filesabs = array_merge(JFolder::files($path2, $filter, true, true),$filesabs);
		}
		
		// changing the filepaths relativ to the joomla root
		$root = JPATH_ROOT;
		$lsdir = strlen($root);
		$avatars = array();
		$avatars[0]->name = JText::_('COM_SERMONSPEAKER_SELECT_NOAVATAR');
		$avatars[0]->file = '';
		$i = 1;
		foreach($filesabs as $file){
			$avatars[$i]->name = trim(strrchr($file,DS),DS);
			$avatars[$i]->file = str_replace('\\','/',substr($file,$lsdir));
			$i++;
		}
		return JHTML::_('select.genericlist', $avatars, 'jform[avatar]', '', 'file', 'name', $this->value, 'jform_avatar');
	}
}
