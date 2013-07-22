<?php
// no direct access
defined('_JEXEC') or die;

/**
 * Sermonspeaker Microdata Helper
 *
 * @static
 * @package		Sermonspeaker
 * @since 5.0
 */
class SermonspeakerHelperMicrodata
{
	public function getScope($type)
	{
		return 'itemscope itemtype="http://schema.org/'.$type.'"';
	}

	public function getProp($property)
	{
		return 'itemprop="'.$property.'"';
	}
}