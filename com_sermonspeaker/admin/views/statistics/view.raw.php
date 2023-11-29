<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  4.x
 */
class SermonspeakerViewStatistics extends HtmlView
{
	/**
	 * @var
	 *
	 * @since ?
	 */
	protected $series;

	/**
	 * @var
	 *
	 * @since ?
	 */
	protected $speakers;

	/**
	 * @var
	 *
	 * @since ?
	 */
	protected $sermons;

	/**
	 * @param   null  $tpl
	 *
	 * @return mixed|void
	 *
	 * @since  ?
	 */
	function display($tpl = null)
	{
		$document = Factory::getDocument();
		$document->setMimeEncoding('text/xml');
		// get data from the model
		$this->series   = $this->get('Series');
		$this->speakers = $this->get('Speakers');
		$this->sermons  = $this->get('Sermons');

		parent::display($tpl);
	}
}