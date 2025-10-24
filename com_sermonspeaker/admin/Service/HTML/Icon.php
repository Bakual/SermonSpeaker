<?php

/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\Service\HTML;

use Joomla\CMS\HTML\HTMLHelper;

\defined('_JEXEC') or die;

/**
 * Sermonspeaker Component HTML Helper
 *
 * @since  7.0.0
 */
class Icon
{
	/**
	 * Show the podcast links, based on JHtmlContentAdministrator::featured
	 *
	 * @param   int      $value      The state value
	 * @param   int      $i          Row number
	 * @param   boolean  $canChange  Is user allowed to change?
	 *
	 * @return  string   HTML code
	 *
	 * @since 7.0.0
	 */
	public static function podcasted($value, $i, $canChange = true)
	{
		HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');

		$states = array(
			0 => array(
				'task'           => 'sermons.podcast_publish',
				'active_title'   => 'COM_SERMONSPEAKER_TOGGLE_PODCASTED',
				'inactive_title' => 'COM_SERMONSPEAKER_UNPODCASTED',
				'active_class'   => 'feed',
				'inactive_class' => 'feed',
			),
			1 => array(
				'task'           => 'sermons.podcast_unpublish',
				'active_title'   => 'COM_SERMONSPEAKER_TOGGLE_PODCASTED',
				'inactive_title' => 'COM_SERMONSPEAKER_PODCASTED',
				'active_class'   => 'feed text-success',
				'inactive_class' => 'feed text-success',
			),
		);

		$value = (int) $value;

		if ($value != 0 && $value != 1)
		{
			$value = 0;
		}

		$state = $states[$value];

		if ($canChange)
		{
			$html = '<a class="tbody-icon' . ($value == 1 ? ' active' : '') . ' hasTooltip"'
				. ' href="#" onclick="return Joomla.listItemTask(\'cb' . $i . '\',\'' . $state['task'] . '\')"'
				. ' title="' . HTMLHelper::_('tooltipText', $state['active_title']) . '">'
				. '<span class="icon-' . $state['active_class'] . '"></span>'
				. '</a>';
		}
		else
		{
			$html = '<a class="tbody-icon' . ($value == 1 ? ' active' : '') . ' hasTooltip disabled"'
				. ' title="' . HTMLHelper::_('tooltipText', $state['inactive_title']) . '">'
				. '<span class="icon-' . $state['inactive_class'] . '"></span>'
				. '</a>';
		}

		return $html;
	}
}
