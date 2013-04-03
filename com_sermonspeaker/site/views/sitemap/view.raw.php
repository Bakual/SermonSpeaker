<?php
defined('_JEXEC') or die;
class SermonspeakerViewSitemap extends JViewLegacy
{
	function display( $tpl = null )
	{
		$this->document->setMimeEncoding('text/xml');
		// get data from the model
		$this->sermons	= $this->get('Sermons');
		$app			= JFactory::getApplication();
		$this->params	= $app->getParams();
		// push data into the template
		parent::display($tpl);
	}
}