<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2015 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Raw View class for the SermonSpeaker Component
 *
 * @since  4.4
 */
class SermonspeakerViewSitemap extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		$this->document->setMimeEncoding('text/xml');

		// Get data from the model
		$this->sermons	= $this->get('Sermons');
		$app			= JFactory::getApplication();
		$this->params	= $app->getParams();

		parent::display($tpl);
	}
}
