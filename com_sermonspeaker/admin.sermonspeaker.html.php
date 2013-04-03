<?php 
defined('_JEXEC') or die('Restricted access'); 

error_reporting(E_ERROR | E_WARNING | E_PARSE);

//Get the right language if it exists
if (empty($this->language)) {
  $lg = &JFactory::getLanguage();
  $this->language = $lg->get('backwardlang');
  //$this->language = JLanguageHelper::detectLanguage();
}
if (file_exists(JPATH_ADMINISTRATOR.DS.components.DS.'com_sermonspeaker'.DS.'admin.language'.DS.'admin.'.$this->language.'.php')) {
	include(JPATH_ADMINISTRATOR.DS.components.DS.'com_sermonspeaker'.DS.'admin.language'.DS.'admin.'.$this->language.'.php');
} else {
	include(JPATH_ADMINISTRATOR.DS.components.DS.'com_sermonspeaker'.DS.'admin.language'.DS.'admin.english.php');
}

class HTML_SermonSpeaker 
{ 

/*********************************************/
/* SERIES                                    */
/*********************************************/

	function editSeries( $row, $lists, $option ) { 
	  $lang = new sermonLang;
	  $editor =& JFactory::getEditor(); 
		?> 
		<form action="index.php" method="post" name="adminForm" id="adminForm"> 
		<fieldset class="adminform"> 
		<legend>Series</legend> 
		<table class="admintable"> 
		<tr> 
		  <td width="100" align="right" class="key"> <?php echo $lang->seriesTitle; ?> </td> 
		  <td> <input class="text_area" type="text" name="series_title" id="series_title" size="50" maxlength="250" value="<?php echo $row->series_title;?>" /> </td> 
		</tr> 
		<tr> 
		  <td width="100" align="right" class="key"> <?php echo $lang->nameEnteredBy; ?> </td> 
		  <td> <?php echo $lists['created_by']; ?> </td> 
		</tr>
		<tr> 
		  <td width="100" align="right" class="key"><?php echo $lang->speakerName; ?></td>
		  <td>
		  <table>
		    <tr>
    		  <td> 1: <?php echo $lists['speaker_id']; ?> </td> 
    		  <td> 2: <?php echo $lists['speaker2']; ?> </td> 
    		  <td> 3: <?php echo $lists['speaker3']; ?> </td>
        </tr>
        <tr>
    		  <td> 4: <?php echo $lists['speaker4']; ?> </td> 
    		  <td> 5: <?php echo $lists['speaker5']; ?> </td> 
    		  <td> 6: <?php echo $lists['speaker6']; ?> </td>
        </tr>
        <tr>
    		  <td> 7: <?php echo $lists['speaker7']; ?> </td> 
    		  <td> 8: <?php echo $lists['speaker8']; ?> </td> 
    		  <td> 9: <?php echo $lists['speaker9']; ?> </td>
        </tr>
        <tr>
    		  <td> 10: <?php echo $lists['speaker10']; ?> </td> 
    		  <td> 11: <?php echo $lists['speaker11']; ?> </td> 
    		  <td> 12: <?php echo $lists['speaker12']; ?> </td>
        </tr>
        <tr>
    		  <td> 13: <?php echo $lists['speaker13']; ?> </td> 
    		  <td> 14: <?php echo $lists['speaker14']; ?> </td> 
    		  <td> 15: <?php echo $lists['speaker15']; ?> </td>
        </tr>
        <tr>
    		  <td> 16: <?php echo $lists['speaker16']; ?> </td> 
    		  <td> 17: <?php echo $lists['speaker17']; ?> </td> 
    		  <td> 18: <?php echo $lists['speaker18']; ?> </td>
        </tr>
        <tr>
    		  <td> 19: <?php echo $lists['speaker19']; ?> </td> 
    		  <td> 20: <?php echo $lists['speaker20']; ?> </td> 
    		  <td> </td>
        </tr>
		  </table>
		  </td>
		</tr> 
    <tr> 
		  <td width="100" align="right" class="key"> <?php echo $lang->SeriesDescription; ?> </td> 		   
		  <td><?php echo $editor->display('series_description',$row->series_description,'100%','200','40','10');	?> </td> 
    </tr> 
    <tr> 
		  <td width="100" align="right" class="key"> <?php echo $lang->SeriesAvatar; ?> </td> 
		  <td> <?php echo $lists['avatar_id']; ?> </td> 
		</tr>    
		<tr> 
		  <td width="100" align="right" class="key"> <?php echo $lang->published; ?> </td> 
		  <td> <?php echo $lists['published']; ?> </td> 
		</tr> 
		</table> 
		</fieldset> 
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" /> 
		<input type="hidden" name="option" value="<?php echo $option;?>" /> 
		<input type="hidden" name="act" value="series" />
		<input type="hidden" name="task" value="series" /> 
		</form> 
		<?php 
	} // end of editSeries
	
	function showSeries( $option, &$rows, &$pageNav ) { 
	  $lang = new sermonLang;
    ?> 
	  <form action="index.php" method="post" name="adminForm"> 
	  <table class="adminlist"> 
	    <thead> 
	      <tr> 
	        <th width="20"> 
	          <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" /> 
	        </th> 
	        <th class="title"><?php
            echo "<small><a href=\"index.php?option=com_sermonspeaker&task=series&order=title_a\">".$lang->up."</a></small>&nbsp;";
            echo $lang->seriesTitle; 
            echo "&nbsp;<small><a href=\"index.php?option=com_sermonspeaker&task=series&order=title_d\">".$lang->down."</a></small>";?>
          </th> 
	        <th width="25%"><?php 
            echo "<small><a href=\"index.php?option=com_sermonspeaker&task=series&order=speaker_a\">".$lang->up."</a></small>&nbsp;";
            echo $lang->speakerName;
            echo "&nbsp;<small><a href=\"index.php?option=com_sermonspeaker&task=series&order=speaker_d\">".$lang->down."</a></small>";?>
          </th> 
	        <th width="25%" nowrap="nowrap"><?php
            echo "<small><a href=\"index.php?option=com_sermonspeaker&task=series&order=published_a\">".$lang->up."</a></small>&nbsp;";
            echo $lang->published;
            echo "&nbsp;<small><a href=\"index.php?option=com_sermonspeaker&task=series&order=published_d\">".$lang->down."</a></small>";?>
          </th> 
	      </tr> 
	    </thead> 
	    <?php
		jimport('joomla.filter.output');
	    $k = 0;
	    for ($i=0, $n=count( $rows ); $i < $n; $i++) 
	    {
			$row = &$rows[$i]; 
			$checked = JHTML::_('grid.id', $i, $row->id );
			$published = JHTML::_('grid.published', $row, $i );
			$link = JFilterOutput::ampReplace( 'index.php?option=' . $option . '&task=edit&cid[]='. $row->id );
	      ?> 
	      <tr class="<?php echo "row$k"; ?>"> 
	        <td> 
	          <?php echo $checked; ?> 
	        </td> 
	        <td>
			      <a href="<?php echo $link; ?>"> 
	          <?php echo $row->series_title; ?></a>
	        </td> 
	        <td> 
	          <?php 
              $db =& JFactory::getDBO(); 
		          $query = "SELECT name FROM #__sermon_speakers WHERE ID=".$row->speaker_id;
		          $db->setQuery( $query );
		          $name = $db->loadResult();
		          echo $name;
            ?> 
	        </td> 
	        <td align="center"> 
	          <?php echo $published;?> 
	        </td> 
	      </tr> 
	      <?php 
	      $k = 1 - $k; 
	    } 
	    ?>
		<tfoot> 
		 <td colspan="7"><?php echo $pageNav->getListFooter(); ?></td> 
		</tfoot>
	  </table> 
	  <?php $order= JRequest::getVar('order'); ?>
	  <input type="hidden" name="option" value="<?php echo $option;?>" /> 
	  <input type="hidden" name="task" value="series" /> 
	  <input type="hidden" name="act" value="series" /> 
	  <input type="hidden" name="boxchecked" value="0" /> 
	  <input type="hidden" name="order" value="<?php echo $order; ?>" />
	  </form> 
	  <?php 
	} // end of showSeries
	
/*********************************************/
/* SPEAKERS                                  */
/*********************************************/
	
