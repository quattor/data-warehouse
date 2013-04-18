<?php
    include("header.inc.php");
?>


<script type="text/javascript" charset="utf-8" src="js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf-8" src="js/dataTables.bootstrap.js"></script>
<script type="text/javascript" charset="utf-8" src="js/config.inc.js"></script>

<div class="container">
	<div class="row-fluid">
		<div class="span12">
			<form name="Search" class="form-inline">
				<div class="input-append">
					<input class="input-xxlarge" data-toggle="tooltip" data-placement="top" data-animation="true" title="Path to a value, e.g. /hardware/model" type="text" placeholder="Attribute" name="attribute" id="attribute" onkeydown="if (event.keyCode == 13) getData()" value="<?php echo $_GET["attribute"] ?>"/>
					<input data-toggle="tooltip" data-placement="top" data-animation="true" title="A value of an attribute, e.g. wn-2007-streamline" type="text" placeholder="Value" name="value" id="value" onkeydown="if (event.keyCode == 13) getData()" value="<?php echo $_GET["value"] ?>"/>
					<button class="btn btn-primary" type="button" data-loading-text="Loading..." id="Search" onclick="getData()">Quattorise</button>
				</div>
			</form>
			
			<div class="results" id="results">
				<div class="loading" id="loading"></div>
				<table class="table table-striped table-bordered" id="resultstable"></table>
			</div>

			<script type="text/javascript">
				$(function() {
					$( "#attribute" ).autocomplete ({
						source: quattorKeylist,
						minLength: 0
					});
					$('#attribute').dblclick(function() {
						$( "#attribute" ).autocomplete("search", "");
					});
				});
				
				//Pops open the info box for a specific node
				var nodewin = null;
				function node(n) {
				  nodewin = window.open(INFO_URL+n, "node", "width=640,height=480,left=128,top=128,resizable=yes,scrollbars=yes,directories=no,titlebar=no,toolbar=no,status=no"); 
				  nodewin.window.focus();
				}

				$(document).ready(function() {
					$("#attribute").tooltip();
					$("#value").tooltip();
					$('#resultstable').dataTable( {
						"sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
						"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
							text = $('td:eq(0)', nRow).html();
							if (text.indexOf("<span ") < 0) {
								$('td:eq(0)', nRow).html("<a href='#' onclick=\"node('"+text+"')\">"+text+"</a>");
							}
							return nRow;
						},
						"bJQueryUI": true,
						"sPaginationType": "bootstrap",
							"aaData": [],
							"aoColumns": [
								{ "sTitle": "Machine Names" }
							],
						"iDisplayLength": 20,
						"aLengthMenu": [
							[20, 40, 80, 160, -1], [20, 40, 80, 160, "All"]
						]
					});
					$.extend( $.fn.dataTableExt.oStdClasses, {
						"sWrapper": "dataTables_wrapper form-inline"
					} );
					getData();
				});
				
				function getData() {
					var x = document.forms["Search"]["attribute"].value;
					var y = document.forms["Search"]["value"].value;
					if (x == null || x == "") {
						return false
					}
					else if (y == null || y == "") {
						return false
					}
					else {
						$("#loading").show();
						document.getElementById("Search").disabled=true;
						document.getElementById("attribute").disabled=true;
						document.getElementById("value").disabled=true;        
						$.get(
							'ServerAttribute-json.php',
							{ attribute: document.forms["Search"]["attribute"].value, value: document.forms["Search"]["value"].value },
							function(response, status, xhr) {
								var data = eval(response);
								var oTable = $("#resultstable").dataTable();
								oTable.fnClearTable();
								oTable.fnAddData(data);
								document.getElementById("Search").disabled=false;
								document.getElementById("attribute").disabled=false;
								document.getElementById("value").disabled=false;
								$("#loading").hide();
							}
						);
					}
				}
			</script>
		</div>
	</div>
</div>

<?php
    include("footer.inc.php");
?>
