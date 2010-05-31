<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewFeed extends JView
{
	function display($tpl = null)
	{
		global $option, $itemid;
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		require_once(JPATH_COMPONENT.DS.'feedcreator.class.php');

		$params	=& JComponentHelper::getParams('com_sermonspeaker');

		$info	=	null;
		$rss	=	null;
		$catID	= JRequest::getInt('catID');
		$type 	= JFilterInput::clean(JRequest::getVar('type'),string);
  
		// parameter initialization
		$info['date']			= date('r');
		$info['year']			= date('Y');
		$info['link']			= htmlspecialchars(JURI::root());
		$info['cache']			= $params->get('cache');
		$info['cache_time']		= $params->get('cache_time');
		$info['count']			= $params->get('count');
		$info['title']			= $params->get('title');
		$info['description']	= $params->get('description');
		$info['mimetype'] 		= $params->get('mimetype');
		$info['copyright'] 		= $params->get('copyright');
		$info['limit_text']		= $params->get('limit_text');
		$info['text_length'] 	= $params->get('text_length');
		// get feed type from url
		$info['feed'] 			= JRequest::getString('feed','rss','get');

		// Parameters which aren't used (yet)
		/*
		$info['orderby']		= '';
		$info['image_file']		= ''; // would be when selecting a picture from a Joomla picture-dropdown
		if($info['image_file'] 	== -1) {
			$info['image'] = NULL;
		} else {
			$info['image'] = JURI::root().'images/M_images/'.$info['image_file'];
		}
		$info['image_alt']		= '';
		// live bookmarks
		$info['live_bookmark'] 	= '';
		$info['bookmark_file'] 	= '';
		// content to syndicate
		$info['podcast_from'] 	= '';
		$info['section'] 		= '';
		
		if($catID != 0)
			$pod = "podCat$catID";
		else
		*/

			$pod = 'pod';

		/*
		// Live Bookmarks
		// set filename for live bookmarks feed
		if(!$showFeed && $info[ 'live_bookmark' ]) {
			if($info['bookmark_file']) {
				// custom bookmark filename
				$info['file'] = JPATH_ROOT.DS.'cache'.DS.$pod.$info['bookmark_file'];
			} else {
				// standard bookmark filename
				$info['file'] = JPATH_ROOT.DS.'cache'.DS.$pod.$info['live_bookmark'];
			}
		} else {
		*/
		
			// set filename for rss feeds
			$info['file'] = strtolower(str_replace('.', '', $info['feed']));
			$info['file'] = JPATH_ROOT.DS.'cache'.DS.$pod.$info['file'].'.xml';

//		}

		// loads cache file
		if($showFeed && $info['cache']) {
			$rss->useCached($info['feed'], $info['file'], $info['cache_time']);
		}

		// loads needed classes from feedcreator.class.php
		$rss 	= new RSSCreator091();
		$image 	= new FeedImage();

		// configuring image creator if needed and applying it to feed creator
		/*
		if($info['image']) {
			$image->url		= $info['image'];
			$image->link 	= $info['link'];
			$image->title 	= $info['image_alt'];
			$image->description	= $info['description'];
			// loads image info into rss array
			$rss->image		= $image;
		}
		*/
		
		// configuring feed creator
		$rss->title 		= $info['title'];
		$rss->description	= $info['description'];
		$rss->link 			= $info['link'];
		$rss->syndicationURL = $info['link'];
		$rss->copyright 	= $info['copyright'];
		$rss->cssStyleSheet = NULL;
		$rss->encoding 		= $params->get('encoding'); // encoding set to UTF-8 by default, iTunes preferred
		// iTunes specific tags
		if($params->get('itOwnerEmail') != "" || $params->get('itOwnerName') != "") {
			$rss->itOwner = array(); 
			$rss->itOwner['email']	= $params->get('itOwnerEmail');
			$rss->itOwner['name'] 	= $params->get('itOwnerName');
		}
		$rss->itKeywords 	= $params->get('itKeywords');
		if(!strstr($params->get('itImage'), 'http://') && !strstr($params->get('itImage'), 'https://')) {
			$rss->itImage = JURI::root().$params->get('itImage');
		} else {
			$rss->itImage = $params->get('itImage');
		}
		$rss->itCategory1 	= $params->get('itCategory1');
		$rss->itCategory2 	= $params->get('itCategory2');
		$rss->itCategory3 	= $params->get('itCategory3');
		$rss->itSubtitle 	= $params->get('itSubtitle');
		$rss->itAuthor 		= $params->get('itAuthor');
		$rss->language 		= $params->get('itLanguage');
		$rss->newfeedurl 	= $params->get('itRedirect');
		$rss->itExplicit 	= "1";
		$rss->itBlock 		= '';
		$rss->itSummary 	= '';

		// get Data from Model (/models/feed.php)
        $rows = &$this->get('Data');

		foreach($rows as $row) {
			// title for particular item
			$item_title = htmlspecialchars($row->sermon_title, ENT_NOQUOTES , 'utf-8');
			$item_title = html_entity_decode($item_title);
		
			//check if url is external
			$strg = substr($row->sermon_path,0,6);
			if ($strg == "http:/") {
				//external link
				$encl = $row->sermon_path;
				$itemlength = 1;
			} else {
				//internal link
				//url to play
				$path = str_replace(array(' ', '%20'), array('%20','%20'), $row->sermon_path); //fix for spaces in the filename
				if(substr($path,0,1) == "/" ) {
					$path = substr($path,1);
				}
				$encl = JURI::root().$path;
				// Filesize for length
				if(file_exists(JPATH_ROOT.$row->sermon_path)) {
					$itemlength = filesize(JPATH_ROOT.$row->sermon_path);
					$itemduration = $row->sermon_time;
				} else {
					$itemlength == 0;
					$item->itDuration = 0;
				}
			}
			$encl = JRoute::_($encl);

			// & used instead of &amp; as this is converted by feed creator
			$item_link = JURI::root().'index.php?option='.$option.'&view=sermon&id='.$row->id.'&Itemid='.$itemid;

			// removes all formating from the intro text for the description text
			$item_description = strip_tags($row->notes);
			if($info['limit_text']) {
				if($info['text_length']) {
					// limits description text to x words
					$item_description_array = split(' ', $item_description);
					$count = count($item_description_array);
					if ($count > $info['text_length']) {
						$item_description = '';
						for ($a = 0; $a < $info['text_length']; $a++) {
							$item_description .= $item_description_array[$a].' ';
						}
						$item_description = trim($item_description);
						$item_description .= '...';
					}
				} else {
					// do not include description when text_length = 0
					$item_description = NULL;
				}
			}
			$item_description = html_entity_decode($item_description);
		
			// load individual item creator class
			$item = new FeedItem();
			// item info
			$item->title	= $item_title;
			$item->link		= $item_link;
			$item->guid		= $item_link;
			$item->description = $item_description;
			$item->source 	= $info['link'];
			$item->date 	= $row->pubdate;
			$item->author 	= $params->get('itOwnerEmail').' ('.$row->name.')'; // for RSS

			// MIME type for content
			$itemtype = substr($row->sermon_path,strrpos($row->sermon_path,'.'));
			if ($itemtype == ".mp3") { $type = "audio/mpeg"; }
			if ($itemtype == ".flv") { $type = "video/x-flv"; }

			// iTunes item specific tags
			$item->itAuthor	= $row->name; // we can use the speakers name for the iTunes author field
			$item->itBlock 	= $row->itBlock;
			$categories = split(',', $row->itCategory);
			if(count($categories) == 1) {
				$item->itCategory = trim($categories[0]);
			}
			else if(count($categories) > 1) {
				foreach($categories as $cat) {
					$item->itCategory[] = trim($cat);
				}
			}

			$item->itDuration = $itemduration;
			
			/* not used (yet)
			if($row->itExplicit != "") $item->itExplicit = $row->itExplicit;
			else $item->itExplicit = "no";
			*/
			$item->itExplicit = "1";
				
			$item->itSubtitle = $item_title; //use the title as subtitle as we don't have this information...
			$item->itKeywords = $item_title.", ".$row->name; //lets create some keywords; maybe we should include keywords in the db...
			$item->itSummary = strip_tags($row->notes); 
		
			$item->addEnclosure($encl, $itemlength, $type);

			// loads item info into rss array
			$rss->addItem($item);
		}
		// push data into the template
		$this->assignRef('rss',$rss);
		$this->assignRef('file',$info['file']);

		parent::display($tpl);
	}	
}