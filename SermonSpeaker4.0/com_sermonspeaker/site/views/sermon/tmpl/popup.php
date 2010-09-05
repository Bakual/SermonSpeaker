<?php
defined('_JEXEC') or die('Restricted access');
if ($this->params->get('popup_color')) :
	$bgcolor = 'background-color:#'.$this->params->get('popup_color').';';
endif;
?>
<body style="<?php echo $bgcolor; ?>">
<div style="padding: 10px; text-align:center;">
	<h3 class="contentheading"><?php echo $this->item->sermon_title; ?></h3>
	<?php SermonspeakerHelperSermonspeaker::insertPlayer($this->lnk, $this->item->sermon_time, 1, $this->item->sermon_title, $this->speaker->name); ?>
</div>