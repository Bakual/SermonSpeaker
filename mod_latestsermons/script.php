<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.LatestSermons
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Installer\InstallerScript;

/**
 * Class Mod_LatestsermonsInstallerScript
 *
 * @since  7.0
 */
class Mod_LatestsermonsInstallerScript extends InstallerScript
{
	/**
	 * Minimum PHP version required to install the extension
	 *
	 * @var    string
	 * @since  5.4.0
	 */
	protected $minimumPhp = '8.3.0';
	/**
	 * Minimum Joomla! version required to install the extension
	 *
	 * @var    string
	 * @since  6.0.0
	 */
	protected $minimumJoomla = '6.0.0';

	/**
	 * method to update the module
	 *
	 * @param Joomla\CMS\Installer\Adapter\ModuleAdapter $parent Installerobject
	 *
	 * @return void
	 *
	 * @since 7.0.0
	 */
	public function update($parent)
	{
		// Remove old MVC files
		$this->deleteFiles[] = '/modules/mod_latestsermons/mod_latestsermons.php';
		$this->deleteFiles[] = '/modules/mod_latestsermons/helper.php';
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @param string                                     $type   'install', 'update' or 'discover_install'
	 * @param Joomla\CMS\Installer\Adapter\ModuleAdapter $parent Installerobject
	 *
	 * @return void
	 *
	 * @since ?
	 */
	public function postflight($type, $parent)
	{
		$this->removeFiles();
	}
}
