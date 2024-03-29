<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;

/**
 * JSON View class for the SermonSpeaker Component
 *
 * @since  5.5
 */
class SermonspeakerViewSermons extends HtmlView
{
	/**
	 * Creates the JSON data.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return void
	 * @throws \Exception
	 *
	 * @since ?
	 */
	public function display($tpl = null)
	{
		// Get some data from the models
		$items = $this->get('Items');

		// Get Category stuff from models
		$category = $this->get('Category');
		$parent   = $this->get('Parent');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			Factory::getApplication()->setHeader('status', 500 . ' ' . implode("\n", $errors));
			echo new JResponseJson(null, implode("\n", $errors), true);

			return;
		}

		if ($category == false)
		{
			Factory::getApplication()->setHeader('status', 404 . ' ' . Text::_('JGLOBAL_CATEGORY_NOT_FOUND'));
			echo new JResponseJson(null, Text::_('JGLOBAL_CATEGORY_NOT_FOUND'), true);

			return;
		}

		if ($parent == false && $category->id != 'root')
		{
			Factory::getApplication()->setHeader('status', 404 . ' ' . Text::_('JGLOBAL_CATEGORY_NOT_FOUND'));
			echo new JResponseJson(null, Text::_('JGLOBAL_CATEGORY_NOT_FOUND'), true);

			return;
		}

		// Check whether category access level allows access.
		$user   = Factory::getUser();
		$groups = $user->getAuthorisedViewLevels();

		if (!in_array($category->access, $groups))
		{
			Factory::getApplication()->setHeader('status', 403 . ' ' . Text::_('JERROR_ALERTNOAUTHOR'));
			echo new JResponseJson(null, Text::_('JERROR_ALERTNOAUTHOR'), true);

			return;
		}

		$response = array();

		foreach ($items as $item)
		{
			$tmp                = new stdClass();
			$tmp->id            = $item->id;
			$tmp->title         = $item->title;
			$tmp->alias         = $item->alias;
			$tmp->slug          = $item->slug;
			$tmp->audiofile     = $item->audiofile;
			$tmp->audiofilesize = $item->audiofilesize;
			$tmp->videofile     = $item->videofile;
			$tmp->videofilesize = $item->videofilesize;
			$tmp->addfile       = $item->addfile;
			$tmp->addfileDesc   = $item->addfileDesc;
			$tmp->picture       = $item->picture;
			$tmp->hits          = $item->hits;
			$tmp->notes         = $item->notes;
			$tmp->sermon_date   = $item->sermon_date;
			$tmp->sermon_time   = $item->sermon_time;
			$tmp->sermon_number = $item->sermon_number;
			$tmp->scripture     = SermonspeakerHelperSermonspeaker::buildScripture($item->scripture, false);

			// Category
			$tmp->category        = new stdClass();
			$tmp->category->title = $item->category_title;
			$tmp->category->slug  = $item->catslug;

			// Speaker
			$tmp->speaker        = new stdClass();
			$tmp->speaker->title = $item->speaker_title;

			// Show only details when speaker is published
			if ($item->speaker_state)
			{
				$tmp->speaker->slug    = $item->speaker_slug;
				$tmp->speaker->pic     = $item->pic;
				$tmp->speaker->intro   = $item->intro;
				$tmp->speaker->bio     = $item->bio;
				$tmp->speaker->website = $item->website;
			}

			// Series
			$tmp->series        = new stdClass();
			$tmp->series->title = $item->series_title;

			// Show only details when series is published
			if ($item->series_state)
			{
				$tmp->series->slug   = $item->series_slug;
				$tmp->series->avatar = $item->avatar;
			}


			$response[] = $tmp;
		}

		$app           = Factory::getApplication();
		$app->mimeType = 'application/json';
		$app->setHeader('Content-Type', $app->mimeType . '; charset=' . $app->charSet);
		$app->sendHeaders();

		echo new JResponseJson($response);
	}
}
