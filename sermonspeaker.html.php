<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

//Get the right language if it exists
if (file_exists(JPATH_COMPONENT.DS.'client.language/'.$this->language.'.php')) {
	include(JPATH_COMPONENT.DS.'client.language'.DS.$this->language.'.php');
} else {
	include(JPATH_COMPONENT.DS.'client.language'.DS.'english.php');
}

class HTML_speaker
{
######################################################
### Full Overviews                                 ###
######################################################

	function speakermain( $option, $rows, $total_rows, $total_pages, $curr_page, $config, $task  ) {
	  HTML_speaker::head( $task );
	  global $Itemid, $database;
	  $lang = new sermonLang;
	  $config = new sermonConfig;
	  echo $lang->totalResults.' '.$total_rows.'<br />';
	  paginate($curr_page, $total_rows, $lang->page, $lang->of, $option, $task, $id, $Itemid, true);
	  /*
	  echo $lang->page.' '.$curr_page.' '.$lang->of.' '.$total_pages.'<br/>';
    $prev_page = $curr_page-1;
	  $next_page = $curr_page+1;
	  if( $curr_page > "1" && $curr_page < $total_pages ) {
		  $link = JRoute::_("index.php?option=$option&curr_page=$prev_page&total_pages=$total_pages&Itemid=$Itemid" );
      echo "<br><a href=".$link.">&lt;&lt;&nbsp;".$lang->previous."</a>&nbsp;&nbsp;&nbsp";
      $link = JRoute::_("index.php?option=$option&curr_page=$next_page&total_pages=$total_pages&Itemid=$Itemid" );
		  echo "<a href=".$link.">".$lang->next."&nbsp;&gt;&gt;</a>";
	  } else if( $curr_page > "1" && $curr_page = $total_pages ) {
		  $link = JRoute::_("index.php?option=$option&curr_page=$prev_page&total_pages=$total_pages&Itemid=$Itemid" );
      echo "<br><a href=".$link.">&lt;&lt;&nbsp;".$lang->previous."</a>";
	  } else if( $curr_page = "1" && $curr_page < $total_pages ) {
		  $link = JRoute::_("index.php?option=$option&curr_page=$next_page&total_pages=$total_pages&Itemid=$Itemid" );
      echo "<br><a href=".$link.">".$lang->next."&nbsp;&gt;&gt;</a>";
	  } else {
		  echo '';
	  }
	  */
	  foreach($rows as $row) { ?>
	    <table cellpadding="2" cellspacing="0" width="100%">
	      <hr style="width: 100%; height: 2px;">
	      <?php echo "<h3>".$row->name."</h3>"; ?>
	      <tr>
  		    <td valign="middle" align="center" width="30%">
      		  <?php if($row->pic) { ?>
      			<a href="<?php echo JRoute::_("index.php?option=$option&task=singlespeaker&id=$row->id&Itemid=$Itemid"); ?>">
      			<img src="<?php echo $row->pic; ?>" border="0" TITLE="<?php echo $row->name; ?>"> </a>
    		    <?php } ?>
  		    </td>
  		    <td colspan="2" valign="top" align="left">
    		    <?php if ($row->website && $row->website != "http://") {
        			echo '<br />'.
        			"<A HREF=\"".$row->website."\" title=\"".$lang->web_link_description."\">".$lang->web_link_tag.$row->name."</A>";
      		  }
      		  if($config->speaker_intro && $row->intro) {
              echo "<br />".$row->intro."<br />";
            }
      		  if ($row->bio) { 
              echo "<br />".$lang->bio.": ".$row->bio."<br />";
           }  ?>
  		    </td>
	      </tr>
	      <tr>
		      <th colspan="2" align="left"><a  TITLE="<?php echo $lang->series_hoover_tag; ?>" href="<?php echo JRoute::_("index.php?option=$option&task=singlespeaker&id=$row->id&Itemid=$Itemid" ); ?>"><?php echo $lang->sermon_series; ?></a></th>
	      </tr>
	      <tr>
		      <th colspan="2" align="left"><a TITLE="<?php echo $lang->sermon_hoover_tag; ?>" href="<?php echo JRoute::_("index.php?option=$option&task=latest_sermons&id=$row->id&Itemid=$Itemid" ); ?>"><?php echo $lang->sermons; ?></a></th>
	      </tr>
	   </table>
	   <p></p>
	<?php }
	paginate($curr_page, $total_rows, $lang->page, $lang->of, $option, $task, $id, $Itemid, false);
	if ($config->search){
	 HTML_speaker::insert_search_box();
	}
  } // end of speakermain
   
