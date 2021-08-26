<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.RelatedSermons
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
?>
<ul class="mod-relateditems relateditems mod-list">
<?php foreach ($list as $item) : ?>
<li>
	<a href="<?php echo $item->route; ?>">
		<?php if ($showDate) : ?>
			<?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC4')) . ' - '; ?>
		<?php endif; ?>
		<?php echo $item->title; ?></a>
</li>
<?php endforeach; ?>
</ul>
