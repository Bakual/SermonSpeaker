<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  4
 */
class SermonspeakerViewFeed extends JViewLegacy
{
	/**
	 * @var  $params  Joomla\Registry\Registry  Holds the component params
	 */
	protected $params;

	/**
	 * @var  $items  array  Array with the item objects
	 */
	protected $items;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		/* @var  JApplicationSite $app The application */
		$app          = JFactory::getApplication();
		$this->params = $app->getParams();

		// Get the log in credentials.
		$credentials             = array();
		$credentials['username'] = $app->input->get->get('username', '', 'USERNAME');
		$credentials['password'] = $app->input->get->get('password', '', 'RAW');

		// Perform the log in.
		if ($credentials['username'] && $credentials['password'])
		{
			$app->login($credentials);
		}

		// Check if access is not public
		$user   = JFactory::getUser();
		$groups = $user->getAuthorisedViewLevels();

		if (!in_array($this->params->get('access'), $groups))
		{
			$app->redirect('', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		$this->document->setMimeEncoding('application/rss+xml');

		// Get Data from Model (/models/feed.php)
		$this->items = $this->get('Data');

		// Get current version of SermonSpeaker
		$component  = JComponentHelper::getComponent('com_sermonspeaker');
		$extensions = JTable::getInstance('extension');
		$extensions->load($component->id);
		$manifest      = json_decode($extensions->manifest_cache);
		$this->version = $manifest->version;

		parent::display($tpl);
	}

	/**
	 * Makes a string save to use in a XML file
	 *
	 * @param   string $string The string to be escaped
	 *
	 * @return  string  $string  The escaped string
	 */
	protected function make_xml_safe($string)
	{
		$string = strip_tags($string);
		$string = html_entity_decode($string, ENT_NOQUOTES, 'UTF-8');
		$string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8', false);

		return $string;
	}

	/**
	 * Creates an iTunes Category
	 *
	 * @param   array $cat iTunes categories
	 *
	 * @return  string  $tags  The iTunes category tag
	 */
	protected function make_itCat($cat)
	{
		$cat_array = explode(' > ', $cat);

		if (!isset($cat_array[1]))
		{
			$tags = htmlspecialchars($cat_array[0]) . "\" />\n";
		}
		else
		{
			$tags = htmlspecialchars($cat_array[0]) . "\">\n";
			$tags .= '		<itunes:category text="' . htmlspecialchars($cat_array[1]) . "\" />\n";
			$tags .= "	</itunes:category>\n";
		}

		return $tags;
	}

	/**
	 * Process notes
	 *
	 * @param   string $text notes
	 * @param   string $meta meta description
	 *
	 * @return  string  $tags  processed notes
	 */
	protected function getNotes($text, $meta = '')
	{
		// If meta description is present, use that one over the notes field.
		if ($meta)
		{
			$text = $meta;
		}

		if ($this->params->get('prepare_content', 1))
		{
			$text = JHtml::_('content.prepare', $text);
		}

		$text = str_replace(array("\r", "\n", '  '), ' ', $this->make_xml_safe($text));

		if ($this->params->get('limit_text'))
		{
			$length = $this->params->get('text_length');
			$array  = explode(' ', $text, $length + 1);

			if (isset($array[$length]))
			{
				$array[$length] = '...';
			}

			$text = implode(' ', $array);
		}

		return $text;
	}

	/**
	 * Creates Enclosure
	 *
	 * @param   object $item The row
	 *
	 * @return  array  $enclosure  Enclosure
	 */
	protected function getEnclosure($item)
	{
		$type = JFactory::getApplication()->input->get('type', 'auto');
		$prio = $this->params->get('fileprio', 0);

		// Create Enclosure
		if ($type == 'video')
		{
			$file = $item->videofile;
		}
		elseif ($type == 'audio')
		{
			$file = $item->audiofile;
		}
		else
		{
			$file = SermonspeakerHelperSermonspeaker::getFileByPrio($item, $prio);
		}

		if ($file)
		{
			// MIME type for content
			$enclosure['type'] = SermonspeakerHelperSermonspeaker::getMime(JFile::getExt($file));

			if (parse_url($file, PHP_URL_SCHEME))
			{
				// External link
				if ((strpos($file, 'http://vimeo.com') === 0) || (strpos($file, 'http://player.vimeo.com') === 0))
				{
					// Vimeo
					$id                = trim(strrchr($file, '/'), '/ ');
					$file              = 'http://vimeo.com/moogaloop.swf?clip_id=' . $id;
					$enclosure['type'] = 'application/x-shockwave-flash';
				}

				$enclosure['url']    = $file;
				$enclosure['length'] = 1;
			}
			else
			{
				// Internal link
				// Fix for spaces in the filename
				$path = str_replace(array(' ', '%20'), array('%20', '%20'), $file);
				$path = trim($path, ' /');

				// Url to play
				$enclosure['url'] = JUri::root() . $path;

				// Filesize for length TODO: Get from database if available
				if (file_exists(JPATH_ROOT . $file))
				{
					$enclosure['length'] = filesize(JPATH_ROOT . $file);
				}
				else
				{
					$enclosure['length'] = 0;
				}
			}
		}
		else
		{
			$enclosure = '';
		}

		return $enclosure;
	}

	/**
	 * Create keywords from series_title and scripture (title and speaker are searchable anyway)
	 *
	 * @param   object $item The row
	 *
	 * @return  string  keywords
	 */
	protected function getKeywords($item)
	{
		$keywords = array();

		if ($item->scripture)
		{
			$scripture = SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '-/*', false);

			if ($this->params->get('prepare_content', 1))
			{
				$scripture = JHtml::_('content.prepare', $scripture);
			}

			// Make english scripture format
			$scripture = str_replace(',', ':', $scripture);
			$scripture = str_replace("\n", '', $this->make_xml_safe($scripture));
			$keywords  = explode('-/*', $scripture);
		}

		if ($item->series_title)
		{
			$keywords[] = $this->make_xml_safe($item->series_title);
		}

		// Add meta keywords
		if ($item->metakey)
		{
			$metakey  = $this->make_xml_safe($item->metakey);
			$keywords = array_merge($keywords, explode(',', $metakey));
		}

		return implode(',', $keywords);
	}
}
