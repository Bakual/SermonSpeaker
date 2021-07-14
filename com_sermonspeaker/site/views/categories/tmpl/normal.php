<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

?>
<div class="com-sermonspeaker-categories categories-list">
	<?php
	echo LayoutHelper::render('joomla.content.categories_default', $this);
	echo $this->loadTemplate('items');
	?>
</div>
