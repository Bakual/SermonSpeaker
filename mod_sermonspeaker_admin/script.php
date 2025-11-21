script.php<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonspeakerAdmin
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Installer\InstallerScript;

/**
 * Class Mod_SermonspeakerInstallerScript
 *
 * @since  7.0
 */
class Mod_SermonspeakerInstallerScript extends InstallerScript
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
		$this->deleteFiles[] = '/administrator/modules/mod_sermonspeaker/mod_sermonspeaker.php';
		$this->deleteFiles[] = '/administrator/modules/mod_sermonspeaker/helper.php';
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
