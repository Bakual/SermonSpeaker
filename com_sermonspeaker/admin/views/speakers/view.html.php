<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerViewSpeakers extends JViewLegacy
{
	/**
	 * Holds an array of item objects
	 *
	 * @var    array
	 *
	 * @since  ?
	 */
	protected $items;

	/**
	 * The pagination object
	 *
	 * @var  \JPagination
	 *
	 * @since  ?
	 */
	protected $pagination;

	/**
	 * A state object
	 *
	 * @var    JObject
	 *
	 * @since  ?
	 */
	protected $state;

	/**
	 * Form object for search filters
	 *
	 * @var  \JForm
	 *
	 * @since  ?
	 */
	public $filterForm;

	/**
	 * The active search filters
	 *
	 * @var  array
	 *
	 * @since  ?
	 */
	public $activeFilters;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @throws Exception
	 *
	 * @since  ?
	 */
	public function display($tpl = null)
	{
		$layout = $this->getLayout();

		$this->state         = $this->get('State');
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		if (!count($this->items) && $this->get('IsEmptyState'))
		{
			$this->setLayout('emptystate');
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		// We don't need toolbar in the modal window.
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
			// In speaker associations modal we need to remove language filter if forcing a language.
			// We also need to change the category filter to show show categories with All or the forced language.
			if ($forcedLanguage = JFactory::getApplication()->input->get('forcedLanguage', '', 'CMD'))
			{
				// If the language is forced we can't allow to select the language, so transform the language selector filter into an hidden field.
				$languageXml = new SimpleXMLElement('<field name="language" type="hidden" default="' . $forcedLanguage . '" />');
				$this->filterForm->setField($languageXml, 'filter', true);

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
	 * @since  ?
	 */
	protected function addToolbar()
	{
		$canDo = SermonspeakerHelper::getActions();
		$user  = Factory::getUser();

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance();

		ToolbarHelper::title(Text::_('COM_SERMONSPEAKER_SPEAKERS_TITLE'), 'users speakers');

		if ($canDo->get('core.create'))
		{
			$toolbar->addNew('speaker.add');
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

			$childBar->publish('speakers.publish')->listCheck(true);

			$childBar->unpublish('speakers.unpublish')->listCheck(true);

			$childBar->archive('speakers.archive')->listCheck(true);

			if ($user->authorise('core.admin'))
			{
				$childBar->checkin('speakers.checkin')->listCheck(true);
			}

			if ($this->state->get('filter.published') != -2)
			{
				$childBar->trash('speakers.trash')->listCheck(true);
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
			$toolbar->standardButton('lightning', 'COM_SERMONSPEAKER_TOOLS_ORDER', 'tools.speakersorder');
		}

		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete'))
		{
			$toolbar->delete('speakers.delete')
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
	protected function getSortFields()
	{
		return array(
			'speakers.ordering' => Text::_('JGRID_HEADING_ORDERING'),
			'speakers.state'    => Text::_('JSTATUS'),
			'speakers.title'    => Text::_('COM_SERMONSPEAKER_FIELD_NAME_LABEL'),
			'category_title'    => Text::_('JCATEGORY'),
			'speakers.pic'      => Text::_('COM_SERMONSPEAKER_FIELD_PICTURE_LABEL'),
			'speakers.home'     => Text::_('JDEFAULT'),
			'speakers.hits'     => Text::_('JGLOBAL_HITS'),
			'language'          => Text::_('JGRID_HEADING_LANGUAGE'),
			'speakers.id'       => Text::_('JGRID_HEADING_ID'),
		);
	}
}
