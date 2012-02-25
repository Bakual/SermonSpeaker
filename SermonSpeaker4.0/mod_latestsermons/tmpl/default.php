<?php 
// no direct access
defined('_JEXEC') or die('Restricted access'); 

if ($params->get('show_list')): ?>
	<ul class="latestsermons<?php echo $params->get('moduleclass_sfx'); ?>">
	<?php foreach($list as $row) : ?>
		<li class="<?php echo $moduleclass_sfx; ?>">
		<?php if ($tooltip) :
			$tips = array();
			if ($params->get('ls_show_mo_speaker') && $row->name) :
				$tips[] = JText::_('MOD_LATESTSERMONS_SPEAKER').": ".$row->name;
			endif;
			if ($params->get('ls_show_mo_series') && $row->series_title) :
				$tips[] = JText::_('MOD_LATESTSERMONS_SERIE').": ".trim($row->series_title);
			endif;
			if ($params->get('ls_show_mo_date') && $row->sermon_date) :
				$date_format = JText::_($params->get('ls_mo_date_format', 'DATE_FORMAT_LC4'));
				$tips[] = JText::_('JDATE').": ".JHtml::Date($row->sermon_date, $date_format, true);
			endif;
			$tip = implode('<br />', $tips);
			$title = htmlspecialchars(stripslashes($row->sermon_title), ENT_QUOTES);
			echo JHTML::tooltip($tip, '', '', $title, JRoute::_('index.php?option=com_sermonspeaker&view=sermon&id='.$row->slug.'&Itemid='.$itemid)); ?>
		<?php else : ?>
			<a href="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=sermon&id='.$row->slug.'&Itemid='.$itemid); ?>"><?php echo stripslashes($row->sermon_title); ?></a>
		<?php endif; ?>
		</li>
	<?php endforeach; ?>
	</ul>
<?php endif;
if ($params->get('show_player')) :
	require_once(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'helpers'.DS.'sermonspeaker.php');
	require_once(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'helpers'.DS.'player.php');
	jimport('joomla.application.component.helper');
	$c_params = JComponentHelper::getParams('com_sermonspeaker');
	$config['autostart']	= 0;
	$config['count']		= 'ls';
	$config['type']			= $c_params->get('fileprio') ? 'video' : 'audio';
	$config['alt_player']	= $c_params->get('alt_player');
	$config['vheight']		= $params->get('vheight');
	$player = new SermonspeakerHelperPlayer($list, $config);
	echo $player->mspace;
	echo $player->script;
endif; ?>
<?php if ($params->get('ls_show_mo_link')) : ?>
	<br />
	<a href="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=sermons&Itemid='.$itemid); ?>"><?php echo JText::_('MOD_LATESTSERMONS_LINK'); ?></a>
<?php endif;