<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();

$dateformat = $mode ? 'F, Y' : 'Y';
?>
<ul class="sermonarchive<?php echo $moduleclass_sfx; ?>">
<?php
foreach ($list as $item) :
	$url = 'index.php?option=com_sermonspeaker&view=sermons&year=' . $item->year . '&month=' . $item->month . '&Itemid=' . $itemid;
	if ($state == 2) :
		$url .= '&state=2';
	endif;
	$link = JRoute::_($url); ?>
	<li><a href="<?php echo $link; ?>"><?php echo HTMLHelper::date($item->date, $dateformat, true); ?></a></li>
	<?php
endforeach; ?>
</ul>
