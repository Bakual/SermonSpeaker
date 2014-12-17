<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2014 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerViewSpeakers extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$layout = $this->getLayout();

		if ($layout !== 'modal')
		{
			SermonspeakerHelper::addSubmenu('speakers');
		}

		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
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
	 */
	protected function addToolbar()
	{
		$canDo = SermonspeakerHelper::getActions();
		JToolBarHelper::title(JText::_('COM_SERMONSPEAKER_SPEAKERS_TITLE'), 'users speakers');

		if ($canDo->get('core.create'))
		{
			JToolBarHelper::addNew('speaker.add','JTOOLBAR_NEW');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))
		{
			JToolBarHelper::editList('speaker.edit','JTOOLBAR_EDIT');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::divider();
			JToolBarHelper::custom('speakers.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('speakers.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();

			if ($this->state->get('filter.state') != 2)
			{
				JToolBarHelper::archiveList('speakers.archive','JTOOLBAR_ARCHIVE');
			}
			else
			{
				JToolBarHelper::unarchiveList('speakers.publish', 'JTOOLBAR_UNARCHIVE');
			}

			JToolBarHelper::checkin('speakers.checkin');
		}

		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete'))
		{
			JToolBarHelper::deleteList('', 'speakers.delete','JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::trash('speakers.trash','JTOOLBAR_TRASH');
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::custom('tools.speakersorder', 'purge icon-lightning', '','COM_SERMONSPEAKER_TOOLS_ORDER', false);
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
			'speakers.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'speakers.state' => JText::_('JSTATUS'),
			'speakers.title' => JText::_('COM_SERMONSPEAKER_FIELD_NAME_LABEL'),
			'category_title' => JText::_('JCATEGORY'),
			'speakers.pic' => JText::_('COM_SERMONSPEAKER_FIELD_PICTURE_LABEL'),
			'speakers.home' => JText::_('JDEFAULT'),
			'speakers.hits' => JText::_('JGLOBAL_HITS'),
			'language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'speakers.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
