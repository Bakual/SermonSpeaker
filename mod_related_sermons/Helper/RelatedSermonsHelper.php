<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.RelatedSermons
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Module\RelatedSermons\Site\Helper;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Registry\Registry;
use Sermonspeaker\Component\Sermonspeaker\Site\Helper\RouteHelper;

defined('_JEXEC') or die();

/**
 * Helper class for Related Sermons module
 *
 * @since  1.0
 */
class RelatedSermonsHelper implements DatabaseAwareInterface
{
	use DatabaseAwareTrait;

	private int $id;
	private string $option;
	/**
	 * @var \Joomla\CMS\Application\SiteApplication
	 */
	private SiteApplication $app;

	/**
	 * Gets the items from the database
	 *
	 * @param Registry        $params The module parameters.
	 * @param SiteApplication $app    The current application.
	 *
	 * @return  array  $related  Array of items
	 *
	 * @since ?
	 */
	public function getRelatedSermons(Registry $params, SiteApplication $app): array
	{
		$this->app = $app;
		$input     = $this->app->getInput();
		$this->id  = $input->getInt('id', 0);

		if (!$this->id)
		{
			return array();
		}

		$this->option = $input->getCmd('option');
		$view         = $input->getCmd('view');

		// Get Params
		$supportContent = $params->get('supportArticles', 0);
		$limitSermons   = $params->get('limitSermons', 10);
		$orderBy        = $params->get('orderBy', 'CreatedDateDesc');
		$sermonCat      = $params->get('sermon_cat', 0);

		$related = array();

		if (($supportContent && $this->option == 'com_content' && $view == 'article')
			|| ($this->option == 'com_sermonspeaker' && $view == 'sermon'))
		{
			$keywords = $this->getKeywords();

			if ($keywords)
			{
				$related = $this->getRelatedSermonsById($keywords, $orderBy, $sermonCat, $limitSermons);

				if ($supportContent && $limitSermons > count($related))
				{
					$articles = $this->getRelatedItemsById($keywords, $limitSermons - count($related));
					$related  = array_merge($related, $articles);
				}
			}
		}

		return $related;
	}

	/**
	 * Get keywords from current item, either com_content or com_sermonspeaker
	 *
	 * @return  array  $keywords  Array of items
	 *
	 * @since ?
	 */
	private function getKeywords(): array
	{
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		$query->select('metakey');
		$query->where('id = ' . $this->id);

		if ($this->option == 'com_content')
		{
			$query->from('#__content');
		}
		else
		{
			$query->from('#__sermon_sermons');
		}

		$db->setQuery($query);
		$metakey  = $db->loadResult();
		$keys     = explode(',', $metakey);
		$keywords = array();

		foreach ($keys as $key)
		{
			$key = trim($key);

			if ($key)
			{
				$keywords[] = $key;
			}
		}

		foreach ($keywords as &$keyword)
		{
			$keyword = $db->escape($keyword);
		}

		return $keywords;
	}

	/**
	 * Search the sermons
	 *
	 * @param array  $keywords     Keywords
	 * @param string $orderBy      Ordering
	 * @param int    $sermonCat    Category
	 * @param int    $limitSermons Limit
	 *
	 * @return  array  $related  Array of items
	 *
	 * @since ?
	 */
	protected
	function getRelatedSermonsById(array $keywords, string $orderBy, int $sermonCat, int $limitSermons): array
	{
		$related = array();
		$db      = $this->getDatabase();

		$SermonOrder = match ($orderBy)
		{
			'NameAsc' => 'a.title ASC',
			'NameDes' => 'a.title DESC',
			'SermonDateAsc' => 'a.sermon_date ASC',
			'SermonDateDes' => 'a.sermon_date DESC',
			'CreatedDateAsc' => 'a.created ASC',
			default => 'a.created DESC',
		};

		$query = $this->getDatabase()->getQuery(true);
		$query->select('a.title, a.created, a.catid, a.language');
		$query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
		$query->select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
		$query->from('#__sermon_sermons AS a');
		$query->leftJoin('#__categories AS cc ON cc.id = a.catid');

		if ($this->option == 'com_sermonspeaker')
		{
			$query->where('a.id != ' . $this->id);
		}

		$query->where('a.state = 1');
		$query->where('(a.catid = 0 OR cc.published = 1)');

		// Define null and now dates
		$nullDate = $db->quote($db->getNullDate());
		$nowDate  = $db->quote(Factory::getDate()->toSql());

		// Filter by start and end dates.
		$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
		$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

		if ($sermonCat)
		{
			$query->where('a.catid = ' . $sermonCat);
		}

		$query->where('(CONCAT(",", REPLACE(a.metakey, ", ", ","), ",") LIKE "%,'
			. implode(',%" OR CONCAT(",", REPLACE(a.metakey, ", ", ","), ",") LIKE "%,', $keywords) . ',%")');

		if ($this->app->getLanguageFilter())
		{
			$query->where('a.language in (' . $db->quote($this->app->getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		}

		$query->group('a.id');
		$query->order($SermonOrder);

		$db->setQuery($query, 0, $limitSermons);
		$temp = $db->loadObjectList();

		foreach ($temp as $row)
		{
			$row->route = Route::_(RouteHelper::getSermonRoute($row->slug, $row->catid, $row->language));
			$related[]  = $row;
		}

		return $related;
	}

	/**
	 * Search articles
	 *
	 * @param array $keywords Keywords
	 * @param int   $limit    Limit
	 *
	 * @return  array  $related  Array of items
	 *
	 * @since ?
	 */
	private
	function getRelatedItemsById(array $keywords, int $limit): array
	{
		$groups   = $this->app->getIdentity()->getAuthorisedViewLevels();
		$db       = $this->getDatabase();
		$nullDate = $db->getNullDate();
		$date     = Factory::getDate();
		$now      = $date->toSql();

		$related = array();

		$query = $db->getQuery(true);
		$query->select('a.title, a.created');
		$query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
		$query->select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
		$query->from('#__content AS a');
		$query->leftJoin('#__categories AS cc ON cc.id = a.catid');

		if ($this->option == 'com_content')
		{
			$query->where('a.id != ' . $this->id);
		}

		$query->where('a.state = 1');
		$query->whereIn($db->quoteName('a.access'), $groups);
		$query->where('cc.published = 1');
		$query->where('(CONCAT(",", REPLACE(a.metakey, ", ", ","), ",") LIKE "%,'
			. implode(',%" OR CONCAT(",", REPLACE(a.metakey, ", ", ","), ",") LIKE "%,', $keywords) . ',%")');
		$query->where('(a.publish_up = ' . $db->quote($nullDate) . ' OR a.publish_up <= ' . $db->quote($now) . ')');
		$query->where('(a.publish_down = ' . $db->quote($nullDate) . ' OR a.publish_down >= ' . $db->quote($now) . ')');

		// Filter by language
		if ($this->app->getLanguageFilter())
		{
			$query->where('a.language in (' . $db->quote($this->app->getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		}

		$db->setQuery($query, 0, $limit);
		$temp = $db->loadObjectList();

		foreach ($temp as $row)
		{
			$row->route = Route::_(\Joomla\Component\Content\Site\Helper\RouteHelper::getArticleRoute($row->slug, $row->catslug, $row->language));
			$related[]  = $row;
		}

		return $related;
	}
}
