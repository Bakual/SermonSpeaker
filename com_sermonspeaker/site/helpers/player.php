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
 * Sermonspeaker Component Player Helper
 *
 * @since  3.4
 */
class SermonspeakerHelperPlayer
{
	public $mspace;

	public $script;

	public $playlist;

	public $error;

	public $popup;

	// Is able to toggle between audio and video
	public $toggle;

	// Name of player
	public $player;

	protected $params;

	protected $config;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->params = JComponentHelper::getParams('com_sermonspeaker');
	}

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
		$this->config['aheight']	= (isset($this->config['aheight'])) ? $this->config['aheight'] : $this->params->get('aheight', $height);
		$this->config['awidth']		= (isset($this->config['awidth'])) ? $this->config['awidth'] : $this->params->get('awidth', $width);
		$this->config['vheight']	= (isset($this->config['vheight'])) ? $this->config['vheight'] : $this->params->get('vheight', '300px');
		$this->config['vwidth']		= (isset($this->config['vwidth'])) ? $this->config['vwidth'] : $this->params->get('vwidth', '100%');

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
		$this->popup['width']	= (strpos($this->config[$type . 'width'], '%')) ? 500 : $this->config[$type . 'width'] + 130;
		$this->popup['height']	= $this->config[$type . 'height'] + $this->params->get('popup_height');

		return;
	}
}
