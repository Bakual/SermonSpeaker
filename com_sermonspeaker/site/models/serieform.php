<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Uri\Uri;

require_once JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/models/serie.php';

/**
 * Model class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerModelSerieform extends SermonspeakerModelSerie
{
	/**
	 * @var  string
	 *
	 * @since ?
	 */
	protected $context = 'serie';

	/**
	 * Get the return URL.
	 *
	 * @return    string    The return URL.
	 *
	 * @since ?
	 */
	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.2
	 */
	public function save($data)
	{
		// Associations are not edited in frontend ATM so we have to inherit them
		if (Associations::isEnabled() && !empty($data['id']))
		{
			if ($associations = Associations::getAssociations('com_sermonspeaker.series', '#__sermon_series', 'com_sermonspeaker.serie', $data['id']))
			{
				foreach ($associations as $tag => $associated)
				{
					$associations[$tag] = (int) $associated->id;
				}

				$data['associations'] = $associations;
			}
		}

		return parent::save($data);
	}

	/**
	 * Method to auto-populate the model state
	 *
	 * Note. Calling getState in this method will result in recursion
	 *
	 * @param   string  $ordering   Ordering column
	 * @param   string  $direction  'ASC' or 'DESC'
	 *
	 * @return  void
	 *
	 * @since ?
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		/** @var SiteApplication $app */
		$app    = Factory::getApplication();
		$jinput = $app->input;

		// Load state from the request
		$pk = $jinput->get('s_id', 0, 'int');
		$this->setState('serieform.id', $pk);

		// Add compatibility variable for default naming conventions
		$this->setState('form.id', $pk);

		$categoryId = $jinput->get('catid', 0, 'int');
		$this->setState('serieform.catid', $categoryId);

		$return = $jinput->get('return', '', 'base64');

		if (!Uri::isInternal(base64_decode($return)))
		{
			$return = null;
		}

		$this->setState('return_page', base64_decode($return));

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		$this->setState('layout', $jinput->get('layout'));
	}
}
