<?php 
//
?>
<script src='http://code.jquery.com/jquery-latest.js'></script>
<script language="javascript">
//var $j = jQuery.noConflict();
var sermonLineHeight = <?php echo $ss_lineSize;?>;
var sermonsBoxHeight = <?php echo $ss_boxHeight;?>;
var sermonBoxWidth = <?php echo $ss_boxWidth;?>;
var sermonImageWidth = <?php echo $ss_imageWidth;?>;
var sermonWidth = <?php echo $sernonWidth ;?>;
var beginingSermon = 0;
var showImageFlg = <?php echo $ss_showImageFlg;?>; 
var showArrowFlg = <?php echo $ss_showArrowFlg;?>; 
var maxSermons = <?php echo $ss_maxSermons;?>;
var showSermons = <?php echo $ss_showSermons;?>;
var sermonElementID = "";
var sermonElementIDPrev = "";
var slideDur = <?php echo $ss_slideDur;?>;
var fadetime = <?php echo $ss_FadeTrans;?>; 
var sermonBoxMiddle = sermonsBoxHeight / 2;
var sermonsAboveCurr = 1;
var sermonsBelowCurr = 1; 
var t ;
$(window).bind("load", function() {
	t = setTimeout(function() { runSermons(0, 1); }, slideDur);
	if(showArrowFlg)
	{
		findArrowLocatactionSlide(0);
	}
});