	function editSpeakers( $row, $lists, $option ) { 
		$lang = new sermonLang;
    $editor =& JFactory::getEditor(); 
		?> 
		<form action="index.php" method="post" name="adminForm" id="adminForm"> 
		<fieldset class="adminform"> 
		<legend>Speakers</legend> 
		<table class="admintable"> 
		<tr> 
		  <td width="100" align="right" class="key"> <?php echo $lang->name; ?> </td> 
		  <td> 
			  <input class="text_area" type="text" name="name" id="name" size="50" maxlength="250" value="<?php echo $row->name;?>" /> 
		  </td> 
		</tr> 
		<tr> 
		  <td width="100" align="right" class="key"> <?php echo $lang->nameEnteredBy; ?> </td> 
		  <td> <?php echo $lists['created_by']; ?> </td> 
		</tr>
    <tr> 
		  <td width="100" align="right" class="key"> <?php echo $lang->website; ?> </td> 
  		  <td> 
  			  <input class="text_area" type="text" name="website" id="website" size="90" maxlength="250" value="<?php echo $row->website;?>" /> 
  		  </td> 
		  </td> 
		</tr> 
		<tr> 
		  <td width="100" align="right" class="key"> <?php echo $lang->picture; ?> </td> 
  		  <td>
  			  <input class="text_area" type="text" name="pic" id="pic" size="90" maxlength="250" value="<?php echo $row->pic;?>" /> 
  		  </td> 
		  </td> 
		</tr> 
    <tr> 
		  <td width="100" align="right" class="key"> <?php echo $lang->intro; ?> </td> 
		  <td> <?php echo $editor->display('intro',$row->intro,'100%','200','40','10');	?> </tr> 
    <tr> 
		  <td width="100" align="right" class="key"> <?php echo $lang->bio; ?> </td> 
		  <td> <?php echo $editor->display('bio',$row->bio,'100%','300','40','10');	?> </td> 
		</tr>    
		<tr> 
		  <td width="100" align="right" class="key"> <?php echo $lang->published; ?> </td> 
		  <td> <?php echo $lists['published']; ?> </td> 
		</tr> 
		</table> 
		</fieldset> 
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" /> 
		<input type="hidden" name="option" value="<?php echo $option;?>" /> 
		<input type="hidden" name="ordering" value="<?php echo $row->ordering;?>" /> 
		<input type="hidden" name="act" value="speakers" />
		<input type="hidden" name="task" value="speakers" /> 
		</form> 
		<?php 
	} // end of editSpeakers
	
	function showSpeakers( $option, &$rows, &$pageNav ) { 
	  $lang = new sermonLang;
	  $database =& JFactory::getDBO();
    ?> 
	  <form action="index.php" method="post" name="adminForm"> 
	  <table class="adminlist"> 
	    <thead> 
	      <tr> 
	        <th width="20"> 
	          <input type="checkbox" name="toggle" 
	               value="" onclick="checkAll(<?php echo 
	               count( $rows ); ?>);" /> 
	        </th> 
	        <th class="title"><?php echo $lang->name; ?></th> 
	        <th width="25%"><?php echo $lang->website; ?></th> 
	        <th width="10%" nowrap="nowrap"><?php echo $lang->ordering; ?></th>
	        <th width="20%"><?php echo $lang->picture; ?></th>
	        <th width="10%" nowrap="nowrap"><?php echo $lang->published; ?></th> 
	      </tr> 
	    </thead> 
	    <?php
		  jimport('joomla.filter.output');
	    $k = 0;
	    $query = "SELECT ordering FROM #__sermon_speakers ORDER BY ordering desc;";
      $database->setQuery( $query );
      $highest_ordering = $database->loadResult();
      
	    for ($i=0, $n=count( $rows ); $i < $n; $i++) {
  			$row = &$rows[$i]; 
  			$checked = JHTML::_('grid.id', $i, $row->id );
  			$published = JHTML::_('grid.published', $row, $i );
  			$link = JFilterOutput::ampReplace('index.php?option='.$option.'&task=editSpeakers&cid[]='.$row->id);
  	    ?> 
	      <tr class="<?php echo "row$k"; ?>"> 
	        <td> <?php echo $checked; ?> </td> 
	        <td> <a href="<?php echo $link; ?>"> <?php echo $row->name; ?></a> </td> 
	        <td> <?php echo $row->website; ?> </td> 
	        <td>
          <?php
          if ($row->ordering > 1) {
            echo "<a href=\"index2.php?option=$option&task=movespeakerup&id=$row->id\">$lang->up</a>&nbsp;";
          }
          echo $row->ordering;
          if (($row->ordering != $highest_ordering) or ($row->ordering == 0)) {
            echo "  &nbsp;<a href=\"index2.php?option=$option&task=movespeakerdown&id=$row->id\">$lang->down</a></td>\n";
          }
          ?>           
          </td>
          <?php
          if ($row->pic == "") {
            $row->pic = JURI::root()."components/com_sermonspeaker/images/nopict.jpg";
          }
          if (substr($row->pic,0,7) == "http://") {
            echo "<td align=center> <img src=\"".$row->pic."\" border=\"1\" width=\"50\" height=\"50\" /></td>";
          } else {
            $path = $row->pic;
            if (substr($path,0,1) == "." ) { $path = substr($path,1); }
            if (substr($path,0,1) == "/" ) { $path = substr($path,1); }
            echo "<td align=center> <img src=\"".JURI::root().$path."\" border=\"1\" width=\"50\" height=\"50\" /></td>";
          }
          ?>
	        <td align="center"> <?php echo $published;?> </td> 
	      </tr> 
	      <?php 
	      $k = 1 - $k; 
	    } 
	    ?>
		<tfoot> 
		 <td colspan="7"><?php echo $pageNav->getListFooter(); ?></td> 
		</tfoot>
	  </table> 
	  <input type="hidden" name="option" value="<?php echo $option;?>" /> 
	  <input type="hidden" name="task" value="speakers" /> 
	  <input type="hidden" name="act" value="speakers" /> 
	  <input type="hidden" name="boxchecked" value="0" /> 
	  </form> 
	  <?php 
	} // end of showSpeakers
	