  function seriesmain( $option, $rows, $total_rows, $total_pages, $curr_page, $config, $task, $av) {
    global $Itemid;
    HTML_speaker::head( $task );
  	$lang = new sermonLang;
  	$config = new sermonConfig;
  	echo $lang->totalResults.' '.$total_rows.'<br />';
  	paginate($curr_page, $total_rows, $lang->page, $lang->of, $option, $task, $id, $Itemid, true);
  	/*
  	echo $lang->page.' '.$curr_page.' '.$lang->of.' '.$total_pages.'<br />';
  	$prev_page = $curr_page-1;
  	$next_page = $curr_page+1;
  	if( $curr_page > "1" && $curr_page < $total_pages ) {
  		$link = JRoute::_("index.php?option=$option&task=seriess&curr_page=$prev_page&total_pages=$total_pages&Itemid=$Itemid" );
      echo "<a href=".$link.">".$lang->previous."</a>&nbsp;&nbsp;&nbsp";
      $link = JRoute::_("index.php?option=$option&task=seriess&curr_page=$next_page&total_pages=$total_pages&Itemid=$Itemid" );
      echo "<a href=".$link.">".$lang->next."&gt;&gt;</a>";
  	} else if( $curr_page > "1" && $curr_page = $total_pages ) {
  		$link = JRoute::_("index.php?option=$option&task=seriess&curr_page=$prev_page&;total_pages=$total_pages&Itemid=$Itemid" );
      echo "<a href=".$link.">".$lang->previous."</a>";
  	} else if( $curr_page = "1" && $curr_page < $total_pages ) {
  		$link = JRoute::_("index.php?option=$option&task=seriess&curr_page=$next_page&total_pages=$total_pages&Itemid=$Itemid" );
      echo "<a href=".$link.">".$lang->next."&gt;&gt;</a>";
  	} else {
  		echo '';
  	}
  	*/
  	echo "<hr style=\"width: 100%; height: 2px;\">";
    ?>
    <table cellpadding="2" cellspacing="2" width="100%">
    <tr>
      <?php if($av) { echo "<th></th>\n"; } ?>
      <th align="left"><?php echo $lang->sermonName;?></th>
      <th align="left"><?php echo $lang->speaker;?></th>
    </tr>
    <?php
    $i = 0;
  	foreach($rows as $row) {
  	  if ($i == 0) {
        echo "<tr bgcolor =\"".$config->color1."\">\n"; 
        $i = 1;
      } else {
        echo "<tr bgcolor =\"".$config->color2."\">\n";
        $i = 0;
      } 
      if ($av) {
        if ($row->avatar_id != 1) { 
          echo "<td><img src='".HTML_speaker::makelink($row->avatar_location)."' ></td>";
        } else { echo "<td></td>"; 
        } 
      }
      ?> 
  		<td align="left"><a href="<?php echo JRoute::_( "index.php?option=$option&task=singleseries&id=$row->id&Itemid=$Itemid" ); ?>"><?php echo $row->series_title; ?></a></td>
  		<td align="left"><?php echo $row->name; ?></td>
  	<p></p>
  	<?php }
  	echo "</table>\n";
  	paginate($curr_page, $total_rows, $lang->page, $lang->of, $option, $task, $id, $Itemid, false);
  } // end of seriesmain

  function sermonmain( $option, $rows, $total_rows, $total_pages, $curr_page, $config, $task  ) {
    global $Itemid;
    HTML_speaker::head( $task );
  	$lang = new sermonLang;
  	$config = new sermonConfig;
  	echo $lang->totalResults.' '.$total_rows.'<br />';
  	paginate($curr_page, $total_rows, $lang->page, $lang->of, $option, $task, $id, $Itemid, true);
  	/*
  	echo $lang->page.' '.$curr_page.' '.$lang->of.' '.$total_pages.'<br />';
  	$prev_page = $curr_page-1;
  	$next_page = $curr_page+1;
  	if( $curr_page > "1" && $curr_page < $total_pages ) {
  		$link = JRoute::_("index.php?option=$option&task=sermons&curr_page=$prev_page&total_pages=$total_pages&Itemid=$Itemid" );
      echo "<a href=".$link.$lang->previous."</a>&nbsp;&nbsp;&nbsp;";
      $link = JRoute::_("index.php?option=$option&task=sermons&curr_page=$next_page&total_pages=$total_pages&Itemid=$Itemid" );
      echo "<a href=".$link.$lang->next."&gt;&gt;</a>";
  	} else if( $curr_page > "1" && $curr_page = $total_pages ) {
  		$link = JRoute::_("index.php?option=$option&task=sermons&curr_page=$prev_page&total_pages=$total_pages&Itemid=$Itemid" );
      echo "<a href=".$link.$lang->previous."</a>";
  	} else if( $curr_page = "1" && $curr_page < $total_pages ) {
  		$link = JRoute::_("index.php?option=$option&task=sermons&curr_page=$next_page&total_pages=$total_pages&Itemid=$Itemid" );
      echo "<a href=".$link.$lang->next."&gt;&gt;</a>";
  	} else {
  		echo '';
  	}
  	*/
  	foreach($rows as $row) {
  		$query = "SELECT name FROM #__sermon_speakers WHERE id='$row->speaker_id'";
  		$database->setQuery( $query );
  		$rows2 = $database->loadObjectList();
  		$row2 = $rows2['0'];
  		$query = "SELECT series_name FROM #__sermon_seriess WHERE id='$row->series_id'";
  		$database->setQuery( $query );
  		$series_name = $database->loadResult(); ?>
  		<table cellpadding="2" cellspacing="0" width="100%">
  		   <tr>
  			<td valign="top" align="left">
  			   <a href="<?php echo JRoute::_("index.php?option=$option&task=singlespeaker&id=$row->speaker_id&Itemid=$Itemid" ); ?>"><?php echo $row2->name; ?></a> -
  			   <a href="<?php echo JRoute::_("index.php?option=$option&task=singleseries&id=$row->series_id&Itemid=$Itemid" ); ?>"><?php echo $series_name; ?></a> -
  			   <?php echo $row->sermon_name; ?>
  			</td>
  			<td valign="top" align="left" width="25">
  			   <?php if($row->play) { ?> <a href="<?php echo JRoute::_("index.php?option=$option&task=playsermon&id=$row->id&Itemid=$Itemid"); ?>"<?php if($config->speaker_popup) { ?> target="_blank"<?php } ?>><?php echo $lang->play; ?></a><?php } ?>
  			</td>
  			<td valign="top" align="left" width="80">
  			   <?php if($row->play) { ?> <a href="<?php echo JRoute::_("index.php?option=$option&task=playlistAdd&id=$row->id&speaker_id=$row->speaker_id&series_id=$row->series_id&user_id=$my->id&Itemid=$Itemid"); ?>"><?php echo $lang->playlistAdd; ?></a><?php } ?>
  			</td>
  		   </tr>
  		</table>
  		<p></p>
  	<?php }
  	paginate($curr_page, $total_rows, $lang->page, $lang->of, $option, $task, $id, $Itemid, true);
  } // end of sermonmain

######################################################
### SermonList                                     ###
######################################################

