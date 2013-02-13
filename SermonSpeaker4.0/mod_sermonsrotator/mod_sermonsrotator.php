<?php
defined('_JEXEC') or die;
if ($params->get('lr_show_mouseover')) {
//include only if needed...
	JHtml::_('behavior.tooltip');
}
$db = JFactory::getDBO();
$where = '';
if ($params->get('sermon_cat')){
	$where = ' AND a.catid = '.(int)$params->get('sermon_cat');
}
if ($params->get('speaker_cat')){
	$where .= ' AND b.catid = '.(int)$params->get('speaker_cat');
}
if ($params->get('series_cat')){
	$where .= ' AND c.catid = '.(int)$params->get('series_cat');
}
$query 	= 'SELECT a.sermon_title, a.id, a.sermon_date, b.name, c.series_title, a.picture, b.pic, a.videofile'
		. ", CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(':', a.id, a.alias) ELSE a.id END as slug \n"
		. ' FROM #__sermon_sermons a'
		. ' LEFT JOIN #__sermon_speakers b ON a.speaker_id = b.id'
		. ' LEFT JOIN #__sermon_series c ON a.series_id = c.id'
		. ' WHERE a.state = 1'
		.$where
		. ' ORDER BY sermon_date DESC, (sermon_number+0) DESC'
		. ' LIMIT '.(int)$params->get('lr_count');
