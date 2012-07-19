<?php
defined('_JEXEC') or die;
echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
<root>
	<sermons>
<?php foreach ($this->sermons as $item): ?>
		<item>
			<?php foreach ($item as $key => $value): ?>
				<?php echo '<'.$key.'>'.htmlspecialchars($value).'</'.$key.'>'; ?>
			<?php endforeach; ?>
		</item>
<?php endforeach; ?>
	</sermons>
	<speakers>
<?php foreach ($this->speakers as $item): ?>
		<item>
			<?php foreach ($item as $key => $value): ?>
				<?php echo '<'.$key.'>'.htmlspecialchars($value).'</'.$key.'>'; ?>
			<?php endforeach; ?>
		</item>
<?php endforeach; ?>
	</speakers>
	<series>
<?php foreach ($this->series as $item): ?>
		<item>
			<?php foreach ($item as $key => $value): ?>
				<?php echo '<'.$key.'>'.htmlspecialchars($value).'</'.$key.'>'; ?>
			<?php endforeach; ?>
		</item>
<?php endforeach; ?>
	</series>
</root>