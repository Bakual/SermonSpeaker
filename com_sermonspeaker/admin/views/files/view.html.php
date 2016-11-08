<?php
defined('_JEXEC') or die;

/**
 * Class SermonspeakerViewFiles
 *
 * @since  ?
 */
class SermonspeakerViewFiles extends JViewLegacy
{
	/**
	 * @var object
	 *
	 * @since  ?
	 */
	protected $state;
	/**
	 * @var array
	 *
	 * @since  ?
	 */
	protected $items;

	/**
	 * @param null $tpl
	 *
	 * @return mixed
	 *
	 * @since  ?
	 */
	function display($tpl = null)
	{
		$this->state	= $this->get('state');
		$this->items = $this->get('items');

		return parent::display($tpl);
	}
}