  function sermonlist( $option, $id, $task, $curr_page, $total_pages, $total_rows, $rows, $sort ) {
    global $Itemid;
  	$lang = new sermonLang;
  	$config = new sermonConfig;
  	HTML_speaker::head( $task );
  	
  	echo "<b>".$lang->sersortby."</b>";
    if($sort == "sermondate")
      echo $lang->serdate.' | <a title="'.$lang->sortpub.'" href="'.JRoute::_( "index.php?option=$option&task=$task&id=$id&sort=mostrecentlypublished&curr_page=1&Itemid=$Itemid" ).'">'.$lang->serpub.'</a> | <a title="'.$lang->sortview.'" href="'.JRoute::_( "index.php?option=$option&task=$task&id=$id&sort=mostviewed&curr_page=1&Itemid=$Itemid" ).'">'.$lang->serview.'</a> | <a title="'.$lang->sortalph.'" href="'.JRoute::_( "index.php?option=$option&task=$task&id=$id&sort=alphabetically&curr_page=1&Itemid=$Itemid" ).'">'.$lang->seralph.'</a>';
    else if($sort == "mostrecentlypublished")
      echo '<a title="'.$lang->sortdate.'" href="'.JRoute::_( "index.php?option=$option&task=$task&id=$id&sort=sermondate&curr_page=1&Itemid=$Itemid" ).'">'.$lang->serdate.'</a> | '.$lang->serpub.' | <a title="'.$lang->sortview.'" href="'.JRoute::_( "index.php?option=$option&task=$task&id=$id&sort=mostviewed&curr_page=1&Itemid=$Itemid" ).'">'.$lang->serview.'</a> | <a title="'.$lang->sortalph.'" href="'.JRoute::_( "index.php?option=$option&task=$task&id=$id&sort=alphabetically&curr_page=1&Itemid=$Itemid" ).'">'.$lang->seralph.'</a>';
    else if($sort == "mostviewed")
      echo '<a title="'.$lang->sortdate.'" href="'.JRoute::_( "index.php?option=$option&task=$task&id=$id&sort=sermondate&curr_page=1&Itemid=$Itemid" ).'">'.$lang->serdate.'</a> | <a title="'.$lang->sortpub.'" href="'.JRoute::_( "index.php?option=$option&task=$task&id=$id&sort=mostrecentlypublished&curr_page=1&Itemid=$Itemid" ).'">'.$lang->serpub.'</a> | '.$lang->serview.' | <a title="'.$lang->sortalph.'" href="'.JRoute::_( "index.php?option=$option&task=$task&id=$id&sort=alphabetically&curr_page=1&Itemid=$Itemid" ).'">'.$lang->seralph.'</a>';
    else if($sort == "alphabetically")
      echo '<a title="'.$lang->sortdate.'" href="'.JRoute::_( "index.php?option=$option&task=$task&id=$id&sort=sermondate&curr_page=1&Itemid=$Itemid" ).'">'.$lang->serdate.'</a> | <a title="'.$lang->sortpub.'" href="'.JRoute::_( "index.php?option=$option&task=$task&id=$id&sort=mostrecentlypublished&curr_page=1&Itemid=$Itemid" ).'">'.$lang->serpub.'</a> | <a title="'.$lang->sortview.'" href="'.JRoute::_( "index.php?option=$option&task=$task&id=$id&sort=mostviewed&curr_page=1&Itemid=$Itemid" ).'">'.$lang->serview.'</a> | '.$lang->seralph.'';
    else
      echo $lang->serdate.' | <a title="'.$lang->sortpub.'" href="'.JRoute::_( "index.php?option=$option&task=$task&id=$id&sort=mostrecentlypublished&curr_page=1&Itemid=$Itemid" ).'">'.$lang->serpub.'</a> | <a title="'.$lang->sortview.'" href="'.JRoute::_( "index.php?option=$option&task=$task&id=$id&sort=mostviewed&curr_page=1&Itemid=$Itemid" ).'">'.$lang->serview.'</a> | <a title="'.$lang->sortalph.'" href="'.JRoute::_( "index.php?option=$option&task=$task&id=$id&sort=alphabetically&curr_page=1&Itemid=$Itemid" ).'">'.$lang->seralph.'</a>';
  	
  	echo '<br>'.$lang->totalResults.' '.$total_rows.'<br>';
  	paginate_sort($curr_page, $total_rows, $lang->page, $lang->of, $option, $task, $id, $Itemid, $sort, true);
  	
    echo "<hr style=\"width: 100%; height: 2px;\">";
    echo "<table cellpadding=\"2\" cellspacing=\"2\" width=\"100%\">\n";
    ?>
    <tr>
      <th align="left"><?php echo $lang->sermonName;?></th>
      <?php if ($config->client_col_sermon_scripture_reference) { echo "<th align=\"left\">".$lang->scripture."</th>\n"; } ?>
      <th align="left"><?php echo $lang->speaker;?></th>
      <?php if ($config->client_col_sermon_date) { echo "<th align=\"left\">".$lang->sermon_date."</th>\n"; } ?>
      <?php if ($config->client_col_sermon_time) { echo "<th align=\"left\">".$lang->sermonTime."</th>\n"; }?>
    </tr>
    <?php
    $i = 0;
  	foreach($rows as $row) {
  	  if ($i == 0) {
        echo "<tr bgcolor =\"".$config->color1."\">\n"; 
        $i = 1;
      } else {
        echo "<tr bgcolor =\"".$config->color2."\">\n";
        $i = 0;
      } ?> 
    	  <td>
    	  <?php
        /*
    	  if (substr($row3->sermon_path,0,7) == "http://") { 
          $lnk = $row3->sermon_path;
        } else {
          $lnk = $mosConfig_live_site . $row3->sermon_path;
        }
        */
        if(strcasecmp(substr($lnk,-4),".wmv") == 0) { 
          echo "&nbsp;&nbsp;<img title=\"".$lang->playtoplay."\" src=\"".JURI::root()."components/com_sermonspeaker/images/play.gif\" width=\"16\" height=\"16\" border=\"0\"> &#40;<a TITLE=\""; 
          if ($task == "singleseries"){
             $tag = $lang->single_sermon_hoover_tag;
          } else {
             $tag = $lang->download_hoover_tag;
          }
          echo $tag; ?>" href="<?php echo JRoute::_( "index.php?option=$option&task=singlesermon&id=$row->id&Itemid=$Itemid" ); ?>">High</a> | <a TITLE="<?php 
          if ($task == "singleseries"){
             $tag = $lang->single_sermon_hoover_tag;
          } else {
             $tag = $lang->download_hoover_tag;
          }
          echo $tag; ?>" href="<?php echo JRoute::_( "index.php?option=$option&task=singlesermonlow&id=$row->id&Itemid=$Itemid" ); ?>">Low</a>&#41;
         <?php
        } // end of if
         else { 
           echo "&nbsp;&nbsp;<img title=\"".$lang->playtoplay."\" src=\"".JURI::root()."components/com_sermonspeaker/images/play.gif\" width=\"16\" height=\"16\" border=\"0\"> &#40;<a TITLE=\"";
          //echo "<a TITLE=\""; 
          if ($task == "singleseries"){
             $tag = $lang->single_sermon_hoover_tag;
          } else {
             $tag = $lang->download_hoover_tag;
          }
          echo $tag; ?>" href="<?php echo JRoute::_( "index.php?option=$option&task=singlesermon&id=$row->id&Itemid=$Itemid" ); ?>" style="text-decoration:none"><?php echo $lang->play; ?></a>&#41;
          <?php
        }
        echo $row->sermon_title; ?>
        </td>
    	  <?php //echo $row3->sermon_title; ?>
        <?php if ($config->client_col_sermon_scripture_reference) { echo "<td>".$row->sermon_scripture."</td>\n"; } ?>
    	  <td><?php echo $row->name;?></td>
    	  <?php
    	  if ($config->client_col_sermon_date) {
      	  if($config->date_format == "1") {
      		  echo "<td align=\"left\" valign=\"middle\">" . date('d.m.Y', strtotime($row->sermon_date)) . "</td>\n";
          } else {
            echo "<td align=\"left\" valign=\"middle\">" . date('m-d-Y', strtotime($row->sermon_date)) . "</td>\n";
          }
        }
    	  ?>
    		<?php if ($config->client_col_sermon_time) { echo "<td>".$row->sermon_time."</td>\n"; } ?>
  	 </tr>
  	<p></p>
  	<?php }
  	echo "</table>\n";
  } // end of sermonlist

######################################################
### SeriesSermons                                  ###
######################################################

