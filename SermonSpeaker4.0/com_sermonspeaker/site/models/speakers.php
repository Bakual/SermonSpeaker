<?php
/**
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * @package		SermonSpeaker
 */
// Based on com_contact
class SermonspeakerModelspeakers extends JModelList
{
	protected $_item = null;
	protected $_siblings = null;
	protected $_children = null;
	protected $_parent = null;

	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'ordering', 'speakers.ordering',
				'name', 'speakers.name',
				'checked_out', 'speakers.checked_out',
				'checked_out_time', 'speakers.checked_out_time',
				'language', 'speakers.language',
				'hits', 'speakers.hits',
			);
		}

		parent::__construct($config);
	}

	protected function getListQuery()
	{
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->authorisedLevels());

		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

				// Select required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'speakers.id, speakers.name, speakers.catid, speakers.pic, ' .
				'CASE WHEN CHAR_LENGTH(speakers.alias) THEN CONCAT_WS(\':\', speakers.id, speakers.alias) ELSE speakers.id END as slug, ' .
				'speakers.hits, speakers.intro, speakers.bio, speakers.website, speakers.alias, ' .
				'speakers.checked_out, speakers.checked_out_time, ' .
				'speakers.state, speakers.ordering, speakers.created, speakers.created_by'
			)
		);
		$query->from('`#__sermon_speakers` AS speakers');

		// Join over Speakers Category.
		$query->join('LEFT', '#__categories AS c_speaker ON c_speaker.id = speakers.catid');
		$query->where('(speakers.catid = 0 OR (c_speaker.access IN ('.$groups.') AND c_speaker.published = 1))');

		// Filter by category
		if ($categoryId = $this->getState('category.id')) {
			$query->where('speakers.catid = '.(int) $categoryId);
		}

		// Subquerie to get counts of sermons and series
		$query->select('(SELECT COUNT(DISTINCT sermons.id) FROM #__sermon_sermons AS sermons WHERE sermons.speaker_id = speakers.id AND sermons.id > 0) AS sermons');
		$query->select('(SELECT COUNT(DISTINCT sermons2.series_id) FROM #__sermon_sermons AS sermons2 WHERE sermons2.speaker_id = speakers.id AND sermons2.series_id > 0) AS series');

		// Grouping by speaker
		$query->group('speakers.id');

		// Join over users for the author names.
		$query->select("user.name AS author");
		$query->join('LEFT', '#__users AS user ON user.id = speakers.created_by');

		// Filter by state
		$state = $this->getState('filter.state');
		if (is_numeric($state)) {
			$query->where('speakers.state = '.(int) $state);
		}

		// Filter by language
		if ($this->getState('filter.language')) {
			$query->where('speakers.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
		}

		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'ordering')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));

		return $query;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app	= JFactory::getApplication();
		$params	= $app->getParams();
		$this->setState('params', $params);

		// Category filter
		$id	= (int)$params->get('catid', 0);
		if (!$id){ $id	= JRequest::getInt('speaker_cat', 0); }
		$this->setState('category.id', $id);

		$this->setState('filter.language', $app->getLanguageFilter());

		$this->setState('filter.state',	1);

		parent::populateState('ordering', 'ASC');
	}

	/**
	 * Method to get category data for the current category
	 *
	 * @param	int		An optional ID
	 *
	 * @return	object
	 * @since	1.5
	 */
	public function getCategory()
	{
		if (!is_object($this->_item)) {
			if( isset( $this->state->params ) ) {
				$params = $this->state->params;
				$options = array();
				$options['countItems'] = $params->get('show_cat_num_items', 1) || !$params->get('show_empty_categories_cat', 0);
			}
			else {
				$options['countItems'] = 0;
			}
			$options['table'] = '#__sermon_'.$this->state->get('category.type', 'speakers');

			$categories = JCategories::getInstance('Sermonspeaker', $options);
			$this->_item = $categories->get($this->getState('category.id', 'root'));

			// Compute selected asset permissions.
			if (is_object($this->_item)) {
				$user	= JFactory::getUser();
				$userId	= $user->get('id');
				$asset	= 'com_sermonspeaker.category.'.$this->_item->id;

				// Check general create permission.
				if ($user->authorise('core.create', $asset)) {
					$this->_item->getParams()->set('access-create', true);
				}

				// TODO: Why aren't we lazy loading the children and siblings?
				$this->_children = $this->_item->getChildren();
				$this->_parent = false;

				if ($this->_item->getParent()) {
					$this->_parent = $this->_item->getParent();
				}

				$this->_rightsibling = $this->_item->getSibling();
				$this->_leftsibling = $this->_item->getSibling(false);
			}
			else {
				$this->_children = false;
				$this->_parent = false;
			}
		}

		return $this->_item;
	}

	/**
	 * Get the parent categorie.
	 *
	 * @param	int		An optional category id. If not supplied, the model state 'category.id' will be used.
	 *
	 * @return	mixed	An array of categories or false if an error occurs.
	 * @since	1.6
	 */
	public function getParent()
	{
		if (!is_object($this->_item)) {
			$this->getCategory();
		}

		return $this->_parent;
	}

	/**
	 * Get the left sibling (adjacent) categories.
	 *
	 * @return	mixed	An array of categories or false if an error occurs.
	 * @since	1.6
	 */
	function &getLeftSibling()
	{
		if (!is_object($this->_item)) {
			$this->getCategory();
		}

		return $this->_leftsibling;
	}

	/**
	 * Get the right sibling (adjacent) categories.
	 *
	 * @return	mixed	An array of categories or false if an error occurs.
	 * @since	1.6
	 */
	function &getRightSibling()
	{
		if (!is_object($this->_item)) {
			$this->getCategory();
		}

		return $this->_rightsibling;
	}

	/**
	 * Get the child categories.
	 *
	 * @param	int		An optional category id. If not supplied, the model state 'category.id' will be used.
	 *
	 * @return	mixed	An array of categories or false if an error occurs.
	 * @since	1.6
	 */
	function &getChildren()
	{
		if (!is_object($this->_item)) {
			$this->getCategory();
		}

		// Order subcategories
		if (sizeof($this->_children)) {
			$params = $this->getState()->get('params');
			if ($params->get('orderby_pri') == 'alpha' || $params->get('orderby_pri') == 'ralpha') {
				jimport('joomla.utilities.arrayhelper');
				JArrayHelper::sortObjects($this->_children, 'title', ($params->get('orderby_pri') == 'alpha') ? 1 : -1);
			}
		}

		return $this->_children;
	}
}