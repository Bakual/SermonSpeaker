<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Site\View\Speaker;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die();

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * @var   stdClass
	 * @since 6
	 */
	protected $item;

	/**
	 * @var   \Joomla\Registry\Registry
	 * @since 6
	 */
	protected $state_sermons;

	/**
	 * @var   \Joomla\CMS\Pagination\Pagination
	 * @since 6
	 */
	protected $pag_sermons;

	/**
	 * @var   \Joomla\Registry\Registry
	 * @since 6
	 */
	protected $state_series;

	/**
	 * @var   \Joomla\CMS\Pagination\Pagination
	 * @since 6
	 */
	protected $pag_series;

	/**
	 * @var  \Joomla\Registry\Registry
	 * @since 6
	 */
	protected $params;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return void
	 *
	 * @throws \Exception
	 * @since ?
	 */
	public function display($tpl = null)
	{
		$app = Factory::getApplication();

		if (!$app->input->get('id', 0, 'int'))
		{
			$app->enqueueMessage(Text::_('JGLOBAL_RESOURCE_NOT_FOUND'), 'error');
			$app->redirect(Route::_('index.php?view=speakers'));
		}

		// Get data from the model
		$state         = $this->get('State');
		$this->item    = $this->get('Item');
		$user          = Factory::getApplication()->getIdentity();
		$this->params  = $state->get('params');
		$this->columns = $this->params->get('col_speaker');

		if (empty($this->columns))
		{
			$this->columns = array();
		}

		$this->col_sermon = $this->params->get('col');

		if (empty($this->col_sermon))
		{
			$this->col_sermon = array();
		}

		$this->col_serie = $this->params->get('col_serie');

		if (empty($this->col_serie))
		{
			$this->col_serie = array();
		}

		// Set layout from parameters if not already set elsewhere
		if ($this->getLayout() == 'default')
		{
			$this->setLayout($this->params->get('speakerlayout', 'series'));
		}

		if (!$this->item)
		{
			$app->enqueueMessage(Text::_('JGLOBAL_RESOURCE_NOT_FOUND'), 'error');
			$app->redirect(Route::_('index.php?view=speakers'));
		}

		// Get Tags
		$this->item->tags = new TagsHelper;
		$this->item->tags->getItemTags('com_sermonspeaker.speaker', $this->item->id);

		// Check if access is not public
		if ($this->item->category_access)
		{
			$groups = $user->getAuthorisedViewLevels();

			if (!in_array($this->item->category_access, $groups))
			{
				$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
				$app->redirect(Route::_('index.php?view=speakers'));
			}
		}

		// Get sermons data from the sermons model
		$sermon_model        = $this->getModel('Sermons');
		$this->state_sermons = $sermon_model->getState();
		$this->state_sermons->set('speaker.id', $state->get('speaker.id'));
		$this->state_sermons->set('category.sermons.id', 0);
		$this->sermons     = $sermon_model->getItems();
		$this->pag_sermons = $sermon_model->getPagination();
		$this->years       = $sermon_model->getYears();
		$this->months      = $sermon_model->getMonths();
		$books             = $sermon_model->getBooks();
		$this->hasTags     = $sermon_model->getTags();
		$this->filterForm  = $sermon_model->getFilterForm();

		// Get Category stuff from models
		$this->category = $sermon_model->getCategory();
		$this->parent   = $sermon_model->getParent();

		// Add filter to pagination, needed since it's no longer stored in userState.
		$this->pag_sermons->setAdditionalUrlParam('year', $this->state_sermons->get('date.year'));
		$this->pag_sermons->setAdditionalUrlParam('month', $this->state_sermons->get('date.month'));

		// Get series data from the series model
		$series_model       = $this->getModel('Series');
		$this->state_series = $series_model->getState();
		$this->state_series->set('speaker.id', $state->get('speaker.id'));
		$this->state_series->set('category.series.id', 0);
		$this->series     = $series_model->getItems();
		$this->pag_series = $series_model->getPagination();

		// Check if there are avatars at all, only showing column if needed
		$this->av = 0;

		foreach ($this->series as $serie)
		{
			if (!$this->av && !empty($serie->avatar))
			{
				$this->av = 1;
			}

			if (in_array('speaker:speaker', $this->col_serie))
			{
				$speakers = $series_model->getSpeakers($serie->id);
				$names    = array();

				foreach ($speakers as $speaker)
				{
					$speaker->speaker_slug  = $speaker->slug;
					$speaker->speaker_state = $speaker->state;
					$names[]                = LayoutHelper::render('titles.speaker', array('item' => $speaker, 'params' => $this->params));
				}

				$serie->speakers = implode(', ', $names);
			}
		}

		// Update Statistic
		if ($this->params->get('track_speaker'))
		{
			$user = Factory::getApplication()->getIdentity();

			if (!$user->authorise('com_sermonspeaker.hit', 'com_sermonspeaker'))
			{
				$model = $this->getModel();
				$model->hit();
			}
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		$js = 'function clear_all(){
			if(document.getElementById(\'filter_tag\')){
				document.getElementById(\'filter_tag\').value="";
			}
			if(document.getElementById(\'filter_books\')){
				document.getElementById(\'filter_books\').value=0;
			}
			if(document.getElementById(\'filter_months\')){
				document.getElementById(\'filter_months\').value=0;
			}
			if(document.getElementById(\'filter_years\')){
				document.getElementById(\'filter_years\').value=0;
			}
			if(document.getElementById(\'filter-search\')){
				document.getElementById(\'filter-search\').value="";
			}
		}';
		$this->getDocument()->addScriptDeclaration($js);

		// Build Books
		$groups = array();

		foreach ($books as $book)
		{
			switch ($book)
			{
				case ($book < 40):
					$group = 'OLD_TESTAMENT';
					break;
				case ($book < 67):
					$group = 'NEW_TESTAMENT';
					break;
				case ($book < 74):
					$group = 'APOCRYPHA';
					break;
				default:
					$group = 'CUSTOMBOOKS';
					break;
			}

			$object                    = new \stdClass;
			$object->value             = $book;
			$object->text              = Text::_('COM_SERMONSPEAKER_BOOK_' . $book);
			$groups[$group]['items'][] = $object;
		}

		foreach ($groups as $key => &$group)
		{
			$group['text'] = Text::_('COM_SERMONSPEAKER_' . $key);
		}

		$this->books = $groups;

		// Process the content plugins.
		PluginHelper::importPlugin('content');

		$this->item->text = $this->item->intro;
		$app->triggerEvent('onContentPrepare', array('com_sermonspeaker.speaker', &$this->item, &$this->params, 0));
		$this->item->intro = $this->item->text;

		$this->item->text = $this->item->bio;
		$app->triggerEvent('onContentPrepare', array('com_sermonspeaker.speaker', &$this->item, &$this->params, 0));
		$this->item->bio = $this->item->text;

		// Store the events for later
		$this->item->event                    = new \stdClass;
		$results                              = $app->triggerEvent('onContentAfterTitle', array('com_sermonspeaker.speaker', &$this->item, &$this->params, 0));
		$this->item->event->afterDisplayTitle = trim(implode("\n", $results));

		$results                                 = $app->triggerEvent('onContentBeforeDisplay', array('com_sermonspeaker.speaker', &$this->item, &$this->params, 0));
		$this->item->event->beforeDisplayContent = trim(implode("\n", $results));

		$results                                = $app->triggerEvent('onContentAfterDisplay', array('com_sermonspeaker.speaker', &$this->item, &$this->params, 0));
		$this->item->event->afterDisplayContent = trim(implode("\n", $results));

		// Trigger events for Sermons.
		foreach ($this->sermons as $item)
		{
			$item->event = new \stdClass;

			// Old plugins: Ensure that text property is available
			$item->text = $item->notes;

			$app->triggerEvent('onContentPrepare', array('com_sermonspeaker.sermons', &$item, &$this->params, 0));

			// Old plugins: Use processed text as notes
			$item->notes = $item->text;

			$results                        = $app->triggerEvent('onContentAfterTitle', array('com_sermonspeaker.sermons', &$item, &$this->params, 0));
			$item->event->afterDisplayTitle = trim(implode("\n", $results));

			$results                           = $app->triggerEvent('onContentBeforeDisplay', array('com_sermonspeaker.sermons', &$item, &$this->params, 0));
			$item->event->beforeDisplayContent = trim(implode("\n", $results));

			$results                          = $app->triggerEvent('onContentAfterDisplay', array('com_sermonspeaker.sermons', &$item, &$this->params, 0));
			$item->event->afterDisplayContent = trim(implode("\n", $results));
		}

		// Trigger events for Series.
		foreach ($this->series as $item)
		{
			$item->event = new \stdClass;

			// Old plugins: Ensure that text property is available
			$item->text = $item->series_description;

			$app->triggerEvent('onContentPrepare', array('com_sermonspeaker.series', &$item, &$this->params, 0));

			// Old plugins: Use processed text as notes
			$item->series_description = $item->text;

			$results                        = $app->triggerEvent('onContentAfterTitle', array('com_sermonspeaker.series', &$item, &$this->params, 0));
			$item->event->afterDisplayTitle = trim(implode("\n", $results));

			$results                           = $app->triggerEvent('onContentBeforeDisplay', array('com_sermonspeaker.series', &$item, &$this->params, 0));
			$item->event->beforeDisplayContent = trim(implode("\n", $results));

			$results                          = $app->triggerEvent('onContentAfterDisplay', array('com_sermonspeaker.series', &$item, &$this->params, 0));
			$item->event->afterDisplayContent = trim(implode("\n", $results));
		}

		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx', ''));
		$this->maxLevel      = $this->params->get('maxLevel', -1);
		$this->_prepareDocument();

		parent::display($tpl);
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
		$app   = Factory::getApplication();
		$menus = $app->getMenu();

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', Text::_('COM_SERMONSPEAKER_SPEAKER_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		// If the menu item does not concern this article
		if ($menu && ($menu->query['option'] != 'com_sermonspeaker' || $menu->query['view'] != 'speaker' || $menu->query['id'] != $this->item->id))
		{
			if ($this->item->title)
			{
				$title = $this->item->title;
			}
		}

		$this->setDocumentTitle($title);

		// Add Breadcrumbs
		$pathway = $app->getPathway();
		$pathway->addItem($this->item->title);

		// Set MetaData
		if ($this->item->metadesc)
		{
			$this->getDocument()->setDescription($this->item->metadesc);
		}
		elseif (!$this->item->metadesc && $this->params->get('menu-meta_description'))
		{
			$this->getDocument()->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->item->metakey)
		{
			$this->getDocument()->setMetaData('keywords', $this->item->metakey);
		}
		elseif (!$this->item->metakey && $this->params->get('menu-meta_keywords'))
		{
			$this->getDocument()->setMetaData('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->getDocument()->setMetaData('robots', $this->params->get('robots'));
		}

		// Add Metadata for Facebook Open Graph API
		if ($this->params->get('opengraph', 1))
		{
			$this->getDocument()->addCustomTag('<meta property="og:title" content="' . $this->escape($this->item->title) . '"/>');
			$this->getDocument()->addCustomTag('<meta property="og:url" content="' . htmlspecialchars(Uri::getInstance()->toString()) . '"/>');
			$this->getDocument()->addCustomTag('<meta property="og:description" content="' . $this->getDocument()->getDescription() . '"/>');
			$this->getDocument()->addCustomTag('<meta property="og:site_name" content="' . $app->get('sitename') . '"/>');
			$this->getDocument()->addCustomTag('<meta property="og:type" content="article"/>');

			if ($this->item->pic)
			{
				$this->getDocument()->addCustomTag('<meta property="og:image" content="' . Sermonspeaker\Component\Sermonspeaker\Site\Helper\SermonspeakerHelper::makeLink($this->item->pic, true) . '"/>');
			}
		}
	}
}
