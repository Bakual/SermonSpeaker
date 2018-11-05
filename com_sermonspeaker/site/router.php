<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Routing class from com_sermonspeaker
 *
 * @since  5.1.3
 */
class SermonspeakerRouter extends JComponentRouterBase
{
	/**
	 * Build the route for the com_sermonspeaker component
	 *
	 * @param   array &$query An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   5.1.2
	 */
	public function build(&$query)
	{
		$segments = array();
		$app      = JFactory::getApplication();
		$view     = '';

		// We need a menu item.  Either the one specified in the query, or the current active one if none specified
		$menu     = $app->getMenu();
		$menuItem = (empty($query['Itemid'])) ? $menu->getActive() : $menu->getItem($query['Itemid']);

		// Calculate View
		if (isset($query['view']))
		{
			$menuView = isset($menuItem->query['view']) ? $menuItem->query['view'] : '';
			$view     = $query['view'];
			unset($query['view']);

			// Check if menuitem matches the query
			if (isset($query['id']))
			{
				$menuId = isset($menuItem->query['id']) ? $menuItem->query['id'] : 0;

				if ($menuView == $view && $menuId == (int) $query['id'])
				{
					unset($query['id']);
				}
				else
				{
					$segments[] = $view;
				}
			}
			elseif (isset($query['catid']))
			{
				$menuCatid = ($menuItem) ? $menuItem->params->get('catid') : null;

				if ($menuView == $view && $menuCatid == (int) $query['catid'])
				{
					unset($query['catid']);
				}
				else
				{
					$segments[] = $view;
				}
			}
			else
			{
				if ($view !== $menuView)
				{
					$segments[] = $view;
				}
			}
		}
		else
		{
			// Get view from Itemid
			if (isset($menuItem->query['view']))
			{
				$view = $menuItem->query['view'];
			}
		}

		if (isset($query['task']))
		{
			$segments[] = str_replace('.', '_', $query['task']);

			if (isset($query['type']))
			{
				$segments[] = $query['type'];
				unset($query['type']);
			}

			unset($query['task']);
		}

		if (isset($query['id']))
		{
			// Make sure we have the id and the alias
			if ($view == 'sermon'
				|| $view == 'serie'
				|| $view == 'speaker'
			)
			{
				if (strpos($query['id'], ':') === false)
				{
					$db      = JFactory::getDbo();
					$dbQuery = $db->getQuery(true)
						->select('alias')
						->from('#__sermon_' . $view . 's')
						->where('id = ' . (int) $query['id']);
					$db->setQuery($dbQuery);
					$alias       = $db->loadResult();
					$query['id'] = $query['id'] . ':' . $alias;
				}
			}

			$segments[] = $query['id'];
			unset($query['id']);
		}

		if (isset($query['state']))
		{
			if ($query['state'] == 2)
			{
				$segments[] = 'archive';
			}

			unset($query['state']);
		}

		if (isset($query['year']))
		{
			$segments[] = $query['year'];
			unset($query['year']);
		}

		if (isset($query['month']))
		{
			$segments[] = $query['month'];
			unset($query['month']);
		}

		if (($view == 'speaker') && isset($query['layout']))
		{
			$segments[] = $query['layout'];
			unset($query['layout']);
		}

		if (($view == 'sitemap' || $view == 'feed') && !isset($query['format']))
		{
			if ($app->get('sef_suffix'))
			{
				$query['format'] = 'raw';
			}
		}

		foreach ($segments as &$segment)
		{
			$segment = str_replace(':', '-', $segment);
		}

		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array &$segments The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   5.1.2
	 */
	public function parse(&$segments)
	{
		$vars = array();

		switch ($segments[0])
		{
			case 'series':
				$vars['view'] = 'series';
				break;
			case 'serie':
				$vars['view'] = 'serie';
				$id           = explode(':', $segments[1]);
				$vars['id']   = (int) $id[0];

				if (isset($segments[2]) && $segments[2])
				{
					$vars['year'] = (int) $segments[2];
				}

				if (isset($segments[3]) && $segments[3])
				{
					$vars['month'] = (int) $segments[3];
				}
				break;
			case 'sermons':
				$vars['view'] = 'sermons';

				if (isset($segments[1]) && $segments[1] == 'archive')
				{
					$vars['state'] = 2;

					if (isset($segments[2]) && $segments[2])
					{
						$vars['year'] = (int) $segments[2];
					}

					if (isset($segments[3]) && $segments[3])
					{
						$vars['month'] = (int) $segments[3];
					}
				}
				else
				{
					if (isset($segments[1]) && $segments[1])
					{
						$vars['year'] = (int) $segments[1];
					}

					if (isset($segments[2]) && $segments[2])
					{
						$vars['month'] = (int) $segments[2];
					}
				}

				break;
			case 'sermon':
				$vars['view'] = 'sermon';
				$id           = explode(':', $segments[1]);
				$vars['id']   = (int) $id[0];

				if (isset($segments[2]))
				{
					$vars['layout'] = $segments[2];
				}

				break;
			case 'speakers':
				$vars['view'] = 'speakers';
				break;
			case 'speaker':
				$vars['view'] = 'speaker';
				$id           = explode(':', $segments[1]);
				$vars['id']   = (int) $id[0];

				if (isset($segments[2]) && is_numeric($segments[2]))
				{
					if ($segments[2])
					{
						$vars['year'] = (int) $segments[2];
					}

					if (isset($segments[3]) && $segments[3])
					{
						$vars['month'] = (int) $segments[3];
					}

					if (isset($segments[4]))
					{
						$vars['layout'] = $segments[4];
					}
				}
				else
				{
					if (isset($segments[2]))
					{
						$vars['layout'] = $segments[2];
					}
				}
				break;
			case 'frontendupload':
				$vars['view'] = 'frontendupload';
				break;
			case 'serieform':
				$vars['view'] = 'serieform';
				break;
			case 'speakerform':
				$vars['view'] = 'speakerform';
				break;
			case 'feed':
				$vars['view']   = 'feed';
				$vars['format'] = 'raw';
				break;
			case 'sitemap':
				$vars['view']   = 'sitemap';
				$vars['format'] = 'raw';
				break;
			case 'serie_download':
				$vars['task'] = 'serie.download';
				$id           = explode(':', $segments[1]);
				$vars['id']   = (int) $id[0];
				break;
			case 'download':
				$vars['task'] = 'download';
				$vars['type'] = $segments[1];
				$id           = explode(':', $segments[2]);
				$vars['id']   = (int) $id[0];
				break;
			case 'frontendupload_edit':
				$vars['task'] = 'frontendupload.edit';
				break;
			case 'frontendupload_add':
				$vars['task'] = 'frontendupload.add';
				break;
			case 'serieform_edit':
				$vars['task'] = 'serieform.edit';
				break;
			case 'serieform_add':
				$vars['task'] = 'serieform.add';
				break;
			case 'speakerform_edit':
				$vars['task'] = 'speakerform.edit';
				break;
			case 'speakerform_add':
				$vars['task'] = 'speakerform.add';
				break;
			case 'scripture':
				$vars['view'] = 'scripture';
				break;
			case 'close':
				$vars['view'] = 'close';
				break;
		}

		return $vars;
	}
}

/**
 * Old SermonSpeaker router function
 *
 * Proxys for the new router interface for old SEF extensions
 *
 * @param   array &$query An array of URL arguments
 *
 * @return  array  The URL arguments to use to assemble the subsequent URL.
 *
 * @deprecated  J4.0  Use Class based routers instead
 *
 * @since       ?
 */
function sermonspeakerBuildRoute(&$query)
{
	$router = new SermonspeakerRouter;

	return $router->build($query);
}

/**
 * Old SermonSpeaker router function
 *
 * Proxys for the new router interface for old SEF extensions
 *
 * @param   array $segments The segments of the URL to parse.
 *
 * @return  array  The URL attributes to be used by the application.
 *
 * @deprecated  J4.0  Use Class based routers instead
 *
 * @since       ?
 */
function sermonspeakerParseRoute($segments)
{
	$router = new SermonspeakerRouter;

	return $router->parse($segments);
}
