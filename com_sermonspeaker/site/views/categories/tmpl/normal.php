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

// Add strings for translations in Javascript.
Text::script('JGLOBAL_EXPAND_CATEGORIES');
Text::script('JGLOBAL_COLLAPSE_CATEGORIES');

HTMLHelper::_('bootstrap.collapse');
?>
<div class="com-sermonspeaker-categories categories-list">
	<?php
	echo LayoutHelper::render('joomla.content.categories_default', $this);
	echo $this->loadTemplate('items');
	?>
</div>
