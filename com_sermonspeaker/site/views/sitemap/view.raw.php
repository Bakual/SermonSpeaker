<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die();

/**
 * Raw View class for the SermonSpeaker Component
 *
 * @since  4.4
 */
class SermonspeakerViewSitemap extends HtmlView
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @since ?
	 */
	public function display($tpl = null)
	{
		$this->getDocument()->setMimeEncoding('text/xml');

		// Get data from the model
		$this->sermons = $this->get('Sermons');
		$app           = Factory::getApplication();
		$this->params  = $app->getParams();

		parent::display($tpl);
	}
}
