<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Plugin.Autotweet
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

// Check for component
if (!JComponentHelper::getComponent('com_autotweet', true)->enabled)
{
	JError::raiseWarning('5', 'AutoTweet NG Component is not installed or not enabled. - ' . __FILE__);

	return;
}

require_once JPATH_ROOT . '/administrator/components/com_autotweet/helpers/autotweetbase.php';

/**
 * PlgContentAutotweetSermonspeaker
 *
 * @package     Extly.Components
 * @subpackage  com_autotweet
 * @since       1.0
 */
class PlgContentAutotweetSermonspeaker extends plgAutotweetBase
{
	// Typeinfo
	const TYPE_SERMON = 1;

	// Plugin params

	protected $categories = '';

	protected $excluded_categories = '';

	protected $post_modified = 0;

	protected $show_catsec = 0;

	protected $show_hash = 0;

	protected $use_text = 0;

	protected $use_text_count = 100;

	protected $static_text = '';

	protected $static_text_pos = 1;

	protected $static_text_source = 0;

	protected $metakey_count = 1;

	protected $interval = 60;

	/**
	 * Constructor
	 *
	 * @param   JEventDispatcher  &$subject  JEventDispatcher object
	 * @param   array             $params    Parameter array containing name, type and params
	 */
	public function __construct(&$subject, $params)
	{
		parent::__construct($subject, $params);

		JLoader::register('SermonspeakerHelperRoute', JPATH_SITE . '/components/com_sermonspeaker/helpers/route.php');
		JLoader::register('SermonspeakerHelperSermonspeaker', JPATH_SITE . '/components/com_sermonspeaker/helpers/sermonspeaker.php');

		// Sermon specific params
		$this->categories          = $this->params->get('categories', '');
		$this->excluded_categories = $this->params->get('excluded_categories', '');
		$this->post_modified       = (int) $this->params->get('post_modified', 0);
		$this->show_category       = (int) $this->params->get('show_catsec', 0);
		$this->show_hash           = (int) $this->params->get('show_hash', 0);
		$this->use_text            = (int) $this->params->get('use_text', 0);
		$this->use_text_count      = $this->params->get('use_text_count', 100);
		$this->static_text         = strip_tags($this->params->get('static_text', ''));
		$this->static_text_pos     = (int) $this->params->get('static_text_pos', 1);
		$this->static_text_source  = (int) $this->params->get('static_text_source', 0);
		$this->metakey_count       = (int) $this->params->get('metakey_count', 1);
		$this->interval            = (int) $this->params->get('interval', 60);
		$this->Itemid              = (int) $this->params->get('menuitem', 0);

		// Correct value if value is under the minimum
		if ($this->interval < 60)
		{
			$this->interval = 60;
		}

		// Check type and range, and correct if needed
		$this->use_text_count = $this->getTextcount($this->use_text_count);
	}

	/**
	 * onContentAfterSave
	 *
	 * @param   object  $context  The context of the content passed to the plugin.
	 * @param   object  $item     A JTableContent object
	 * @param   bool    $isNew    If the content is just about to be created
	 *
	 * @return  boolean
	 *
	 * @since  1.5
	 */
	public function onContentAfterSave($context, $item, $isNew)
	{
		/*
		 request from backend:
		 - com_sermonspeaker.sermon
		 requests form frontend: com_sermonspeaker.form ->TODO: ?
		 */
		// Sermon
		if ((($context == 'com_sermonspeaker.sermon') || ($context == 'com_sermonspeaker.frontendupload'))
			&& ($isNew || $this->post_modified) && (1 == $item->state))
		{
			$this->postSermon($item);
		}

		return true;
	}

	/**
	 * onContentChangeState
	 *
	 * @param   object  $context  The context of the content passed to the plugin.
	 * @param   array   $pks      A list of primary key ids of the content that has changed state.
	 * @param   int     $value    The value of the state that the content has been changed to.
	 *
	 * @return	boolean
	 *
	 * @since	1.5
	 */
	public function onContentChangeState($context, $pks, $value)
	{
		// Sermon
		if ((($context == 'com_sermonspeaker.sermon') || ($context == 'com_sermonspeaker.frontendupload'))
			&& ($value == 1))
		{
			$item = JTable::getInstance('Sermon', 'SermonspeakerTable');

			foreach ($pks as $id)
			{
				$item->load($id);
				$this->postSermon($item);
			}
		}

		return true;
	}

