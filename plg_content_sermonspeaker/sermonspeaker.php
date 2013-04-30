<?php
defined('_JEXEC') or die;

/**
 * Plug-in to show a SermonSpeaker player in an article
 * This uses the {sermonspeaker} syntax
 */
class plgContentSermonspeaker extends JPlugin
{
	/**
	 * Plugin that shows a SermonSpeaker player
	 *
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	object	The article object.  Note $article->text is also available
	 * @param	object	The article params
	 * @param	int		The 'page' number
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		// Don't run this plugin when the content is being indexed
		if ($context == 'com_finder.indexer') {
			return true;
		}

		// simple performance check to determine whether bot should process further
		if (strpos($article->text, 'sermonspeaker') === false) {
			return true;
		}

		// expression to search for (positions)
		$regex		= '/{sermonspeaker\s+(.*?)}/i';

		// Find all instances of plugin and put in $matches for sermonspeaker
		// $matches[0] is full pattern match, $matches[1] is the id
		preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER);
		if ($matches)
		{
			$default_mode	= $this->params->get('mode', 1);
			require_once(JPATH_ROOT.'/components/com_sermonspeaker/helpers/sermonspeaker.php');
			require_once(JPATH_ROOT.'/components/com_sermonspeaker/helpers/player.php');
			require_once(JPATH_ROOT.'/components/com_sermonspeaker/helpers/route.php');

			$db = JFactory::getDBO();
			foreach ($matches as $i => $match) {
				$explode = explode(',', $match[1]);
				$id = (int)$explode[0];
				if (!$id)
				{
					continue;
				}

				// Check $match for a defined mode, use plugin default if not present.
				$mode = (isset($explode[1])) ? $explode[1] : $default_mode;

				$query	= $db->getQuery(true);
				$query->select('sermons.id, sermons.title, sermon_date, sermon_time, audiofile, videofile, sermons.hits, sermon_date');
				$query->select('CASE WHEN CHAR_LENGTH(sermons.alias) THEN CONCAT_WS(\':\', sermons.id, sermons.alias) ELSE sermons.id END as slug');
				$query->from('`#__sermon_sermons` AS sermons');
				$query->where('sermons.id = '.$id);

				// Join over Speaker
				$query->select(
					'speakers.title AS speaker_title, speakers.pic AS pic, speakers.state as speaker_state, ' .
					'CASE WHEN CHAR_LENGTH(speakers.alias) THEN CONCAT_WS(\':\', speakers.id, speakers.alias) ELSE speakers.id END as speaker_slug'
				);
				$query->join('LEFT', '#__sermon_speakers AS speakers ON speakers.id = sermons.speaker_id');

				// Join over Series
				$query->select(
					'series.series_title AS series_title, series.state as series_state, series.avatar, ' .
					'CASE WHEN CHAR_LENGTH(series.alias) THEN CONCAT_WS(\':\', series.id, series.alias) ELSE series.id END as series_slug'
				);
				$query->join('LEFT', '#__sermon_series AS series ON series.id = sermons.series_id');

				$db->setQuery($query);
				$item = $db->loadObject();

				switch ($mode)
				{
					case 1:
					default:
						$link	= JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug));
						$output	= '<a href="'.$link.'">'.$item->title.'</a>';
						break;
					case 2:
						$config['count'] = '_plg_'.$item->id.'_'.$i;
						$player	= SermonspeakerHelperSermonspeaker::getPlayer($item, $config);
						$output	= $player->mspace;
						$output	.= $player->script;
						break;
					case 3:
						$this->loadLanguage();
						$link		= JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug));
						$speakerlnk	= JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->speaker_slug));
						$serieslnk	= JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->series_slug));
						$contents	= '<div class="ss-content-plg">';
						$contents	.= '<table class="table table-striped table-condensed">';
						if ($item->speaker_title)
						{
							$contents	.= '<tr><td>'.JText::_('PLG_CONTENT_SERMONSPEAKER_SPEAKER').'</td>';
							if ($item->speaker_state)
							{
								$contents	.= '<td>'.SermonspeakerHelperSermonSpeaker::SpeakerTooltip($item->speaker_slug, $item->pic, $item->speaker_title).'</td></tr>';
							}
							else
							{
								$contents	.= '<td>'.$item->speaker_title.'</td></tr>';
							}
						}
						if ($item->series_title)
						{
							$contents	.= '<tr><td>'.JText::_('PLG_CONTENT_SERMONSPEAKER_SERIE').'</td>';
							$contents	.= '<td><a href="'.$serieslnk.'">'.$item->series_title.'</a></td></tr>';
						}
						if ($item->sermon_date != '0000-00-00 00:00:00')
						{
							$contents	.= '<tr><td>'.JText::_('JDATE').'</td>';
							$contents	.= '<td>'.JHtml::Date($item->sermon_date, JText::_('DATE_FORMAT_LC3')).'</td></tr>';
						}
						if ($item->hits)
						{
							$contents	.= '<tr><td>'.JText::_('JGLOBAL_HITS').'</td>';
							$contents	.= '<td>'.$item->hits.'</td></tr>';
						}
						$contents	.= '</table>';
						if ($this->params->get('show_player'))
						{
							$config['count'] = '_plg_'.$item->id.'_'.$i;
							$player	= SermonspeakerHelperSermonspeaker::getPlayer($item, $config);
							$contents	.= $player->mspace;
							$contents	.= $player->script;
						}
						$contents	.= '</div>';
						$module_params['style']				= $this->params->get('style', 'rounded');
						$module_params['moduleclass_sfx']	= $this->params->get('moduleclass_sfx');
						$module = new StdClass;
						$module->id			= 0;
						$module->title		= '<a href="'.$link.'">'.$item->title.'</a>';
						$module->module		= 'mod_custom';
						$module->position	= '';
						$module->content	= $contents;
						$module->showtitle	= 1;
						$module->control	= '';
						$module->params		= json_encode($module_params);
						$output	= JModuleHelper::renderModule($module, $module_params);
						break;
				}

				$article->text = preg_replace("|$match[0]|", addcslashes($output, '\\$'), $article->text, 1);
			}
		}
	}
}
