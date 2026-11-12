<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2026 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Site\Trait;

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Categories\CategoryNode;
use Joomla\CMS\Factory;
use Joomla\CMS\Tree\NodeInterface;
use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die();

trait CategoryTreeTrait
{
	private ?CategoryNode $categoryNode = null;
	private bool $categoryLoaded = false;

	/**
	 * Context for Categories::getInstance(), e.g. 'com_sermonspeaker.sermons'
	 */
	abstract protected function getCategoryContext(): string;

	public function getCategory(): ?CategoryNode
	{
		if ($this->categoryLoaded)
		{
			return $this->categoryNode;
		}

		$this->categoryLoaded = true;

		if (isset($this->state->params))
		{
			$params                = $this->state->params;
			$options['countItems'] = $params->get('show_cat_num_items', 1) || !$params->get('show_empty_categories', 0);
			$options['access']     = $params->get('check_access_rights', 1);
		}
		else
		{
			$options['countItems'] = 0;
		}

		$categories = Categories::getInstance($this->getCategoryContext(), $options);
		$node       = $categories->get($this->getState('category.id', 'root'));

		if ($node instanceof CategoryNode)
		{
			$user  = Factory::getApplication()->getIdentity();
			$asset = $this->getCategoryContext() . '.category.' . $node->id;

			if ($user->authorise('core.create', $asset))
			{
				$node->getParams()->set('access-create', true);
			}

			$this->categoryNode = $node;
		}

		return $this->categoryNode;
	}

	/**
	 * Lazy: only runs the parent lookup when it is requested.
	 */
	public function getParent(): NodeInterface|false
	{
		return $this->getCategory()?->getParent() ?: false;
	}

	public function getLeftSibling(): CategoryNode|false|null
	{
		return $this->getCategory()?->getSibling(false) ?? false;
	}

	public function getRightSibling(): CategoryNode|false|null
	{
		return $this->getCategory()?->getSibling() ?? false;
	}

	public function getChildren(): array
	{
		$children = $this->getCategory()?->getChildren() ?? [];

		if ($children)
		{
			$params  = $this->getState()->get('params');
			$orderBy = $params ? $params->get('orderby_pri') : null;

			if ($orderBy === 'alpha' || $orderBy === 'ralpha')
			{
				$children = ArrayHelper::sortObjects($children, 'title', $orderBy === 'alpha' ? 1 : -1);
			}
		}

		return $children;
	}
}