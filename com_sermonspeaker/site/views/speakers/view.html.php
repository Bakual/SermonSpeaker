<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerViewSpeakers extends HtmlView
{
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
		// Get some data from the models
		$this->state      = $this->get('State');
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->hasTags    = $this->get('Tags');

		// Get Category stuff from models
		$this->category       = $this->get('Category');
		$this->category->tags = new TagsHelper;
		$this->category->tags->getItemTags('com_sermonspeaker.speakers.category', $this->category->id);
		$children          = $this->get('Children');
		$this->parent      = $this->get('Parent');
		$this->children    = array($this->category->id => $children);
		$this->params      = $this->state->get('params');
		$this->col_speaker = $this->params->get('col_speaker');

		if (!$this->col_speaker)
		{
			$this->col_speaker = array();
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		if ($this->category == false)
		{
			throw new Exception(Text::_('JGLOBAL_CATEGORY_NOT_FOUND'), 404);
		}

		if ($this->parent == false && $this->category->id != 'root')
		{
			throw new Exception(Text::_('JGLOBAL_CATEGORY_NOT_FOUND'), 404);
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
		$user   = Factory::getUser();
		$groups = $user->getAuthorisedViewLevels();

		if (!in_array($this->category->access, $groups))
		{
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$app = Factory::getApplication();

		// Run plugin events for each item.
		foreach ($this->items as $item)
		{
			$item->event = new stdClass;

			// Old plugins: Ensure that text property is available
			$item->text = $item->intro;

			$app->triggerEvent('onContentPrepare', array('com_sermonspeaker.speakers', &$item, &$this->params, 0));

			// Old plugins: Use processed text as notes
			$item->intro = $item->text;

			$results                        = $app->triggerEvent('onContentAfterTitle', array('com_sermonspeaker.speakers', &$item, &$this->params, 0));
			$item->event->afterDisplayTitle = trim(implode("\n", $results));

			$results                           = $app->triggerEvent('onContentBeforeDisplay', array('com_sermonspeaker.speakers', &$item, &$this->params, 0));
			$item->event->beforeDisplayContent = trim(implode("\n", $results));

			$results                          = $app->triggerEvent('onContentAfterDisplay', array('com_sermonspeaker.speakers', &$item, &$this->params, 0));
			$item->event->afterDisplayContent = trim(implode("\n", $results));
		}

		// Set layout from parameters if not already set elsewhere
		if ($this->getLayout() == 'default')
		{
			$this->setLayout($this->params->get('speakerslayout', 'normal'));
		}

		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx', ''));
		$this->maxLevel      = $this->params->get('maxLevel', -1) < 0 ? PHP_INT_MAX : $this->params->get('maxLevel', PHP_INT_MAX);
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

		// Because the application sets a default page title, we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', Text::_('COM_SERMONSPEAKER_SPEAKERS_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		$this->setDocumentTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->getDocument()->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->getDocument()->setMetaData('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->getDocument()->setMetaData('robots', $this->params->get('robots'));
		}
	}
}
