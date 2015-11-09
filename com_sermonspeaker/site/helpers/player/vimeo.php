<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2015 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

require_once JPATH_SITE . '/components/com_sermonspeaker/helpers/player.php';

/**
 * Vimeo
 *
 * @since  5
 */
class SermonspeakerHelperPlayerVimeo extends SermonspeakerHelperPlayer
{
	private static $script_loaded;

	/**
	 * Checks the filename if it's supported by the player
	 *
	 * @param   string  $item  Filename
	 *
	 * @return  mixed  Mode (audio or video) or false when not supported
	 */
	public function isSupported($item)
	{
		if ((strpos($item, 'http://vimeo.com') === 0) || (strpos($item, 'http://player.vimeo.com') === 0))
		{
			$this->mode = 'video';
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
		return 'Vimeo';
	}

	/**
	 * Prepares the player
	 *
	 * @param   object  $item    Itemobject
	 * @param   array   $config  Config array
	 *
	 * @return  void
	 */
	public function preparePlayer($item, $config)
	{
		$this->config = $config;
		$this->player = 'Vimeo';
		$this->script = '';
		$this->toggle = false;

		if (is_array($item))
		{
			$this->mspace = '<div class="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Warning!</strong> '
							. $this->player . ' doesn\'t support Playlists</div>';

							return;
		}

		$id            = trim(strrchr($item->videofile, '/'), '/ ');
		$this->file    = 'http://vimeo.com/' . $id;
		$this->fb_file = 'http://vimeo.com/moogaloop.swf?clip_id=' . $id
			. '&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00adef&amp;fullscreen=1&amp;autoplay=0&amp;loop=0';
		$this->setDimensions(50, '100%');
		$this->setPopup('v');
		$start = $this->config['autostart'] ? 1 : 0;
		$this->mspace = '<iframe id="mediaspace' . $this->config['count'] . '" width="' . $this->config['vwidth'] . '" height="' . $this->config['vheight']
						. '" src="http://player.vimeo.com/video/' . $id . '?title=0&byline=0&portrait=0&border=0&autoplay=' . $start . '&player_id=vimeo'
						. $this->config['count'] . '&api=1"></iframe>';

		// Loading needed Javascript only once
		if (!self::$script_loaded)
		{
			if ($this->params->get('ga_id', ''))
			{
				JHtml::Script('media/com_sermonspeaker/player/vimeo/ganalytics.js', true);
				$doc = JFactory::getDocument();
				$doc->addScriptDeclaration("window.addEvent('domready', _trackVimeo);");
			}

			self::$script_loaded = 1;
		}

		return;
	}
}
