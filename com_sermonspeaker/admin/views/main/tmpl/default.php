<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;
?>
<div class="row-fluid">
	<div id="j-main-container">
		<div class="card-deck">
			<a class="card text-center" href="index.php?option=com_sermonspeaker&view=sermons">
				<div class="card-block">
					<img src="<?php echo JUri::base()."components/com_sermonspeaker/images/sermon.png"; ?>">
					<h3 class="card-title"><?php echo JText::_('COM_SERMONSPEAKER_SERMONS_TITLE'); ?></h3>
				</div>
			</a>
			<a class="card text-center" href="index.php?option=com_sermonspeaker&view=series">
				<div class="card-block">
					<img src="<?php echo JUri::base()."components/com_sermonspeaker/images/series.png"; ?>">
					<h3 class="card-title"><?php echo JText::_('COM_SERMONSPEAKER_SERIES_TITLE'); ?></h3>
				</div>
			</a>
			<a class="card text-center" href="index.php?option=com_sermonspeaker&view=speakers">
				<div class="card-block">
					<img class="card-text" src="<?php echo JUri::base()."components/com_sermonspeaker/images/speakers.png"; ?>">
					<h3 class="card-title"><?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS_TITLE'); ?></h3>
				</div>
			</a>
		</div>
		<div class="card-deck">
			<a class="card text-center" href="index.php?option=com_categories&extension=com_sermonspeaker.sermons">
				<div class="card-block">
					<img src="<?php echo JUri::base()."components/com_sermonspeaker/images/category.png"; ?>">
					<h3 class="card-title"><?php echo JText::_('COM_SERMONSPEAKER_MENU_CATEGORIES_SERMONS'); ?></h3>
				</div>
			</a>
			<a class="card text-center" href="index.php?option=com_categories&extension=com_sermonspeaker.series">
				<div class="card-block">
					<img src="<?php echo JUri::base()."components/com_sermonspeaker/images/category.png"; ?>">
					<h3 class="card-title"><?php echo JText::_('COM_SERMONSPEAKER_MENU_CATEGORIES_SERIES'); ?></h3>
				</div>
			</a>
			<a class="card text-center" href="index.php?option=com_categories&extension=com_sermonspeaker.speakers">
				<div class="card-block">
					<img src="<?php echo JUri::base()."components/com_sermonspeaker/images/category.png"; ?>">
					<h3 class="card-title"><?php echo JText::_('COM_SERMONSPEAKER_MENU_CATEGORIES_SPEAKERS'); ?></h3>
				</div>
			</a>
		</div>
		<div class="card-deck">
			<a class="card text-center" href="index.php?option=com_sermonspeaker&view=tools">
				<div class="card-block">
					<img src="<?php echo JUri::base()."components/com_sermonspeaker/images/tools.png"; ?>">
					<h3 class="card-title"><?php echo JText::_('COM_SERMONSPEAKER_MAIN_TOOLS'); ?></h3>
				</div>
			</a>
			<a class="card text-center" href="index.php?option=com_sermonspeaker&view=languages">
				<div class="card-block">
					<img src="<?php echo JUri::base()."components/com_sermonspeaker/images/globe.png"; ?>">
					<h3 class="card-title"><?php echo JText::_('COM_SERMONSPEAKER_MAIN_LANGUAGES'); ?></h3>
				</div>
			</a>
			<a class="card text-center" href="index.php?option=com_sermonspeaker&view=help">
				<div class="card-block">
					<img src="<?php echo JUri::base()."components/com_sermonspeaker/images/help.png"; ?>">
					<h3 class="card-title"><?php echo JText::_('COM_SERMONSPEAKER_MENU_HELP'); ?></h3>
				</div>
			</a>
		</div>
	</div>
</div>