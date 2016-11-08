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
class SermonspeakerViewSpeaker extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();

		if (!$app->input->get('id', 0, 'int'))
		{
			$app->redirect(JRoute::_('index.php?view=speakers'), JText::_('JGLOBAL_RESOURCE_NOT_FOUND'), 'error');
		}

		require_once JPATH_COMPONENT . '/helpers/player.php';

		// Get data from the model
		$state         = $this->get('State');
		$this->item    = $this->get('Item');
		$user          = JFactory::getUser();
		$this->params  = $state->get('params');
		$this->columns = $this->params->get('col_speaker');

		if (!$this->columns)
		{
			$this->columns = array();
		}

		$this->col_sermon = $this->params->get('col');

		if (!$this->col_sermon)
		{
			$this->col_sermon = array();
		}

		$this->col_serie = $this->params->get('col_serie');

		if (!$this->col_serie)
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
			$app->redirect(JRoute::_('index.php?view=speakers'), JText::_('JGLOBAL_RESOURCE_NOT_FOUND'), 'error');
		}

		// Get Tags
		$this->item->tags = new JHelperTags;
		$this->item->tags->getItemTags('com_sermonspeaker.speaker', $this->item->id);

		// Check if access is not public
		if ($this->item->category_access)
		{
			$groups = $user->getAuthorisedViewLevels();

			if (!in_array($this->item->category_access, $groups))
			{
				$app->redirect(JRoute::_('index.php?view=speakers'), JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			}
		}

		// Get sermons data from the sermons model
		$sermon_model        = $this->getModel('Sermons');
		$this->state_sermons = $sermon_model->getState();
		$this->state_sermons->set('speaker.id', $state->get('speaker.id'));
		$this->sermons     = $sermon_model->getItems();
		$this->pag_sermons = $sermon_model->getPagination();
		$this->years       = $sermon_model->getYears();
		$this->months      = $sermon_model->getMonths();
		$books             = $sermon_model->getBooks();

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
					$names[]                = JLayoutHelper::render('titles.speaker', array('item' => $speaker, 'params' => $this->params));
				}

				$serie->speakers = implode(', ', $names);
			}
		}

		// Update Statistic
		if ($this->params->get('track_speaker'))
		{
			$user = JFactory::getUser();

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

		if ($this->category == false)
		{
			throw new Exception(JText::_('JGLOBAL_CATEGORY_NOT_FOUND'), 404);
		}

		if ($this->parent == false && $this->category->id != 'root')
		{
			throw new Exception(JText::_('JGLOBAL_CATEGORY_NOT_FOUND'), 404);
		}

		if ($this->category->id == 'root')
		{
			$this->params->set('show_category_title', 0);
			$this->cat = '';
		}
		else
		{
			// Get the category title for backward compatibility
			$this->cat = $this->category->title;
		}

		$js = 'function clear_all(){
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
		$this->document->addScriptDeclaration($js);

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

			$object           = new stdClass;
			$object->value    = $book;
			$object->text     = JText::_('COM_SERMONSPEAKER_BOOK_' . $book);
			$groups[$group][] = $object;
		}

		foreach ($groups as $key => &$group)
		{
			array_unshift($group, JHtml::_('select.optgroup', JText::_('COM_SERMONSPEAKER_' . $key)));
			array_push($group, JHtml::_('select.optgroup', JText::_('COM_SERMONSPEAKER_' . $key)));
		}

		$this->books = array_reduce($groups, 'array_merge', array());

		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
		$this->maxLevel      = $this->params->get('maxLevel', -1);
		$this->_prepareDocument();
		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 */
	protected function _prepareDocument()
	{
		$app     = JFactory::getApplication();
		$menus   = $app->getMenu();
		$pathway = $app->getPathway();

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_SERMONSPEAKER_SPEAKER_TITLE'));
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
		$pathway->addItem($this->item->title);

		// Set MetaData
		if ($this->item->metadesc)
		{
			$this->document->setDescription($this->item->metadesc);
		}
		elseif (!$this->item->metadesc && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->item->metakey)
		{
			$this->document->setMetadata('keywords', $this->item->metakey);
		}
		elseif (!$this->item->metakey && $this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

		// Add Metadata for Facebook Open Graph API
		if ($this->params->get('opengraph', 1))
		{
			$this->document->addCustomTag('<meta property="og:title" content="' . $this->escape($this->item->title) . '"/>');
			$this->document->addCustomTag('<meta property="og:url" content="' . htmlspecialchars(JUri::getInstance()->toString()) . '"/>');
			$this->document->addCustomTag('<meta property="og:description" content="' . $this->document->getDescription() . '"/>');
			$this->document->addCustomTag('<meta property="og:site_name" content="' . $app->get('sitename') . '"/>');
			$this->document->addCustomTag('<meta property="og:type" content="article"/>');

			if ($this->item->pic)
			{
				$this->document->addCustomTag('<meta property="og:image" content="' . SermonSpeakerHelperSermonSpeaker::makelink($this->item->pic, true) . '"/>');
			}
		}
	}
}
