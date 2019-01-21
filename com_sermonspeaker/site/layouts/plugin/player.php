<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site.Layouts
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2019 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * @var  array                    $displayData Contains the following items:
 * @var  array|object             $items       An array of sermon objects or a single object
 * @var  object                   $player      The player object
 * @var  Joomla\Registry\Registry $params      The params
 */
extract($displayData);

JHtml::_('stylesheet', 'com_sermonspeaker/player.css', array('relative' => true));
?>
<?php if (is_array($items)) :
	echo $this->sublayout('info', $items);
endif; ?>
echo $player->mspace;
echo $player->script;
?>
<hr/>
<?php if ($player->toggle) :
	echo $this->sublayout('toggler', $items);
endif; ?>
