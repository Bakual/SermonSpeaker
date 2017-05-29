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
class SermonspeakerViewSerie extends JViewLegacy
{
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
		$app = JFactory::getApplication();

		if (!$app->input->get('id', 0, 'int'))
		{
			$app->enqueueMessage(JText::_('JGLOBAL_RESOURCE_NOT_FOUND'), 'error');
			$app->redirect(JRoute::_('index.php?view=series'));
		}

		// Applying CSS file
		JHtml::_('stylesheet', 'com_sermonspeaker/sermonspeaker.css', array('relative' => true));
		require_once JPATH_COMPONENT . '/helpers/player.php';

		// Initialise variables.
		$user = JFactory::getUser();

		// Get some data from the model
		$this->item = $this->get('Item');

		if (!$this->item)
		{
			$app->enqueueMessage(JText::_('JGLOBAL_RESOURCE_NOT_FOUND'), 'error');
			$app->redirect(JRoute::_('index.php?view=series'));
		}

		// Get Tags
		$this->item->tags = new JHelperTags;
		$this->item->tags->getItemTags('com_sermonspeaker.serie', $this->item->id);

		// Check if access is not public
		if ($this->item->category_access)
		{
			$groups = $user->getAuthorisedViewLevels();

			if (!in_array($this->item->category_access, $groups))
			{
				$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
				$app->redirect(JRoute::_('index.php?view=series'));
			}
		}

		// Get Params
		$state        = $this->get('State');
		$this->params = $state->get('params');

		// Get sermons data from the sermons model
		$sermon_model = $this->getModel('Sermons');
		$this->state  = $sermon_model->getState();
		$this->state->set('serie.id', $state->get('serie.id'));
		$this->items      = $sermon_model->getItems();
		$this->pagination = $sermon_model->getPagination();
		$this->years      = $sermon_model->getYears();
		$this->months     = $sermon_model->getMonths();
		$books            = $sermon_model->getBooks();

		// Get Category stuff from models
		$this->category = $sermon_model->getCategory();
		$this->parent   = $sermon_model->getParent();

		// Add filter to pagination, needed since it's no longer stored in userState.
		$this->pagination->setAdditionalUrlParam('year', $this->state->get('date.year'));
		$this->pagination->setAdditionalUrlParam('month', $this->state->get('date.month'));

		$this->columns = $this->params->get('col');

		if (!$this->columns)
		{
			$this->columns = array();
		}

		$this->col_serie = $this->params->get('col_serie');

		if (!$this->col_serie)
		{
			$this->col_serie = array();
		}

		if (in_array('series:speaker', $this->col_serie))
		{
			$model    = $this->getModel();
			$speakers = $model->getSpeakers($this->item->id);
			$names    = array();

			foreach ($speakers as $speaker)
			{
				$speaker->speaker_slug  = $speaker->slug;
				$speaker->speaker_state = $speaker->state;
				$names[]                = JLayoutHelper::render('titles.speaker', array('item' => $speaker, 'params' => $this->params));
			}

			$this->item->speakers = implode(', ', $names);
		}

		// Update Statistic
		if ($this->params->get('track_series'))
		{
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

		// Set layout from parameters if not already set elsewhere
		if ($this->getLayout() == 'default')
		{
			$this->setLayout($this->params->get('serielayout', 'table'));
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

		// Process the content plugins.
		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('content');

		$this->item->text = $this->item->series_description;
		$dispatcher->trigger('onContentPrepare', array('com_sermonspeaker.serie', &$this->item, &$this->params, 0));
		$this->item->series_description = $this->item->text;

		// Store the events for later
		$this->item->event                    = new stdClass;
		$results                              = $dispatcher->trigger('onContentAfterTitle', array('com_sermonspeaker.serie', &$this->item, &$this->params, 0));
		$this->item->event->afterDisplayTitle = trim(implode("\n", $results));

		$results                                 = $dispatcher->trigger('onContentBeforeDisplay', array('com_sermonspeaker.serie', &$this->item, &$this->params, 0));
		$this->item->event->beforeDisplayContent = trim(implode("\n", $results));

		$results                                = $dispatcher->trigger('onContentAfterDisplay', array('com_sermonspeaker.serie', &$this->item, &$this->params, 0));
		$this->item->event->afterDisplayContent = trim(implode("\n", $results));

		// Trigger events for Sermons.
		foreach ($this->items as $item)
		{
			$item->event = new stdClass;

			// Old plugins: Ensure that text property is available
			$item->text = $item->notes;

			$dispatcher->trigger('onContentPrepare', array('com_sermonspeaker.sermons', &$item, &$this->params, 0));

			// Old plugins: Use processed text as notes
			$item->notes = $item->text;

			$results                        = $dispatcher->trigger('onContentAfterTitle', array('com_sermonspeaker.sermons', &$item, &$this->params, 0));
			$item->event->afterDisplayTitle = trim(implode("\n", $results));

			$results                           = $dispatcher->trigger('onContentBeforeDisplay', array('com_sermonspeaker.sermons', &$item, &$this->params, 0));
			$item->event->beforeDisplayContent = trim(implode("\n", $results));

			$results                          = $dispatcher->trigger('onContentAfterDisplay', array('com_sermonspeaker.sermons', &$item, &$this->params, 0));
			$item->event->afterDisplayContent = trim(implode("\n", $results));
		}

		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
		$this->maxLevel      = $this->params->get('maxLevel', -1);
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
			$this->params->def('page_heading', JText::_('COM_SERMONSPEAKER_SERIE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		// If the menu item does not concern this article
		if ($menu && ($menu->query['option'] != 'com_sermonspeaker' || $menu->query['view'] != 'serie' || $menu->query['id'] != $this->item->id))
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
			$this->document->setMetaData('keywords', $this->item->metakey);
		}
		elseif (!$this->item->metakey && $this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetaData('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetaData('robots', $this->params->get('robots'));
		}

		// Add Metadata for Facebook Open Graph API
		if ($this->params->get('opengraph', 1))
		{
			$this->document->addCustomTag('<meta property="og:title" content="' . $this->escape($this->item->title) . '"/>');
			$this->document->addCustomTag('<meta property="og:url" content="' . JUri::getInstance()->toString() . '"/>');
			$this->document->addCustomTag('<meta property="og:description" content="' . $this->document->getDescription() . '"/>');
			$this->document->addCustomTag('<meta property="og:site_name" content="' . $app->get('sitename') . '"/>');
			$this->document->addCustomTag('<meta property="og:type" content="article"/>');

			if ($this->item->avatar)
			{
				$this->document->addCustomTag('<meta property="og:image" content="'
					. SermonspeakerHelperSermonspeaker::makeLink($this->item->avatar, true) . '"/>');
			}
		}
	}
}
