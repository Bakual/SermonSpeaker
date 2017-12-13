<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Model class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerModelCategories extends JModelLegacy
{
	/**
	 * Model context string.
	 *
	 * @var        string
	 *
	 * @since ?
	 */
	public $_context = 'com_sermonspeaker.categories';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var        string
	 *
	 * @since ?
	 */
	protected $_extension = 'com_sermonspeaker';

	private $_parent = null;

	private $_items = null;

	/**
	 * Method to auto-populate the model state
	 *
	 * Note. Calling getState in this method will result in recursion
	 *
	 * @return  void
	 *
	 * @since ?
	 */
	protected function populateState()
	{
		/** @var JApplicationSite $app */
		$app = JFactory::getApplication();
		$this->setState('filter.extension', $this->_extension);

		// Get the parent id if defined.
		$parentId = $app->input->get('id', 0, 'int');
		$this->setState('filter.parentId', $parentId);

		$params = $app->getParams();
		$this->setState('params', $params);

		$this->setState('filter.state', 1);
		$this->setState('filter.access', true);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string $id A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since ?
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.extension');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . $this->getState('filter.access');
		$id .= ':' . $this->getState('filter.parentId');

		return $id;
	}

	/**
	 * Redefine the function an add some properties to make the styling more easy
	 *
	 * @param   bool $recursive True if you want to return children recursively.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since ?
	 */
	public function getItems($recursive = false)
	{
		if (!count($this->_items))
		{
			$app    = JFactory::getApplication();
			$menu   = $app->getMenu();
			$active = $menu->getActive();
			$params = new Joomla\Registry\Registry;

			if ($active)
			{
				$params->loadString($active->params);
			}

			$options               = array();
			$options['table']      = '#__sermon_sermons';
			$options['countItems'] = $params->get('show_cat_num_items_cat', 1) || !$params->get('show_empty_categories_cat', 0);
			$categories            = \JCategories::getInstance('sermonspeaker.sermons', $options);
			$this->_parent         = $categories->get($this->getState('filter.parentId', 'root'));

			if (is_object($this->_parent))
			{
				$this->_items = $this->_parent->getChildren($recursive);
			}
			else
			{
				$this->_items = false;
			}
		}

		return $this->_items;
	}

	/**
	 * Get the parent category
	 *
	 * @return  mixed  An array of categories or false if an error occurs
	 *
	 * @since ?
	 */
	public function getParent()
	{
		if (!is_object($this->_parent))
		{
			$this->getItems();
		}

		return $this->_parent;
	}
}