  function seriessermons( $option, $rows, $total_rows, $total_pages, $curr_page, $config, $task ) {
    global $Itemid;
  	$lang = new sermonLang;
  	$config = new sermonConfig;
  	HTML_speaker::head( $task );
  	echo $lang->totalResults.' '.$total_rows.'<br />';
  	paginate($curr_page, $total_rows, $lang->page, $lang->of, $option, $task, $id, $Itemid, true);
  	/*
  	echo $lang->page.' '.$curr_page.' '.$lang->of.' '.$total_pages.'<br />';
  	$prev_page = $curr_page-1;
  	$next_page = $curr_page+1;
  	if( $curr_page > "1" && $curr_page < $total_pages ) {
  		$link = JRoute::_("index.php?option=$option&task=sermons&curr_page=$prev_page&total_pages=$total_pages&Itemid=$Itemid" );
      echo "<a href=".$link.$lang->previous."</a>&nbsp;&nbsp;&nbsp;";
      $link = JRoute::_("index.php?option=$option&task=sermons&curr_page=$next_page&total_pages=$total_pages&Itemid=$Itemid" );
      echo "<a href=".$link.$lang->next."&gt;&gt;</a>";
  	} else if( $curr_page > "1" && $curr_page = $total_pages ) {
  		$link = JRoute::_("index.php?option=$option&task=sermons&curr_page=$prev_page&total_pages=$total_pages&Itemid=$Itemid" );
      echo "<a href=".$link.$lang->previous."</a>";
  	} else if( $curr_page = "1" && $curr_page < $total_pages ) {
  		$link = JRoute::_("index.php?option=$option&task=sermons&curr_page=$next_page&total_pages=$total_pages&Itemid=$Itemid" );
      echo "<a href=".$link.$lang->next."&gt;&gt;</a>";
  	} else {
  		echo '';
  	}
    echo "<hr style=\"width: 100%; height: 2px;\">";
    */
    echo "<table style=\"width: 100%\" cellpadding=\"10\" cellspacing=\"0\" width=\"100%\">\n";
    $i = 0;
  	foreach($rows as $row) {
  	  $database =& JFactory::getDBO();
  	  $query = 'SELECT sermon_path, sermon_title, sermon_number, notes, sermon_date'
             . ' FROM #__sermon_sermons WHERE series_id='.$row->id.' ORDER by ordering, sermon_number desc, sermon_date desc';
  	  $database->setQuery( $query );
  	  $datas = $database->loadObjectList();
  	  
      ?>
      <tr>
        <td style="width: 10%" align="left" valign="top">
        <?php 
        if ($row->avatar_location) { echo "<img src='".HTML_speaker::makelink($row->avatar_location)."' >"; }
        ?>
        </td>
        <td style="width: 90%" align="left" valign="top">
        <h3><?php echo $row->series_title; ?></h3>
        <p<?php echo $row->series_description; ?></p>
        
        <?php
        foreach($datas as $data) {
          echo "<p>\n";
          echo "<b>".$data->sermon_title."</b> (";
          if($config->date_format == "1") {
    		    echo date('d.m.Y', strtotime($data->sermon_date));
          } else {
            echo date('m-d-Y', strtotime($data->sermon_date));
          }
          echo ")<br>\n";
          echo $data->notes."<br>\n";
          
          $itemtype = substr($data->sermon_path,strrpos($data->sermon_path,'.'));
      		if ($itemtype == ".mp3") { 
            echo "<embed src=\"".JURI::root()."components/com_sermonspeaker/media/player/player.swf\" width=\"200\" height=\"20\" allowfullscreen=\"true\" 
            allowscriptaccess=\"always\" flashvars=\"&file=".HTML_speaker::makelink($data->sermon_path)."&height=20&width=200\" >";
          }
      		if ($itemtype == ".flv") { 
            echo "<embed src=\"".JURI::root()."components/com_sermonspeaker/media/player/mediaplayer.swf\" width=\"$config->mp_width\" height=\"$config->mp_height\" allowfullscreen=\"true\" 
            allowscriptaccess=\"always\" flashvars=\"&file=".HTML_speaker::makelink($data->sermon_path)."&height=$config->mp_height&width=$config->mp_width\" >";
          }
          echo "</p>\n";
        }
        ?>
        </td>
      </tr>
      <tr>
  			<td colspan="2"> <hr size="2" width="100%" /> </td>
  		</tr>
    	<p></p>
  	<?php }
  	echo "</table>\n";
  	paginate($curr_page, $total_rows, $lang->page, $lang->of, $option, $task, $id, $Itemid, false);
  } // end of seriessermons

######################################################
### Single Items                                   ###
######################################################
  function singlespeaker( $option, $row, $config, $task  ) {
  	HTML_speaker::head( $task );
  	global $database, $Itemid;
  	$lang = new sermonLang; ?>
  	<table cellpadding="2" cellspacing="0" width="100%">
  		<tr>
  		   <th colspan="2" align="left">
  			<?php echo $row->name; ?>
  		   </th>
  		</tr>
  		<tr>
  		   <?php if ($row->pic) { ?>
  			<td width="<?php echo $config->singlewidth; ?>">
  				<img src="<?php echo $row->pic; ?>" border="0" >
  			</td>
  		   <?php } ?>
  		   <td align="left" valign="top">
  			<p>
  			<?php
  			if ($row->website && $row->website != "http://") { ?> <br /><a href="<?php echo $row->website; ?>" target="_blank"> <?php echo $lang->web_link_tag.$row->name; ?></a><?php }
  			if ($row->bio) { ?> <br /> <?php echo $lang->bio; ?>: <?php if($config->speaker_intro) { echo $row->intro.'<br />'; } echo $row->bio; } ?>
  			</p>
  		   </td>
  		</tr>
  	</table>
  	<p></p>
  	<?php
      $query = "SELECT j . id , speaker_id , series_title , series_description , "
          . " published , ordering , hits , created_by , created_on , avatar_id , "
          . " avatar_location "
          . " FROM #__sermon_series j , #__sermon_avatars k "
          . " where j.avatar_id = k.id and speaker_id='".$row[0]->id."' "
          . " AND published = '1' order by series_title";
          
  		$database =& JFactory::getDBO();
      $database->setQuery( $query );
  		$rows2 = $database->loadObjectList();
  		if( $rows2 ) {
  	?>
  	<table border="0" cellpadding="2" cellspacing="1" width="100%">
  		<tr>
  		  <th align="left" >
  		  </th>
  		  <th align="left" >
  			<?php echo $lang->seriesTitle; ?>
  		  </th>		  
  		  <th align="left" valign="bottom">
  			<?php echo $lang->seriesDescription; ?>
  		  </th>
  		</tr>
  		<?php
  		$i = 0;
  		foreach($rows2 as $row2) { 
        if ($i == 0) {
          echo "<tr bgcolor =\"".$config->color1."\">\n"; 
          $i = 1;
        } else {
          echo "<tr bgcolor =\"".$config->color2."\">\n";
          $i = 0;
        } ?>
  			<td align="left" valign="top"  width="80">
  			  <?php if ($row2->avatar_id != 1) { echo "<img src='".HTML_speaker::makelink($row2->avatar_location)."' >";} ?>
  			</td>		  
  			<td align="left" valign="middle" width="125">
  			  <a TITLE="<?php echo $lang->series_select_hoover_tag; ?> " href="<?php echo JRoute::_("index.php?option=$option&task=singleseries&id=$row2->id&Itemid=$Itemid"); ?>">
  				<?php echo $row2->series_title; ?>
  			  </a>
  			</td>
  			<td align="left" valign="middle" >
  			  <?php echo $row2->series_description; ?>
  			</td>
  		  </tr>
  		<?php } ?>
  	</table>
  	<?php } ?>
    <?php 
  } // end of singlespeaker 
  
