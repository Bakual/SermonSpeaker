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
class SermonspeakerViewSerie extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		$this->addToolbar();

		return parent::display($tpl);
	}
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$user       = JFactory::getUser();
		$userId     = $user->get('id');
		$isNew      = ($this->item->id == 0);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		$canDo      = SermonspeakerHelper::getActions();
		JToolbarHelper::title(JText::sprintf('COM_SERMONSPEAKER_PAGE_' . ($checkedOut ? 'VIEW' : ($isNew ? 'ADD' : 'EDIT')), JText::_('COM_SERMONSPEAKER_SERIES_TITLE'), JText::_('COM_SERMONSPEAKER_SERIE')), 'pencil-2 series');

		// Build the actions for new and existing records.
		if ($isNew)
		{
			// For new records, check the create permission.
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::apply('serie.apply');
				JToolBarHelper::save('serie.save');
				JToolbarHelper::save2new('serie.save2new');
			}
			JToolbarHelper::cancel('serie.cancel');
		}
		else
		{
			// Can't save the record if it's checked out.
			if (!$checkedOut)
			{
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId))
				{
					JToolBarHelper::apply('serie.apply');
					JToolBarHelper::save('serie.save');

					// We can save this record, but check the create permission to see if we can return to make a new one.
					if ($canDo->get('core.create'))
					{
						JToolbarHelper::save2new('serie.save2new');
					}
				}
			}

			// If checked out, we can still save to copy
			if ($canDo->get('core.create'))
			{
				JToolbarHelper::save2copy('serie.save2copy');
			}

			JToolbarHelper::cancel('serie.cancel', 'JTOOLBAR_CLOSE');
		}

		if ($this->state->params->get('save_history') && $user->authorise('core.edit'))
		{
			JToolbarHelper::versions('com_sermonspeaker.sermon', $this->item->id);
		}
	}
}