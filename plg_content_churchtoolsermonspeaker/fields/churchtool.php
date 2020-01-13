<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Plugin.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2019 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('sql');

/**
 * Shows data from ChurchTool to select
 *
 * @since       1.0.0
 */
class JFormFieldChurchtool extends JFormFieldSQL
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $type = 'Churchtool';

	/**
	 * Helps to only show SQL error once.
	 *
	 * @var    bool
	 * @since  1.0.0
	 */
	private static $error_done = false;

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   3.3.7
	 */
	protected function getOptions()
	{
		$options = array();

		// Initialize some field attributes.
		$key   = $this->keyField;
		$value = $this->valueField;

		// Create the database object to access the ChurchTool database.
		$form   = $this->form->getData();
		$params = new Joomla\Registry\Registry($form->get('params'));

		$option = array();
		$option['driver']   = $params->get('db_type', 'mysqli');
		$option['host']     = $params->get('db_host', 'localhost');
		$option['database'] = $params->get('db_database');
		$option['user']     = $params->get('db_user');
		$option['password'] = $params->get('db_pass');
		$option['prefix']   = '';

		$db = JDatabaseDriver::getInstance($option);

		// Set the query and get the result list.
		$db->setQuery($this->query);

		try
		{
			$items = $db->loadObjectlist();
		}
		catch (RuntimeException $e)
		{
			if (!self::$error_done)
			{
				JFactory::getApplication()->enqueueMessage(JText::_('PLG_CONTENT_CHURCHTOOLSERMONSPEAKER_ERROR_DATABASE'), 'warning');
				self::$error_done = true;
			}
		}

		// Build the field options.
		if (!empty($items))
		{
			foreach ($items as $item)
			{
				if ($this->translate == true)
				{
					$options[] = JHtml::_('select.option', $item->$key, JText::_($item->$value));
				}
				else
				{
					$options[] = JHtml::_('select.option', $item->$key, $item->$value);
				}
			}
		}

		return $options;
	}
}