if (showSermons%2 == 0)
{
	sermonsAboveCurr = showSermons / 2;
	sermonsBelowCurr = showSermons / 2;
	showSermons = showSermons +1;
}
else
{
	sermonsAboveCurr = (showSermons-1) / 2;
	sermonsBelowCurr = (showSermons-1) / 2;
}
function rotateImages(last, current)
{
	clearTimeout(t);
	sermonElementID = last;		  // leave there needs to stay global
	sermonElementIDPrev = current; // leave there needs to stay global	
	if(sermonElementIDPrev == "")
	{
		sermonElementIDPrev = 0;
	}
	for (x = 0; x < maxSermons; x++)
	{
		$(document.getElementById('sermons-rotator-right-'+x)).fadeOut(fadetime/2).css({"visibility" : "hidden", "display" : "none" });  
		$(document.getElementById('sermon-rotator-'+x)).removeClass("sermon-rotator-current-"+sermonLineHeight);
	}
	var currSermonRotator = $(document.getElementById('sermon-rotator-'+sermonElementID));
	var currSermonRight = $(document.getElementById('sermons-rotator-right-'+sermonElementID));
	var sermonDiff = 0;
	var sermonDiffPixel = 0;
	currSermonRotator.addClass("sermon-rotator-current-"+sermonLineHeight);
	if(showArrowFlg)
	{
		findArrowLocatactionSlide(sermonElementID);
	}		
	currSermonRight.fadeIn(fadetime/2).css({"visibility" : "visible", "display" : "inline" , "cursor" : "pointer" });
	sermonDiff = Math.abs(sermonElementID - sermonElementIDPrev);
		
	if(sermonElementIDPrev <= sermonElementID)
	{
		//used to move down in natural order
		<?php if($ss_showSermons == 3){ ?>
			for(x = 0; x < sermonsBelowCurr+1; x++)
			{
				if((sermonElementIDPrev == 0)&&(sermonElementID == (x+1)))
				{
					sermonDiff = x;
				}
			}
			if((sermonElementIDPrev == (maxSermons-2))&&(sermonElementID == (maxSermons-1)))
			{
				sermonDiff = 0;
			}		
		<?php } if($ss_showSermons == 5){?>
		for(m = 0; m < sermonsAboveCurr ; m++)
		{
			if((sermonElementIDPrev == m)&&(sermonElementID == (m+1)))
			{
					sermonDiff = 0;
			}
		}
		for(n = 0 ; n < showSermons; n++ )
		{
			if((sermonElementIDPrev == 0)&&(sermonElementID == (n+2)))
			{
				sermonDiff = n;
			}
		}
		var o = sermonsAboveCurr-1;
		for(t = (maxSermons-sermonsBelowCurr-2); t <maxSermons; t++ )
		{
			if((sermonElementIDPrev == t)&&(sermonElementID == (t+2)))
			{
				sermonDiff = o;
			}
			o--;
		}
		for(r = (maxSermons-sermonsBelowCurr-1); r< maxSermons; r++)
		{
			if((sermonElementIDPrev == r)&&(sermonElementID == (r+1)))
			{
				sermonDiff = 0;
			}
		}
		for(q = 0; q < sermonsAboveCurr; q++ )
		{
			if((sermonElementIDPrev == 1)&&(sermonElementID == (q+3)))
			{
				sermonDiff = q+1;
			}
		}	
		<?php }?>
		
		if(sermonDiff != 0)
		{
			sermonDiffPixel = sermonDiff * sermonLineHeight;
		}
		else 
		{
			sermonDiffPixel = 0;	
		}
		//alert("last:"+sermonElementIDPrev+"|current:"+sermonElementID+"|diff:"+sermonDiff+"|pixels:"+sermonDiffPixel+"|moveDown");
		$(document.getElementById('sermons-rotator-left-slide')).animate({"top": "-="+sermonDiffPixel+"px"}, fadetime);
		if(showArrowFlg)
		{
			$(document.getElementById('sermons-rotator-arrow')).animate({"top": "-="+sermonDiffPixel+"px"}, fadetime);
		}
	}
	else
	{
		// used to move up in reverse order
		<?php if($ss_showSermons == 3){ ?>
			if((sermonElementIDPrev == 1)&&(sermonElementID == 0))
			{
				sermonDiff = 0;
			}
			var d = 1;
			for(y = maxSermons-showSermons; y < maxSermons; y++)
			{
				if((sermonElementIDPrev == (maxSermons-sermonsAboveCurr))&&(sermonElementID == y))
				{
					sermonDiff = d;
				}				
				d--;
			}	
			
		<?php } if($ss_showSermons == 5){?>
		for(y = 0; y < sermonsAboveCurr; y++)
		{
			if((sermonElementIDPrev == (y+1))&&(sermonElementID == y))
			{
				sermonDiff = 0;
			}
		}
		for (x = 0; x < sermonsAboveCurr; x++)
		{
			if((sermonElementIDPrev == (x + sermonsAboveCurr))&&(sermonElementID == x))
			{
					sermonDiff = x;
			}
		}
		var a = sermonsBelowCurr - 1;
		for(w = (maxSermons-sermonsBelowCurr); w < maxSermons; w++)
		{
			if((sermonElementIDPrev == w)&&(sermonElementID == (w-sermonsBelowCurr) ))
			{
				sermonDiff = a;
			}
			a--;
		}
		for(u = (maxSermons-sermonsBelowCurr-1); u < maxSermons; u++)
		{
			if((sermonElementIDPrev == (u+1))&&(sermonElementID == u))
			{
				sermonDiff = 0;
			}				
		}
		b = sermonsBelowCurr;
		for(y = (maxSermons-sermonsBelowCurr); y < maxSermons; y++)
		{
			if((sermonElementIDPrev == y)&&(sermonElementID == (y-3)))
			{
				sermonDiff = b;
			}
			b--;
		}
		for(i = (maxSermons-1); i < maxSermons; i++)
		{
			// partial macro 14 jump to 10
			if((sermonElementIDPrev == i)&&(sermonElementID == (i-4)))
			{
				sermonDiff = 2;
			}
		}
		<?php }?>
		
		if(sermonDiff != 0)
		{
			sermonDiffPixel = sermonDiff * sermonLineHeight;
		}
		else 
		{
			sermonDiffPixel = 0;	
		}
		//alert("last:"+sermonElementIDPrev+"|current:"+sermonElementID+"|diff:"+sermonDiff+"|pixels:"+sermonDiffPixel+"|aboveCurr:"+sermonsAboveCurr+"|moveUp");
		$(document.getElementById('sermons-rotator-left-slide')).animate({"top": "+="+sermonDiffPixel+"px"}, fadetime);
		if(showArrowFlg)
		{
			$(document.getElementById('sermons-rotator-arrow')).animate({"top": "+="+sermonDiffPixel+"px"}, fadetime);
		}
	}
	var nextSermon = sermonElementID + 1;
	t = setTimeout(function() { runSermons(sermonElementID, nextSermon); }, slideDur);
}
function findArrowLocatactionSlide(id)
{

	var sermonRotatorCurrent = $(".sermon-rotator-current-"+sermonLineHeight);
	var sermonRotatorCurrentPos = sermonRotatorCurrent.position();
	var sermonRotatorCurrentPosX = sermonRotatorCurrentPos.top;
	var sermonRotatorCurrentPosY = sermonRotatorCurrentPos.left;	
	
	var currSermonRotatorSlidePos = $(document.getElementById('sermons-rotator-left-slide'));
	var currSermonSlidePos = currSermonRotatorSlidePos.position();
	var currSermonSlidePosX = currSermonSlidePos.top;
	
	var currSermonRotatorPos = $(document.getElementById('sermon-rotator-'+id));
	var currSermonPos = currSermonRotatorPos.position();
	var currSermonPosX = currSermonPos.top;
	var currSermonPosY = currSermonPos.left;

	var sermonRotatorMain = $(document.getElementById('sermons-rotator-main'));
	var sermonRotatorMainPos = sermonRotatorMain.position();
	var sermonRotatorMainPosX = sermonRotatorMainPos.top;
	var sermonRotatorMainPosY = sermonRotatorMainPos.left;
	
	var currSermonArrowPosY = currSermonPosY + sermonWidth + sermonRotatorMainPosY;	
	var sermonArrow = $(document.getElementById('sermons-rotator-arrow')); 
	
	var posx = sermonRotatorCurrentPosX + currSermonSlidePosX ;
	sermonArrow.css({"position" : "absolute", "top" : posx, "left" : currSermonArrowPosY});
}

