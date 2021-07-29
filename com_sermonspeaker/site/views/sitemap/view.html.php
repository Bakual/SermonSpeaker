<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  5.9.9
 */
class SermonspeakerViewSitemap extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void  View always triggers a redirect.
	 *
	 * @since 5.9.9
	 */
	public function display($tpl = null)
	{
		$uri = Uri::getInstance();
		$uri->setVar('format', 'raw');
		$url = $uri->toString();
		$app = Factory::getApplication();
		$app->redirect($url, 301);
	}
}
