<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\View\Sermons;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Sermonspeaker\Component\Sermonspeaker\Administrator\Helper\SermonspeakerHelper;
use Sermonspeaker\Component\Sermonspeaker\Administrator\Model\SermonsModel;

defined('_JEXEC') or die;

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * Form object for search filters
	 *
	 * @var  Form
	 *
	 * @since  ?
	 */
	public Form $filterForm;
	/**
	 * The active search filters
	 *
	 * @var  array
	 *
	 * @since  ?
	 */
	public array $activeFilters;
	/**
	 * Holds an array of item objects or false
	 *
	 * @var    mixed
	 *
	 * @since  ?
	 */
	protected mixed $items;
	/**
	 * The pagination object
	 *
	 * @var  Pagination
	 *
	 * @since  ?
	 */
	protected Pagination $pagination;

	/**
	 * A state object
	 *
	 * @var    \Joomla\Registry\Registry
	 *
	 * @since  ?
	 */
	protected $state;

	/**
	 * Execute and display a template script.
	 *
	 * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @throws \Exception
	 *
	 * @since  ?
	 */
	public function display($tpl = null): void
	{
		/** @var SermonsModel $model */
		$model  = $this->getModel();
		$layout = $this->getLayout();

		$this->state         = $model->getState();
		$this->items         = $model->getItems();
		$this->pagination    = $model->getPagination();
		$this->speakers      = $model->getSpeakers();
		$this->series        = $model->getSeries();
		$this->filterForm    = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();

		if (!$this->items && $model->getIsEmptyState())
		{
			$this->setLayout('emptystate');
		}

		// We don't need toolbar and sidebar in the modal window.
		if ($layout !== 'modal')
		{
			$this->addToolbar();

			// We do not need to filter by language when multilingual is disabled
			if (!Multilanguage::isEnabled())
			{
				unset($this->activeFilters['language']);
				$this->filterForm->removeField('language', 'filter');
			}
		}
		else
		{
			// In sermon associations modal we need to remove language filter if forcing a language.
			// We also need to change the category filter to show show categories with All or the forced language.
			if ($forcedLanguage = Factory::getApplication()->input->get('forcedLanguage', '', 'CMD'))
			{
				// If the language is forced we can't allow to select the language, so transform the language selector filter into an hidden field.
				$languageXml = new \SimpleXMLElement('<field name="language" type="hidden" default="' . $forcedLanguage . '" />');
				$this->filterForm->setField($languageXml, 'filter');

				// Also, unset the active language filter so the search tools is not open by default with this filter.
				unset($this->activeFilters['language']);

				// One last changes needed is to change the category filter to just show categories with All language or with the forced language.
				$this->filterForm->setFieldAttribute('category_id', 'language', '*,' . $forcedLanguage, 'filter');
			}
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 * @since  ?
	 */
	protected function addToolbar(): void
	{
		$canDo = SermonspeakerHelper::getActions();
		$user  = Factory::getApplication()->getIdentity();

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance();

		ToolbarHelper::title(Text::_('COM_SERMONSPEAKER_SERMONS_TITLE'), 'quote-3 sermons');

		if ($canDo->get('core.create') || count($user->getAuthorisedCategories('com_sermonspeaker', 'core.create')) > 0)
		{
			$toolbar->addNew('sermon.add');
		}

		if ($canDo->get('core.edit.state'))
		{
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('fas fa-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();

			$childBar->publish('sermons.publish')->listCheck(true);

			$childBar->unpublish('sermons.unpublish')->listCheck(true);

			$childBar->archive('sermons.archive')->listCheck(true);

			$childBar->standardButton('feed', 'COM_SERMONSPEAKER_PODCASTED', 'sermons.podcast_publish')->listCheck(true);

			$childBar->standardButton('feed', 'COM_SERMONSPEAKER_UNPODCASTED', 'sermons.podcast_unpublish')->listCheck(true);

			if ($user->authorise('core.admin'))
			{
				$childBar->checkin('sermons.checkin')->listCheck(true);
			}

			if ($this->state->get('filter.published') != -2)
			{
				$childBar->trash('sermons.trash')->listCheck(true);
			}

			// Add a batch button
			if ($user->authorise('core.create', 'com_sermonspeaker')
				&& $user->authorise('core.edit', 'com_sermonspeaker')
				&& $user->authorise('core.edit.state', 'com_sermonspeaker'))
			{
				$childBar->popupButton('batch')
					->text('JTOOLBAR_BATCH')
					->selector('collapseModal')
					->listCheck(true);
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			$toolbar->standardButton('lightning', 'COM_SERMONSPEAKER_TOOLS_ORDER', 'tools.order');
		}

		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete'))
		{
			$toolbar->delete('sermons.delete')
				->text('JTOOLBAR_EMPTY_TRASH')
				->message('JGLOBAL_CONFIRM_DELETE')
				->listCheck(true);
		}

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			$toolbar->preferences('com_sermonspeaker');
		}
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields(): array
	{
		return array(
			'sermons.ordering'    => Text::_('JGRID_HEADING_ORDERING'),
			'sermons.state'       => Text::_('JSTATUS'),
			'sermons.podcast'     => Text::_('COM_SERMONSPEAKER_FIELD_SERMONCAST_LABEL'),
			'sermons.title'       => Text::_('JGLOBAL_TITLE'),
			'category_title'      => Text::_('JCATEGORY'),
			'speaker_title'       => Text::_('COM_SERMONSPEAKER_SPEAKER'),
			'scripture'           => Text::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'),
			'series_title'        => Text::_('COM_SERMONSPEAKER_SERIE'),
			'sermons.sermon_date' => Text::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'),
			'sermons.hits'        => Text::_('JGLOBAL_HITS'),
			'language'            => Text::_('JGRID_HEADING_LANGUAGE'),
			'sermons.id'          => Text::_('JGRID_HEADING_ID'),
		);
	}
}
