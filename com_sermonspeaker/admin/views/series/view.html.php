<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerViewSeries extends JViewLegacy
{
	protected $items;

	protected $pagination;

	/**
	 * A state object
	 *
	 * @var    JObject
	 *
	 * @since  ?
	 */
	protected $state;

	public $filterForm;

	public $activeFilters;

	protected $sidebar;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return mixed A string if successful, otherwise a Error object.
	 *
	 * @throws Exception
	 *
	 * @since  ?
	 */
	public function display($tpl = null)
	{
		$layout = $this->getLayout();

		if ($layout !== 'modal')
		{
			SermonspeakerHelper::addSubmenu('series');
		}

		$this->state         = $this->get('State');
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// We don't need toolbar in the modal window.
		if ($layout !== 'modal')
		{
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
		}

		return parent::display($tpl);
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

		// Get the toolbar object instance
		$bar = JToolbar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_SERMONSPEAKER_SERIES_TITLE'), 'drawer-2 series');

		if ($canDo->get('core.create'))
		{
			JToolbarHelper::addNew('serie.add', 'JTOOLBAR_NEW');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))
		{
			JToolbarHelper::editList('serie.edit', 'JTOOLBAR_EDIT');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::custom('series.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::custom('series.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);

			if ($this->state->get('filter.state') != 2)
			{
				JToolbarHelper::archiveList('series.archive', 'JTOOLBAR_ARCHIVE');
			}
			else
			{
				JToolbarHelper::unarchiveList('series.publish', 'JTOOLBAR_UNARCHIVE');
			}

			JToolbarHelper::checkin('series.checkin');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::custom('tools.seriesorder', 'purge icon-lightning', '', 'COM_SERMONSPEAKER_TOOLS_ORDER', false);
		}

		// Add a batch button
		if ($canDo->get('core.edit'))
		{
			$title = JText::_('JTOOLBAR_BATCH');

			// Instantiate a new JLayoutFile instance and render the batch button
			$layout = new JLayoutFile('joomla.toolbar.batch');

			$dhtml = $layout->render(array('title' => $title));
			$bar->appendButton('Custom', $dhtml, 'batch');
		}

		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'series.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('series.trash', 'JTOOLBAR_TRASH');
		}

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			JToolbarHelper::preferences('com_sermonspeaker');
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
			'series.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'series.state'    => JText::_('JSTATUS'),
			'series.title'    => JText::_('JGLOBAL_TITLE'),
			'category_title'  => JText::_('JCATEGORY'),
			'series.avatar'   => JText::_('COM_SERMONSPEAKER_FIELD_PICTURE_LABEL'),
			'series.home'     => JText::_('JDEFAULT'),
			'series.hits'     => JText::_('JGLOBAL_HITS'),
			'language'        => JText::_('JGRID_HEADING_LANGUAGE'),
			'series.id'       => JText::_('JGRID_HEADING_ID'),
		);
	}
}
