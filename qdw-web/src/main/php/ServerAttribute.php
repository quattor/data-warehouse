<?php
    include("header.inc.php");
?>
<script type="text/javascript" charset="utf-8" src="src/DataTables/media/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css" href="src/DataTables/media/css/jquery.dataTables_themeroller.css" />

<div class="serverAttribute">
    <form name="Search">
        <div>   
            <label for="attribute">Attribute</label>
            <input type="text" name="attribute" id="attribute" onkeydown="if (event.keyCode == 13) getData()" value="<?php echo $_GET["attribute"] ?>"/>
        </div>
        <div>
            <label for="value">Value</label>
            <input type="text" name="value" id="value" onkeydown="if (event.keyCode == 13) getData()" value="<?php echo $_GET["value"] ?>"/>
        </div>
        <input class="buttontheme" type="button" id="Search" value="Quattorise" onclick="getData()"/><br><br>
    </form>
</div>

<div class="results" id="results" >
    <div class="loading" id="loading"></div>
    <table class="display" id="resultstable"></table>
</div>

<script type="text/javascript">
    //Pops open the info box for a specific node
    var nodewin = null;
        var INFO_URL = "http://localhost/info.php?n="; //This needs to come from config...

    function node(n) {
      nodewin = window.open(INFO_URL+n, "node", "width=640,height=480,left=128,top=128,resizable=yes,scrollbars=yes,directories=no,titlebar=no,toolbar=no,status=no"); 
      nodewin.window.focus();
    }

    $(document).ready(function() {
        $('#resultstable').dataTable( {
        "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
            text = $('td:eq(0)', nRow).html();
            if (text.indexOf("<span ") < 0) {
                $('td:eq(0)', nRow).html("<span class=\"clickable\" onclick=\"node('"+text+"')\">"+text+"</span>");
            }
            return nRow;
        },
        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
            "aaData": [],
            "aoColumns": [
                { "sTitle": "Machine Names" }
            ],
        "iDisplayLength": 20,
        "aLengthMenu": [
            [20, 40, 80, 160, -1], [20, 40, 80, 160, "All"]
        ]
        });
        getData();
    });
</script>

<script type="text/javascript">
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
            $("#resultstable_wrapper").hide();
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
                    $("#resultstable_wrapper").show();
                }
            );
        }
    }
</script>

<?php
    include("footer.inc.php");
?>
