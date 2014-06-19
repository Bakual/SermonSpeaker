<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2014 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Baseclass for Sermonspeaker Player Plugins
 *
 * @since  5.3
 */
abstract class SermonspeakerPluginPlayer extends JPlugin
{
	/**
	 * @var  object  $player  Holds The player object
	 */
	protected $player;

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
	 *                             Recognized key values include 'name', 'group', 'params', 'language'
	 *                             (this list is not meant to be comprehensive).
	 *
	 * @since   5.3
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

		// Initialise the player object so we don't have to do it in every plugin.
		$this->player = new stdClass;
		$this->player->popup['height'] = 0;
		$this->player->popup['width']  = 0;
		$this->player->error           = '';
		$this->player->toggle          = false;
		$this->player->mspace          = '';
		$this->player->script          = '';
		$this->player->player          = '';
	}

	/**
	 * Creates the player
	 *
	 * @param   array/object  $items  An array of objects or a single object
	 *
	 * @return  string  The output needed to load the player
	 */
	public abstract function onPlayerInsert($items);
}
