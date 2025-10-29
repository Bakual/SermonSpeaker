<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Plugin.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\Route;

/**
 * Plug-in to show a SermonSpeaker player in an article
 * This uses the {sermonspeaker} syntax
 *
 * @since  1.0
 */
class PlgContentSermonspeaker extends CMSPlugin
{
	/**
	 * Plugin that shows a SermonSpeaker player
	 *
	 * @param   string  $context  The context of the content being passed to the plugin.
	 * @param   object &$item     The item object.  Note $item->text is also available
	 * @param   object &$params   The article params
	 * @param   int     $page     The 'page' number
	 *
	 * @return void
	 *
	 * @since ?
	 */
	public function onContentPrepare($context, $item, &$params, $page = 0)
	{
		// Don't run this plugin when the content is being indexed
		if ($context == 'com_finder.indexer')
		{
			return;
		}

		// Don't run if there is no text property (in case of bad calls) or it is empty
		if (empty($item->text))
		{
			return;
		}

		// Simple performance check to determine whether bot should process further
		if (strpos($item->text, 'sermonspeaker') === false)
		{
			return;
		}

		$regex = '/{sermonspeaker\s+(.*?)}/i';

		// $matches[0] is full pattern match, $matches[1] is the id
		preg_match_all($regex, $item->text, $matches, PREG_SET_ORDER);

		if ($matches)
		{
			$default_mode = $this->params->get('mode', 1);
			require_once JPATH_ROOT . '/components/com_sermonspeaker/helpers/sermonspeaker.php';
			require_once JPATH_ROOT . '/components/com_sermonspeaker/helpers/route.php';

			$db = Factory::getDbo();

			foreach ($matches as $i => $match)
			{
				$explode = explode(',', $match[1]);
				$id      = (int) $explode[0];

				if (!$id)
				{
					continue;
				}

				// Check $match for a defined mode, use plugin default if not present.
				$mode = (isset($explode[1])) ? $explode[1] : $default_mode;

				$query = $db->getQuery(true);
				$query->select('sermons.id, sermons.title, sermon_date, sermon_time, audiofile, videofile, sermons.hits, sermon_date');
				$query->select('sermons.catid, sermons.language');
				$query->select('CASE WHEN CHAR_LENGTH(sermons.alias) THEN CONCAT_WS(\':\', sermons.id, sermons.alias) ELSE sermons.id END as slug');
				$query->from('`#__sermon_sermons` AS sermons');
				$query->where('sermons.id = ' . $id);

				// Join over Speaker
				$query->select(
					'speakers.id as speaker_id, speakers.title as speaker_title, speakers.state as speaker_state, ' .
					'speakers.intro, speakers.bio, speakers.website, speakers.pic, speakers.catid as speaker_catid, speakers.language as speaker_language, ' .
					'CASE WHEN CHAR_LENGTH(speakers.alias) THEN CONCAT_WS(\':\', speakers.id, speakers.alias) ELSE speakers.id END as speaker_slug'
				);
				$query->join('LEFT', '#__sermon_speakers AS speakers ON speakers.id = sermons.speaker_id');

				// Join over Series
				$query->select(
					'series.title AS series_title, series.state AS series_state, series.avatar, series.catid as series_catid, series.language as series_language, ' .
					'CASE WHEN CHAR_LENGTH(series.alias) THEN CONCAT_WS(\':\', series.id, series.alias) ELSE series.id END as series_slug'
				);
				$query->join('LEFT', '#__sermon_series AS series ON series.id = sermons.series_id');

				$db->setQuery($query);
				$sermon = $db->loadObject();

				if (!$sermon)
				{
					// Sermon not found, remove plugin tag and return early.
					$item->text = preg_replace("|$match[0]|", addcslashes('', '\\$'), $item->text, 1);

					return;
				}

				switch ($mode)
				{
					case 1:
					default:
						$link   = Route::_(SermonspeakerHelperRoute::getSermonRoute($sermon->slug, $sermon->catid, $sermon->language));
						$output = '<a href="' . $link . '">' . $sermon->title . '</a>';
						break;
					case 2:
						$config['count'] = '_plg_' . $sermon->id . '_' . $i;
						$player          = SermonspeakerHelperSermonspeaker::getPlayer($sermon, $config);
						$output          = $player->mspace;
						$output          .= $player->script;
						break;
					case 3:
						$this->loadLanguage();
						$link      = Route::_(SermonspeakerHelperRoute::getSermonRoute($sermon->slug, $sermon->catid, $sermon->language));
						$serieslnk = Route::_(SermonspeakerHelperRoute::getSerieRoute($sermon->series_slug, $sermon->series_catid, $sermon->series_language));
						$contents  = '<div class="ss-content-plg">';
						$contents  .= '<table class="table table-striped table-condensed">';

						if ($sermon->speaker_title)
						{
							$contents .= '<tr><td>' . Text::_('PLG_CONTENT_SERMONSPEAKER_SPEAKER') . '</td>';

							if ($sermon->speaker_state)
							{
								$layout   = new FileLayout('titles.speaker', null, array('component' => 'com_sermonspeaker'));
								$contents .= '<td>' . $layout->render(array('item' => $sermon, 'params' => $this->params)) . '</td></tr>';
							}
							else
							{
								$contents .= '<td>' . $sermon->speaker_title . '</td></tr>';
							}
						}

						if ($sermon->series_title)
						{
							$contents .= '<tr><td>' . Text::_('PLG_CONTENT_SERMONSPEAKER_SERIE') . '</td>';
							$contents .= '<td><a href="' . $serieslnk . '">' . $sermon->series_title . '</a></td></tr>';
						}

						if ($sermon->sermon_date != '0000-00-00 00:00:00')
						{
							$contents .= '<tr><td>' . Text::_('JDATE') . '</td>';
							$contents .= '<td>' . HTMLHelper::date($sermon->sermon_date, Text::_('DATE_FORMAT_LC3')) . '</td></tr>';
						}

						if ($sermon->hits)
						{
							$contents .= '<tr><td>' . Text::_('JGLOBAL_HITS') . '</td>';
							$contents .= '<td>' . $sermon->hits . '</td></tr>';
						}

						$contents .= '</table>';

						if ($this->params->get('show_player'))
						{
							$config['count'] = '_plg_' . $sermon->id . '_' . $i;
							$player          = SermonspeakerHelperSermonspeaker::getPlayer($sermon, $config);
							$contents        .= $player->mspace;
							$contents        .= $player->script;
						}

						$contents                         .= '</div>';
						$attribs['style']                 = $this->params->get('style', 'html5');
						$module                           = new StdClass;
						$module->id                       = 0;
						$module->title                    = '<a href="' . $link . '">' . $sermon->title . '</a>';
						$module->module                   = 'mod_custom';
						$module->position                 = '';
						$module->content                  = $contents;
						$module->showtitle                = 1;
						$module->control                  = '';
						$module_params['moduleclass_sfx'] = $this->params->get('moduleclass_sfx');
						$module->params                   = json_encode($module_params);
						$output                           = ModuleHelper::renderModule($module, $attribs);
						break;
				}

				$item->text = preg_replace("|$match[0]|", addcslashes($output, '\\$'), $item->text, 1);
			}
		}
	}
}
