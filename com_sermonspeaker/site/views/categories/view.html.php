<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\MVC\View\CategoriesView;

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerViewCategories extends CategoriesView
{
	/**
	 * Language key for default page heading
	 *
	 * @var    string
	 * @since  6.0
	 */
	protected $pageHeading = 'JGLOBAL_ARTICLES';

	/**
	 * @var    string  The name of the extension for the category
	 * @since  6.0
	 */
	protected $extension = 'com_sermonspeaker';

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return void
	 *
	 * @throws \Exception
	 * @since ?
	 */
	public function display($tpl = null)
	{
		$state  = $this->get('State');
		$params = &$state->params;

		// Set layout from parameters if not already set elsewhere
		if ($this->getLayout() == 'default')
		{
			$this->setLayout($params->get('categorieslayout', 'normal'));
		}

		parent::display($tpl);
	}
}
