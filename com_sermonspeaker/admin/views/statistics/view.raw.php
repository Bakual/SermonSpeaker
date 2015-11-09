<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2015 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  4.x
 */
class SermonspeakerViewStatistics extends JViewLegacy
{
	function display( $tpl = null )
	{
		$document = JFactory::getDocument();
		$document->setMimeEncoding('text/xml');
		// get data from the model
		$this->series	= $this->get('Series');
		$this->speakers	= $this->get('Speakers');
		$this->sermons	= $this->get('Sermons');
		parent::display($tpl);
	}
}