/*********************************************/
/* SERMON                                    */
/*********************************************/
	
		function editSermons( $row, $lists, $option ) { 
  		global $mainframe;
      $lang = new sermonLang;
      $editor =& JFactory::getEditor(); 
  		JHTML::_('behavior.calendar'); 
  		JHTML::_('behavior.modal', 'a.modal-button'); 
      
      $js = 'var currentEnabled = null;
        function enableElement(elem) {
          if (currentEnabled) {
            currentEnabled.disabled = true;
          }
          elem.disabled = false;
          currentEnabled = elem;
          
        }
        function disElement(elem) {
          elem.disabled = true;
        }';
      $document =& JFactory::getDocument();
      $document->addScriptDeclaration($js);
      
  		?> 
  		<form action="index.php" method="post" name="adminForm" id="adminForm" > 
  		<fieldset class="adminform"> 
  		<legend>Sermons</legend> 
  		<table class="admintable"> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sermonTitle; ?> </td> 
  		  <td> <input class="text_area" type="text" name="sermon_title" id="sermon_title" size="50" maxlength="250" value="<?php echo $row->sermon_title;?>" /> </td> 
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->scripture; ?> </td> 
  		  <td> <input class="text_area" type="text" name="sermon_scripture" id="sermon_scripture" size="50" maxlength="250" value="<?php echo $row->sermon_scripture;?>" /> </td> 
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sermon_date; ?>: </td> 
  		  <td> <input class="inputbox" type="text" name="sermon_date" id="sermon_date" size="25" maxlenght="20" value="<?php echo $row->sermon_date;?>" /> 
        <input type="reset" class="button" value="..." onclick="return showCalendar('sermon_date','%Y-%m-%d');" /> </td> 
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sermonPath; ?> </td> 
  		  <td> 
  		  <input type="radio" name="sel" value="1" onclick="enableElement(this.form.elements['sermon_path_txt']);" checked>
        <input class="text_area" type="text" name="sermon_path_txt" id="sermon_path_txt" size="80" maxlength="250" value="<?php echo $row->sermon_path;?>" /> 
        <br>
        <input type="radio" name="sel" value="2" onclick="disElement(this.form.elements['sermon_path_txt']);enableElement(this.form.elements['sermon_path_choice']);">
        <?php
        // The code below is donated by Jay Sauce
        // and modified for better windows compatibility by MHe
        $dirLoc = JPATH_ROOT.DS.components.DS.'com_sermonspeaker'.DS.'media';
        $dirLoc2 = DS.components.DS.'com_sermonspeaker'.DS.'media';
        $startdir = JPATH_ROOT;
        $lsdir = strlen($startdir);
        $from = $dirLoc;
        if(! is_dir($from))
          return false;
        $files = array();
        $dirs = array( $from);
        while( NULL !== ($dir = array_pop( $dirs))) {
          if( $dh = opendir($dir)) {
            while( false !== ($file = readdir($dh))) {
              if( $file == '.' || $file == '..')
                continue;
              //$path = $dir . '/' . $file;
              $path = $dir.DS.$file;
              if( is_dir($path))
                $dirs[] = $path;
              else
                //$files[] = $path;
                $files[] = substr($path,$lsdir);
            } // of while
            closedir($dh);
          }
        } //of while
        ?>
        <select name="sermon_path_choice" disabled="disabled">
	      <option value="">None</option>
				<?php
				$fileAry = array();
        if ($files) {
          for($fc = 0; $fc < count($files); $fc++){
  					$file = $files[$fc];
            $exp = explode("/", $file);
  					$filename = $exp[count($exp) - 1];
  					$exp2 = explode(".", $filename);
            $ext = $exp2[count($exp2) - 1];
            if ($file != "." && $file != ".." && ($ext == "mp3" or $ext == "flv" or $ext == "wmv" )) {
  					  array_push($fileAry, $file);
            }
          }
					sort($fileAry);
					for($fc = 0; $fc < count($fileAry); $fc++){
						//echo "<option value=\"/components/com_sermonspeaker/media/".str_replace($dirLoc.DS, "", $fileAry[$fc])."\"";
						echo "\n<option value=\"".str_replace($dirLoc.DS, "", $fileAry[$fc])."\"";
						if($row->sermon_path != ""){
							//$mp3Loc = str_replace($dirLoc2.DS, "", $row->sermon_path);
							$mp3Loc = str_replace($lsdir, "", $row->sermon_path);
							if(str_replace($dirLoc.DS, "", $fileAry[$fc]) == $mp3Loc){
								echo " selected=\"selected\"";
							}
						}
						echo ">".str_replace($dirLoc.DS, "", $fileAry[$fc])."</option>";
					} // of for
        } // of if
        //closedir($handle); 
        ?>
        </select>
        </td> 
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sermonNumber; ?> </td> 
  		  <td> <input class="text_area" type="text" name="sermon_number" id="sermon_number" size="10" maxlength="250" value="<?php echo $row->sermon_number;?>" /> </td> 
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sermonTime; ?> </td> 
  		  <td> 
  			  <input class="text_area" type="text" name="sermon_time" id="sermon_time" size="50" maxlength="250" value="<?php echo $row->sermon_time;?>" /> 
  		  </td> 
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->speaker; ?> </td> 
  		  <td> <?php echo $lists['speaker_id']; ?> </td> 
  		</tr> 
      <tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->series; ?> </td> 
  		  <td> <?php echo $lists['series_id'];?> </td> 
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->nameEnteredBy; ?> </td> 
  		  <td> <?php echo $lists['created_by'];?> </td> 
  		</tr>
      <tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->notes; ?> </td> 
  		  <td> <?php echo $editor->display('notes',$row->notes,'100%','200','40','10');	?> </td> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->published; ?> </td> 
  		  <td> <?php echo $lists['published']; ?> </td> 
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sermoncast; ?> </td> 
  		  <td> <?php echo $lists['podcast']; ?> </td> 
  		</tr> 
    	<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->addfile; ?> </td> 
  		  <td> <input class="text_area" type="text" name="addfile" id="addfile" size="80" maxlength="250" value="<?php echo $row->addfile;?>" /> </td> 
  		</tr> 
  			<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->addfileDesc; ?> </td> 
  		  <td> <input class="text_area" type="text" name="addfileDesc" id="addfileDesc" size="80" maxlength="250" value="<?php echo $row->addfileDesc;?>" /> </td> 
  		</tr> 
  		</table> 
  		</fieldset> 
  		<input type="hidden" name="id" value="<?php echo $row->id; ?>" /> 
  		<input type="hidden" name="option" value="<?php echo $option;?>" /> 
  		<input type="hidden" name="act" value="sermons" />
  		<input type="hidden" name="task" value="sermons" /> 
  		</form> 
  		<?php 
	 } // end of editSermons
	
	function showSermons( $option, &$rows, &$pageNav ) { 
	  $lang = new sermonLang;
    ?> 
	  <form action="index.php" method="post" name="adminForm"> 
	  <table class="adminlist"> 
	    <thead> 
	      <tr> 
	        <th width="20"> 
	          <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
	        </th> 
	        <th class="title"><?php 
            echo "<small><a href=\"index.php?option=com_sermonspeaker&task=sermons&order=title_a\">".$lang->up."</a></small>&nbsp;";
            echo $lang->sermonTitle; 
            echo "&nbsp;<small><a href=\"index.php?option=com_sermonspeaker&task=sermons&order=title_d\">".$lang->down."</a></small>";?>
          </th> 
	        <th width="14%"><?php
	          echo "<small><a href=\"index.php?option=com_sermonspeaker&task=sermons&order=speaker_a\">".$lang->up."</a></small>&nbsp;";
            echo $lang->speaker; 
            echo "&nbsp;<small><a href=\"index.php?option=com_sermonspeaker&task=sermons&order=speaker_d\">".$lang->down."</a></small>";?>
          </th> 
	        <th width="14%"><?php echo $lang->scripture; ?></th>
	        <th width="14%"><?php 
            echo "<small><a href=\"index.php?option=com_sermonspeaker&task=sermons&order=series_a\">".$lang->up."</a></small>&nbsp;";
            echo $lang->series; 
            echo "&nbsp;<small><a href=\"index.php?option=com_sermonspeaker&task=sermons&order=series_d\">".$lang->down."</a></small>";?>
          </th>
	        <th width="12%"><?php  
            echo "<small><a href=\"index.php?option=com_sermonspeaker&task=sermons&order=date_a\">".$lang->up."</a></small>&nbsp;";
            echo $lang->sermon_date; 
            echo "&nbsp;<small><a href=\"index.php?option=com_sermonspeaker&task=sermons&order=date_d\">".$lang->down."</a></small>";?>
          </th>
	        <th width="8%" nowrap="nowrap"><?php 
            echo "<small><a href=\"index.php?option=com_sermonspeaker&task=sermons&order=pub_a\">".$lang->up."</a></small>&nbsp;";
            echo $lang->published; 
            echo "&nbsp;<small><a href=\"index.php?option=com_sermonspeaker&task=sermons&order=pub_d\">".$lang->down."</a></small>";?>
          </th>
	        <th width="8%" nowrap="nowrap"><?php echo $lang->sermoncast; ?></th>
	      </tr> 
	    </thead> 
	    <?php
		  jimport('joomla.filter.output');
	    $k = 0;
	    for ($i=0, $n=count($rows); $i<$n; $i++) {
  			$row = &$rows[$i]; 
  			$checked = JHTML::_('grid.id', $i, $row->id );
  			$published = JHTML::_('grid.published', $row, $i );
  			$sc = JHTML::_('grid.published', $row->podcast,$i );
  			$link = JFilterOutput::ampReplace('index.php?option='.$option.'&task=editSermons&cid[]='.$row->id);
	      ?> 
	      <tr class="<?php echo "row$k"; ?>"> 
	        <td> <?php echo $checked; ?> </td> 
	        <td> <a href="<?php echo $link; ?>"> <?php echo $row->sermon_title; ?></a> </td> 
	        <td> 
	          <?php 
              $db =& JFactory::getDBO(); 
		          $query = "SELECT name FROM #__sermon_speakers WHERE ID=".$row->speaker_id;
		          $db->setQuery( $query );
		          $name = $db->loadResult();
		          echo $name;
            ?> 
	        </td> 
	        <td> <?php echo $row->sermon_scripture; ?> </td>
	        <td>
	         <?php 
              $db =& JFactory::getDBO(); 
              $query = "SELECT series_title FROM #__sermon_series WHERE ID=".$row->series_id;
              $db->setQuery( $query );
              $title = $db->loadResult();
              echo $title;
            ?> 
	        </td>
	        <td> <?php echo $row->sermon_date; ?> </td>
	        <td align="center"> <?php echo $published;?> </td> 
	        <td align="center"> <?php
            if($row->podcast == 1) { 
              echo "<a href=\"index.php?option=com_sermonspeaker&task=un_pc&id=".$row->id."\">".JHTML::_('image.administrator', 'tick.png')."</a>";
            } else {
              echo "<a href=\"index.php?option=com_sermonspeaker&task=pc&id=".$row->id."\">".JHTML::_('image.administrator', 'publish_x.png')."</a>";                        
            } ?>
          </td>
	      </tr> 
	      <?php 
	      $k = 1 - $k; 
	    } 
	    $order= JRequest::getVar('order');
	    ?>
		<tfoot> 
		<td colspan="8"><?php echo $pageNav->getListFooter(); ?></td> 
		</tfoot>
	  </table> 
	  <input type="hidden" name="option" value="<?php echo $option;?>" /> 
	  <input type="hidden" name="task" value="sermons" /> 
	  <input type="hidden" name="act" value="sermons" /> 
	  <input type="hidden" name="boxchecked" value="0" /> 
	  <input type="hidden" name="order" value="<?php echo $order; ?>" />
	  </form> 
	  <?php 
	} // end of showSermons
	
	function showPopup() {
	  $lang = new sermonLang;
	  
	  //include js
    $js = '  function makeSelection(frm, id) {
      if(!frm || !id)
        return;
      var elem = frm.elements[id];
      if(!elem)
        return;
      var val = elem.options[elem.selectedIndex].value;
      opener.targetElement.value = val;
      this.close();
    }';
    $document =& JFactory::getDocument();
    $document->addScriptDeclaration($js);
	  
    echo "<h2>".$lang->select_file."</h2>";
    echo "<form id=\"pp_adminform\" name=\"pp_adminform\" action=\"#\">";
    
    $dirLoc = JPATH_ROOT.DS.components.DS.'com_sermonspeaker'.DS.'media';
    $dirLoc2 = DS.components.DS.'com_sermonspeaker'.DS.'media';
 
    $from = $dirLoc;
    if(! is_dir($from))
      return false;
    $files = array();
    $dirs = array( $from);
    while( NULL !== ($dir = array_pop( $dirs))) {
      if( $dh = opendir($dir)) {
        while( false !== ($file = readdir($dh))) {
            if( $file == '.' || $file == '..')
              continue;
            $path = $dir . '/' . $file;
            if( is_dir($path))
              $dirs[] = $path;
            else
              $files[] = $path;
        } // of while
        closedir($dh);
      }
    } //of while
    echo "<select name=\"sermon_path\">";
    echo "<option value=\"\">None</option>";
		$fileAry = array();
    if ($files) {
      for($fc = 0; $fc < count($files); $fc++){
				$file = $files[$fc];
        $exp = explode("/", $file);
				$filename = $exp[count($exp) - 1];
				$exp2 = explode(".", $filename);
        $ext = $exp2[count($exp2) - 1];
        if ($file != "." && $file != ".." && $ext == "mp3") {
				  array_push($fileAry, $file);
        }
      }
			sort($fileAry);
			for($fc = 0; $fc < count($fileAry); $fc++){
				echo "<option value=\"/components/com_sermonspeaker/media/".str_replace($dirLoc.DS, "", $fileAry[$fc])."\"";
				if($row->sermon_path != ""){
					$mp3Loc = str_replace($dirLoc2.DS, "", $row->sermon_path);
					if(str_replace($dirLoc.DS, "", $fileAry[$fc]) == $mp3Loc){
						echo " selected=\"selected\"";
					}
				}
				echo ">".str_replace($dirLoc.DS, "", $fileAry[$fc])."</option>";
			} // of for
    }
    closedir($handle);
    echo "</select>";   
    
    echo "<input type=\"button\" value=\"OK!\" onclick=\"makeSelection(this.form,'sermon_path');\">";
    echo "<input type=\"button\" value=\"OK2!\"onClick=\"opener.adminForm.sermon_path.value = document.adminform.sermon_path.value\">";
    
    echo "</form>";

    
  } // end of showPopup
	
