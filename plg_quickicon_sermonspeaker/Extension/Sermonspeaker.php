<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Plugin.Quickicon
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Plugin\Quickicon\Sermonspeaker\Extension;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\Module\Quickicon\Administrator\Event\QuickIconsEvent;

defined('_JEXEC') or die;

/**
 * SermonSpeaker Quickicons plugin
 *
 * @since  1.0
 */
class Sermonspeaker extends CMSPlugin implements SubscriberInterface
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return  array
	 *
	 * @since   4.0.0
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onGetIcons' => 'onGetIcons',
		];
	}

	/**
	 * This method is called when the Quick Icons module is constructing its set
	 * of icons. You can return an array which defines a single icon and it will
	 * be rendered right after the stock Quick Icons.
	 *
	 * @param QuickIconsEvent $event The event object
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 * @since   1.0
	 */
	public function onGetIcons(QuickIconsEvent $event): void
	{
		$context = $event->getContext();

		if ($context != $this->params->get('context', 'mod_quickicon')
			|| !$this->getApplication()->getIdentity()->authorise('core.manage', 'com_sermonspeaker'))
		{
			return;
		}

		// Add the icon to the result array
		$result = $event->getArgument('result', []);

		if ($this->params->get('sermons', 1))
		{
			$result[] = [
				[
					'image' => 'fas fa-list-alt',
					'link' => 'index.php?option=com_sermonspeaker&view=sermons',
					'linkadd' => 'index.php?option=com_sermonspeaker&task=sermon.add',
					'name' => 'PLG_QUICKICON_SERMONSPEAKER_SERMONS_MANAGER',
					'group' => 'PLG_QUICKICON_SERMONSPEAKER_GROUP'
				],
			];
		}

		if ($this->params->get('speakers', 1))
		{
			$result[] = [
				[
					'image' => 'fas fa-comment',
					'link'  => 'index.php?option=com_sermonspeaker&view=speakers',
					'linkadd'  => 'index.php?option=com_sermonspeaker&task=speaker.add',
					'name'  => 'PLG_QUICKICON_SERMONSPEAKER_SPEAKERS_MANAGER',
					'group' => 'PLG_QUICKICON_SERMONSPEAKER_GROUP'
				],
			];
		}

		if ($this->params->get('series', 1))
		{
			$result[] = [
				[
					'image' => 'fas fa-object-group',
					'link'  => 'index.php?option=com_sermonspeaker&view=series',
					'linkadd'  => 'index.php?option=com_sermonspeaker&task=serie.add',
					'name'  => 'PLG_QUICKICON_SERMONSPEAKER_SERIES_MANAGER',
					'group' => 'PLG_QUICKICON_SERMONSPEAKER_GROUP'
				],
			];
		}

		$event->setArgument('result', $result);
	}
}
