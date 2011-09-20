<?php
/**
 * SermonSpeaker search plugin
 * based on Weblinks Search plugin
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
 
 
// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

require_once JPATH_SITE.'/components/com_sermonspeaker/helpers/route.php';

/**
 * SermonSpeaker Search plugin
 *
 * @package		Joomla
 * @subpackage	Search
 * @since		1.6
 */
class plgSearchSermonspeaker extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * @return array An array of search areas
	 */
	function onContentSearchAreas() {
		static $areas = array();
		$areas['spsermons'] = 'PLG_SEARCH_SERMONSPEAKER_SERMONS';
		if($this->params->get('search_series', 1)){
			$areas['spseries'] = 'PLG_SEARCH_SERMONSPEAKER_SERIES';
		}
		if($this->params->get('search_speakers', 1)){
			$areas['spspeakers'] = 'PLG_SEARCH_SERMONSPEAKER_SPEAKERS';
		}
		if($this->params->get('sermons_speaker', 0)){
			$db		= JFactory::getDbo();
			$query	= "SELECT `id`, `name` FROM #__sermon_speakers WHERE state = '1'";
			$db->setQuery($query);
			$speakers	= $db->loadAssocList();
			foreach ($speakers as $speaker){
				$areas['spspeakers-'.$speaker['id']] = JText::sprintf('PLG_SEARCH_SERMONSPEAKER_SERMONS_FROM', $speaker['name']);
			}
		}

		return $areas;
	}

	/**
	 * SermonSpeaker Search method
	 *
	 * The sql must return the following fields that are used in a common display
	 * routine: href, title, section, created, text, browsernav
	 * @param string Target search string
	 * @param string mathcing option, exact|any|all
	 * @param string ordering option, newest|oldest|popular|alpha|category
	 * @param mixed An array if the search it to be restricted to areas, null if search all
	 */
	function onContentSearch($text, $phrase='', $ordering='', $areas=null)
	{
		$db		= JFactory::getDbo();
		$app	= JFactory::getApplication();
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());

		$searchText = $text;

		if (is_array($areas)) {
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas()))) {
				return array();
			}
		} else {
			$areas = array_keys($this->onContentSearchAreas());
		}

		$sContent		= $this->params->get('search_content',		1);
		$sArchived		= $this->params->get('search_archived',		1);
		$limit			= $this->params->def('search_limit',		50);
		$state = array();
		if ($sContent) {
			$state[]=1;
		}
		if ($sArchived) {
			$state[]=2;
		}

		$text = trim($text);
		if ($text == '') {
			return array();
		}
		$section	= JText::_('PLG_SEARCH_SERMONSPEAKER_SERMONS');

		$speakers = array();
		if($this->params->get('sermons_speaker', 0)){
			foreach($areas as $key => $value){
				if (strpos($value, 'spspeakers-') === 0){
					$speakers[] = (int)substr($value, 11);
				}
			}
		}

		$rows = array();
		if(in_array('spsermons', $areas) || $speakers) {

			$wheres	= array();
			switch ($phrase)
			{
				case 'exact':
					$text		= $db->Quote('%'.$db->getEscaped($text, true).'%', false);
					$wheres2	= array();
					$wheres2[]	= 'a.sermon_title LIKE '.$text;
					$wheres2[]	= 'a.sermon_scripture LIKE '.$text;
					$wheres2[]	= 'a.notes LIKE '.$text;
					$where		= '(' . implode(') OR (', $wheres2) . ')';
					break;

				case 'all':
				case 'any':
				default:
					$words	= explode(' ', $text);
					$wheres = array();
					foreach ($words as $word)
					{
						$word		= $db->Quote('%'.$db->getEscaped($word, true).'%', false);
						$wheres2	= array();
						$wheres2[]	= 'a.sermon_title LIKE '.$word;
						$wheres2[]	= 'a.sermon_scripture LIKE '.$word;
						$wheres2[]	= 'a.notes LIKE '.$word;
						$wheres[]	= implode(' OR ', $wheres2);
					}
					$where	= '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
					break;
			}

			switch ($ordering)
			{
				case 'oldest':
					$order = 'a.created ASC';
					break;

				case 'popular':
					$order = 'a.hits DESC';
					break;

				case 'alpha':
					$order = 'a.sermon_title ASC';
					break;

				case 'category':
					$order = 'c.title ASC, a.sermon_title ASC';
					$morder = 'a.sermon_title ASC';
					break;

				case 'newest':
				default:
					$order = 'a.created DESC';
			}

			if (!empty($state)) {
				$query	= $db->getQuery(true);
				$query->select('a.sermon_title AS title, a.notes AS text, a.created AS created, '
							.'CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug, '
							.'CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as catslug, '
							.'CONCAT_WS(" / ", '.$db->Quote($section).', c.title) AS section, "1" AS browsernav');
				$query->from('#__sermon_sermons AS a');
				$query->leftJoin('#__categories AS c ON c.id = a.catid');
				$query->where('('.$where.')');
				$query->where('a.state in ('.implode(',', $state).')');
				
				if (!in_array('spsermons', $areas)){
					$query->where('a.speaker_id in ('.implode(',', $speakers).')');
				}
//				$query->where ('(c.published=1 AND c.access IN ('.$groups.'))');
				$query->order($order);

				// Filter by language
				if ($app->isSite() && $app->getLanguageFilter()) {
					$tag = JFactory::getLanguage()->getTag();
					$query->where('c.language in (' . $db->Quote($tag) . ',' . $db->Quote('*') . ')');
				}

				$db->setQuery($query, 0, $limit);
				$list = $db->loadObjectList();

				if (isset($list))
				{
					foreach($list as $key => $item)
					{
						$list[$key]->href = SermonspeakerHelperRoute::getSermonRoute($item->slug, $item->catslug);
					}
				}
				$rows[] = $list;
			}
		}

		if(in_array('spseries', $areas)) {

			$wheres	= array();
			switch ($phrase)
			{
				case 'exact':
					$text		= $db->Quote('%'.$db->getEscaped($text, true).'%', false);
					$wheres2	= array();
					$wheres2[]	= 'a.series_title LIKE '.$text;
					$wheres2[]	= 'a.series_description LIKE '.$text;
					$where		= '(' . implode(') OR (', $wheres2) . ')';
					break;

				case 'all':
				case 'any':
				default:
					$words	= explode(' ', $text);
					$wheres = array();
					foreach ($words as $word)
					{
						$word		= $db->Quote('%'.$db->getEscaped($word, true).'%', false);
						$wheres2	= array();
						$wheres2[]	= 'a.series_title LIKE '.$word;
						$wheres2[]	= 'a.series_description LIKE '.$word;
						$wheres[]	= implode(' OR ', $wheres2);
					}
					$where	= '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
					break;
			}

			switch ($ordering)
			{
				case 'oldest':
					$order = 'a.created ASC';
					break;

				case 'popular':
					$order = 'a.hits DESC';
					break;

				case 'alpha':
					$order = 'a.series_title ASC';
					break;

				case 'category':
					$order = 'c.title ASC, a.series_title ASC';
					$morder = 'a.series_title ASC';
					break;

				case 'newest':
				default:
					$order = 'a.created DESC';
			}

			if (!empty($state)) {
				$query	= $db->getQuery(true);
				$query->select('a.series_title AS title, a.series_description AS text, a.created AS created, '
							.'CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug, '
							.'CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as catslug, '
							.'CONCAT_WS(" / ", '.$db->Quote($section).', c.title) AS section, "1" AS browsernav');
				$query->from('#__sermon_series AS a');
				$query->leftJoin('#__categories AS c ON c.id = a.catid');
				$query->where('('.$where.')');
				$query->where('a.state in ('.implode(',',$state).')');
//				$query->where ('(c.published=1 AND c.access IN ('.$groups.'))');
				$query->order($order);

				// Filter by language
				if ($app->isSite() && $app->getLanguageFilter()) {
					$tag = JFactory::getLanguage()->getTag();
					$query->where('c.language in (' . $db->Quote($tag) . ',' . $db->Quote('*') . ')');
				}

				$db->setQuery($query, 0, $limit);
				$list = $db->loadObjectList();

				if (isset($list))
				{
					foreach($list as $key => $item)
					{
						$list[$key]->href = SermonspeakerHelperRoute::getSerieRoute($item->slug, $item->catslug);
					}
				}
				$rows[] = $list;
			}
		}

		if(in_array('spspeakers', $areas)) {

			$wheres	= array();
			switch ($phrase)
			{
				case 'exact':
					$text		= $db->Quote('%'.$db->getEscaped($text, true).'%', false);
					$wheres2	= array();
					$wheres2[]	= 'a.name LIKE '.$text;
					$wheres2[]	= 'a.intro LIKE '.$text;
					$wheres2[]	= 'a.bio LIKE '.$text;
					$where		= '(' . implode(') OR (', $wheres2) . ')';
					break;
				case 'all':
				case 'any':
				default:
					$words	= explode(' ', $text);
					$wheres = array();
					foreach ($words as $word)
					{
						$word		= $db->Quote('%'.$db->getEscaped($word, true).'%', false);
						$wheres2	= array();
						$wheres2[]	= 'a.name LIKE '.$word;
						$wheres2[]	= 'a.intro LIKE '.$word;
						$wheres2[]	= 'a.bio LIKE '.$word;
						$wheres[]	= implode(' OR ', $wheres2);
					}
					$where	= '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
					break;
			}

			switch ($ordering)
			{
				case 'oldest':
					$order = 'a.created ASC';
					break;

				case 'popular':
					$order = 'a.hits DESC';
					break;

				case 'alpha':
					$order = 'a.name ASC';
					break;

				case 'category':
					$order = 'c.title ASC, a.name ASC';
					$morder = 'a.name ASC';
					break;

				case 'newest':
				default:
					$order = 'a.created DESC';
			}

			if (!empty($state)) {
				$query	= $db->getQuery(true);
				$query->select('a.name AS title, CONCAT_WS(" ", a.intro, a.bio) AS text, a.created AS created, '
							.'CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug, '
							.'CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as catslug, '
							.'CONCAT_WS(" / ", '.$db->Quote($section).', c.title) AS section, "1" AS browsernav');
				$query->from('#__sermon_speakers AS a');
				$query->leftJoin('#__categories AS c ON c.id = a.catid');
				$query->where('('.$where.')');
				$query->where('a.state in ('.implode(',',$state).')');
//				$query->where ('(c.published=1 AND c.access IN ('.$groups.'))');
				$query->order($order);

				// Filter by language
				if ($app->isSite() && $app->getLanguageFilter()) {
					$tag = JFactory::getLanguage()->getTag();
					$query->where('c.language in (' . $db->Quote($tag) . ',' . $db->Quote('*') . ')');
				}

				$db->setQuery($query, 0, $limit);
				$list = $db->loadObjectList();

				if (isset($list))
				{
					foreach($list as $key => $item)
					{
						$list[$key]->href = SermonspeakerHelperRoute::getSpeakerRoute($item->slug, $item->catslug);
					}
				}
				$rows[] = $list;
			}
		}

		$results = array();
		if (count($rows))
		{
			foreach($rows as $row)
			{
				$new_row = array();
				foreach($row AS $key => $item) {
					if (searchHelper::checkNoHTML($item, $searchText, array('text', 'title'))) {
						$new_row[] = $item;
					}
				}
				$results = array_merge($results, (array) $new_row);
			}
		}

		return $results;
	}
}