/*********************************************/
/* Config                                    */
/*********************************************/

  function showConfig( $row, $lists, $option ) {  
    $lang = new sermonLang;
    $config = new sermonConfig;
    $sc_config = new sermonCastConfig;
    
    global $ss_version;
    
    $editor =& JFactory::getEditor();
    
    if (SermonSpeakerController::checkdb() == 1 ) {
      echo "<h3>DB Update needed!</h3>Backup your database, then click <a href=\"index.php?option=com_sermonspeaker&task=updateDB\">here!</a>"; 
    }
    if ($config->ss_version != $ss_version) { echo $lang->ss_ver_mismatch; }
    echo "<div align=\"right\">".$lang->ss_version." : ".$ss_version."<br><a href=\"http://joomlacode.org/gf/project/sermon_speaker\" target=\"_blank\">".$lang->c4nv."</a></div>";
    ?> 
		<form action="index.php" method="post" name="adminForm" id="adminForm"> 
		<fieldset class="adminform"> 
		<legend>Configuration</legend> 
		<?php
    jimport('joomla.html.pane');
    $tabs	=& JPane::getInstance('tabs');
    echo $tabs->startPane('Configuration');
    echo $tabs->startPanel($lang->displaySettings, 'Display');
    ?>
		<table class="admintable" cellpadding="10" cellspacing="3"> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->resultsPerPage; ?> </td> 
  		  <td> <?php echo $lists['sermonresults']; ?> </td> 
  		  <td> <?php echo $lang->numberOfResults; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->limitspeaker; ?> </td> 
  		  <td width="100"> <?php echo $lists['limit_speaker']; ?></td> 
  		  <td> <?php echo $lang->limitspeakerDesc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->showIntro; ?> </td> 
  		  <td width="100"> <?php echo $lists['speaker_intro']; ?></td> 
  		  <td> <?php echo $lang->showIntroDesc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->startpage; ?> </td> 
  		  <td width="100"> <?php echo $lists['startpage']; ?></td> 
  		  <td> <?php echo $lang->startpage_desc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->allow_ussp; ?> </td> 
  		  <td width="100"> <?php echo $lists['allow_ussp']; ?></td> 
  		  <td> <?php echo $lang->allow_usspDesc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->showClientDisplay; ?> </td> 
  		  <td> <?php echo $lists['sermon_number']; ?> </td> 
  		  <td> <?php echo $lang->client_col_sermon_number_tag; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> </td> 
  		  <td> <?php echo $lists['sermon_scripture_reference']; ?> </td> 
  		  <td> <?php echo $lang->client_col_sermon_scripture_reference_tag; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> </td> 
  		  <td> <?php echo $lists['sermon_date']; ?> </td> 
  		  <td> <?php echo $lang->client_col_sermon_date_tag; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> </td> 
  		  <td> <?php echo $lists['sermon_time']; ?> </td> 
  		  <td> <?php echo $lang->client_col_sermon_time_tag; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> </td> 
        <td> <?php echo $lists['sermon_notes']; ?> </td> 
        <td> <?php echo $lang->client_col_sermon_notes_tag; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> </td> 
  		  <td> <?php echo $lists['player']; ?> </td> 
  		  <td> <?php echo $lang->client_col_player_tag; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> </td> 
  		  <td> <?php echo $lists['search']; ?> </td> 
  		  <td> <?php echo $lang->search; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> </td> 
  		  <td> <?php echo $lists['sermon_addfile']; ?> </td> 
  		  <td> <?php echo $lang->client_col_sermon_addfile_desc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> </td> 
  		  <td> <?php echo $lists['dl_button']; ?> </td> 
  		  <td> <?php echo $lang->dl_button; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> </td> 
  		  <td> <?php echo $lists['popup_player']; ?> </td> 
  		  <td> <?php echo $lang->popup_player; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->hide_dl; ?> </td> 
  		  <td> <?php echo $lists['hide_dl']; ?> </td> 
  		  <td> <?php echo $lang->hide_dl_desc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->popup_color; ?> </td> 
  		  <td> <input type="text" name="popup_color" value="<?php 
        if ($config->popup_color) {echo $config->popup_color;} else { echo "FFFFFF";}
        ?>" class="text_area" size="10" /> </td> 
  		  <td> <?php echo $lang->popup_colorDesc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->popup_height; ?> </td> 
  		  <td> <input type="text" name="popup_height" value="<?php 
        if ($config->popup_height) {echo $config->popup_height;} else { echo "40";}
        ?>" class="text_area" size="10" /> </td> 
  		  <td> <?php echo $lang->popup_heightDesc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->date_format; ?> </td> 
  		  <td> <?php echo $lists['dateformat']; ?> </td> 
  		  <td> <?php echo $lang->date_formatdesc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->mp_width; ?> </td> 
  		  <td> <input type="text" name="mp_width" value="<?php echo $config->mp_width?>" class="text_area" size="10" /> </td> 
  		  <td> <?php echo $lang->mp_widthDesc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->mp_height; ?> </td> 
  		  <td> <input type="text" name="mp_height" value="<?php echo $config->mp_height?>" class="text_area" size="10" /> </td> 
  		  <td> <?php echo $lang->mp_heightDesc; ?> </td>
  		</tr>
      	<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->autostart; ?> </td> 
  		  <td width="100"> <?php echo $lists['autostart']; ?></td> 
  		  <td> <?php echo $lang->autostartDesc; ?> </td>
  		</tr>  
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->color1; ?> </td> 
  		  <td> <input type="text" name="color1" value="<?php echo $config->color1?>" class="text_area" size="10" /> </td> 
  		  <td> <?php echo $lang->color1Desc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->color2; ?> </td> 
  		  <td> <input type="text" name="color2" value="<?php echo $config->color2?>" class="text_area" size="10" /> </td> 
  		  <td> <?php echo $lang->color2Desc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sermonlayout; ?> </td>
  		  <td width="100"> <?php echo $lists['sermonlayout']; ?></td> 
  		  <td> <?php echo $lang->sermonlayoutDesc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->ga; ?> </td> 
  		  <td> <input type="text" name="ga" value="<?php echo $config->ga?>" class="text_area" size="60" /> </td> 
  		  <td> <?php echo $lang->gaDesc; ?> </td>
  		</tr>
		</table> 
		<?php 
		echo $tabs->endPanel();
    echo $tabs->startPanel($lang->statisticsSettings, 'Stat');
    ?>
    <table class="admintable" cellpadding="10" cellspacing="3">
      <tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->trackspeakerViews; ?> </td> 
  		  <td> <?php echo $lists['track_speaker']; ?> </td> 
  		  <td> <?php echo $lang->trackspeakerViewsDesc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->trackSeriesViews; ?> </td> 
  		  <td> <?php echo $lists['track_series']; ?> </td> 
  		  <td> <?php echo $lang->trackSeriesViewsDesc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->trackSermonPlays; ?> </td> 
  		  <td> <?php echo $lists['track_sermon']; ?> </td> 
  		  <td> <?php echo $lang->trackSermonPlaysDesc; ?> </td>
  		</tr>
    </table>  
		<?php
    echo $tabs->endPanel();
    echo $tabs->startPanel($lang->sermoncastSettings, 'Sermoncast');
    ?>
    <table class="admintable" cellpadding="10" cellspacing="3">
      <tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_showpcast; ?> </td> 
  		  <td> <?php echo $lists['mod_showpcast']; ?> </td> 
  		  <td> <?php echo $lang->sc_showpcastDesc; ?> </td>
  		</tr> 
      <tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_showplink; ?> </td> 
  		  <td> <?php echo $lists['mod_showplink']; ?> </td> 
  		  <td> <?php echo $lang->sc_showplinkDesc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_mod_text; ?> </td> 
  		  <td> <input type="text" name="mod_text" value="<?php echo $sc_config->mod_text?>" class="text_area" size="60" /> </td> 
  		  <td> <?php echo $lang->sc_mod_textDesc; ?> </td>
  		</tr> 
      <tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_cache; ?> </td> 
  		  <td> <?php echo $lists['cache']; ?> </td> 
  		  <td> <?php echo $lang->sc_cacheDesc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_cache_time; ?> </td> 
  		  <td> <input type="text" name="cache_time" value="<?php echo $sc_config->cache_time?>" class="text_area" size="8" /> </td> 
  		  <td> <?php echo $lang->sc_cache_timeDesc; ?> </td>
  		</tr>
      <tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_mimetype; ?> </td> 
  		  <td> <input type="text" name="mimetype" value="<?php echo $sc_config->mimetype?>" class="text_area" size="30" /> </td> 
  		  <td> <?php echo $lang->sc_mimetypeDesc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_encoding; ?> </td> 
  		  <td> <input type="text" name="encoding" value="<?php echo $sc_config->encoding?>" class="text_area" size="30" /> </td> 
  		  <td> <?php echo $lang->sc_encodingDesc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_count; ?> </td> 
  		  <td> <input type="text" name="count" value="<?php echo $sc_config->count?>" class="text_area" size="8" /> </td> 
  		  <td> <?php echo $lang->sc_countDesc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_title; ?> </td> 
  		  <td> <input type="text" name="title" value="<?php echo $sc_config->title?>" class="text_area" size="40" /> </td> 
  		  <td> <?php echo $lang->sc_titleDesc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_description; ?> </td> 
  		  <td> <input type="text" name="description" value="<?php echo $sc_config->description?>" class="text_area" size="60" /> </td> 
  		  <td> <?php echo $lang->sc_descriptionDesc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_copyright; ?> </td> 
  		  <td> <input type="text" name="copyright" value="<?php echo $sc_config->copyright?>" class="text_area" size="60" /> </td> 
  		  <td> <?php echo $lang->sc_copyrightDesc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_limit_text; ?> </td> 
  		  <td> <?php echo $lists['limittext']; ?> </td> 
  		  <td> <?php echo $lang->sc_limit_textDesc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_text_length; ?> </td> 
  		  <td> <input type="text" name="text_length" value="<?php echo $sc_config->text_length?>" class="text_area" size="8" /> </td> 
  		  <td> <?php echo $lang->sc_text_lengthDesc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_itauthor; ?> </td> 
  		  <td> <input type="text" name="itAuthor" value="<?php echo $sc_config->itAuthor?>" class="text_area" size="40" /> </td> 
  		  <td> <?php echo $lang->sc_itauthorDesc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_itimage; ?> </td> 
  		  <td> <input type="text" name="itImage" value="<?php echo $sc_config->itImage?>" class="text_area" size="40" /> </td> 
  		  <td> <?php echo $lang->sc_itimageDesc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_itcat1; ?> </td> 
  		  <td> <?php echo $lists['itcat1']; ?> </td> 
  		  <td> <?php echo $lang->sc_itcat1Desc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_itcat2; ?> </td> 
  		  <td> <?php echo $lists['itcat2']; ?> </td> 
  		  <td> <?php echo $lang->sc_itcat2Desc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_itcat3; ?> </td> 
  		  <td> <?php echo $lists['itcat3']; ?> </td> 
  		  <td> <?php echo $lang->sc_itcat3Desc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_it_prefix; ?> </td> 
  		  <td> <?php echo $lists['it_prefix']; ?> </td> 
  		  <td> <?php echo $lang->sc_it_prefixDesc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_itkeywords; ?> </td> 
  		  <td> <input type="text" name="itKeywords" value="<?php echo $sc_config->itKeywords?>" class="text_area" size="60" /> </td> 
  		  <td> <?php echo $lang->sc_itkeywordsDesc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_itowneremail; ?> </td> 
  		  <td> <input type="text" name="itOwnerEmail" value="<?php echo $sc_config->itOwnerEmail?>" class="text_area" size="40" /> </td> 
  		  <td> <?php echo $lang->sc_itowneremailDesc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_itownername; ?> </td> 
  		  <td> <input type="text" name="itOwnerName" value="<?php echo $sc_config->itOwnerName?>" class="text_area" size="40" /> </td> 
  		  <td> <?php echo $lang->sc_itownernameDesc; ?> </td>
  		</tr>
  			<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_itsubtitle; ?> </td> 
  		  <td> <input type="text" name="itSubtitle" value="<?php echo $sc_config->itSubtitle?>" class="text_area" size="60" /> </td> 
  		  <td> <?php echo $lang->sc_itsubtitleDesc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_itLanguage; ?> </td> 
  		  <td> <input type="text" name="itLanguage" value="<?php echo $sc_config->itLanguage?>" class="text_area" size="60" /> </td> 
  		  <td> <?php echo $lang->sc_itLanguageDesc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_itRedirect; ?> </td> 
  		  <td> <input type="text" name="itRedirect" value="<?php echo $sc_config->itRedirect?>" class="text_area" size="60" /> </td> 
  		  <td> <?php echo $lang->sc_itRedirectDesc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_showhelp; ?> </td> 
  		  <td> <?php echo $lists['mod_showhelp']; ?> </td> 
  		  <td> <?php echo $lang->sc_showhelpDesc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_helpwidth; ?> </td> 
  		  <td> <input type="text" name="sc_helpwidth" value="<?php if ($sc_config->sc_helpwidth) {echo $sc_config->sc_helpwidth;} else {echo "700";}?>" class="text_area" size="8" /> </td> 
  		  <td> <?php echo $lang->sc_helpwidthDesc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_helpheight; ?> </td> 
  		  <td> <input type="text" name="sc_helpheight" value="<?php if ($sc_config->sc_helpheight) {echo $sc_config->sc_helpheight;} else {echo "500";}?>" class="text_area" size="8" /> </td> 
  		  <td> <?php echo $lang->sc_helpheightDesc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->sc_helpeditor; ?> </td> 
  		  <td colspan="2">
        <p><?php echo $lang->sc_helpeditorDesc; ?></p>
        <?php echo $editor->display('sc_helpeditor',$sc_config->sc_helpeditor,'100%','300','40','10');	?> 
        </td>
  		</tr> 
    </table>
    <?php
    echo $tabs->endPanel();
    echo $tabs->startPanel($lang->latestsermonsSettings, 'LatestSermons');
    ?>
    <table class="admintable" cellpadding="10" cellspacing="3">
      <tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->ls_nbr_latest; ?> </td> 
  		  <td> <?php echo $lists['ls_nbr']; ?> </td> 
  		  <td> <?php echo $lang->ls_nbr_latest; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->ls_show_mouseover; ?> </td> 
  		  <td> <?php echo $lists['ls_show_mouseover']; ?> </td> 
  		  <td> <?php echo $lang->ls_show_mouseover_desc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->ls_show_mo_speaker; ?> </td> 
  		  <td> <?php echo $lists['ls_show_mo_speaker']; ?> </td> 
  		  <td> <?php echo $lang->ls_show_mo_speaker_desc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->ls_show_mo_series; ?> </td> 
  		  <td> <?php echo $lists['ls_show_mo_series']; ?> </td> 
  		  <td> <?php echo $lang->ls_show_mo_series_desc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->ls_show_mo_date; ?> </td> 
  		  <td> <?php echo $lists['ls_show_mo_date']; ?> </td> 
  		  <td> <?php echo $lang->ls_show_mo_date_desc; ?> </td>
  		</tr> 
    </table>
    <?php
    echo $tabs->endPanel();
    echo $tabs->startPanel($lang->fronenduploading, 'Frontend Uploading');
    ?>
    <table class="admintable" cellpadding="10" cellspacing="3">
      <tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->fu_enable; ?> </td> 
  		  <td> <?php echo $lists['fu_enable']; ?> </td> 
  		  <td> <?php echo $lang->fu_enable_desc; ?> </td>
  		</tr> 
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->fu_taskname; ?> </td> 
  		  <td> <input type="text" name="fu_taskname" value="<?php echo $config->fu_taskname?>" class="text_area" size="60" /> </td> 
  		  <td> <?php echo $lang->fu_taskname_desc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->fu_pwd; ?> </td> 
  		  <td> <input type="password" name="fu_pwd1" value="" class="text_area" size="60" /> </td> 
  		  <td> <?php echo $lang->fu_pwd_desc; ?> </td>
  		</tr>
  		<tr> 
  		  <td> </td> 
  		  <td> <input type="password" name="fu_pwd2" value="" class="text_area" size="60" /> </td> 
  		  <td> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->fu_destdir; ?> </td> 
  		  <td> <input type="text" name="fu_destdir" value="<?php echo $config->fu_destdir?>" class="text_area" size="60" /> </td> 
  		  <td> <?php echo $lang->fu_destdir_desc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->fu_id3_title; ?> </td> 
  		  <td> <?php echo $lists['fu_id3_title']; ?> </td> </td> 
  		  <td> <?php echo $lang->fu_id3_title_desc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->fu_id3_speaker; ?> </td> 
  		  <td> <?php echo $lists['fu_id3_speaker']; ?> </td> </td> 
  		  <td> <?php echo $lang->fu_id3_speaker_desc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->fu_id3_series; ?> </td> 
  		  <td> <?php echo $lists['fu_id3_series']; ?> </td> </td> 
  		  <td> <?php echo $lang->fu_id3_series_desc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->fu_id3_ref; ?> </td> 
  		  <td> <?php echo $lists['fu_id3_ref']; ?> </td> </td> 
  		  <td> <?php echo $lang->fu_id3_ref_desc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->fu_id3_number; ?> </td> 
  		  <td> <?php echo $lists['fu_id3_number']; ?> </td> </td> 
  		  <td> <?php echo $lang->fu_id3_number_desc; ?> </td>
  		</tr>
  		<tr> 
  		  <td width="100" align="right" class="key"> <?php echo $lang->fu_id3_notes; ?> </td> 
  		  <td> <?php echo $lists['fu_id3_notes']; ?> </td> </td> 
  		  <td> <?php echo $lang->fu_id3_notes_desc; ?> </td>
  		</tr>
    </table>
    <?php
    echo $tabs->endPanel();
    echo $tabs->endPane();
    ?>
    </fieldset> 
		<input type="hidden" name="option" value="<?php echo $option;?>" /> 
		<input type="hidden" name="act" value="config" />
		<input type="hidden" name="task" value="config" /> 
		</form> 
		<?php
  } //end of showConfig
	
	function showComments( $option, &$rows, &$pageNav ) 
	{ 
	  ?> 
	  <form action="index.php" method="post" name="adminForm"> 
	  <table class="adminlist"> 
	    <thead> 
	      <tr> 
	        <th width="20"> 
	        <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" /> 
	        </th> 
	        <th class="title">Review Name</th> 
	        <th width="15%">Commenter</th> 
	        <th width="20%">Comment Date</th> 
	        <th width="30%">Comment</th> 
	      </tr> 
	    </thead> 
	    <?php 
	    jimport('joomla.filter.output'); 
	    $k = 0; 
	    for ($i=0, $n=count( $rows ); $i < $n; $i++) { 
	      $row = &$rows[$i]; 
	      $checked = JHTML::_('grid.id', $i, $row->id ); 
	      $link = JOutputFilter::ampReplace( 'index.php?option=' . $option . '&task=editComment&cid[]='. $row->id ); 
	      ?>
		      <tr class="<?php echo "row$k"; ?>"> 
		        <td><?php echo $checked; ?></td> 
		        <td><a href="<?php echo $link; ?>"><?php echo $row->name; ?></a></td> 
		        <td><?php echo $row->full_name; ?></td> 
		        <td><?php echo JHTML::Date($row->comment_date); ?></td> 
		        <td><?php echo substr($row->comment_text, 0, 149); ?></td> 
		      </tr> 
		      <?php 
		      $k = 1 - $k; 
		    } 
		    ?> 
		  <tfoot> 
		    <td colspan="5"><?php echo $pageNav->getListFooter(); ?></td> 
		  </tfoot> 
		  </table> 
		  <input type="hidden" name="option" 
		                       value="<?php echo $option;?>" /> 
		  <input type="hidden" name="task" value="comments" /> 
		  <input type="hidden" name="boxchecked" value="0" /> 
		  </form> 
		  <?php  
	} 
	
	function editComment ($row, $option) 
	{ 
	  JHTML::_('behavior.calendar'); 
	  ?> 
	  <form action="index.php" method="post" name="adminForm" id="adminForm"> 
	    <fieldset class="adminform"> 
	      <legend>Comment</legend> 
	      <table> 
	      <tr> 
	        <td width="100" align="right" class="key"> 
	          Name: 
	        </td> 
	        <td> 
	          <input class="text_area" type="text" name="full_name" id="full_name" size="50" maxlength="250" value="<?php echo $row->full_name;?>" /> 
	        </td> 
	      </tr>
		      <tr> 
		        <td width="100" align="right" class="key"> 
		          Comment: 
		        </td> 
		        <td> 
		          <textarea class="text_area" cols="20" rows="4" name="comment_text" id="comment_text" style="width:500px"><?php echo $row->comment_text; ?></textarea> 
		        </td> 
		      </tr> 
		      <tr> 
		        <td width="100" align="right" class="key"> 
		          Comment Date: 
		        </td> 
		        <td>          
		          <input class="inputbox" type="text" name="comment_date" id="comment_date" size="25" maxlength="19" value="<?php echo $row->comment_date; ?>" /> 
		          <input type="reset" class="button" value="..." onclick="return showCalendar('comment_date', 'y-mm-dd');" /> 
		        </td> 
		      </tr> 
		      </table> 
		    </fieldset> 
		    <input type="hidden" name="id" value="<?php echo $row->id; ?>" /> 
		    <input type="hidden" name="option" value="<?php echo $option; ?>" /> 
		    <input type="hidden" name="task" value="" /> 
		  </form> 
		  <?php 
	 }
	 
