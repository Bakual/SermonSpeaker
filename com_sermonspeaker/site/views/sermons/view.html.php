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
class SermonspeakerViewSermons extends JViewLegacy
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
		// Get some data from the models
		$this->state      = $this->get('State');
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->years      = $this->get('Years');
		$this->months     = $this->get('Months');
		$books            = $this->get('Books');

		// Get Category stuff from models
		$this->category = $this->get('Category');
		$children       = $this->get('Children');
		$this->parent   = $this->get('Parent');
		$this->children = array($this->category->id => $children);

		// Add view to pagination, needed since it may be called from module?
		$this->pagination->setAdditionalUrlParam('view', 'sermons');

		// Add filter to pagination, needed since it's no longer stored in userState.
		$this->pagination->setAdditionalUrlParam('year', $this->state->get('date.year'));
		$this->pagination->setAdditionalUrlParam('month', $this->state->get('date.month'));
		$this->params = $this->state->get('params');

		if ((int) $this->params->get('limit', ''))
		{
			$this->params->set('filter_field', 0);
			$this->params->set('show_pagination_limit', 0);
			$this->params->set('show_pagination', 0);
			$this->params->set('show_pagination_results', 0);
		}

		$this->columns = $this->params->get('col');

		if (!$this->columns)
		{
			$this->columns = array();
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

		// Check whether category access level allows access.
		$user   = JFactory::getUser();
		$groups = $user->getAuthorisedViewLevels();

		if (!in_array($this->category->access, $groups))
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$app = JFactory::getApplication();

		// Run plugin events for each item.
		foreach ($this->items as $item)
		{
			$item->event = new stdClass;

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

		// Set layout from parameters if not already set elsewhere
		if ($this->getLayout() == 'default')
		{
			$this->setLayout($this->params->get('sermonslayout', 'table'));
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
			adminForm.submit();
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
			$this->params->def('page_heading', JText::_('COM_SERMONSPEAKER_SERMONS_TITLE'));
		}

		$title = $this->params->get('page_title', '');

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

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetaData('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetaData('robots', $this->params->get('robots'));
		}

		// Add feed links
		if ($this->params->get('show_feed_link', 1))
		{
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$this->document->addHeadLink(JRoute::_('&view=feed&format=raw&catid=' . $this->category->id), 'alternate', 'rel', $attribs);
		}
	}
}