function runSermons(last, current)
{
	$(document.getElementById('sermon-rotator-'+current)).addClass("sermon-rotator-current-"+sermonLineHeight);	
	$(document.getElementById('sermons-rotator-right-'+last)).fadeOut(fadetime/2).css({"visibility" : "hidden", "display" : "none" }); 	
	$(document.getElementById('sermon-rotator-'+last)).removeClass("sermon-rotator-current-"+sermonLineHeight);	
	$(document.getElementById('sermons-rotator-right-'+current)).fadeIn(fadetime/2).css({"visibility" : "visible", "display" : "inline" , "cursor" : "pointer" });	
	if(showArrowFlg)
	{
		findArrowLocatactionSlide(current);
	}
	if(( showSermons <= (maxSermons - (current-sermonsBelowCurr) ))&&(current > sermonsAboveCurr ))
	{
		$(document.getElementById('sermons-rotator-left-slide')).animate({"top": "-="+sermonLineHeight+"px"}, fadetime);
		if(showArrowFlg)
		{
			$(document.getElementById('sermons-rotator-arrow')).animate({"top": "-="+sermonLineHeight+"px"}, fadetime);
		}
	}

	sermonElementID = current; 
	sermonElementIDPrev = last;
	last = current;
	current ++;
	if(current < maxSermons)
	{
		t = setTimeout(function() { runSermons(last, current); }, slideDur);
	}	
}
function prepRotator()
{
	$(document.getElementById('sermon-rotator-'+beginingSermon)).addClass("sermon-rotator-current-"+sermonLineHeight);
	$(document.getElementById('sermons-rotator-right-'+beginingSermon)).fadeIn(fadetime/2).css({"visibility" : "visible", "display" : "inline" , "cursor" : "pointer" });
}
</script>