/*********************************************/
/* Help                                      */
/*********************************************/
	 
	 function showHelp() {
	   if (file_exists(JPATH_ADMINISTRATOR.DS.components.DS.'com_sermonspeaker'.DS.'admin.help'.DS.'help'.$this->language.'.php')) {
	     require_one(JPATH_ADMINISTRATOR.DS.components.DS.'com_sermonspeaker'.DS.'admin.help'.DS.'help'.$this->language.'.php');
     } else {
	     require_once(JPATH_ADMINISTRATOR.DS.components.DS.'com_sermonspeaker'.DS.'admin.help'.DS.'help.english.php');
     }
   } // of showHelp()

/*********************************************/
/* Main                                      */
/*********************************************/   
   
   function showmain( $option, $config ) {
		//HTML_sermon:: head( $config );
		$lang = new sermonLang; ?>
		<table class="adminform">
      <tbody><tr>
  	  <td valign="top">
  	   	<div id="cpanel">
  			  <div style="float: left;">
  			    <div class="icon">
  			      <a href="index.php?option=<?php echo $option; ?>&task=speakers">
  			        <img border="0" align="middle" alt="<?php echo $lang->speakersNav; ?>" src="<?php echo JURI::root()."components/com_sermonspeaker/images/speakers.png"; ?>"/>
                <span><?php echo $lang->speakersNav; ?></span>
  			      </a>
  			    </div>
  		    </div>
    			<div style="float: left;">
    			  <div class="icon">
    				  <a href="index2.php?option=<?php echo $option; ?>&task=sermons">
    					  <img border="0" align="middle" alt="<?php echo $lang->sermonsNav; ?>" src="<?php echo JURI::root()."components/com_sermonspeaker/images/sermon.png"; ?>"/>
                <span><?php echo $lang->sermonsNav; ?></span>
    				  </a>
    			 </div>
    		  </div>
      		<div style="float: left;">
      			<div class="icon">
      				<a href="index2.php?option=<?php echo $option; ?>&task=series">
      					<img border="0" align="middle" alt="<?php echo $lang->seriesNav; ?>" src="<?php echo JURI::root()."components/com_sermonspeaker/images/series.png"; ?>"/>
                <span><?php echo $lang->seriesNav; ?></span>
      				</a>
      			</div>
      		</div>
      		<div style="float: left;">
      			<div class="icon">
      				<a href="index2.php?option=com_sermonspeaker&task=config">
      					<img border="0" align="middle" alt="<?php echo $lang->controlPanel; ?>" src="<?php echo JURI::root()."components/com_sermonspeaker/images/controlpanel.png"; ?>"/>
                <span><?php echo $lang->controlPanel; ?></span>
      				</a>
      			</div>
      		</div>
      		<div style="float: left;">
      			<div class="icon">
      				<a href="index2.php?option=com_sermonspeaker&task=stats">
      					<img border="0" align="middle" alt="<?php echo $lang->statistics; ?>" src="<?php echo JURI::root()."components/com_sermonspeaker/images/stats.png"; ?>"/>
                <span><?php echo $lang->statistics; ?></span>
      				</a>
      			</div>
      		</div>
          <div style="float: left;">
      			<div class="icon">
      				<a href="index2.php?option=com_sermonspeaker&task=media">
      					<img border="0" align="middle" alt="<?php echo $lang->mediaManager; ?>" src="<?php echo JURI::root()."components/com_sermonspeaker/images/upload.png"; ?>"/>
                <span><?php echo $lang->mediaManager; ?></span>
      				</a>
      			</div>
      		</div>
    		  <div style="float: left;">
    			  <div class="icon">
    			    <a href="index2.php?option=com_sermonspeaker&task=help">
    				    <img border="0" align="middle" alt="<?php echo $lang->help; ?>" src="<?php echo JURI::root()."components/com_sermonspeaker/images/help.png"; ?>""/>
                <span><?php echo $lang->help; ?></span>
    				  </a>
    			  </div>
    		  </div>
  	    </div>
  	    <div style="clear: both;"> </div>
  	  </td>
    </tr></tbody>
  </table>	
	<?php } // end of showmain
   
