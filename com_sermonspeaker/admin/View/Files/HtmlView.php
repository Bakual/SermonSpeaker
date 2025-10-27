<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\View\Files;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die;

/**
 * Class SermonspeakerViewFiles
 *
 * @since  ?
 */
class HtmlView extends HtmlView
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
	 * @param   null  $tpl
	 *
	 * @return mixed
	 *
	 * @since  ?
	 */
	function display($tpl = null)
	{
		$this->state = $this->get('state');
		$this->items = $this->get('items');

		parent::display($tpl);
	}
}