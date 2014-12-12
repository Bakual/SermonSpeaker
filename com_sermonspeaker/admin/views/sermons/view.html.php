<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2014 - Thomas Hunziker
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
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		$layout = $this->getLayout();

		$this->state         = $this->get('State');
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->speakers      = $this->get('Speakers');
		$this->series        = $this->get('Series');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// We don't need toolbar and sidebar in the modal window.
		if ($layout !== 'modal')
		{
			$this->addToolbar();
			SermonspeakerHelper::addSubmenu('sermons');
			$this->sidebar = JHtmlSidebar::render();
		}

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 */
	protected function addToolbar()
	{
		$canDo = SermonspeakerHelper::getActions();
		JToolBarHelper::title(JText::_('COM_SERMONSPEAKER_SERMONS_TITLE'), 'quote-3 sermons');

		if ($canDo->get('core.create'))
		{
			JToolBarHelper::addNew('sermon.add', 'JTOOLBAR_NEW');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))
		{
			JToolBarHelper::editList('sermon.edit', 'JTOOLBAR_EDIT');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::divider();
			JToolBarHelper::custom('sermons.publish', 'publish', '','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('sermons.unpublish', 'unpublish', '', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();

			if ($this->state->get('filter.state') != 2)
			{
				JToolBarHelper::archiveList('sermons.archive','JTOOLBAR_ARCHIVE');
			}
			else
			{
				JToolBarHelper::unarchiveList('sermons.publish', 'JTOOLBAR_UNARCHIVE');
			}

			JToolBarHelper::checkin('sermons.checkin');
		}

		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete'))
		{
			JToolBarHelper::deleteList('', 'sermons.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::trash('sermons.trash', 'JTOOLBAR_TRASH');
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::custom('tools.order', 'purge icon-lightning', '', 'COM_SERMONSPEAKER_TOOLS_ORDER', false);
			JToolBarHelper::divider();
		}

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		// Add a batch button
		if ($canDo->get('core.edit'))
		{
			$title = JText::_('JTOOLBAR_BATCH');
			$dhtml = '<button data-toggle="modal" data-target="#collapseModal" class="btn btn-small">'
						. '<i class="icon-checkbox-partial" title="' . $title . '"></i>'
						. $title . '</button>';
			$bar->appendButton('Custom', $dhtml, 'batch');
		}

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			JToolBarHelper::preferences('com_sermonspeaker');
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
			'sermons.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'sermons.state' => JText::_('JSTATUS'),
			'sermons.podcast' => JText::_('COM_SERMONSPEAKER_FIELD_SERMONCAST_LABEL'),
			'sermons.title' => JText::_('JGLOBAL_TITLE'),
			'category_title' => JText::_('JCATEGORY'),
			'speaker_title' => JText::_('COM_SERMONSPEAKER_SPEAKER'),
			'scripture' => JText::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'),
			'series_title' => JText::_('COM_SERMONSPEAKER_SERIE'),
			'sermons.sermon_date' => JText::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'),
			'sermons.hits' => JText::_('JGLOBAL_HITS'),
			'language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'sermons.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
