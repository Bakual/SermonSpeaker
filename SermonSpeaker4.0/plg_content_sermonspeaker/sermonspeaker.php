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
				$query->select('sermons.id, sermon_title, sermon_date, sermon_time, audiofile, videofile');
				$query->select('CASE WHEN CHAR_LENGTH(sermons.alias) THEN CONCAT_WS(\':\', sermons.id, sermons.alias) ELSE sermons.id END as slug');
				$query->select('name');
				$query->from('`#__sermon_sermons` AS sermons');
				$query->join('LEFT', '#__sermon_speakers AS speakers ON speakers.id = sermons.speaker_id');
				$query->where('sermons.id = '.$id);

				$db->setQuery($query);
				$item = $db->loadObject();

				if ($mode == 2)
				{
					$config['count'] = '_plg_'.$item->id.'_'.$i;
					$player	= new SermonspeakerHelperPlayer($item, $config);
					$output	= $player->mspace;
					$output	.= $player->script;
				}
				else
				{
					$link	= JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug));
					$output	= '<a href="'.$link.'">'.$item->sermon_title.'</a>';
				}

				$article->text = preg_replace("|$match[0]|", addcslashes($output, '\\$'), $article->text, 1);
			}
		}
	}
}
