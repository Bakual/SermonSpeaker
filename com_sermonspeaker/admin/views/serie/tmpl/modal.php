<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

?>
<button id="applyBtn" type="button" class="hidden" onclick="Joomla.submitbutton('serie.apply');"></button>
<button id="saveBtn" type="button" class="hidden" onclick="Joomla.submitbutton('serie.save');"></button>
<button id="closeBtn" type="button" class="hidden" onclick="Joomla.submitbutton('serie.cancel');"></button>

<div class="container-popup">
	<?php $this->setLayout('edit'); ?>
	<?php echo $this->loadTemplate(); ?>
</div>