  function singlesermon( $option, $row, $config, $task  ) {
    HTML_speaker::head( $task  );
  	$lang = new sermonLang;
  	$config = new sermonConfig;
    
    //Check if link targets to an external source
    if (substr($row->sermon_path,0,7) == "http://") {
      $lnk = $row[0]->sermon_path; 
    } else {  
      $lnk = HTML_speaker::makelink($row[0]->sermon_path); 
    }
  
  	echo "<p></p>";
  	echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">";
  	  echo "<tr>";
  		  echo "<th align=\"left\" >$lang->sermonName</th>";
  		  
  		  if ($config->client_col_sermon_scripture_reference){
  		  	echo "<th align=\"left\" >$lang->scripture</th>";
  		  	}
  		  if ($config->client_col_sermon_notes){
  		  	echo "<th align=\"left\" valign=\"bottom\">$lang->sermonNotes</th>";
  		  	}
  		  if ($config->client_col_player){
  		  	echo "<th align=\"left\" valign=\"bottom\">$lang->play</th>";
  		  	}
  		echo "</tr>";
  		echo "<tr>";
  			echo "<td align=\"left\" valign=\"top\" >";
  			echo "<a TITLE=\"".$lang->download_hoover_tag."\" href=\"".HTML_speaker::makelink($row[0]->sermon_path)."\">".$row[0]->sermon_title."</a>";
  			echo "</td>";
  		  if ($config->client_col_sermon_scripture_reference){
  			  echo "<td align=\"left\" valign=\"top\">".$row[0]->sermon_scripture."</td>";
  			}
  		  if ($config->client_col_sermon_notes){
          echo "<td align=\"left\" valign=\"top\">".$row[0]->notes."</td>";
  			}
  			/*
  			if ($config->client_col_player){
  			  echo "<td align=\"left\" valign=\"top\" >";
  			  $itemtype = substr($row[0]->sermon_path,strrpos($row[0]->sermon_path,'.'));
      		if ($itemtype == ".mp3") { 
            echo "<embed src=\"".JURI::root()."components/com_sermonspeaker/media/player/player.swf\" width=\"200\" height=\"20\" allowfullscreen=\"true\" 
            allowscriptaccess=\"always\" flashvars=\"&file=".HTML_speaker::makelink($row[0]->sermon_path)."&height=20&width=200\" >";
          }
      		if ($itemtype == ".flv") { 
            echo "<embed src=\"".JURI::root()."components/com_sermonspeaker/media/player/mediaplayer.swf\" width=\"$config->mp_width\" height=\"$config->mp_height\" allowfullscreen=\"true\" 
            allowscriptaccess=\"always\" flashvars=\"&file=".HTML_speaker::makelink($row[0]->sermon_path)."&height=$config->mp_height&width=$config->mp_width\" >";
          }
  			}
  			*/
  			if ($config->client_col_player){
          echo "<td valign=\"top\" ><br><center>";
          if(strcasecmp(substr($lnk,-4),".mp3") == 0) {
            echo "<embed src=\"".JURI::root()."components/com_sermonspeaker/media/player/player.swf\" width=\"200\" height=\"20\" allowfullscreen=\"true\" allowscriptaccess=\"always\" flashvars=\"&file=".$lnk."&autostart=true&height=20&width=200\" />";
          }
          if(strcasecmp(substr($lnk,-4),".flv") == 0) {
            echo "<embed src=\"".JURI::root()."components/com_sermonspeaker/media/player/player.swf\" width=\"200\" height=\"323\" allowfullscreen=\"true\" allowscriptaccess=\"always\" flashvars=\"&file=".$lnk."&autostart=true&height=323&width=200\" />";
          }
  
          if(strcasecmp(substr($lnk,-4),".wmv") == 0) {
            echo "<object id=mediaplayer width=400 height=323 classid=clsid:22d6f312-b0f6-11d0-94ab-0080c74c7e95 22d6f312-b0f6-11d0-94ab-0080c74c7e95 type=application/x-oleobject>
                    <param name=filename value=$lnk>
                    <param name=autostart value=true>
                    <param name=transparentatstart value=true>
                    <param name=showcontrols value=1>
                    <param name=showdisplay value=0>
                    <param name=showstatusbar value=1>
                    <param name=autosize value=1>
                    <param name=animationatstart value=false>
                <embed name=\"MediaPlayer\" src=$lnk width=400 height=323 type=application/x-mplayer2 autostart=1 showcontrols=1 showstatusbar=1 transparentatstart=1 animationatstart=0 loop=false pluginspage=http://www.microsoft.com/windows/windowsmedia/download/default.asp></embed>
                </object>";
          }
          echo "</center><br></td>";
       }
  		echo "</tr>";
  	echo "</table>";;
  } // end of singleSermon
  
