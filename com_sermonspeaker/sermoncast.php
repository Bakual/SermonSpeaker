<?php

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access'); 

//include the config file
require_once( JPATH_ROOT.DS.'administrator/components/com_sermonspeaker/sermoncastconfig.sermonspeaker.php' );

$info	=	null;
$rss	=	null;
$type = JFilterInput::clean(JRequest::getVar('type'),string);

switch ( $task ) {
	case 'launchitunes':
		launchItunes();
		break;
		
	case 'live_bookmark':
		feedPodcast( false );
		break;
		
	//default:
	case 'feedPodcast':
		feedPodcast( true );
		break;
}

function removeSpace($file) {
  $file = str_replace(array(' ', '%20'), array('%20','%20'), $file);
  return $file;
}

function launchItunes() {
  $lang = new sermonLang;
	global $database, $option;
	
	$conf = new sermonCastConfig;
  
	$pcastFeed = '<?xml version="1.0" encoding="UTF-8"?'.'>' . "\n"; 
	$pcastFeed .= '<!DOCTYPE pcast PUBLIC "-//Apple Computer//DTD PCAST 1.0//EN" "http://www.itunes.com/DTDs/pcast-1.0.dtd">' . "\n";
	$pcastFeed .= '<pcast version="1.0">' . "\n"; 
	$pcastFeed .= '<channel>' . "\n";
	$pcastFeed .= '	<link rel="feed" type="application/rss+xml" href="' . JURI::root(). 'index2.php?option=' . $option . '&feed=RSS2.0&no_html=1" />' . "\n";
	$pcastFeed .= '	<title>' . $conf->def( 'title', 'Powered by Joomla' ) . '</title>' . "\n"; 
	$pcastFeed .= '	<category>' . /* $categories[0] */ 'Podcasting' . '</category>' . "\n"; 
	$pcastFeed .= '	<subtitle>' . $conf->def( 'description', 'Joomla site syndication' ) . '</subtitle>' . "\n"; 
	$pcastFeed .= '</channel>' . "\n";
	$pcastFeed .= '</pcast>' . "\n";
	$pcastFeed .= $lang->autostart . "\n";
	
	$filename = JURI::root().'cache/joomla.pcast';
	
	$pcastFile = fopen($filename, 'w+');
	if($pcastFile) {
		fputs($pcastFile, $pcastFeed);
		fclose($pcastFile);
	} else {
		//echo "<br /><b>Error creating feed file, please check write permissions.</b><br />";
		echo "<br /><b>".$lang->err_pcast."</b><br />";
	}

	Header("Content-Type: application/octet-stream; charset=UTF-8");
	Header("Content-Disposition: inline; filename=".basename($filename));
	readfile($filename, "r");
	exit;
}

