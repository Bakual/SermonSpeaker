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
 * @since  3.4
 */
class SermonspeakerViewSermon extends JViewLegacy
{
	protected $state;

	protected $item;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return mixed A string if successful, otherwise a Error object.
	 *
	 * @throws \Exception
	 * @since ?
	 */
	public function display($tpl = null)
	{
		$app    = JFactory::getApplication();
		$jinput = $app->input;

		if (!$jinput->get('id', 0, 'int'))
		{
			$id = $this->get('Latest');

			if (!$id)
			{
				throw new Exception(JText::_('JGLOBAL_RESOURCE_NOT_FOUND'), 404);
			}

			$jinput->set('id', $id);
		}

		require_once JPATH_COMPONENT . '/helpers/player.php';

		// Initialise variables.
		$params     = $app->getParams();
		$this->user = JFactory::getUser();
		$groups     = $this->user->getAuthorisedViewLevels();

		// Check if access is not public
		if (!in_array($params->get('access'), $groups))
		{
			$app->redirect(JRoute::_('index.php?view=sermons'), JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		$this->columns = $params->get('col');

		if (!$this->columns)
		{
			$this->columns = array();
		}
		// Get model data (/models/sermon.php)
		$this->state = $this->get('State');
		$item        = $this->get('Item');

		if (!$item)
		{
			$app->redirect(JRoute::_('index.php?view=sermons'), JText::_('JGLOBAL_RESOURCE_NOT_FOUND'), 'error');
		}
		// Get Tags
		$item->tags = new JHelperTags;
		$item->tags->getItemTags('com_sermonspeaker.sermon', $item->id);

		// Check for category ACL
		if ($item->category_access)
		{
			if (!in_array($item->category_access, $groups))
			{
				$app->redirect(JRoute::_('index.php?view=sermons'), JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			}
		}

		if ($item->speaker_category_access)
		{
			if (!in_array($item->speaker_category_access, $groups))
			{
				$app->redirect(JRoute::_('index.php?view=sermons'), JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			}
		}

		if ($item->series_category_access)
		{
			if (!in_array($item->series_category_access, $groups))
			{
				$app->redirect(JRoute::_('index.php?view=sermons'), JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			}
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Set layout from parameters if not already set elsewhere
		if ($this->getLayout() == 'default')
		{
			$this->setLayout($params->get('sermonlayout', 'icon'));
		}

		// Update Statistic
		if ($params->get('track_sermon') && !$this->user->authorise('com_sermonspeaker.hit', 'com_sermonspeaker'))
		{
			$model = $this->getModel();
			$model->hit();
		}

		$this->params = $params;
		$this->item   = $item;

		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

		$this->_prepareDocument();

		return parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 *
	 * @since ?
	 */
	protected function _prepareDocument()
	{
		$app   = JFactory::getApplication();
		$menus = $app->getMenu();

		// Because the application sets a default page title, we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_SERMONSPEAKER_SERMON_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		// If the menu item does not concern this item
		if ($menu && ($menu->query['option'] != 'com_sermonspeaker' || $menu->query['view'] != 'sermon' || $menu->query['id'] != $this->item->id))
		{
			if ($this->item->title)
			{
				$title = $this->item->title;
			}
		}

		// Check for empty title and add site name if param is set
		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		if (empty($title))
		{
			$title = $this->item->title;
		}

		$this->document->setTitle($title);

		// Add Breadcrumbs
		$pathway = $app->getPathway();

		if ($menu && ($menu->query['view'] == 'series'))
		{
			$pathway->addItem($this->item->series_title, JRoute::_(SermonspeakerHelperRoute::getSerieRoute($this->item->series_slug, $this->item->series_catid, $this->item->series_language)));
		}
		elseif ($menu && ($menu->query['view'] == 'speakers'))
		{
			$pathway->addItem($this->item->speaker_title, JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->item->speaker_slug, $this->item->speaker_catid, $this->item->speaker_language)));
		}

		$pathway->addItem($this->item->title, '');

		// Set MetaData
		if ($this->item->metadesc)
		{
			$this->document->setDescription($this->item->metadesc);
		}
		elseif (!$this->item->metadesc && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		$keywords = '';

		if ($this->item->metakey)
		{
			$keywords = $this->item->metakey;
		}
		elseif ($this->params->get('menu-meta_keywords'))
		{
			$keywords = $this->params->get('menu-meta_keywords');
		}

		if ($this->item->tags->itemTags && $this->params->get('tags_to_metakey', 0))
		{
			$metatags = array();

			foreach ($this->item->tags->itemTags as $tag)
			{
				$metatags[] = $this->escape($tag->title);
			}

			$metatags = implode(', ', $metatags);

			if ($keywords)
			{
				$keywords .= ', ';
			}

			$keywords .= $metatags;
		}

		if ($keywords)
		{
			$this->document->setMetaData('keywords', $keywords);
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetaData('robots', $this->params->get('robots'));
		}

		if ($app->get('MetaAuthor'))
		{
			$this->document->setMetaData('author', $this->item->speaker_title);
		}

		// Add Metadata for Facebook Open Graph API
		if ($this->params->get('opengraph', 1))
		{
			$this->document->addCustomTag('<meta property="og:title" content="' . $this->escape($this->item->title) . '"/>');
			$this->document->addCustomTag('<meta property="og:url" content="' . htmlspecialchars(JUri::getInstance()->toString()) . '"/>');
			$this->document->addCustomTag('<meta property="og:description" content="' . $this->document->getDescription() . '"/>');
			$this->document->addCustomTag('<meta property="og:site_name" content="' . $app->get('sitename') . '"/>');

			if ($picture = SermonspeakerHelperSermonspeaker::insertPicture($this->item))
			{
				$this->document->addCustomTag('<meta property="og:image" content="' . SermonspeakerHelperSermonspeaker::makeLink($picture, true) . '"/>');
			}

			if ($this->params->get('fbmode', 0))
			{
				$this->document->addCustomTag('<meta property="og:type" content="article"/>');

				if ($this->item->speaker_title)
				{
					$this->document->addCustomTag(
						'<meta property="article:author" content="'
						. JUri::base() . trim(JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->item->speaker_slug, $this->item->speaker_catid, $this->item->speaker_language)), '/')
						. '"/>'
					);
				}

				if ($this->item->series_title)
				{
					$this->document->addCustomTag('<meta property="article:section" content="' . $this->escape($this->item->series_title) . '"/>');
				}
			}
			else
			{
				if ($this->item->videofile && ($this->params->get('fileprio', 0) || !$this->item->audiofile))
				{
					$this->document->addCustomTag('<meta property="og:type" content="movie"/>');

					if ((strpos($this->item->videofile, 'http://vimeo.com') === 0) || (strpos($this->item->videofile, 'http://player.vimeo.com') === 0))
					{
						$id   = trim(strrchr($this->item->videofile, '/'), '/ ');
						$file = 'http://vimeo.com/moogaloop.swf?clip_id=' . $id
							. '&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00adef&amp;fullscreen=1&amp;autoplay=0&amp;loop=0';
					}
					else
					{
						$file = SermonspeakerHelperSermonspeaker::makeLink($this->item->videofile, true);
					}

					$this->document->addCustomTag('<meta property="og:video" content="' . $file . '"/>');
				}
				else
				{
					$this->document->addCustomTag('<meta property="og:type" content="song"/>');
					$this->document->addCustomTag(
						'<meta property="og:audio" content="' . SermonspeakerHelperSermonspeaker::makeLink($this->item->audiofile, true) . '"/>'
					);
					$this->document->addCustomTag('<meta property="og:audio:title" content="' . $this->escape($this->item->title) . '"/>');

					if ($this->item->speaker_title)
					{
						$this->document->addCustomTag('<meta property="og:audio:artist" content="' . $this->escape($this->item->speaker_title) . '"/>');
					}

					if ($this->item->series_title)
					{
						$this->document->addCustomTag('<meta property="og:audio:album" content="' . $this->escape($this->item->series_title) . '"/>');
					}
				}
			}

			if ($fbadmins = $this->params->get('fbadmins', ''))
			{
				$this->document->addCustomTag('<meta property="fb:admins" content="' . $fbadmins . '"/>');
			}

			if ($fbapp_id = $this->params->get('fbapp_id', ''))
			{
				$this->document->addCustomTag('<meta property="fb:app_id" content="' . $fbapp_id . '"/>');
			}
		}
	}
}