  function showseries( $option, $id, $task  ) {
    global $Itemid;
    $lang = new sermonLang;
    $database =& JFactory::getDBO();
    $query = "SELECT * FROM #__sermon_series WHERE id='$id'";
    $database->setQuery( $query );
    $row = $database->loadObjectList();
  
    HTML_speaker::head( $task,$row[0]->series_title );
    ?>
	  <table cellpadding="2" cellspacing="0" width="100%">
		<tr>
		  <?php	
      $query = "SELECT distinct  avatar_location "
      . " FROM   #__sermon_avatars k, #__sermon_series x "
      . " WHERE  k.id = '".$row[0]->avatar_id."' ";
	    $database->setQuery( $query );
	    $avatar = $database->loadObjectList();
      ?>
			<td>
			<?php
			foreach( $avatar as $avatar ) {
				if ($avatar->avatar_location != "") {
          echo "<img src='".HTML_speaker::makelink($avatar->avatar_location)."' >";
        }
			} ?>
			</td>
		</tr>
		<tr>
		   <td align="left" valign="top" width="90%">
		     <table border="0" cellpadding="2" cellspacing="0" width="100%">
			     <tr>
			       <?php if( $config->client_col_sermon_number){ ?> <th width="5%" align="left" valign="bottom"><?php echo $lang->sermonNumber; ?></th> <?php } ?>
      			 <th width="50%"  align="left" valign="bottom"> <?php echo $lang->sermonName; ?></th>
      			 <th width="20%"  align="left" valign="bottom"> <?php echo $lang->speaker; ?></th>
      			 <th width="20%"  align="left" valign="bottom"> <?php echo $lang->scripture; ?></th>
			       <?php if( $config->client_col_sermon_date){ ?> <th width="10%"  align="left" valign="bottom"><?php echo $lang->sermon_date; ?></th> <?php } ?>
			       <?php if( $config->client_col_sermon_time){ ?> <th width="10%"  align="left" valign="bottom"><?php echo $lang->sermonTime; ?></th> <?php } ?>
				     <th></th>
				     <th></th>
			     </tr>
	         <?php
      	 	 $query="SELECT a.*, b.name FROM #__sermon_sermons a, #__sermon_speakers b WHERE  a.series_id='".$row[0]->id."'and a.speaker_id = b.id AND a.published='1' order by a.sermon_date, sermon_number desc";
      		 $database->setQuery( $query );
      		 $rows3 = $database->loadObjectList();
      		 if( $rows3 ) {
      		   $i = 0;
	           ?>
      		   <?php foreach( $rows3 as $row3 ) { 
             if ($i == 0) {
               echo "<tr bgcolor =\"".$config->color1."\">\n"; 
               $i = 1;
             } else {
               echo "<tr bgcolor =\"".$config->color2."\">\n";
               $i = 0;
             } 
      if( $config->client_col_sermon_number){ ?>
			<td><?php echo $row3->sermon_number; ?></td> <?php } ?>
			<td>
			  <a  TITLE="<?php 
			  if ($task = "singleseries"){
			     $tag = $lang->single_sermon_hoover_tag;
			  }else{
			     $tag = $lang->download_hoover_tag;
			  }
			  echo $tag; ?>" href="<?php echo JRoute::_("index.php?option=$option&task=singlesermon&id=$row3->id&Itemid=$Itemid"); ?>">
				<?php echo $row3->sermon_title; ?>
			  </a>
			</td>
			<td align="left" valign="top" >
			  <?php echo $row3->name; ?>
			</td>			
			<td align="left" valign="top" >
			  <?php echo $row3->sermon_scripture; ?>
			</td>
			 <?php if( $config->client_col_sermon_date){ ?>
			<td align="left" valign="top" >
			  <?php echo date("m-d-Y", strtotime($row3->sermon_date)); ?>
			</td> <?php } ?>
			 <?php if( $config->client_col_sermon_time){ ?>
			<td><?php echo $row3->sermon_time; ?></td> <?php } ?>
		   </tr>
		   <?php } ?>
		<?php } ?>
				</table>
			</td>
		</tr>
	</table>
<?php } // end of showseries
  
  
 function show_latest_sermons( $option, $id, $task ) {
  global $Itemid;
	$lang = new sermonLang;
	$config = new sermonConfig;
	$database =& JFactory::getDBO();
	
	$query = "SELECT name FROM #__sermon_speakers WHERE id = ".$id;
	$database->setQuery( $query );
	$name = $database->loadResult();
	if ($config->limit_speaker == 1) { 
	  $query = "SELECT id, sermon_number,sermon_scripture, sermon_title, sermon_time, notes,sermon_date FROM #__sermon_sermons WHERE  speaker_id='$id' AND published='1' ORDER BY sermon_date desc, sermon_number desc limit ".$config->sermonresults;
	  $title = $lang->latest." ".$config->sermonresults." ".$lang->sermons_of." ".$name;
	} else {
    $query = "SELECT id, sermon_number,sermon_scripture, sermon_title, sermon_time, notes,sermon_date FROM #__sermon_sermons WHERE  speaker_id='$id' AND published='1' ORDER BY sermon_date desc, sermon_number desc";
    $title = $lang->sermons_of." ".$name; 
  }
  $database->setQuery( $query );
	$rows3 = $database->loadObjectList();
	echo $database->getErrorMsg();
	HTML_speaker::head( $task,$name);
	echo "<table cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">\n";
		echo "<tr>\n";
		  echo "<th align=\"left\">".$title."</th>\n";
		  echo "<th align=\"left\"></th>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		   echo "<td align=\"left\" valign=\"top\" width=\"90%\">\n";
		      echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"100%\">\n";
			 echo "<tr>\n";
			 if( $config->client_col_sermon_number){
				echo "<th width=\"5%\" align=\"left\" valign=\"bottom\">$lang->sermonNumber</th>\n";}
				echo "<th width=\"50%\" align=\"left\" valign=\"bottom\">$lang->sermonName</th>\n";
			 if( $config->client_col_sermon_scripture_reference){
				echo "<th width=\"20%\" align=\"left\" valign=\"bottom\">$lang->scripture</th>\n";	}
			 if( $config->client_col_sermon_date){
				echo "<th width=\"10%\" align=\"left\" valign=\"bottom\">$lang->sermon_date</th>\n";}
			 if( $config->client_col_sermon_time){
				echo "<th width=\"10%\" align=\"left\" valign=\"bottom\">$lang->sermonTime</th>\n";}
			echo "</tr>\n";
			if( $rows3 ) {
			$i = 0;
			   foreach( $rows3 as $row3 ) {
			      if ($i == 0) {
              echo "<tr bgcolor =\"".$config->color1."\">\n"; 
              $i = 1;
            } else {
              echo "<tr bgcolor =\"".$config->color2."\">\n";
              $i = 0;
            } 
			      if( $config->client_col_sermon_number){
				      echo "<td align=\"left\" valign=\"middle\" > $row3->sermon_number </td>\n";
              }
				    echo "<td><a  TITLE=\"$lang->single_sermon_hoover_tag\" href=\"".JRoute::_("index.php?option=$option&task=singlesermon&id=$row3->id&Itemid=$Itemid")."\">".$row3->sermon_title."</a></td>";
			      if( $config->client_col_sermon_scripture_reference){
				      echo "<td align=\"left\" valign=\"middle\" >$row3->sermon_scripture</td>\n";
            }
			      if( $config->client_col_sermon_date){
				      echo "<td align=\"left\" valign=\"middle\">" . date('m-d-Y', strtotime($row3->sermon_date)) . "</td>\n";
            }
			      if( $config->client_col_sermon_time){
				      echo "<td align=\"left\" valign=\"middle\">$row3->sermon_time</td>\n";
            }
			      echo "</tr>\n";
			  } // end of foreach
			}
		     echo "</table>\n";
        	echo "</td>\n";
	echo "</tr>\n";
  echo "</table>\n";
 } // end of show_latest_sermons  
    
