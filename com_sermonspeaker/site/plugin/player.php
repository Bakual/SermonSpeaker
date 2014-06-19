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
	 * @var  object  Holds The player object
	 */
	protected $player;

	/**
	 * @var  array  Config values
	 */
	protected $config;

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
	 * @param   string        $context  The context from where it's triggered
	 * @param   array|object  $items    An array of sermnon objects or a single sermon object
	 * @param   array         $config   Should be an array of config options. Valid options:
	 *  - count (id of the player)
	 *  - type (may be audio, video or auto)
	 *  - prio (may be 0 for audio or 1 for video)
	 *  - autostart (overwrites the backend setting)
	 *  - alt_player (overwrites the backend setting)
	 *  - awidth, aheight (width and height for audio)
	 *  - vwidth, vheight (width and height for video)
	 * @param   boolean       &$loaded  Set to true if another player is already loaded
	 *
	 * @return  object|false  The player object or false
	 */
	public abstract function onGetPlayer($context, $items, $config, &$loaded);

	/**
	 * Sets the dimensions of the player for audio and video. $height and $width are default values.
	 *
	 * @param   string  $height  Height of the player
	 * @param   string  $width   Width of the player
	 *
	 * @return  void
	 */
	protected function setDimensions($height, $width)
	{
		$this->config['aheight'] = (isset($this->config['aheight'])) ? $this->config['aheight'] : $this->params->get('aheight', $height);
		$this->config['awidth']  = (isset($this->config['awidth'])) ? $this->config['awidth'] : $this->params->get('awidth', $width);
		$this->config['vheight'] = (isset($this->config['vheight'])) ? $this->config['vheight'] : $this->params->get('vheight', '300px');
		$this->config['vwidth']  = (isset($this->config['vwidth'])) ? $this->config['vwidth'] : $this->params->get('vwidth', '100%');

		return;
	}

	/**
	 * Sets the dimensions of the Popup window. $type can be 'a' (audio) or 'v' (video)
	 *
	 * @param   string  $type  a => audio, v => video
	 *
	 * @return  void
	 */
	protected function setPopup($type = 'a')
	{
		$this->player->popup['width']  = (strpos($this->config[$type . 'width'], '%')) ? 500 : $this->config[$type . 'width'] + 130;
		$this->player->popup['height'] = $this->config[$type . 'height'] + $this->params->get('popup_height');

		return;
	}

}
