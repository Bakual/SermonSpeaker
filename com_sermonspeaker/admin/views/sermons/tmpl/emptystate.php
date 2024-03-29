<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;

$displayData = [
	'textPrefix' => 'COM_SERMONSPEAKER_SERMONS',
	'formURL'    => 'index.php?option=com_sermonspeaker&view=sermons',
	'icon'       => 'icon-quote-3 sermons',
];

$user = Factory::getApplication()->getIdentity();

if ($user->authorise('core.create', 'com_sermonspeaker') || count($user->getAuthorisedCategories('com_sermonspeaker', 'core.create')) > 0)
{
	$displayData['createURL'] = 'index.php?option=com_sermonspeaker&task=sermon.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);