  function head( $task, $name=NULL  ) {
	$lang = new sermonLang;
	$config = new sermonConfig;

	echo "<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\">\n";
	   echo "<tr class=\"componentheading\">\n";
	      echo "<th align=\"left\" valign=\"bottom\">\n";
			if (is_null($name)){
			   echo $lang->$task;
			}else{
			   echo $name.": ".$lang->$task;
			}
	      echo "</th>\n";
	   echo "</tr>\n";
	echo "</table>\n";
	echo "<p></p>\n";
  } // end of head
  
  
  
  function help( $option ) {
  	HTML_speaker::head( $option, $config );
  	$lang = new sermonLang;?>
  	<table border="0" cellpadding="0" cellspacing="0" width="100%">
  		<tr>
  			<td><?php echo $lang->helpText; ?></td>
  		</tr>
  	</table>
  <?php } // end of help
  
  
  function insert_search_box(){
    echo "<hr style=\"width: 100%; height: 2px;\">";
  	echo "<form action=\"".JRoute::_( "index.php?option=com_sermonspeaker&task=search&Itemid=$Itemid" )."\" method=\"post\">";	
  	echo "<input class=\"inputbox\" type=\"text\" name=\"search\">";
  	echo "&nbsp;&nbsp;&nbsp;<input type=\"submit\" value=\"Search\">";
    echo "</form>";
  }  // end of insert_search_box 