$db->setQuery($query);
$rows = $db->loadObjectList();
// get the menu item from the params
$ss_itemid = $params->get('lr_mo_menuitem');
$ss_lineSize = $params->get('lr_show_mo_size');
$ss_boxWidth = $params->get('lr_box_width');
$ss_boxHeight = $params->get('lr_box_height');
$ss_popupWidth = $params->get('lr_popup_width');
$ss_popupHeight = $params->get('lr_height_width');
$ss_thumbWidth = $params->get('lr_thumb_width');
$ss_thumbHeight = $params->get('lr_thumb_height');
$ss_imageWidth = $params->get('lr_image_width');
$ss_showTitleFlg = $params->get('lr_show_mo_title');
$ss_showThumbFlg = $params->get('lr_show_mo_thumb');
$ss_showImageFlg = $params->get('lr_show_mo_image');
$ss_showSpeakerFlg = $params->get('lr_show_mo_speaker');
$ss_showSeriesFlg = $params->get('lr_show_mo_series');
$ss_thumbClickableFlg = $params->get('lr_thumb_mo_click');
$ss_showArrowFlg = $params->get('lr_show_arrow');
$ss_jQueryConflictFlg = 0;
$ss_showLablesFlg = $params->get('lr_show_lables');
$ss_slideDur = $params->get('lr_slideDuration');
$ss_maxSermons = $params->get('lr_count');
$ss_showSermons = $params->get('lr_boxshow_count');
$ss_FadeTrans = $params->get('lr_transTime');
$ss_ImageClickable = $params->get('lr_image_mo_click');
$ss_SermonLandpage = $params->get('lr_sermonlayout');
$ss_styleSheet = $params->get('lr_show_style');
$videoURL = array();
$sermonID = 0;
if($ss_showImageFlg)
{
	$sernonWidth = $ss_boxWidth - $ss_imageWidth;
}
else
{
	$sernonWidth = $ss_boxWidth;
}
if($ss_lineSize < $ss_thumbHeight)
{
	$ss_thumbHeight = $ss_lineSize;
	$ss_thumbWidth = $ss_lineSize;
}
foreach($rows as $row) 
{
	$videoURL[] = '"'.$row->videofile.'"';
}
JHtml::stylesheet('modules/mod_sermonsrotator/mod_sermonsrotator.css'); 
switch ($ss_styleSheet)
{
	case "radvest":
		JHtml::stylesheet('modules/mod_sermonsrotator/css/radvest.css'); 
		break;
	default:
		JHtml::stylesheet('modules/mod_sermonsrotator/css/mcg.css'); 
}
$videoURL = implode(',', $videoURL);
include("modules/mod_sermonsrotator/mod_sermonsrotator-js-min.php");
?>
<div id="sermons-rotator-main" class="sermons-rotator-main" style="width:<?php echo $ss_boxWidth;?>px;height:<?php echo $ss_boxHeight;?>px;">
	<div class="sermons-rotator-left" style="width:<?php echo $sernonWidth;?>px;">
        <div id="sermons-rotator-left-slide" >
        <?php foreach($rows as $row) { ?>
            <div id="sermon-rotator-<?php echo $sermonID;?>" class="sermon-rotator-<?php echo $ss_lineSize;?>" style="width:<?php echo $sernonWidth;?>px;" onClick="rotateImages(<?php echo $sermonID;?>,sermonElementID);">
                <?php
                 if($ss_showThumbFlg)
                 {
                    if($row->picture)
                    {
                        $sermonImage = $row->picture;
                    }
                    else
                    {
                        $sermonImage = $row->pic;
                    }
                    if($ss_showThumbFlg)
                    {?>
                        <div id="sermon-rotator-image-div-<?php echo $sermonID;?>" class="sermon-rotator-image-div" >
                           <?php if($ss_thumbClickableFlg) {?>
                            <a href="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=sermon&id='.$row->slug.'&Itemid='.$ss_itemid); ?>">
                                <img class="sermon-rotator-image" src="<?php echo $sermonImage;?>" width="<?php echo $ss_thumbWidth;?>" height="<?php echo $ss_thumbHeight;?>" border="0"/>
                            </a>
                            <?php }else {?>
                                <img class="sermon-rotator-image" src="<?php echo $sermonImage;?>" width="<?php echo $ss_thumbWidth;?>" height="<?php echo $ss_thumbHeight;?>" border="0"/>
                            <?php }?>
                        </div>
                      <?php  
                    }
                 }
                ?>
                <div id="sermon-rotator-text-<?php echo $sermonID;?>" class="sermon-rotator-text">
                    <?php 
                    if($ss_showTitleFlg)
                    {
                        echo '<div class="sermon-rotator-title" style="width:'.($sernonWidth- $ss_thumbWidth-5).'px;">';
                        echo stripslashes($row->sermon_title);
                        echo "</div>";
                    }
                    if($ss_showSpeakerFlg)
                    {
                        echo '<div class="sermon-rotator-speaker" style="width:'.($sernonWidth- $ss_thumbWidth-5).'px;">';
                        if($ss_showLablesFlg){ echo JText::_('MOD_SERMONSROTATOR_SPEAKER').": "; }
                        echo stripslashes($row->name);
                        echo "</div>";
                    }
                    if($ss_showSeriesFlg)
                    {
                        echo '<div class="sermon-rotator-serie" style="width:'.($sernonWidth- $ss_thumbWidth-5).'px;">';
                        if($ss_showLablesFlg){echo JText::_('MOD_SERMONSROTATOR_SERIE').": ";}
                        echo stripslashes($row->series_title);
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
            <div style="clear:both;"></div>
            <?php $sermonID++;} ?>
        </div> 
    </div>
	<?php 
	$sermonID = 0;
	if($ss_showImageFlg){ ?>
		<?php foreach($rows as $row) { 
                if($row->picture)
                {
                    $sermonImage = $row->picture;
                }
                else
                {
                    $sermonImage = $row->pic;
                }		
		?>
            <div id="sermons-rotator-right-<?php echo $sermonID;?>" class="sermons-rotator-right" style="width:<?php echo $ss_imageWidth;?>px;height:<?php echo $ss_boxHeight;?>px;">
                <?php if($ss_ImageClickable){?>
                	<?php if($ss_showImageFlg == 1){?>
                    	<a href="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=sermon&id='.$row->slug.'&Itemid='.$ss_itemid); ?>">
                    <? }else if($ss_showImageFlg == 2) {?>
	                    <a href="<?php echo JRoute::_('index.php?view=sermon&layout='.substr($ss_SermonLandpage, 2).'&id='.$row->slug.'&tmpl=component&option=com_sermonspeaker'); ?>" onclick="" class="modal" rel="{handler: 'iframe', size: {x: <?php echo $ss_popupWidth;?>, y: <?php echo $ss_popupHeight;?>}}">
					<?php } ?>
                        <img src="<?php echo $sermonImage;?>" width="<?php echo $ss_imageWidth;?>" height="<?php echo $ss_boxHeight;?>" border="0">
                    </a>
                <? }else {?>
                	<img src="<?php echo $sermonImage;?>" width="<?php echo $ss_imageWidth;?>" height="<?php echo $ss_boxHeight;?>" border="0"> 
                <?php } ?>
            </div>
        <?php $sermonID++;} ?>    
    <?php } ?>
  <div style="clear:both;"></div>
  <?php if($ss_showArrowFlg){?>
	<div id="sermons-rotator-arrow" class="sermon-rotator-arrow-<?php echo $ss_lineSize;?>"></div> 
  <?php } ?>
</div>
<script language="javascript">
prepRotator(); 
</script>
