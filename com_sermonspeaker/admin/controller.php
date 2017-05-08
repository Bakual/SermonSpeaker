<?php
defined('_JEXEC') or die;

class SermonspeakerController extends JControllerLegacy
{
	protected $default_view = 'main';

	public function display($cachable = false, $urlparams = false)
	{
		$view   = $this->input->get('view', 'main');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');
		$views  = array('sermon', 'serie', 'speaker');

		// Check for edit form.
		if (in_array($view, $views) && $layout == 'edit' && !$this->checkEditId('com_sermonspeaker.edit.' . $view, $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_sermonspeaker&view=main', false));

			return false;
		}

		$params = JComponentHelper::getParams('com_sermonspeaker');

		if ($params->get('css_icomoon') == '')
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_SERMONSPEAKER_NOTSAVED'), 'warning');
		}

		if (!JPluginHelper::isEnabled('sermonspeaker'))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_SERMONSPEAKER_NO_PLAYER_ENABLED'), 'warning');
		}

		return parent::display();
	}
}