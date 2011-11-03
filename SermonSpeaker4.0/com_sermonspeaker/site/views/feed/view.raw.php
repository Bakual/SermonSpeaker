<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewFeed extends JView
{
	function display($tpl = null)
	{
		$app 	= JFactory::getApplication();
		$params = $app->getParams();

		// Get the log in credentials.
		$credentials = array();
		$credentials['username'] = JRequest::getVar('username', '', 'get', 'username');
		$credentials['password'] = JRequest::getString('password', '', 'get', JREQUEST_ALLOWRAW);

		// Perform the log in.
		if ($credentials['username'] && $credentials['password']){
			$app->login($credentials);
		}

		// check if access is not public
		$user = JFactory::getUser();
		$groups	= $user->getAuthorisedViewLevels();

		if (!in_array($params->get('access'), $groups)) {
			$app->redirect('', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		$type	= JRequest::getCmd('type', 'auto');
		$prio	= $params->get('fileprio', 0);
		$this->document->setMimeEncoding('application/rss+xml'); 

		$link = JURI::root();

		// Loading Joomla Filefunctions
		jimport('joomla.filesystem.file');

		// Channel

		// Save Parameters and stuff xmlsafe into $channel
		$channel->title 		= $this->make_xml_safe($params->get('sc_title'));
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
		$itImage = $params->get('itImage');
		if ($itImage && !strstr($itImage, 'http://') && !strstr($itImage, 'https://')) {
			$channel->itImage = $link.$itImage;
		} elseif ($itImage){
			$channel->itImage = $itImage;
		} else {
			$channel->itImage = '';
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

		// Support for Content Plugins
		$dispatcher	= &JDispatcher::getInstance();
		JPluginHelper::importPlugin('content');
		$row->params = clone($params);

		// Items
		
		$items = array();
		foreach($rows as $row) {
			// Trigger Event for `notes` and `sermon_scripture`
			$row->text	= &$row->notes;
			$dispatcher->trigger('onContentPrepare', array('com_sermonspeaker.sermon', &$row, &$row->params, 0));
			$row->text	= &$row->sermon_scripture;
			$dispatcher->trigger('onContentPrepare', array('com_sermonspeaker.sermon', &$row, &$row->params, 0));

			$item = NULL;
			// todo: ItemId des Predigten Menupunkts suchen und an Link anhängen, maybe use HelperRoute (check if feed will be valid then)
			$item_link = $link.'index.php?option=com_sermonspeaker&amp;view=sermon&amp;id='.$row->id;

			// limits notes text to x words for itDescription and RSS (if set)
			$item_notes = str_replace(array("\r","\n",'  '), ' ', $this->make_xml_safe($row->notes));
			$text_length = $params->get('text_length', '10');
			$item_notes_array = explode(' ', $item_notes, $text_length + 1);
			if (isset($item_notes_array[$text_length])) {
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
			$item->date		= JHTML::Date($row->sermon_date, 'r', 'UTC');
			$item->author 	= '<dc:creator>'.$this->make_xml_safe($row->name).'</dc:creator>'; // todo: maybe add email of speaker if present (not yet in database), format is emailadress (name) and then use author instead
			$item->category = $this->make_xml_safe($row->series_title); // using the series title as an item category

			// iTunes item specific tags
			$item->itAuthor		= $row->name; // only speaker name here for iTunes
			$item->itDuration 	= SermonspeakerHelperSermonSpeaker::insertTime($row->sermon_time);
			if (strlen($item_notes_short) > 255){
				$item->itSubtitle 	= mb_substr($item_notes_short, 0, 252, 'UTF-8').'...';
			} else {
				$item->itSubtitle 	= $item_notes_short;
			}
			$item->itSummary 	= $item_notes;
			if ($row->picture){
				if(substr($row->picture, 0, 1) == '/' ) {
					$row->picture = substr($row->picture, 1);
				}
				$item->itImage	= $link.$row->picture;
			} else {
				$item->itImage	= $channel->itImage;
			}

			// create keywords from series_title and scripture (title and speaker are searchable anyway)
			$keywords = str_replace(' ', ',', $item->category);
			$item->itKeywords 	= $this->make_xml_safe(str_replace(',', ':', $row->sermon_scripture)).','.$keywords;

			// Create Enclosure

			if (($type != 'video') && ($row->audiofile && (!$prio || ($type == 'audio') || !$row->videofile))){
				$file = $row->audiofile;
			} elseif (($type != 'audio') && ($row->videofile && ($prio || ($type == 'video') || !$row->audiofile))){
				$file = $row->videofile;
			} else {
				$file = '';
			}

			if($file){
				if (substr($file, 0, 7) == 'http://') {
					//external link
					$item->enclosure['url'] = $file;
					$item->enclosure['length'] = 1;
				} else {
					//internal link
					//url to play
					$path = str_replace(array(' ', '%20'), array('%20', '%20'), $file); //fix for spaces in the filename
					$path = trim($path, ' /');
					$item->enclosure['url'] = $link.$path;
					// Filesize for length
					if(file_exists(JPATH_ROOT.$file)) {
						$item->enclosure['length'] = filesize(JPATH_ROOT.$file);
					} else {
						$item->enclosure['length'] = 0;
					}
				}
				// MIME type for content
				$item->enclosure['type'] = SermonspeakerhelperSermonspeaker::getMime(JFile::getExt($file));
			} else {
				$item->enclosure = '';
			}

			// Add sermonlink to the description
			if($params->get('include_link')) {
				$item->description = '&lt;a href=&quot;'.$item->enclosure['url'].'&quot;&gt;'.JText::_('COM_SERMONSPEAKER_DOWNLOADBUTTON').'&lt;/a&gt;&lt;br&gt;'.$item->description;
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
		$cat_array = explode(' > ', $cat);
		if(!isset($cat_array[1])){
			$tags = "\n".'	<itunes:category text="'.htmlspecialchars($cat_array[0]).'" />';
		} else {
			$tags = "\n".'	<itunes:category text="'.htmlspecialchars($cat_array[0]).'">'."\n";
			$tags .= '		<itunes:category text="'.htmlspecialchars($cat_array[1]).'" />'."\n";
			$tags .= '	</itunes:category>';
		}
		
		return $tags;
	}
}