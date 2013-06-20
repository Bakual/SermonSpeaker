<?php
defined('_JEXEC') or die;

require_once(JPATH_SITE.'/components/com_sermonspeaker/helpers/player.php');

/**
 * Soundcloud
 */
class SermonspeakerHelperPlayerSoundcloud extends SermonspeakerHelperPlayer
{
	public function isSupported($item)
	{
		if (parse_url($item, PHP_URL_HOST) == 'soundcloud.com')
		{
			$this->mode	= 'audio';
		}
		else
		{
			$this->mode	= false;
		}
		return $this->mode;
	}

	public function getName()
	{
		return 'Soundcloud';
	}

	public function preparePlayer($item, $config)
	{
		$this->config	= $config;
		$this->player	= 'Soundcloud';
		$this->script	= '';
		$this->toggle	= false;

		if (is_array($item))
		{
			$this->mspace	= '<div class="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Warning!</strong> '.$this->player.' doesn\'t support Playlists</div>';
			return false;
		}
		$this->file		= $item->audiofile;
		$this->fb_file	= $item->audiofile;
		$this->setDimensions('305', '100%');
		$this->setPopup('a');
		$start = $this->config['autostart'] ? 'true' : 'false';
		$url	= 'http://soundcloud.com/oembed?format=xml&url='.$item->audiofile.'&auto_play='.$start.'&maxheight='.$this->config['aheight'];
		if ($this->config['awidth'] != '100%')
		{
			$url .= '&maxwidth='.$this->config['awidth'];
		}
		$xml	= simplexml_load_file($url);
		$string = str_replace('<![CDATA[', '', $xml->html);
		$string = str_replace(']]>', '', $string);
		$this->mspace = $string;

		return;
	}
}