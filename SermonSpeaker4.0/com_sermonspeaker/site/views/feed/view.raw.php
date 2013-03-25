<?php
defined('_JEXEC') or die;
class SermonspeakerViewFeed extends JViewLegacy
{
	function display($tpl = null)
	{
		$app 	= JFactory::getApplication();
		$this->params	= $app->getParams();

		// Get the log in credentials.
		$credentials = array();
		$credentials['username'] = $app->input->get->get('username', '', 'username');
		// Todo: How do I get ALLOWRAW with JInput or how does the com_users do it?
		$credentials['password'] = JRequest::getString('password', '', 'get', JREQUEST_ALLOWRAW);
		// Perform the log in.
		if ($credentials['username'] && $credentials['password']){
			$app->login($credentials);
		}
		// check if access is not public
		$user	= JFactory::getUser();
		$groups	= $user->getAuthorisedViewLevels();
		if (!in_array($this->params->get('access'), $groups)) {
			$app->redirect('', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		$this->document->setMimeEncoding('application/rss+xml'); 

		// Loading Joomla Filefunctions for enclosures
		jimport('joomla.filesystem.file');

		// get Data from Model (/models/feed.php)
        $this->items	= $this->get('Data');

		parent::display($tpl);
	}

	function make_xml_safe($string)
	{
		$string	= strip_tags($string);
		$string	= html_entity_decode($string, ENT_NOQUOTES, 'UTF-8');
		$string	= htmlspecialchars($string, ENT_QUOTES, 'UTF-8', FALSE);

		return $string;
	}

	function make_itCat($cat)
	{
		$cat_array	= explode(' > ', $cat);
		if (!isset($cat_array[1]))
		{
			$tags = htmlspecialchars($cat_array[0])."\" />\n";
		}
		else
		{
			$tags = htmlspecialchars($cat_array[0])."\">\n";
			$tags .= '		<itunes:category text="'.htmlspecialchars($cat_array[1])."\" />\n";
			$tags .= "	</itunes:category>\n";
		}

		return $tags;
	}

	function getNotes($text)
	{
		if ($this->params->get('prepare_content', 1))
		{
			$text	= JHtml::_('content.prepare', $text);
		}
		$text	= str_replace(array("\r","\n",'  '), ' ', $this->make_xml_safe($text));

		if ($this->params->get('limit_text'))
		{
			$length	= $this->params->get('text_length');
			$array	= explode(' ', $text, $length + 1);
			if (isset($array[$length]))
			{
				$array[$length] = '...';
			}
			$text = implode(' ', $array);
		}

		return $text;
	}

	function getEnclosure($item)
	{
		$type	= JFactory::getApplication()->input->get('type', 'auto');
		$prio	= $this->params->get('fileprio', 0);

		// Create Enclosure
		if ($type == 'video')
		{
			$file	= $item->videofile;
		}
		elseif ($type == 'audio')
		{
			$file	= $item->audiofile;
		}
		else
		{
			$file	= SermonspeakerHelperSermonspeaker::getFileByPrio($item, $prio);
		}
		if ($file)
		{
			// MIME type for content
			$enclosure['type']	= SermonspeakerHelperSermonspeaker::getMime(JFile::getExt($file));
			if (strpos($file, 'http://') === 0)
			{
				//external link
				if ((strpos($file, 'http://vimeo.com') === 0) || (strpos($file, 'http://player.vimeo.com') === 0))
				{
					// Vimeo
					$id					= trim(strrchr($file, '/'), '/ ');
					$file				= 'http://vimeo.com/moogaloop.swf?clip_id='.$id;
					$enclosure['type']	= 'application/x-shockwave-flash';
				}
				$enclosure['url']		= $file;
				$enclosure['length']	= 1;
			}
			else
			{
				//internal link
				//url to play
				$path	= str_replace(array(' ', '%20'), array('%20', '%20'), $file); //fix for spaces in the filename
				$path	= trim($path, ' /');
				$enclosure['url'] = JURI::root().$path;
				// Filesize for length TODO: Get from database if available
				if (file_exists(JPATH_ROOT.$file))
				{
					$enclosure['length'] = filesize(JPATH_ROOT.$file);
				}
				else
				{
					$enclosure['length'] = 0;
				}
			}
		} else {
			$enclosure = '';
		}

		return $enclosure;
	}

	// create keywords from series_title and scripture (title and speaker are searchable anyway)
	function getKeywords($item)
	{
		$keywords = array();
		if ($item->scripture)
		{
			$scripture	= SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '-/*', false);
			if ($this->params->get('prepare_content', 1))
			{
				$scripture	= JHtml::_('content.prepare', $scripture);
			}
			$scripture	= str_replace(',', ':', $scripture); // Make english scripture format
			$scripture	= str_replace("\n", '', $this->make_xml_safe($scripture));
			$keywords	= explode('-/*', $scripture);
		}
		if ($item->series_title)
		{
			$keywords[]	= $this->make_xml_safe($item->series_title);
		}

		return implode(',', $keywords);
	}
}