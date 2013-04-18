<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<title>Quattor | Data Warehouse</title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<link rel="stylesheet" type="text/css" href="css/styling.css" />
	<link rel="stylesheet" type="text/css" href="css/jquery-ui.min.css" />
	<link rel="stylesheet" type="text/css" href="css/jquery.jqplot.min.css" />
	<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" />
	<link rel="stylesheet" type="text/css" href="bootstrap/css/pygments.css" />
	<script type="text/javascript" charset="utf-8" src="js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" charset="utf-8" src="js/jquery-ui.min.js"></script>
	<script type="text/javascript" charset="utf-8" src="js/jquery.ui.core.min.js"></script>
	<script type="text/javascript" charset="utf-8" src="js/keylist.js"></script>
	<script type="text/javascript" charset="utf-8" src="js/jquery.ui.autocomplete.min.js"></script>
	<script type="text/javascript" charset="utf-8" src="bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" charset="utf-8" src="bootstrap/js/bootstrap-button.js"></script>
	<script type="text/javascript" charset="utf-8" src="bootstrap/js/bootstrap-alert.js"></script>
	<script type="text/javascript" charset="utf-8" src="bootstrap/js/bootstrap-tooltip.js"></script>
	<style type="text/css">
		@import url(http://fonts.googleapis.com/css?family=Lato:400);
		body {
			font-family: 'Lato', 'Helvetica', sans-serif;
			padding-top: 60px;
			padding-bottom: 40px;
		}
	</style>
	<link rel="icon" href="images/favicon.ico"/>
</head>
<body>
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" href="http://quattor.org/index.html"><img src="images/quattor_logo_navbar.png" alt="quattor logo"/></a>
				<div class="nav-collapse collapse">
					<ul class="nav">
						<?php
							$currentFile = $_SERVER["PHP_SELF"];
							$parts = Explode('/', $currentFile);
							$page = $parts[count($parts) - 1];
						?>
						<li<?php if ($page == "index.php") { echo ' class="active"'; } ?>><a href="index.php">Distribution</a></li>
						<li<?php if ($page == "ServerAttribute.php") { echo ' class="active"'; } ?>><a href="ServerAttribute.php">Server Attribute</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
