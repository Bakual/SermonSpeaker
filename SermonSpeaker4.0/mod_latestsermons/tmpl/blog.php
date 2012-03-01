<?php 
// no direct access
defined('_JEXEC') or die('Restricted access'); 

if ($params->get('show_list')): ?>
	<div class="latestsermons<?php echo $params->get('moduleclass_sfx'); ?>">
	<?php foreach($list as $row) : ?>
		<div class="<?php echo $moduleclass_sfx; ?>">
			<h3><a href="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=sermon&id='.$row->slug.'&Itemid='.$itemid); ?>">
					<?php echo stripslashes($row->sermon_title); ?>
			</a></h3>
			<dl class="article-info sermon-info" style="display:block; margin:0;">
				<dt class="article-info-term"><?php echo JText::_('JDETAILS'); ?></dt>
				<?php if ($params->get('ls_show_mo_series') && $row->series_title) : ?>
					<dd class="category-name">
						<?php echo JText::_('MOD_LATESTSERMONS_SERIE'); ?>: 
						<?php if ($row->series_state): ?>
							<a href="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=serie&id='.$row->series_slug.'&Itemid='.$itemid); ?>">
								<?php echo trim($row->series_title); ?>
							</a>
						<?php else:
							echo trim($row->series_title); 
						endif; ?>
					</dd>
				<?php endif;
				if ($params->get('ls_show_mo_date') && $row->sermon_date) : ?>
					<dd class="published">
						<?php $date_format = JText::_($params->get('ls_mo_date_format', 'DATE_FORMAT_LC4'));
						echo JText::_('JDATE').": ".JHtml::Date($row->sermon_date, $date_format, true); ?>
					</dd>
				<?php endif;
				if ($params->get('ls_show_mo_speaker') && $row->name) : ?>
					<dd class="createdby">
						<?php echo JText::_('MOD_LATESTSERMONS_SPEAKER'); ?>: 
						<?php if ($row->speaker_state): ?>
							<a href="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=speaker&id='.$row->speaker_slug.'&Itemid='.$itemid); ?>">
								<?php echo trim($row->name); ?>
							</a>
						<?php else:
							echo trim($row->name); 
						endif; ?>
					</dd>
				<?php endif; ?>
			</dl>
			<div style="clear:left;"></div>
			<?php if (strlen($row->notes) > 0) : ?>
				<div>
					<?php echo JHTML::_('content.prepare', $row->notes); ?>
				</div>
			<?php endif; ?>
			<hr />
		</div>
	<?php endforeach; ?>
	</div>
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