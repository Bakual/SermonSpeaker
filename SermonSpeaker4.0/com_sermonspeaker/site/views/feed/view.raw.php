<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewFeed extends JView
{
	function display($tpl = null)
	{
		$app 	=& JFactory::getApplication();
		$user	=& JFactory::getUser();
		$params	=& JComponentHelper::getParams('com_sermonspeaker');

		// Check to see if the user has access to view the sermon
		$aid	= $user->get('aid'); // 0 = public, 1 = registered, 2 = special

		if ($params->get('access') > $aid){
			if (!$aid){
				// Redirect to login
				$uri	= JFactory::getURI();
				$return	= $uri->toString();

				$url  = 'index.php?option=com_user&view=login&return='.base64_encode($return);

				//$url	= JRoute::_($url, false);
				$app->redirect($url, JText::_('You must login first'));
			} else {
				JError::raiseWarning(403, JText::_('ALERTNOTAUTH'));
				return;
			}
		}

		$link = JURI::root();

		// Channel
  
		// Save Parameters and stuff xmlsafe into $channel
		$channel->title 		= $this->make_xml_safe($params->get('title'));
		$channel->link 			= $link;
		$channel->atomlink		= $this->make_xml_safe($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		$channel->description	= $this->make_xml_safe($params->get('description'));
		$channel->copyright 	= $this->make_xml_safe($params->get('copyright'));
		if ($params->get('editorEmail')){
			$channel->manEditor 	= '<managingEditor>'.$this->make_xml_safe($params->get('editorEmail')).' ('.$params->get('editor').')</managingEditor>';
		} else {
			$channel->manEditor 	= '<dc:creator>'.$this->make_xml_safe($params->get('editor')).'</dc:creator>';
		}
		$channel->language 		= $this->make_xml_safe($params->get('itLanguage'));
		
		// iTunes specific tags
		if($params->get('itOwnerEmail') != "" || $params->get('itOwnerName') != "") {
			$channel->itOwner = array();
			$channel->itOwner['email']	= $this->make_xml_safe($params->get('itOwnerEmail'));
			$channel->itOwner['name'] 	= $this->make_xml_safe($params->get('itOwnerName'));
		}
		$channel->itKeywords 	= $this->make_xml_safe($params->get('itKeywords'));
		if(!strstr($params->get('itImage'), 'http://') && !strstr($params->get('itImage'), 'https://')) {
			$channel->itImage = $link.$params->get('itImage');
		} else {
			$channel->itImage = $params->get('itImage');
		}
		$channel->itCategories	= $this->make_itCat($params->get('itCategory1'))
							.$this->make_itCat($params->get('itCategory2'))
							.$this->make_itCat($params->get('itCategory3'));
		$channel->itSubtitle 	= $this->make_xml_safe($params->get('itSubtitle'));
		$channel->itSummary 	= $channel->description;
		$channel->itAuthor 		= $this->make_xml_safe($params->get('editor'));
		$channel->itNewfeedurl 	= $this->make_xml_safe($params->get('itRedirect'));

		// get Data from Model (/models/feed.php)
        $rows = &$this->get('Data');

		// Items
		
		$items = array();
		foreach($rows as $row) {
			$item = NULL;
			// todo: ItemId des Predigten Menupunkts suchen und an Link anhängen
			$item_link = $link.'index.php?option=com_sermonspeaker&amp;view=sermon&amp;id='.$row->id;

			// limits notes text to x words for itDescription and RSS (if set)
			$item_notes = str_replace(array("\r","\n",'  '), ' ', $this->make_xml_safe($row->notes));
			$text_length = $params->get('text_length', '10');
			$item_notes_array = explode(' ', $item_notes, $text_length + 1);
			if ($item_notes_array[$text_length]) {
				$item_notes_array[$text_length] = '...';
			}
			$item_notes_short = implode(' ', $item_notes_array);
			if($params->get('limit_text')) {
				$item->description = $item_notes_short;
			} else {
				$item->description = $item_notes;
			}

			$item->title	= $this->make_xml_safe($row->sermon_title);
			$item->link		= $item_link; // todo: maybe make this link with JRoute to have a SEF link
			$item->guid		= $item_link;
			date_default_timezone_set(date_default_timezone_get()); // todo: maybe include the TZ from Joomla
			$item->date		= date("r", strtotime($row->sermon_date));
			$item->author 	= '<dc:creator>'.$this->make_xml_safe($row->name).'</dc:creator>'; // todo: maybe add email of speaker if present (not yet in database), format is emailadress (name) and then use author instead
			$item->category = $this->make_xml_safe($row->series_title); // using the series title as an item category

			// iTunes item specific tags
			$item->itAuthor		= $row->name; // only speaker name here for iTunes
			$item->itDuration 	= SermonspeakerHelperSermonSpeaker::insertTime($row->sermon_time);
			$item->itSubtitle 	= $item_notes_short;
			$item->itSummary 	= $item_notes;
			
			// create keywords from series_title and scripture (title and speaker are searchable anyway)
			$keywords = str_replace(' ', ',', $item->category);
			$item->itKeywords 	= $this->make_xml_safe(str_replace(',', ':', $row->sermon_scripture)).','.$keywords;

			// Create Enclosure
			
			//check if url is external
			if (substr($row->sermon_path,0,7) == "http://") {
				//external link
				$item->enclosure['url'] = $row->sermon_path;
				$item->enclosure['length'] = 1;
			} else {
				//internal link
				//url to play
				$path = str_replace(array(' ', '%20'), array('%20','%20'), $row->sermon_path); //fix for spaces in the filename
				if(substr($path,0,1) == "/" ) {
					$path = substr($path,1);
				}
				$item->enclosure['url'] = $link.$path;
				// Filesize for length
				if(file_exists(JPATH_ROOT.$row->sermon_path)) {
					$item->enclosure['length'] = filesize(JPATH_ROOT.$row->sermon_path);
				} else {
					$item->enclosure['length'] = 0;
				}
			}
			// MIME type for content
			$extension = substr($row->sermon_path, strrpos($row->sermon_path, '.'));
			switch ($extension) {
				case '.mp3':
					$item->enclosure['type'] = 'audio/mpeg';
					break;
				case '.m4a':
					$item->enclosure['type'] = 'audio/x-m4a';
					break;
				case '.mp4':
					$item->enclosure['type'] = 'video/mp4';
					break;
				case '.m4v':
					$item->enclosure['type'] = 'video/x-m4v';
					break;
				case '.mov':
					$item->enclosure['type'] = 'video/quicktime';
					break;
				case '.pdf':
					$item->enclosure['type'] = 'application/pdf';
					break;
				default:
					$item->enclosure['type'] = 'audio/mpeg';
					break;
			}

			// loads item info into items array
			$items[] = $item;
		}
		// push data into the template
		$this->assignRef('items',$items);
		$this->assignRef('channel',$channel);
		parent::display($tpl);
	}
	
	function make_xml_safe ($string){
		$string = strip_tags($string);
		$string = html_entity_decode($string, ENT_NOQUOTES, 'UTF-8');
		$string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8', FALSE);

		return $string;
	}

	function make_itCat ($cat){
		if($cat == '') {
			return '';
		}
		list($parent, $child) = explode(' > ', $cat);
		if($child == ''){
			$tags = "\n".'	<itunes:category text="'.htmlspecialchars($parent).'" />';
		} else {
			$tags = "\n".'	<itunes:category text="'.htmlspecialchars($parent).'">'."\n";
			$tags .= '		<itunes:category text="'.htmlspecialchars($child).'" />'."\n";
			$tags .= '	</itunes:category>';
		}
		
		return $tags;
	}
}