<?php
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View to edit a speaker.
 *
 * @package        Sermonspeaker.Administrator
 *
 * @since          ?
 */
class SermonspeakerViewSpeaker extends HtmlView
{
	protected $state;
	protected $item;
	protected $form;

	/**
	 * Display the view
	 *
	 * @param   null  $tpl
	 *
	 * @return mixed
	 * @throws \Exception
	 * @since  ?
	 *
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

		// If we are forcing a language in modal (used for associations).
		if ($this->getLayout() === 'modal' && $forcedLanguage = Factory::getApplication()->input->get('forcedLanguage', ''))
		{
			// Set the language field to the forcedLanguage and disable changing it.
			$this->form->setValue('language', null, $forcedLanguage);
			$this->form->setFieldAttribute('language', 'readonly', 'true');

			// Only allow to select categories with All language or with the forced language.
			$this->form->setFieldAttribute('catid', 'language', '*,' . $forcedLanguage);

			// Only allow to select tags with All language or with the forced language.
			$this->form->setFieldAttribute('tags', 'language', '*,' . $forcedLanguage);
		}

		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);
		$user       = Factory::getUser();
		$isNew      = ($this->item->id == 0);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->id);
		$canDo      = SermonspeakerHelper::getActions();
		$toolbar    = Toolbar::getInstance();

		ToolbarHelper::title(
			Text::sprintf('COM_SERMONSPEAKER_PAGE_' . ($checkedOut ? 'VIEW' : ($isNew ? 'ADD' : 'EDIT')),
				Text::_('COM_SERMONSPEAKER_SPEAKERS_TITLE')),
			'pencil-2 speakers'
		);

		// Build the actions for new and existing records.
		if ($isNew)
		{
			// For new records, check the create permission.
			if ($canDo->get('core.create'))
			{
				$toolbar->apply('speaker.apply');

				$saveGroup = $toolbar->dropdownButton('save-group');

				$saveGroup->configure(
					function (Toolbar $childBar) use ($user) {
						$childBar->save('speaker.save');

						if ($user->authorise('core.create', 'com_menus.menu'))
						{
							$childBar->save('speaker.save2menu', Text::_('JTOOLBAR_SAVE_TO_MENU'));
						}

						$childBar->save2new('speaker.save2new');
					}
				);
			}
		}
		else
		{
			// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
			$itemEditable = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $user->id);

			if (!$checkedOut && $itemEditable)
			{
				$toolbar->apply('speaker.apply');
			}

			$saveGroup = $toolbar->dropdownButton('save-group');

			$saveGroup->configure(
				function (Toolbar $childBar) use ($checkedOut, $itemEditable, $canDo, $user) {
					// Can't save the record if it's checked out and editable
					if (!$checkedOut && $itemEditable)
					{
						$childBar->save('speaker.save');

						// We can save this record, but check the create permission to see if we can return to make a new one.
						if ($canDo->get('core.create'))
						{
							$childBar->save2new('speaker.save2new');
						}
					}

					// If checked out, we can still save2menu
					if ($user->authorise('core.create', 'com_menus.menu'))
					{
						$childBar->save('speaker.save2menu', Text::_('JTOOLBAR_SAVE_TO_MENU'));
					}

					// If checked out, we can still save2copy
					if ($canDo->get('core.create'))
					{
						$childBar->save2copy('speaker.save2copy');
					}
				}
			);

			if (ComponentHelper::isEnabled('com_contenthistory') && $this->state->params->get('save_history') && $itemEditable)
			{
				$toolbar->versions('com_sermonspeaker.speaker', $this->item->id);
			}

			require_once JPATH_COMPONENT_SITE . '/helpers/route.php';
			$url = Route::link(
				'site',
				SermonspeakerHelperRoute::getSpeakerRoute($this->item->id . ':' . $this->item->alias, $this->item->catid, $this->item->language),
				true
			);

			$toolbar->preview($url)
				->bodyHeight(80)
				->modalWidth(90);
		}

		$toolbar->cancel('speaker.cancel');
	}
}
