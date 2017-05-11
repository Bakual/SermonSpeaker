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
<button id="applyBtn" type="button" class="hidden" onclick="Joomla.submitbutton('speaker.apply');"></button>
<button id="saveBtn" type="button" class="hidden" onclick="Joomla.submitbutton('speaker.save');"></button>
<button id="closeBtn" type="button" class="hidden" onclick="Joomla.submitbutton('speaker.cancel');"></button>

<div class="container-popup">
	<?php $this->setLayout('edit'); ?>
	<?php echo $this->loadTemplate(); ?>
</div>