	/**
	 * postSermon
	 *
	 * @param   object  $item  The item object.
	 *
	 * @return	boolean
	 *
	 * @since	1.5
	 */
	protected function postSermon($item)
	{
		// Check categories
		$cats   = $this->getContentCategories($item->catid);
		$catIds = $cats[0];

		if (!$this->isCategoryIncluded($catIds) || $this->isCategoryExcluded($catIds))
		{
			return true;
		}

		// Determine Publish Up date
		if ($item->publish_up && ($item->publish_up != '0000-00-00 00:00:00'))
		{
			$publish_up = $item->publish_up;
		}
		elseif ($item->sermon_date && ($item->sermon_date != '0000-00-00 00:00:00'))
		{
			$publish_up = $item->sermon_date;
		}
		else
		{
			$publish_up = JFactory::getDate()->toSql();
		}

		$url = SermonspeakerHelperRoute::getSermonRoute($item->id, $item->catid, $item->language);

		// Get some additional information
		if ($series_id = (int) $item->series_id)
		{
			$db    = Jfactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('`avatar`, `title` AS series_title');
			$query->from('#__sermon_series');
			$query->where('`id` = ' . $series_id);
			$db->setQuery($query);
			$result = $db->loadAssoc();

			foreach ($result as $key => $value)
			{
				$item->$key = $value;
			}
		}

		if ($speaker_id = (int) $item->speaker_id)
		{
			$db    = Jfactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('`pic`, `title` AS speaker_title');
			$query->from('#__sermon_speakers');
			$query->where('`id` = ' . $speaker_id);
			$db->setQuery($query);
			$result = $db->loadAssoc();

			foreach ($result as $key => $value)
			{
				$item->$key = $value;
			}
		}

		// Get the image
		$image_url = SermonspeakerHelperSermonspeaker::insertPicture($item, 1);

		$native_object = json_encode($item);
		$this->postStatusMessage($item->id, $publish_up, $item->title, self::TYPE_SERMON, $url, $image_url, $native_object);
	}

	/**
	 * getExtendedData
	 *
	 * @param   string  $id              Param.
	 * @param   string  $typeinfo        Param.
	 * @param   string  &$native_object  Param.
	 *
	 * @return	array
	 *
	 * @since	1.5
	 */
	public function getExtendedData($id, $typeinfo, &$native_object)
	{
		$item = json_decode($native_object);

		// Get category path for item
		$cats      = $this->getContentCategories($item->catid);
		$catids    = $cats[0];
		$cat_names = $cats[1];

		// Use item title or text as message
		$title     = $item->title;
		$item_text = JHtml::_('content.prepare', $item->notes);
		$text      = $this->getMessagetext($this->use_text, $this->use_text_count, $title, $item_text);
		$hashtags  = '';

		// Use metakey or static text or nothing
		if ((2 == $this->static_text_source) || ((1 == $this->static_text_source) && (empty($item->metakey))))
		{
			$title = $this->addStatictext($this->static_text_pos, $title, $this->static_text);
			$text  = $this->addStatictext($this->static_text_pos, $text, $this->static_text);
		}
		elseif (1 == $this->static_text_source)
		{
			$hashtags .= $this->getHashtags($item->metakey, $this->metakey_count);
		}

		// Title
		$catsec_result = $this->addCategories($this->show_catsec, $cat_names, $title, 0);
		$title         = $catsec_result['text'];

		// Text
		$catsec_result = $this->addCategories($this->show_catsec, $cat_names, $text, $this->show_hash);
		$text          = $catsec_result['text'];

		if ('' != $catsec_result['hashtags'])
		{
			$hashtags .= ' ';
			$hashtags .= $catsec_result['hashtags'];
		}

		$data = array(
			'title' => $title,
			'text' => $text,
			'hashtags' => $hashtags,

			// Already done when msg is inserted in queue
			'url' => '',

			// Already done when msg is inserted in queue
			'image_url' => '',

			'fulltext' => $item_text,
			'catids' => $catids,
			'cat_names' => $cat_names,
			// 'author' => $this->getArticleAuthor($item),
			'language' => $item->language,

			// Sermons don't have an access field, maybe take category access instead
			// 'access' => $item->access,

			'is_valid' => true
		);

		// Use speakername as author if available
		$data['author'] = ($item->speaker_title) ? $item->speaker_title : $this->getArticleAuthor($item);

		return $data;
	}
}
