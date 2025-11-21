<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Site\View\Sermons;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use Sermonspeaker\Component\Sermonspeaker\Site\Model\SermonsModel;
use stdClass;

defined('_JEXEC') or die();

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class HtmlView extends BaseHtmlView
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
	public function display($tpl = null): void
	{
		// Get some data from the models
		/** @var SermonsModel $model */
		$model = $this->getModel();
		$this->state      = $model->getState();
		$this->items      = $model->getItems();
		$this->pagination = $model->getPagination();
		$this->years      = $model->getYears();
		$this->months     = $model->getMonths();
		$books            = $model->getBooks();
		$this->hasTags    = $model->getTags();
		$this->filterForm = $model->getFilterForm();

		// Get Category stuff from models
		$this->category       = $model->getCategory();
		$this->category->tags = new TagsHelper;
		$this->category->tags->getItemTags('com_sermonspeaker.sermons.category', $this->category->id);

		$children       = $model->getChildren();
		$this->parent   = $model->getParent();
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
		if (count($errors = $model->getErrors()))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		if (!$this->category)
		{
			throw new Exception(Text::_('JGLOBAL_CATEGORY_NOT_FOUND'), 404);
		}

		if (!$this->parent && $this->category->id != 'root')
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
		$user   = Factory::getApplication()->getIdentity();
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

		// Build Books
		$groups = array();

		foreach ($books as $book)
		{
			$group = match ($book)
			{
				$book < 40 => 'OLD_TESTAMENT',
				$book < 67 => 'NEW_TESTAMENT',
				$book < 74 => 'APOCRYPHA',
				default => 'CUSTOMBOOKS',
			};

			$object                    = new stdClass;
			$object->value             = $book;
			$object->text              = Text::_('COM_SERMONSPEAKER_BOOK_' . $book);
			$groups[$group]['items'][] = $object;
		}

		foreach ($groups as $key => &$group)
		{
			$group['text'] = Text::_('COM_SERMONSPEAKER_' . $key);
		}

		$this->books = $groups;

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
	 * @throws \Exception
	 * @since ?
	 */
	protected function _prepareDocument(): void
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
			$this->params->def('page_heading', Text::_('COM_SERMONSPEAKER_SERMONS_TITLE'));
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

		// Add feed links
		if ($this->params->get('show_feed_link', 1))
		{
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$this->getDocument()->addHeadLink(Route::_('&view=feed&format=raw&catid=' . $this->category->id), 'alternate', 'rel', $attribs);
		}
	}
}
