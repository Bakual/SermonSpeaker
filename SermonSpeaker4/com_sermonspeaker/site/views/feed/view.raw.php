<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewFeed extends JView
{
	function display($tpl = null)
	{
		$params	=& JComponentHelper::getParams('com_sermonspeaker');

		$cat['series'] = $params->get('series_cat', JRequest::getInt('series_cat', ''));
		$cat['speaker'] = $params->get('speaker_cat', JRequest::getInt('speaker_cat', ''));
		$cat['sermon'] = $params->get('sermon_cat', JRequest::getInt('sermon_cat', ''));
		$catID = implode('_',$cat);
		if ($catID) {
			$catID = '_'.$catID;
		}
		
		$link = JURI::root();
  
		// Save Parameters and stuff htmlsafe into $rss
		$rss->title 		= htmlspecialchars($params->get('title'));
		$rss->description	= htmlspecialchars($params->get('description'));
		$rss->link 			= $link;
		$rss->copyright 	= htmlspecialchars($params->get('copyright'));
		// iTunes specific tags
		if($params->get('itOwnerEmail') != "" || $params->get('itOwnerName') != "") {
			$rss->itOwner = array(); 
			$rss->itOwner['email']	= htmlspecialchars($params->get('itOwnerEmail')); // todo: wegen @ gucken
			$rss->itOwner['name'] 	= htmlspecialchars($params->get('itOwnerName'));
		}
		$rss->itKeywords 	= htmlspecialchars($params->get('itKeywords'));
		if(!strstr($params->get('itImage'), 'http://') && !strstr($params->get('itImage'), 'https://')) {
			$rss->itImage = JURI::root().$params->get('itImage');
		} else {
			$rss->itImage = $params->get('itImage');
		}
		$rss->itCategory1 	= $params->get('itCategory1'); // todo: Helperfunktion auflösen und hier intergieren
		$rss->itCategory2 	= $params->get('itCategory2');
		$rss->itCategory3 	= $params->get('itCategory3');
		$rss->itSubtitle 	= htmlspecialchars($params->get('itSubtitle'));
		$rss->itAuthor 		= htmlspecialchars($params->get('itAuthor'));
		$rss->language 		= htmlspecialchars($params->get('itLanguage'));
		$rss->newfeedurl 	= $params->get('itRedirect');

		// get Data from Model (/models/rss.php)
        $rows = &$this->get('Data');

		$items = array();
		foreach($rows as $row) {
			$item = NULL;
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
			// todo: ItemId des Predigten Menupunkts suchen und an Link anhängen
			$item_link = JURI::root().'index.php?option=com_sermonspeaker&amp;view=sermon&amp;id='.$row->id;

			// removes all formating from the intro text for the description text
			$item_description = strip_tags($row->notes);
			if($params->get('limit_text')) {
				$length = $params->get('text_length');
				if($text_length) {
					// limits description text to x words
					$item_description_array = split(' ', $item_description);
					$count = count($item_description_array);
					if ($count > $text_length) {
						$item_description = '';
						for ($a = 0; $a < $text_length; $a++) {
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
		
			// item info
			$item->title	= $item_title;
			$item->link		= $item_link;
			$item->guid		= $item_link;
			$item->description = $item_description;
			$item->source 	= JURI::root();
			$item->date 	= $row->pubdate;
			$item->author 	= $params->get('itOwnerEmail').' ('.$row->name.')'; // for RSS

			// MIME type for content
			$extension = substr($row->sermon_path, strrpos($row->sermon_path, '.'));
			if ($extension == '.mp3') {
				$type = 'audio/mpeg';
			} elseif ($extension == '.flv') {
				$type = 'video/x-flv';
			} else {
				$type = JFilterInput::clean(JRequest::getString('type','audio/mpeg'));
			}


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
			
			$item->itExplicit = "1";
				
			$item->itSubtitle = $item_title; //use the title as subtitle as we don't have this information...
			$item->itKeywords = $item_title.", ".$row->name; //lets create some keywords; maybe we should include keywords in the db...
			$item->itSummary = strip_tags($row->notes); 
		
			$item->enclosure['url'] = $encl;
			$item->enclosure['length'] = $itemlength;
			$item->enclosure['type'] = $type;

			// loads item info into rss array
			$items[] = $item;
		}
		// push data into the template
		$this->assignRef('items',$items);
		$this->assignRef('rss',$rss);
		parent::display($tpl);
	}

	function make_itCat ($cat){
		if($cat == '') {
			return '';
		}
		list($parent, $child) = explode(' > ', $cat);
		if($child == ''){
			$tags = '    <itunes:category text="'.htmlspecialchars($parent).'" />'."\n";
		} else {
			$tags = '    <itunes:category text="'.htmlspecialchars($parent).'">'."\n";
			$tags .= '        <itunes:category text="'.htmlspecialchars($child).'" />'."\n";
			$tags .= '    </itunes:category>'."\n";
		}
		
		return $tags;
	}
}