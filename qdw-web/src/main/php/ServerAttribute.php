<?php
	include("header.inc.php");
?>
<script type="text/javascript" charset="utf-8" src="js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf-8" src="js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" charset="utf-8" src="js/config.inc.min.js"></script>
<div class="container">
	<div class="row-fluid">
		<div class="span12">
			<form name="Search" class="form-inline">
				<div class="input-append">
					<input class="input-xxlarge" data-toggle="tooltip" data-placement="top" data-animation="true" title="Path to a value, e.g. /hardware/model" type="text" placeholder="Attribute" name="attribute" id="attribute" onkeydown="if (event.keyCode == 13) getData()" value="<?php echo $_GET["attribute"] ?>"/>
					<input class="input-xlarge" data-toggle="tooltip" data-placement="top" data-animation="true" title="A value of an attribute, e.g. wn-2007-streamline" type="text" placeholder="Value" name="value" id="value" onkeydown="if (event.keyCode == 13) getData()" value="<?php echo $_GET["value"] ?>"/>
					<button class="btn btn-primary" type="button" data-loading-text="Loading..." id="search" onclick="getData()">Quattorise</button>
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
				function validateData() {
					var x = document.forms["Search"]["attribute"].value;
					var y = document.forms["Search"]["value"].value;
					if (x == null || x == "" || y == null || y == "") {
						$('#search').prop("disabled", true);
						return false;
					}
					else {
						$('#search').prop("disabled", false);
						return true;
					}
				}
				function getData() {
					if (validateData()) {
						var x = $("#attribute").val();
						if (x[0] !== "/") {
							x = "/" + x;
						}
						$("#resultstable_wrapper").hide();
						$("#loading").show();
						$('#search').prop("disabled", true);
						$("#attribute").prop("disabled", true);
						$("#value").prop("disabled", true);
						$.get(
							'api-json-attribute.php',
							{ attribute: document.forms["Search"]["attribute"].value, value: document.forms["Search"]["value"].value },
							function(response, status, xhr) {
								var data = eval(response);
								var oTable = $("#resultstable").dataTable();
								oTable.fnClearTable();
								oTable.fnAddData(data);
								$('#search').prop("disabled", false);
								$("#attribute").prop("disabled", false);
								$("#value").prop("disabled", false);
								$("#loading").hide();
								$("#resultstable_wrapper").show();
							}
						);
					}
				}
				$(document).ready(function() {
					$("#attribute").tooltip();
					$("#value").tooltip();
					$("#attribute").keyup(function() {
						validateData();
					});
					$("#attribute").change(function() {
						validateData();
					});
					$("#value").keyup(function() {
						validateData();
					});
					$("#value").change(function() {
						validateData();
					});
					validateData();
					$("#search").click(getData);
					$('#resultstable').dataTable( {
						"sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
						"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
							text = $('td:eq(0)', nRow).html();
							if (text.indexOf("<a ") < 0) {
								$('td:eq(0)', nRow).html("<a class='clickable' onclick=\"node('"+text+"')\">"+text+"</a>");
							}
							return nRow;
						},
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
			</script>
		</div>
	</div>
</div>
<?php
	include("footer.inc.php");
?>