/*
* Creates feed from Content Iems containing enclosure tags
*/
function feedPodcast( $showFeed ) {
  // load feed creator class
  require_once( 'feedcreator.class.php' );
	global $mainframe, $itemid;

  $conf = new sermonCastConfig;
  $catID = JRequest::getVar('catID','post',string);
  
  $JApp =& JFactory::getApplication();  
	$now 	= date( 'Y-m-d H:i:s', time() + $JApp->getCfg('offset') * 3600 );

	// parameter intilization
	$info[ 'date' ] = date( 'r' );
	$info[ 'year' ] = date( 'Y' );
	$info[ 'link' ] = htmlspecialchars( JURI::root() );
	$info[ 'cache' ] = $conf->cache;
	$info[ 'cache_time' ]	= $conf->cache_time;
	$info[ 'count' ] = $conf->count;
	$info[ 'orderby' ]	= $conf->orderby;
	$info[ 'title' ] = $conf->title;
	$info[ 'description' ] = $conf->description;
	$info[ 'image_file' ]	= $conf->image_file;
	$info[ 'mimetype' ] = $conf->mimetype;
	$info[ 'copyright' ] = $conf->copyright;
	if ( $info[ 'image_file' ] == -1 ) {
		$info[ 'image' ] = NULL;
	} else{
		$info[ 'image' ] = JURI::root() .'images/M_images/'. $info[ 'image_file' ];
	}
	$info[ 'image_alt' ] = $conf->image_alt;
	$info[ 'limit_text' ]	= $conf->limit_text;
	$info[ 'text_length' ] = $conf->text_length;
	// get feed type from url
	$info[ 'feed' ] = JRequest::getVar('feed','get',string);
	// live bookmarks
	$info[ 'live_bookmark' ] = $conf->live_bookmark;
	$info[ 'bookmark_file' ] = $conf->bookmark_file;
	// content to syndicate
	
	$info[ 'podcast_from' ] = $conf->podcast_from;
	$info[ 'section' ] = $conf->section;

	if($catID != '')
		$pod = "podCat$catID";
	else
		$pod = 'pod';
	
	// set filename for live bookmarks feed
	if ( !$showFeed && $info[ 'live_bookmark' ] ) {
		if ( $info[ 'bookmark_file' ] ) {
		  // custom bookmark filename
			$info[ 'file' ] = JPATH_ROOT .'/cache/'. $pod . $info[ 'bookmark_file' ];
		} else {
		  // standard bookmark filename
			$info[ 'file' ] = JPATH_ROOT .'/cache/'. $pod . $info[ 'live_bookmark' ];
		}		
	} else {
	// set filename for rss feeds
		$info[ 'file' ] = strtolower( str_replace( '.', '', $info[ 'feed' ] ) );
		$info[ 'file' ] = JPATH_ROOT .'/cache/'. $pod . $info[ 'file' ] .'.xml';
	}
	
	// load feed creator class
	$rss 	= new RSSCreator091();
	
	// load image creator class
	$image 	= new FeedImage();
	
	// loads cache file
	if ( $showFeed && $info[ 'cache' ] ) {
		$rss->useCached( $info[ 'feed' ], $info[ 'file' ], $info[ 'cache_time' ] );
	}
	
	$rss->title = $info[ 'title' ];
	$rss->description	= $info[ 'description' ];
	$rss->link = $info[ 'link' ];
	$rss->syndicationURL = $info[ 'link' ];
	$rss->copyright = $info[ 'copyright' ];
	$rss->cssStyleSheet = NULL;
	
	
	if ( $info[ 'image' ] ) {
		$image->url = $info[ 'image' ];
		$image->link = $info[ 'link' ];
		$image->title = $info[ 'image_alt' ];
		$image->description	= $info[ 'description' ];
		// loads image info into rss array
		$rss->image	= $image;
	}
	
	$rss->encoding = $conf->encoding; // encoding set to UTF-8 by default, iTunes preferred
	
	// iTunes specific tags
	$rss->itBlock = $conf->itBlock;
	if($conf->itOwnerEmail != "" || $conf->itOwnerName != "")
	{
		$rss->itOwner = array(); 
		$rss->itOwner['email'] = $conf->itOwnerEmail;
		$rss->itOwner['name'] = $conf->itOwnerName;
	}
	$rss->itExplicit = $conf->itBlock;
	$rss->itKeywords = $conf->itKeywords;

	if(!strstr($conf->itImage, 'http://') && !strstr($conf->itImage, 'https://'))
		//$rss->itImage = $mosConfig_live_site . '/' . $conf->mediapath . '/' . $conf->itImage;
		$rss->itImage = JURI::root() . $conf->mediapath . '/' . $conf->itImage;
	else
		$rss->itImage = $conf->itImage;
	
	$rss->itCategory1 = $conf->itCategory1;
	$rss->itCategory2 = $conf->itCategory2;
	$rss->itCategory3 = $conf->itCategory3;
	$rss->itSubtitle = $conf->itSubtitle;
	$rss->itAuthor = $conf->itAuthor;
	$rss->itSummary = $conf->itSummary;
	$rss->language = $conf->itLanguage;
	$rss->newfeedurl = $conf->itRedirect;
	$rss->itExplicit = "1";
	
	$database =& JFactory::getDBO();
	//get UTF-8 results...
	$query = "SET character_set_results ='utf8';";
	$database->setQuery( $query );
	$query = "SELECT UNIX_TIMESTAMP(sermon_date) AS pubdate, sermon_title, sermon_path,"
  . "\n notes, sermon_time, sermon_scripture, s.name, a.id "
	. "\n FROM #__sermon_sermons AS a"
	. "\n INNER JOIN  #__sermon_speakers AS s"
	. "\n ON a.speaker_id = s.id  WHERE a.published='1' AND a.podcast='1'"
  . "\n ORDER by pubdate desc";
	
	$database->setQuery( $query );
	$rows = $database->loadObjectList();
	
	if(!count($rows))
		echo mysql_error();

	foreach ( $rows as $row ) {
		// title for particular item
		$item_title = htmlspecialchars( $row->sermon_title, ENT_NOQUOTES , 'utf-8');
		$item_title = html_entity_decode( $item_title );
    //$item_title = utf8_encode($item_title);
    
    //check if url is external
    $strg = substr($row->sermon_path,0,6);
    if ($strg == "http:/") {
      //external link
      $encl = $row->sermon_path;
      $itemlength = 1;
    } else {
      //internal link
      // url to play
      
      //$path = $row->sermon_path;
      $path = removeSpace($row->sermon_path); //fix for spaces in the filename
      if (substr($path,0,1) == "/" ) {
        $path = substr($path,1);
      }
      $encl = JURI::root().$path;
      
      // Filesize for length
		  if ( file_exists(JPATH_ROOT.$row->sermon_path)) {
        $itemlength = filesize(JPATH_ROOT.$row->sermon_path);
        $itemduration = $row->sermon_time;
      } else {
        $itemlength == 0;
        $item->itDuration = 0;
      }
    }
		
		$encl = JRoute::_($encl);
		
		// & used instead of &amp; as this is converted by feed creator
    $item_link = JURI::root() .'index.php?option=com_sermonspeaker&task=singlesermon&id='. $row->id.'&Itemid='. $mainframe->getItemid( $row->id );
    
		// removes all formating from the intro text for the description text
		$item_description = $row->notes;
		$item_description = strip_tags( $item_description );
		//$item_description = convertUmlaute( $item_description );
    //$item_description = utf8_encode($item_description);

		if ( $info[ 'limit_text' ] ) {
			if ( $info[ 'text_length' ] ) {
				// limits description text to x words
				$item_description_array = split( ' ', $item_description );
				$count = count( $item_description_array );
				if ( $count > $info[ 'text_length' ] ) {
					$item_description = '';
					for ( $a = 0; $a < $info[ 'text_length' ]; $a++ ) {
						$item_description .= $item_description_array[$a]. ' ';
					}
					$item_description = trim( $item_description );
					$item_description .= '...';
				}
			} else  {
				// do not include description when text_length = 0
				$item_description = NULL;
			}
		}    
    
    $item_description = html_entity_decode($item_description);
    //$item_description = utf8_encode($item_description);
    
		// load individual item creator class
		$item = new FeedItem();
		// item info
		$item->title = $item_title;
		$item->link = $item_link;
		$item->guid = $item_link;
		$item->description 	= $item_description;
		$item->source = $info[ 'link' ];
		$item->date = $row->pubdate;
		/*
    if ($type = "rss") {
      $item->author = $row->name." / ".$conf->itAuthor; //for iTunes
      $item->itAuthor = $row->name;
    } else {
      $item->author = $conf->itOwnerEmail.' ('.$row->name.')'; // for RSS
      $item->itAuthor = $conf->itOwnerEmail;
    }
    */
    $item->author = $conf->itOwnerEmail.' ('.$row->name.')'; // for RSS
    //$item->itAuthor = $conf->itOwnerEmail;
    $item->itAuthor = $row->name; // we can use the speakers name for the iTunes author field
		// iTunes item specific tags
		$item->itBlock = $row->itBlock;
		
		// MIME type for content
		$itemtype = substr($row->sermon_path,strrpos($row->sermon_path,'.'));
		if ($itemtype == ".mp3") { $type = "audio/mpeg"; }
		if ($itemtype == ".flv") { $type = "video/x-flv"; }

		$categories = split(',', $row->itCategory);
		if(count($categories) == 1)
		{
			$item->itCategory = trim($categories[0]);
		}
		else if(count($categories) > 1)
		{
			foreach($categories as $cat)
				$item->itCategory[] = trim($cat);
		}
		
		//$item->itDuration = $row->sermon_time;
		$item->itDuration = $itemduration;
		if($row->itExplicit != "")
			$item->itExplicit = $row->itExplicit;
		else
			$item->itExplicit = "no";
			
		//$item->itKeywords = $row->itKeywords;
		//$item->itSubtitle = $row->itSubtitle;
		$item->itSubtitle = $item_title; //use the title as subtitle as we don't have this information...
		$item->itKeywords = $item_title.", ".$row->name; //lets create some keywords; maybe we should include keywords in the db...
		//$item->itSummary = $item_description; // using the same description as the RSS feed in general.
		$item->itSummary = strip_tags($row->notes); 
    $item->itExplicit = "1";
    
    $item->addEnclosure($encl, $itemlength, $type);
	 
		// loads item info into rss array
		$rss->addItem( $item );
	}

	// save feed file
	$rss->saveFeed( $info[ 'file' ], $showFeed );		
}

?>
