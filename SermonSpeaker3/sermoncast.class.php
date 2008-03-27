<?php

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class Podcast extends mosDBTable {
	
	var $podcast_id;
	var $article_id;
	var $itAuthor;
	var $itBlock;
	var $itCategory;
	var $itDuration;
	var $itExplicit;
	var $itKeywords;
	var $itSubtitle;

	function Podcast( &$db ) {
		$this->mosDBTable( '#__podcast', 'podcast_id', $db );
	}
}

?>
