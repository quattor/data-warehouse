<!DOCTYPE html>
<!--

	Nonzero1.0 by nodethirtythree design
	http://www.nodethirtythree.com
	missing in a maze

-->
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<title>Machine Profiles Search Directory</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link rel="stylesheet" type="text/css" title="Red" href="css/style_quattor.css" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" href="src/themes/ui-smoothness/css/smoothness/jquery-ui-1.8.23.custom.css" />
<link rel="stylesheet" type="text/css" href="src/CookieCuttr/cookiecuttr.css" />
<script type="text/javascript" charset="utf-8" src="src/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" charset="utf-8" src="src/ui/minified/jquery.ui.core.min.js"></script>
<script type="text/javascript" charset="utf-8" src="src/ui/minified/jquery.ui.widget.min.js"></script>
<script type="text/javascript" charset="utf-8" src="src/ui/minified/jquery.ui.button.min.js"></script>
<script type="text/javascript" charset="utf-8" src="src/ui/minified/jquery.ui.dialog.min.js"></script>
<script type="text/javascript" charset="utf-8" src="src/ui/minified/jquery.ui.position.min.js"></script>
<script type="text/javascript" charset="utf-8" src="src/ui/minified/jquery.ui.autocomplete.min.js"></script>
<script type="text/javascript" charset="utf-8" src="src/ui/minified/jquery.ui.mouse.min.js"></script>
<script type="text/javascript" charset="utf-8" src="src/ui/minified/jquery.ui.slider.min.js"></script>
<script type="text/javascript" charset="utf-8" src="src/ui/minified/jquery.effects.core.min.js"></script>
<script type="text/javascript" charset="utf-8" src="src/ui/minified/jquery.effects.blind.min.js"></script>
<script type="text/javascript" charset="utf-8" src="src/external/jquery.cookie.js"></script>
<script type="text/javascript" charset="utf-8" src="src/CookieCuttr/jquery.cookiecuttr.js"></script>
<script type="text/javascript" charset="utf-8" src="keylist.js"></script>

</head>
<body>

<div id="header">

	<div id="header_inner" class="fixed">
		
		<div id="logo">
                    <a href="http://quattor.sourceforge.net/">
                    	<img src="images/quattorlogo.png" alt="quattor" title="Quattor logo"/>
                    </a>
		</div>
		
<?php
	$page = (explode("/", $_SERVER['REQUEST_URI']));
	$page = (explode("?", $page[2]));
	$page = $page[0];
	if($page == "") {
		$page = ".";
	}
	$tabs = Array(
		"." => "Distribution",
		"ServerAttribute.php" => "Server Attribute"
	);
?>

		<div id="menu">
			<ul>	
				<?php
					foreach ($tabs as $u => $t) {
						$class = "";
						if ($page == $u) {
							$class = "active";
						}
						echo "<li><a class=\"$class\" href=\"$u\">$t</a></li>\n";
					}
				?>
			</ul>
		</div>
	</div>
</div>

<script type="text/javascript">
	function updateHistory(x) {
		var cookie = $.cookie("attributeHistory");
		var items = cookie ? cookie.split(/,/) : new Array();
		if (x && items.indexOf(x) < 0) {
			items.push(x);
			$.cookie("attributeHistory", items.join(), { expires: 7 });
		}	
		$( "#attribute" ).autocomplete ({
			minLength: 0,
			source: items.concat(quattorKeylist)
		});
	}
</script>

<script type="text/javascript">
	function updateValueHistory(y) {
		var cookie = $.cookie("valueHistory");
		var items = cookie ? cookie.split(/,/) : new Array();
		if (y && items.indexOf(y) < 0) {
			items.push(y);
			$.cookie("valueHistory", items.join(), { expires: 7 });
		}	
		$( "#value" ).autocomplete ({
			minLength: 0,
			source: items
		});
	
	}
</script>

<script>
	$(function() {
		$('#attribute').dblclick(function() {
			$( "#attribute" ).autocomplete("search", "");
		});
	});
</script>

<script>
	$(function() {
		$('#value').dblclick(function() {
			$( "#value" ).autocomplete("search", "");
		});
	});
</script>

<div id="main">

	<div id="main_inner" class="fixed">

		<div id="primaryContent_columnless">

			<div id="columnA_columnless">