/*********************************************/
/* Media Manager                             */
/*********************************************/
	 	 
	function showMedia($dirPath,$listdir ) {
		$lang = new sermonLang; ?>
		<html style="width: 580; height: 440;">
		<style type="text/css">
		<!--
		.buttonHover {
			border: 1px solid;
			border-color: ButtonHighlight ButtonShadow ButtonShadow ButtonHighlight;
			cursor: hand;
		}
		.buttonOut {
			border: 1px solid ButtonFace;
		}

		.separator {
		  position: relative;
		  margin: 3px;
		  border-left: 1px solid ButtonShadow;
		  border-right: 1px solid ButtonHighlight;
		  width: 0px;
		  height: 16px;
		  padding: 0px;
		}
		.manager{
		}
		.statusLayer{
			background:#FFFFFF;
			border: 1px solid #CCCCCC;
		}
		.statusText {
			font-family: Verdana, Arial, Helvetica, sans-serif;
			font-size: 15px;
			font-weight: bold;
			color: #6699CC;
			text-decoration: none;
		}
		-->
		</style>
		</head>

		<script language="javascript" type="text/javascript">
		function dirup() {
			var urlquery=frames['imgManager'].location.search.substring(1);
			var curdir= urlquery.substring(urlquery.indexOf('listdir=')+8);
			var listdir=curdir.substring(0,curdir.lastIndexOf('/'));
			frames['imgManager'].location.href='index3.php?option=com_sermonspeaker&task=listImages&listdir=' + listdir;
		} 

		function goUpDir() {
			var selection = document.forms[0].dirPath;
			var dir = selection.options[selection.selectedIndex].value;
			frames['imgManager'].location.href='index3.php?option=com_sermonspeaker&task=listImages&listdir=' + dir;
		}

		</script>
		<body>
		<form action="index2.php" name="adminForm" method="post" enctype="multipart/form-data" >
		  <table class="adminheading">
			  <td align="center">	  <fieldset>
				<table width="900" align="center" border="0" cellspacing="2" cellpadding="2">
				  <tr>
					<td><table border="0" cellspacing="1" cellpadding="3">
						<tr>
						  <td><?php echo $lang->directory; ?></td>
						  <td> <?php echo $dirPath;?> </td>
						  <td class="buttonOut">
						  <a href="javascript:dirup()"><img src="components/com_media/images/btnFolderUp.gif" width="15" height="15" border="0" alt="Up"></a></td>
						</tr>
					  </table></td>
				  </tr>
				  <tr>
					<td align="center" bgcolor="white"><div name="manager" class="manager">
					<iframe src="index3.php?option=com_sermonspeaker&task=listImages&listdir=<?php echo $listdir?>" name="imgManager" id="imgManager" width="100%" height="200" marginwidth="0" marginheight="0" align="top" scrolling="auto" frameborder="0" hspace="0" vspace="0" background="white"></iframe>
					</div>
					</td>
				  </tr>
				</table>
				</fieldset></td>
			</tr>
			<tr>
			  <td><table border="0" align="center" cellpadding="2" cellspacing="2" width="900">
					<tr>
					<td align="left"><?php echo $lang->upload; ?></td>
					<td><input class="inputbox" type="file" name="upload" id="upload" size="79">&nbsp;</td>
					</tr>
				  <tr>
				  <td align="left"><?php echo $lang->code; ?></td>
				<td><input class="inputbox" type="text" name="imagecode" size="80" /></td>
				  </tr>
				  <tr>
				  <td align="left"><?php echo $lang->createDirectory; ?></td>
					<td><input class="inputbox" type="text" name="foldername" size="80" />
					 </td>
				  </tr>
				</table>

			  </td>
			</tr>
			<tr>
			  <td><div style="text-align: right;">
				</div></td>
			</tr>
		  </table>
		  <input type="hidden" name="option" value="com_sermonspeaker" />
		  <input type="hidden" name="task" value="" />
		  <input type="hidden" name="cb1" id="cb1" value="0">
		  <input type="hidden" name="listdir" value="<?php echo $listdir ?>">
		</form>
		</body>
		</html>
	<?php } // end of showMedia


	//Built in function of dirname is faulty
	//It assumes that the directory name can not contain a . (period)
	function dir_name($dir){
		$lastSlash = intval(strrpos($dir, '/'));
		if($lastSlash == strlen($dir)-1) {
			return substr($dir, 0, $lastSlash);
		} else {
			return dirname($dir);
		}
	} // end of dir_name

	function draw_no_results(){ ?>
		<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
		  <tr>
			<td><div align="center" style="font-size:large;font-weight:bold;color:#CCCCCC;font-family: Helvetica, sans-serif;">No Media Found</div></td>
		  </tr>
		</table>
	<?php } // end of draw_no_results

	function draw_no_dir($listdir) {
		global $BASE_DIR, $BASE_ROOT; ?>
		<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
		  <tr>
			<td><div align="center" style="font-size:small;font-weight:bold;color:#CC0000;font-family: Helvetica, sans-serif;">Configuration Problem: &quot;<?php echo $listdir." / ".$BASE_DIR.$BASE_ROOT; ?>&quot; does not exist.</div></td>
		  </tr>
		</table>
	<?php } // end of draw_no_dir

	function draw_table_header() {
		echo '<table border="0" cellpadding="0" cellspacing="2">';
		echo '<tr>';
	}

	function draw_table_footer() {
		echo '</tr>';
		echo '</table>';
	} // end of draw_table_footer

	function show_image($img, $file, $info, $size, $listdir) {
		$img_file = basename($img);
    $img_url = JURI::root()."components/com_sermonspeaker/media".$listdir."/".$img_file;
		$filesize = HTML_SermonSpeaker::parse_size($size); ?>
		<td>
			<table width="102" border="0" cellpadding="0" cellspacing="2">
				<tr>
					<td align="center" class="imgBorder">
						<a href="javascript:;" onClick="javascript:window.top.document.forms[0].imagecode.value = '<?php echo $img_url;?>';"><img src="<?php echo $img_url; ?>" <?php echo HTML_SermonSpeaker::imageResize($info[0], $info[1], 80); ?> alt="<?php echo $file; ?> - <?php echo $filesize; ?>" border="0"></a>
					</td>
				</tr>
				<tr>
					<td> <?php echo $file; ?> </td>
				</tr>
				<tr>
					<td>
						<table width="100%" border="0" cellspacing="0" cellpadding="2">
							<tr>
								<td width="1%" class="buttonOut">
									<a href="javascript:;" onClick="javascript:window.top.document.forms[0].imagecode.value = '<?php echo $img_url;?>';"><img src="components/com_media/images/edit_pencil.gif" width="15" height="15" border="0" alt="Code"></a>
								</td>
								<td width="1%" class="buttonOut">
									<a href="index2.php?option=com_sermonspeaker&task=deleteFile&delFile=<?php echo $file; ?>&listdir=<?php echo $listdir; ?>" target="_top" onClick="return deleteImage('<?php echo $file; ?>');"><img src="components/com_media/images/edit_trash.gif" width="15" height="15" border="0" alt="Delete"></a>
								</td>
								<td width="98%" class="imgCaption"><?php echo $info[0].'x'.$info[1]; ?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	<?php } // end of show_image

	function show_dir($path, $dir,$listdir) {
		$num_files = HTML_SermonSpeaker::num_files($mosConfig_absolute_path.$path);

		if ($listdir=='/') {
			$listdir='';
		} ?>
		<td>
			<table width="102" border="0" cellpadding="0" cellspacing="2">
				<tr>
					<td align="center" class="imgBorder">
					  <?php $output = ereg_replace('\\\\','/',$listdir.$path)?>
						<a href="index3.php?option=com_sermonspeaker&task=listImages&listdir=<?php echo $output; ?>" target="imgManager" onClick="javascript:updateDir();">
							<img src="components/com_media/images/folder.gif" width="80" height="80" border="0" alt="<?php echo $dir; ?>">
						</a>
					</td>
				</tr>
				<tr>
					<td> <?php echo $dir; ?> </td>
				</tr>
				<tr>
					<td>
						<table width="100%" border="0" cellspacing="1" cellpadding="2">
							<tr>
								<td width="1%" class="buttonOut">
									<a href="index2.php?option=com_sermonspeaker&task=deletefolder&delFolder=<?php echo $path; ?>&listdir=<?php echo $listdir; ?>" target="_top" onClick="return deleteFolder('<?php echo $dir; ?>', <?php echo $num_files; ?>);"><img src="components/com_media/images/edit_trash.gif" width="15" height="15" border="0" alt="Delete"></a>
								</td>
								<td width="99%" class="imgCaption"></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	<?php } // end of show_dir

	function show_doc($doc, $listdir, $icon) { ?>
		<td>
			<table width="102" border="0" cellpadding="0" cellspacing="2">
				<tr>
					<td align="center" class="imgBorder">
						<a href="index3.php?option=com_sermonspeaker&task=listImages&listdir=<?php echo $listdir; ?>" onClick="javascript:window.top.document.forms[0].imagecode.value = '<?php echo $mosConfig_live_site.'/components/com_sermonspeaker/media'.$listdir.'/'.$doc;?>';">
							<img border="0" src="<?php echo $icon ?>" alt="<?php echo $doc; ?>">
						</a>
					</td>
				</tr>
				<tr>
					<td> <?php echo $doc; ?> </td>
				</tr>
				<tr>
					<td>
						<table width="100%" border="0" cellspacing="0" cellpadding="2">
							<tr>
								<td width="1%" class="buttonOut">
								  <?php $listdir = ereg_replace('\\\\','/',$listdir) ?>
									<a href="javascript:;" onClick="javascript:window.top.document.forms[0].imagecode.value = '<a href=&quot;<?php echo $mosConfig_live_site.'/components/com_sermonspeaker/media'.$listdir.'/'.$doc;?>&quot;>Insert your text here</a>';"><img src="components/com_media/images/edit_pencil.gif" width="15" height="15" border="0" alt="Code"></a>
								</td>
								<td width="1%" class="buttonOut">
									<a href="index2.php?option=com_sermonspeaker&task=deleteFile&delFile=<?php echo $doc; ?>&listdir=<?php echo $listdir; ?>" target="_top" onClick="return deleteImage('<?php echo $doc; ?>');"><img src="components/com_media/images/edit_trash.gif" width="15" height="15" border="0" alt="Delete"></a>
								</td>
								<td width="98%" class="imgCaption"></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	<?php } // end of show_doc

	function parse_size($size){
		if($size < 1024) {
			return $size.' bytes';
		}
		else if($size >= 1024 && $size < 1024*1024) {
			return sprintf('%01.2f',$size/1024.0).' Kb';
		}
		else {
			return sprintf('%01.2f',$size/(1024.0*1024)).' Mb';
		}
	} // end of parse_size

	function imageResize($width, $height, $target) {
		//takes the larger size of the width and height and applies the
		//formula accordingly...this is so this script will work
		//dynamically with any size image
		if ($width > $height) {
			$percentage = ($target / $width);
		} else {
			$percentage = ($target / $height);
		} 

		//gets the new value and applies the percentage, then rounds the value
		$width = round($width * $percentage);
		$height = round($height * $percentage);

		//returns the new sizes in html image tag format...this is so you
		//can plug this function inside an image tag and just get the
		return "width=\"$width\" height=\"$height\"";
	} // end of imageResize

	function num_files($dir) {
		$total = 0;

		if(is_dir($dir)) {
			$d = @dir($dir);
			while (false !== ($entry = $d->read())) {
				//echo $entry."<br>";
				if(substr($entry,0,1) != '.') {
					$total++;
				}
			}
			$d->close();
		}
		return $total;
	} // end of num_files


	function imageStyle($listdir){ ?>
		<script language="javascript" type="text/javascript">
		function updateDir(){
			var allPaths = window.top.document.forms[0].dirPath.options;
			for(i=0; i<allPaths.length; i++) {
				allPaths.item(i).selected = false;
				if((allPaths.item(i).value)== '<?php if (strlen($listdir)>0) { echo ereg_replace('\\\\','/',$listdir) ;} else { echo '/';}  ?>') {
					allPaths.item(i).selected = true;
				}
			}
		} 

		function deleteImage(file)
		{
			if(confirm("Delete file \""+file+"\"?"))
			return true;

			return false;
		}
		function deleteFolder(folder, numFiles)
		{
			if(numFiles > 0) {
				alert("There are "+numFiles+" files/folders in \""+folder+"\".\n\nPlease delete all files/folder in \""+folder+"\" first.");
				return false;
			}

			if(confirm("Delete folder \""+folder+"\"?"))
			return true;

			return false;
		} 
		</script>
		</head>
		<body onLoad="updateDir()">
		<style type="text/css">
		<!--
		.imgBorder {
			height: 96px;
			border: 1px solid threedface;
			vertical-align: middle;
		}
		.imgBorderHover {
			height: 96px;
			border: 1px solid threedface;
			vertical-align: middle;
			background: #FFFFCC;
			cursor: hand;
		}
		.buttonHover {
			border: 1px solid;
			border-color: ButtonHighlight ButtonShadow ButtonShadow ButtonHighlight;
			cursor: hand;
			background: #FFFFCC;
		}
		.buttonOut{
		 border: 0px;
		}
		.imgCaption {
			font-size: 9pt;
			font-family: "MS Shell Dlg", Helvetica, sans-serif;
			text-align: center;
		}
		.dirField {
			font-size: 9pt;
			font-family: "MS Shell Dlg", Helvetica, sans-serif;
			width:110px;
		}
		-->
		</style>
	<?php } // end of imageStyle

