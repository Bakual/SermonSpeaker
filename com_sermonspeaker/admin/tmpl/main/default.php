<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>
<div class="row">
	<div class="col-md-12">
		<div id="j-main-container" class="j-main-container">
			<div class="row row-cols-1 row-cols-md-3 g-2">
				<div class="col">
					<div class="card text-center">
						<div class="card-header">
							<span class="fas fa-list-alt fa-4x m-auto"></span>
						</div>
						<div class="card-body">
							<a class="stretched-link" href="index.php?option=com_sermonspeaker&view=sermons">
								<h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_SERMONS_TITLE'); ?></h3>
							</a>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card text-center">
						<div class="card-header">
							<span class="fas fa-object-group fa-4x m-auto"></span>
						</div>
						<div class="card-body">
							<a class="stretched-link" href="index.php?option=com_sermonspeaker&view=series">
								<h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_SERIES_TITLE'); ?></h3>
							</a>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card text-center">
						<div class="card-header">
							<span class="fas fa-comment fa-4x m-auto"></span>
						</div>
						<div class="card-body">
							<a class="stretched-link" href="index.php?option=com_sermonspeaker&view=speakers">
								<h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_SPEAKERS_TITLE'); ?></h3>
							</a>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card text-center">
						<div class="card-header">
							<span class="fa-stack fa-2x m-auto">
								<span class="fas fa-folder fa-stack-2x"></span>
								<span class="fas fa-list-alt fa-stack-1x fa-inverse"></span>
							</span>
						</div>
						<div class="card-body">
							<a class="stretched-link"
							   href="index.php?option=com_categories&extension=com_sermonspeaker.sermons">
								<h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_MENU_CATEGORIES_SERMONS'); ?></h3>
							</a>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card text-center">
						<div class="card-header">
							<span class="fa-stack fa-2x m-auto">
								<span class="fas fa-folder fa-stack-2x"></span>
								<span class="fas fa-object-group fa-stack-1x fa-inverse"></span>
							</span>
						</div>
						<div class="card-body">
							<a class="stretched-link"
							   href="index.php?option=com_categories&extension=com_sermonspeaker.series">
								<h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_MENU_CATEGORIES_SERIES'); ?></h3>
							</a>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card text-center">
						<div class="card-header">
							<span class="fa-stack fa-2x m-auto">
								<span class="fas fa-folder fa-stack-2x"></span>
								<span class="fas fa-comment fa-stack-1x fa-inverse"></span>
							</span>
						</div>
						<div class="card-body">
							<a class="stretched-link"
							   href="index.php?option=com_categories&extension=com_sermonspeaker.speakers">
								<h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_MENU_CATEGORIES_SPEAKERS'); ?></h3>
							</a>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card text-center">
						<div class="card-header">
							<span class="fas fa-wrench fa-4x m-auto"></span>
						</div>
						<div class="card-body">
							<a class="stretched-link" href="index.php?option=com_sermonspeaker&view=tools">
								<h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_MAIN_TOOLS'); ?></h3>
							</a>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card text-center">
						<div class="card-header">
							<span class="fas fa-question-circle fa-4x m-auto"></span>
						</div>
						<div class="card-body">
							<a class="stretched-link" href="index.php?option=com_sermonspeaker&view=help">
								<h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_MENU_HELP'); ?></h3>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
