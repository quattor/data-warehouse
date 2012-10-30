<?php
	include("header.inc.php");
?>
<script type="text/javascript" charset="utf-8" src="src/ui/minified/jquery.ui.progressbar.min.js"></script>
<script type="text/javascript" charset="utf-8" src="src/jquery.jqplot.min.js"></script>
<script type="text/javascript" charset="utf-8" src="src/plugins/jqplot.pieRenderer.min.js"></script>
<script type="text/javascript" charset="utf-8" src="src/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<link rel="stylesheet" type="text/css" href="src/jquery.jqplot.min.css" />
<link rel="stylesheet" type="text/css" href="css/styleforpiechart.css" />

<script type="text/javascript">
function updateSlider() {
    $( "#slider" ).slider("option", "value", document.getElementById("threshold").value);
}

function drawPiechart() {
	var data = document.piedata;
	document.getElementById("chartdiv").style.backgroundImage = "none";
	var threshold = document.piethreshold;
	var other = 0;
	var abovethreshold = [];
	var belowthreshold = [];

	for (row in data) {
		row = data[row];
		if (row[1] <= threshold) {
			other = other + row[1];
			belowthreshold.push(row);
		}
		else {
			abovethreshold.push(row);
		}
	}

	if (other > 0) {
		abovethreshold.push(["Below Threshold", other]);
	}
	document.belowthreshold = belowthreshold;

	document.plot1 = jQuery.jqplot ('chartdiv', [abovethreshold], {
		grid: {
		    drawBorder: false,
		    drawGridlines: false,
		    background: '#ffffff',
		    shadow:false
		},
		seriesDefaults: {
		    // Make this a pie chart.
		    renderer: jQuery.jqplot.PieRenderer,
		    rendererOptions: {
			// Put data labels on the pie slices.
			// By default, labels show the percentage of the slice.
			showDataLabels: true,
			startAngle: 180,
			dataLabelFormatString: null,
			dataLabelPositionFactor: 0.80
		    }      
		},
		highlighter: {
		    show: true,
		    sizeAdjust: 7.5
		},	
	});
}

function thresholdTable() {
	var extra = "";
	var x=document.forms["Analyse"]["attribute"].value;
	if (document.belowthreshold.length > 0) {
		extra = "<h3 id=\"thresholdtableheader\">Below Threshold</h3><table class=\"belowthreshold\"><tr><th>Value</th><th>Count</th></tr>";
		for (row in document.belowthreshold) {
			row = document.belowthreshold[row];
			extra = extra + "<tr><td>";
			if (row[0] != "No Data") {
				extra = extra + "<a href=\"ServerAttribute.php?attribute="+x+"&value="+row[0]+"\">"+row[0]+"</a>";
			}
			else {
				extra = extra + row[0];
			}
			extra = extra + "</td><td>"+row[1] + "</td></tr>";
		}
		extra = extra + "</table>";
	}
	$("#thresholdTable").html(extra);
}

function validateDistributionForm() {
    var x = document.forms["Analyse"]["attribute"].value;
    if (x == null || x == "") {
	  $("#alertBox").dialog("open");
      return false 
	}
    else {
		if (x[0] !== "/") {
			x = "/" + x;
		}
		document.forms["Analyse"]["attribute"].value = x;
		$( "#sliderbox:visible" ).hide().effect("blind");
    	document.getElementById("enter").disabled=true;
		document.getElementById("attribute").disabled=true;
    	$('#thresholdTable').hide();
        document.getElementById("chartdiv").innerHTML = "";
        document.getElementById("chartdiv").style.backgroundImage = "url('images/loadingred.gif')";
        $.get('DistributionIndex-json.php', {attribute: x},
            function(response, status, xhr) {
				if(response.length > 0) {
					document.piedata = eval(response);
					drawPiechart();
					//plot1.replot();
					thresholdTable();
					document.getElementById("enter").disabled=false;
					document.getElementById("attribute").disabled=false;
					$( "#sliderbox" ).show().effect("blind", {'mode' : 'show'});
					$( "#slider" ).slider( "option", "disabled", false );
					updateHistory(x);
				}
				else {
					document.getElementById("enter").disabled=false;
					document.getElementById("attribute").disabled=false;
					document.getElementById("chartdiv").innerHTML = "<p class=\"error\" style=\"text-align: center\">No data found for attribute: "+x+"</p>";
					document.getElementById("chartdiv").style.backgroundImage = "";
				}
			}
		);	          
    }
}


$(document).ready(function() {
	$( "#sliderbox" ).hide();
	$('#thresholdTable').hide();
	updateHistory();
	$('#chartdiv').bind('jqplotDataClick',
		function (ev, seriesIndex, pointIndex, data) {
			if (data[0] == "No Data") {
				return false
			}
			if (data[0] != "Below Threshold") {
				var x = document.forms["Analyse"]["attribute"].value;
				window.location = "ServerAttribute.php?attribute="+x+"&value="+data[0];
			}
			else {
				$("#thresholdTable").effect("blind", {'mode' : 'toggle'});
			}
	    }
	);
	$('#chartdiv').bind('jqplotDataMouseOver',
	    function (ev, seriesIndex, pointIndex, data) {
	    	$("#datainfo").html("<strong>" + data[0] + "</strong>&nbsp;:&nbsp;" + data[1] + " profiles");
			$("#datainfo").css("left", (ev.pageX - $("#primaryContent_columnless").offset().left + 8) + "px");
			$("#datainfo").css("top",  (ev.pageY - $("#primaryContent_columnless").offset().top  + 8)  + "px");
	    }
	);
	$('#chartdiv').bind('jqplotDataHighlight',
	    function (ev, seriesIndex, pointIndex, data) {
	            $("#datainfo").css("visibility","visible");
	    }
	);
	$('#chartdiv').bind('jqplotDataUnhighlight',
	    function (ev, seriesIndex, pointIndex, data) {
	            $("#datainfo").css("visibility","hidden");
	    }
	);
	$.cookieCuttr({
		"cookieDeclineButton" : true,
		"cookieAnalyticsMessage" : "We use cookies to store search history. We don't track users or store personal information."
	});
	if (jQuery.cookie('cc_cookie_accept') == "cc_cookie_accept") {	
	}
});
</script>

<div id="alertBox">
	A value is required to be able to analyse
</div>

<div class="distributionAttribute">
	<form name="Analyse">
		<div>
		    <label for="attribute">Attribute</label>
		    <input type="text" name="attribute" id="attribute" onkeydown="if (event.keyCode == 13) {validateDistributionForm(); return(false)}" value="<?php echo $_GET["attribute"] ?>" />
		</div>
		<div id="sliderbox">
			Minimum profile count: <span id="sliderval">0</span>
			<div id="slider"></div>	 
		</div>
		<div>
	    	    <input class="buttontheme" type="button" id="enter" value="Quattorise" onclick="validateDistributionForm()" /> 
		</div>
	</form>
</div>

<script>
    $(function () {
        $("#slider").slider({
		    change: function(event, ui) {
				document.piethreshold = ui.value;
				drawPiechart();
				thresholdTable();
			},
			slide: function(event, ui) {
				$("#sliderval").html(ui.value);
			},
			create: function(event, ui) {
				ui.value = 0;
			}
		});
		$("#alertBox").dialog({ autoOpen: false });
   });
</script>

<div id="datainfo"></div>

<div id="chartdiv"></div>

<div id="thresholdTable"></div>

<?php
	include("footer.inc.php");
?>
