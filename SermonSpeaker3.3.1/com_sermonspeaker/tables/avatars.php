<?php
defined('_JEXEC') or die('Restricted access');

class TableAvatars extends JTable
{
	var $id = null;
	var $avatar_name = null;
	var $avatar_location = null;

	function __construct(&$db)
	{
		parent::__construct( '#__sermon_avatars', 'id', $db );
	}
}

?>