/*********************************************/
/* Statistics                                */
/*********************************************/

function showstats( $rows1, $rows2, $rows3, $option, $config ) {
	$lang = new sermonLang; ?>
	<table border="0" cellpadding="2" cellspacing="0" width="40%" class="adminlist">
		<tr>
			<td style="background-color: #6D86BE; color: #CCC;" colspan="4">
				<img src="<?php echo JURI::root(); ?>components/com_sermonspeaker/images/speakers.png" border="0" width="48" height="48" alt="<?php echo $lang->speakers; ?>" /> <?php echo $lang->speakerStats; ?>
			</td>
		</tr>
		<tr>
			<th width="20" align="left"><?php echo $lang->id; ?></th>
			<th align="left"><?php echo $lang->speaker; ?></th>
			<th width="20" align="left"><?php echo $lang->count; ?></th>
			<th width="20" align="left"><?php echo $lang->reset; ?></th>
		</tr>
		<?php
			$k = 0;
			for ($i=0, $n=count( $rows1 ); $i < $n; $i++) {
				$row = &$rows1[$i];
				echo "<tr class=\"row$k\">";
				echo "	<td align=\"left\">$row->id</td>";
				echo "	<td align=\"left\">$row->name</td>";
				echo "	<td align=\"left\">$row->hits</td>";
				echo "	<td align=\"center\"><a href=\"index.php?option=$option&task=resetcount&table=speakers&id=$row->id\"><img src=\"../components/com_sermonspeaker/images/reset.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"Reset\" /></a></td>";
				echo "</tr>";
				$k = 1 - $k;
			} ?>
	</table>

	<table border="0" cellpadding="2" cellspacing="0" width="40%" class="adminlist">
		<tr>
			<td style="background-color: #6D86BE; color: #CCC;" colspan="4">
				<img src="<?php echo JURI::root(); ?>components/com_sermonspeaker/images/series.png" border="0" width="48" height="48" alt="<?php echo $lang->series; ?>" /> <?php echo $lang->seriestats; ?>
			</td>
		</tr>
		<tr>
			<th width="20" align="left"><?php echo $lang->id; ?></th>
			<th align="left"><?php echo $lang->series; ?></th>
			<th width="20" align="left"><?php echo $lang->count; ?></th>
			<th width="20" align="left"><?php echo $lang->reset; ?></th>
		</tr>
		<?php
			$k = 0;
			for ($i=0, $n=count( $rows2 ); $i < $n; $i++) {
				$row = &$rows2[$i];
				echo "<tr class=\"row$k\">";
				echo "<td align=\"left\">$row->id</td>";
				echo "<td align=\"left\">$row->series_title</td>";
				echo "<td align=\"left\">$row->hits</td>";
				echo "<td align=\"center\"><a href=\"index.php?option=$option&task=resetcount&table=series&id=$row->id\"><img src=\"../components/com_sermonspeaker/images/reset.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"Reset\" /></a></td>";
				echo "</tr>";
				$k = 1 - $k;
			} ?>
	</table>

	<table border="0" cellpadding="2" cellspacing="0" width="40%" class="adminlist">
		<tr>
			<td style="background-color: #6D86BE; color: #CCC;" colspan="4">
				<img src="<?php echo JURI::root(); ?>/components/com_sermonspeaker/images/sermon.png" border="0" width="48" height="48" alt="<?php echo $lang->sermons; ?>" /> <?php echo $lang->sermonstats; ?>
			</td>
		</tr>
		<tr>
			<th width="20" align="left"><?php echo $lang->id; ?></th>
			<th align="left"><?php echo $lang->sermon; ?></th>
			<th width="20" align="left"><?php echo $lang->count; ?></th>
			<th width="20" align="left"><?php echo $lang->reset; ?></th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows3 ); $i < $n; $i++) {
			$row = &$rows3[$i];
			echo "<tr class=\"row$k\">";
			echo "<td align=\"left\">$row->id</td>";
			echo "<td align=\"left\">$row->sermon_title</td>";
			echo "<td align=\"left\">$row->hits</td>";
			echo "<td align=\"center\"><a href=\"index.php?option=$option&task=resetcount&table=sermons&id=$row->id\"><img src=\"../components/com_sermonspeaker/images/reset.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"Reset\" /></a></td>";
			echo "</tr>";
			$k = 1 - $k;
		} ?>
	</table>
<?php } // end of showstats


//end
}
?>
