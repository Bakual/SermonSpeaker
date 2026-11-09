<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.Sermonarchive
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Installer\InstallerScriptTrait;

return new class () implements InstallerScriptInterface
{
	use InstallerScriptTrait;

	public function __construct()
	{
		// Minimum PHP version required to install the extension
		$this->minimumPhp = '8.3.0';

		// Minimum Joomla! version required to install the extension
		$this->minimumJoomla = '6.0.0';
	}

	/**
	 * method to update the module
	 *
	 * @param InstallerAdapter $adapter The adapter calling this method
	 *
	 * @return boolean  True on success
	 *
	 * @since 7.0.0
	 */
	public function update(InstallerAdapter $adapter): bool
	{
		// Remove old MVC files
		$this->deleteFiles[] = '/modules/mod_sermonarchive/mod_sermonarchive.php';
		$this->deleteFiles[] = '/modules/mod_sermonarchive/helper.php';

		return true;
	}
};
