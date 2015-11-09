<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2015 - Thomas Hunziker
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
	 * @var  Joomla\Registry\Registry
	 */
	protected $config;

	/**
	 * @var  object
	 */
	protected $player;

	/**
	 * Creates the player
	 *
	 * @param   string                    $context  The context from where it's triggered
	 * @param   object                    &$player  Player object
	 * @param   array|object              $items    An array of sermnon objects or a single sermon object
	 * @param   Joomla\Registry\Registry  $config   A config object. Special properties:
	 *  - count (id of the player)
	 *  - type (may be audio, video or auto)
	 *  - prio (may be 0 for audio or 1 for video)
	 *  - autostart (overwrites the backend setting)
	 *  - alt_player (overwrites the backend setting)
	 *  - awidth, aheight (width and height for audio)
	 *  - vwidth, vheight (width and height for video)
	 *
	 * @return  void
	 */
	public abstract function onGetPlayer($context, &$player, $items, $config);

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
