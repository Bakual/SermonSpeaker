<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

require_once JPATH_SITE . '/components/com_sermonspeaker/helpers/player.php';

/**
 * HTML5
 *
 * @since  5
 */
class SermonspeakerHelperPlayerHtml5 extends SermonspeakerHelperPlayer
{
	/**
	 * @var string
	 *
	 * @since ?
	 */
	public $mode;

	/**
	 * Checks the filename if it's supported by the player
	 *
	 * @param   string $file Filename
	 *
	 * @return  mixed  Mode (audio or video) or false when not supported
	 *
	 * @since ?
	 */
	public function isSupported($file)
	{
		// Exclude wma and wmv files since these are not supported by HTML5 and we have a player for those
		$ext = JFile::getExt($file);
		$exclude = array('wma', 'wmv');

		if (in_array($ext, $exclude))
		{
			return false;
		}

		// Always true since no actual player is loaded
		return true;
	}

	/**
	 * Gets name of player
	 *
	 * @return  string  Name of player
	 *
	 * @since ?
	 */
	public function getName()
	{
		return 'HTML5 Player';
	}

	/**
	 * Prepares the player
	 *
	 * @param   object $item   Itemobject
	 * @param   array  $config Config array
	 *
	 * @return  void
	 *
	 * @since ?
	 */
	public function preparePlayer($item, $config)
	{
		$this->config = $config;
		$this->player = $this->getName();

		if (is_array($item))
		{
			$this->mspace = '<div class="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Warning!</strong> '
				. $this->player . ' doesn\'t support Playlists</div>';
			$this->script = '';

			return;
		}

		$type = $this->config['prio'] ? 'v' : 'a';
		$this->setDimensions('21px', '250px');
		$this->setPopup($type);

		$autoplay = $this->config['autostart'] ? 'autoplay="autoplay"' : '';
		$this->mode = ($this->config['prio']) ? 'video' : 'audio';
		$property = $this->mode . 'file';
		$file = $item->$property;
		$this->mspace = '<' . $this->mode . ' id="mediaspace' . $this->config['count'] . '" ' . $autoplay . ' controls="controls" width="'
			. $this->config[$type . 'width'] . '" height="' . $this->config[$type . 'height'] . '">'
			. '<source src="' . SermonspeakerHelperSermonspeaker::makeLink($file) . '">'
			. '</' . $this->mode . '>';

		$this->script = '';
		$this->toggle = false;

		return;
	}
}
