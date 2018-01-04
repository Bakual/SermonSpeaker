<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die();

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerViewSermon extends HtmlView
{
	/**
	 * @var
	 * @since ?
	 */
	protected $state;

	/**
	 * @var
	 * @since ?
	 */
	protected $item;

	/**
	 * @var  \Joomla\Registry\Registry
	 * @since 6
	 */
	protected $params;

	/**
	 * @var  \Joomla\CMS\User\User
	 * @since 6
	 */
	protected $user;

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
		$app = Factory::getApplication();

		// Get model data (/models/sermon.php)
		$this->state = $this->get('State');

		if (!$this->state->get('sermon.id'))
		{
			$id = $this->get('Latest');

			if (!$id)
			{
				throw new Exception(Text::_('JGLOBAL_RESOURCE_NOT_FOUND'), 404);
			}

			$this->state->set('sermon.id', $id);
			$app->input->set('id', $id);
		}

		// Initialise variables.
		$params     = $this->state->get('params');
		$this->user = Factory::getUser();
		$groups     = $this->user->getAuthorisedViewLevels();

		// Check if access is not public
		if (!in_array($params->get('access'), $groups))
		{
			$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->redirect(Route::_('index.php?view=sermons'));
		}

		$this->columns = $params->get('col');

		if (empty($this->columns))
		{
			$this->columns = array();
		}

		$item = $this->get('Item');

		if (!$item)
		{
			$app->enqueueMessage(Text::_('JGLOBAL_RESOURCE_NOT_FOUND'), 'error');
			$app->redirect(Route::_('index.php?view=sermons'));
		}

		// Get Tags
		$item->tags = new TagsHelper;
		$item->tags->getItemTags('com_sermonspeaker.sermon', $item->id);

		// Check for category ACL
		if ($item->category_access)
		{
			if (!in_array($item->category_access, $groups))
			{
				$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
				$app->redirect(Route::_('index.php?view=sermons'));
			}
		}

		if ($item->speaker_category_access)
		{
			if (!in_array($item->speaker_category_access, $groups))
			{
				$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
				$app->redirect(Route::_('index.php?view=sermons'));
			}
		}

		if ($item->series_category_access)
		{
			if (!in_array($item->series_category_access, $groups))
			{
				$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
				$app->redirect(Route::_('index.php?view=sermons'));
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

		// Process the content plugins.
		PluginHelper::importPlugin('content');

		$item->text = $item->notes;
		$app->triggerEvent('onContentPrepare', array('com_sermonspeaker.sermon', &$item, &$params, 0));
		$item->notes = $item->text;

		// Store the events for later
		$item->event                    = new stdClass;
		$results                        = $app->triggerEvent('onContentAfterTitle', array('com_sermonspeaker.sermon', &$item, &$params, 0));
		$item->event->afterDisplayTitle = trim(implode("\n", $results));

		$results                           = $app->triggerEvent('onContentBeforeDisplay', array('com_sermonspeaker.sermon', &$item, &$params, 0));
		$item->event->beforeDisplayContent = trim(implode("\n", $results));

		$results                          = $app->triggerEvent('onContentAfterDisplay', array('com_sermonspeaker.sermon', &$item, &$params, 0));
		$item->event->afterDisplayContent = trim(implode("\n", $results));

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
	 * @throws Exception
	 */
	protected function _prepareDocument()
	{
		$app   = Factory::getApplication();
		$menus = $app->getMenu();

		// Because the application sets a default page title, we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', Text::_('COM_SERMONSPEAKER_SERMON_TITLE'));
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
			$title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
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
			$pathway->addItem($this->item->series_title, Route::_(SermonspeakerHelperRoute::getSerieRoute($this->item->series_slug, $this->item->series_catid, $this->item->series_language)));
		}
		elseif ($menu && ($menu->query['view'] == 'speakers'))
		{
			$pathway->addItem($this->item->speaker_title, Route::_(SermonspeakerHelperRoute::getSpeakerRoute($this->item->speaker_slug, $this->item->speaker_catid, $this->item->speaker_language)));
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
			$this->document->addCustomTag('<meta property="og:url" content="' . htmlspecialchars(Uri::getInstance()->toString()) . '"/>');
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
						. JUri::base() . trim(Route::_(SermonspeakerHelperRoute::getSpeakerRoute($this->item->speaker_slug, $this->item->speaker_catid, $this->item->speaker_language)), '/')
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
