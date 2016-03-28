<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

require_once JPATH_SITE . '/components/com_sermonspeaker/helpers/player.php';

/**
 * Soundcloud
 *
 * @since  5
 */
class SermonspeakerHelperPlayerSoundcloud extends SermonspeakerHelperPlayer
{
	/**
	 * Checks the filename if it's supported by the player
	 *
	 * @param   string  $file  Filename
	 *
	 * @return  mixed  Mode (audio or video) or false when not supported
	 */
	public function isSupported($file)
	{
		if (parse_url($item, PHP_URL_HOST) == 'soundcloud.com')
		{
			$this->mode = 'audio';
		}
		else
		{
			$this->mode = false;
		}

		return $this->mode;
	}

	/**
	 * Gets name of player
	 *
	 * @return  string  Name of player
	 */
	public function getName()
	{
		return 'Soundcloud';
	}

	/**
	 * Prepares the player
	 *
	 * @param   object  $item    Itemobject
	 * @param   array   $config  Config array
	 *
	 * @return  object  Player object
	 */
	public function preparePlayer($item, $config)
	{
		$this->config = $config;
		$this->player = 'Soundcloud';
		$this->script = '';
		$this->toggle = false;

		if (is_array($item))
		{
			// Get first item and work from that
			$first = reset($item);
			$link  = $first->audiofile;
			$link  = substr($link, 0, strrpos($link, '/'));
		}
		else
		{
			$link = $item->audiofile;
		}

		$this->file    = $link;
		$this->fb_file = $link;
		$this->setDimensions('305', '100%');
		$this->setPopup('a');
		$start = $this->config['autostart'] ? 'true' : 'false';
		$url = 'http://soundcloud.com/oembed?format=xml&url=' . $link . '&auto_play=' . $start . '&maxheight=' . $this->config['aheight'];

		if ($this->config['awidth'] != '100%')
		{
			$url .= '&maxwidth=' . $this->config['awidth'];
		}

		$xml    = simplexml_load_file($url);
		$string = str_replace('<![CDATA[', '', $xml->html);
		$string = str_replace(']]>', '', $string);
		$this->mspace = $string;

		return;
	}
}