  function searchResults( $option, $speakers, $seriess, $sermons, $config, $task  ) {
  	HTML_speaker::head($task);
  	global $Itemid;
  	$lang = new sermonLang; ?>
  	<table cellpadding="2" cellspacing="0" width="100%" class="search">
  		<tr>
  			<th colspan="2" align="left"><? echo $lang->searchResults; ?></th>
  		</tr>
  		<tr>
  		   <td align="left" valign="top" width="100%">
  		      <table border="0" cellpadding="2" cellspacing="0" width="100%">
  			<?php
           if ($speakers != 0) {
           ?>
        <tr>
  			  <th align="left" valign="middle">
  				  <?php if ($config->search_icons) { ?><img src="<?php echo HTML_speaker::makelink("/components/com_sermonspeaker/images/speakers.png")?>" width="24" height="24" border="0" /><?php } ?> <?php echo $lang->speakers; ?>
          </th>
  			</tr>
  			<tr>
  			   <td align="left" valign="top">
  				<?php
  				foreach($speakers as $row) { ?>
  					<a href="<?php echo JRoute::_("index.php?option=$option&task=singlespeaker&id=$row->id&Itemid=$Itemid" ); ?>"><?php echo $row->name; ?></a><br />
  				<?php } ?>
  			   </td>
  			</tr>
  			<?php
          }
          if ($seriess != 0) {
          ?>
  			<tr>
  			   <th align="left" valign="middle">
  				<?php if($config->search_icons) { ?><img src="<?php echo HTML_speaker::makelink("/components/com_sermonspeaker/images/series.png")?>" width="24" height="24" border="0" /><?php } ?> <?php echo $lang->series; ?>
  			   </th>
  			</tr>
  			<tr>
  			   <td align="left" valign="top">
  				<?php
  				foreach($seriess as $row) { ?>
  					<a href="<?php echo JRoute::_("index.php?option=$option&task=singleseries&id=$row->id&Itemid=$Itemid" ); ?>"><?php echo $row->series_title; ?></a><br />
  				<?php } ?>
  			   </td>
  			</tr>
  			<?php } 
        if ($sermons != 0) { ?>
  			<tr>
  			   <th align="left" valign="middle">
  				<?php if($config->search_icons) { ?><img src="<?php echo HTML_speaker::makelink("/components/com_sermonspeaker/images/sermon.png")?>" width="24" height="24" border="0" /><?php } ?> <?php echo $lang->sermons; ?>
  			   </th>
  			</tr>
  			<tr>
  			   <td align="left" valign="top">
  				<?php
  				foreach($sermons as $row) { ?>
  					<a href="<?php echo JRoute::_("index.php?option=$option&task=singlesermon&id=$row->id&Itemid=$Itemid"); ?>"><?php echo $row->sermon_title; ?></a><br />
  				<?php } ?>
  			   </td>
  			</tr>
  			<?php } // end of if
        ?>
  		   </table>
  		  </td>
  		</tr>
  	</table>
  <?php } // end of searchResults

  function makelink($path) {
    $base = JURI::root();
    if (substr($path,0,1) == "/" ) {
      $path = substr($path,1);
    }
    $link = $base.$path;
    return $link;
  } // end of makelink
    
    
    
    
    
  // maybe not needed anymore!
  
  function add_task_name($task){
	$lang = new sermonLang;
	$tag = "search_box_".$task;
	echo "<P ALIGN=\"CENTER\"><B>".$lang->$tag."</B></P>";
  } // end of add_task_name


}

?>
