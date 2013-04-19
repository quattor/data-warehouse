<?php
	include("header.inc.php");
?>
<script type="text/javascript" charset="utf-8" src="js/jquery.jqplot.min.js"></script>
<script type="text/javascript" charset="utf-8" src="js/jqplot.pieRenderer.min.js"></script>
<script type="text/javascript" charset="utf-8" src="js/jqplot.categoryAxisRenderer.min.js"></script>
<script type="text/javascript" charset="utf-8" src="js/html5slider.min.js"></script>
<div class="container">
	<div class="row-fluid">
		<div class="span12">
				
			<script type="text/javascript">
				function pathList() { 
					$( "#attribute" ).autocomplete ({
						source: quattorKeylist,
						minLength: 0,
						close: function( event, ui ) {
							validateDistributionForm();
						}
					});
				}
				
				$(function() {
					$('#attribute').dblclick(function() {
						$( "#attribute" ).autocomplete("search", "");
					});
				});
				
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
						extra = "<h3 id=\"thresholdtableheader\">Below Threshold</h3><table class=\"table table-striped table-bordered\"><tr><th>Value</th><th>Count</th></tr>";
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
					var x = $("#attribute").val();
					if (x == null || x == "") {
						$('#enter').prop("disabled", true);
					}
					else {
						$('#enter').prop("disabled", false);
					}
				}
				
				function submitDistributionForm() {
					var x = $("#attribute").val();
					
					if (x[0] !== "/") {
						x = "/" + x;
					}
					document.forms["Analyse"]["attribute"].value = x;
					document.getElementById("enter").disabled=true;
					document.getElementById("attribute").disabled=true;
					$('#thresholdTable').hide();
					document.getElementById("chartdiv").innerHTML = "";
					document.getElementById("chartdiv").style.backgroundImage = "url('images/loadingred.gif')";
					$.get('DistributionIndex-json.php', {attribute: x},
						function(response, status, xhr) {
							if(response.length > 0) {
								document.piedata = eval(response);
								$( "#chartdiv" ).show();
								drawPiechart();
								thresholdTable();
								document.getElementById("enter").disabled=false;
								document.getElementById("attribute").disabled=false;
								$( "#slider" ).show().effect("fade", {'mode' : 'show'});
								pathList();
							} else {
								document.getElementById("enter").disabled=false;
								document.getElementById("attribute").disabled=false;
								document.getElementById("chartdiv").innerHTML = "<p class=\"error\" style=\"text-align: center\">No data found for attribute: "+x+"</p>";
								document.getElementById("chartdiv").style.backgroundImage = "";
							}
						}
					);
				}
				
				$(document).ready(function() {
					$("#attribute").tooltip();
					$("#attribute").keyup(function() {
						validateDistributionForm();
					});
					$("#attribute").change(function() {
						validateDistributionForm();
					});
					validateDistributionForm();
					$("#enter").click(submitDistributionForm);
					$( "#chartdiv" ).hide();
					$( "#slider" ).hide();
					$( '#thresholdTable' ).hide();
					pathList();
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
								$("#thresholdTable").effect("fade", {'mode' : 'toggle'});
							}
						}
					);
					$('#chartdiv').bind('jqplotDataMouseOver',
						function (ev, seriesIndex, pointIndex, data) {
							$("#datainfotext").html("<strong>" + data[0] + "</strong><br />" + data[1] + " profiles");
							$("#datainfo").css("left", ev.pageX + 8 + "px");
							$("#datainfo").css("top",  ev.pageY -8 + "px");
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
					$("#slider").change (
						function() {
							document.piethreshold = $("#slider").val();
							$("#sliderval").html(document.piethreshold);
							drawPiechart();
							thresholdTable();
						}
					);
			   });
			</script>
				
			<div class="row-fluid">
				<form name="Analyse" class="form-inline">
					<div class="input-append">
						<input class="input-xxlarge"  data-toggle="tooltip" data-placement="top" data-animation="true" title="Path to a value, e.g. /hardware/model" type="text" placeholder="Attribute" name="attribute" id="attribute" onkeydown="if (event.keyCode == 13) {submitDistributionForm(); return(false)}" value="<?php echo $_GET["attribute"] ?>" />
						<button class="btn btn-primary" type="button" id="enter">Quattorise</button>
					</div>
					<label for="slider">Threshold: <span id="sliderval">0</span></label>
					<input type="range" id="slider" value="0" />
				</form>
			</div>
			
			<div id="datainfo" class="tooltip fade right in">
				<div class="tooltip-arrow"></div>
				<div id="datainfotext" class="tooltip-inner"></div>
			</div>
			<div class="row">
				<div id="chartdiv" class="span9"></div>
				<div id="thresholdTable" class="span3" style="display:none"></div>
			</div>
		</div>
	</div>
</div>

<?php
	include("footer.inc.php");
?>
