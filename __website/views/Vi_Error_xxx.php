<?php
$base = isset($_SERVER["__WEBSITE_ROOT__"]) ? $_SERVER["__WEBSITE_ROOT__"] : "";
$data = json_decode(base64_decode($_GET["data"]),true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<!-- General mobile devices web-app optimisation -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="HandheldFriendly" content="True">
	<meta name="MobileOptimized" content="320">
	<meta http-equiv="cleartype" content="on">
	<meta charset="utf-8">
	<!-- TODO --- fix for iPhone5 -- width=320.1 -->
	<!-- viewport - only web app -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<meta http-equiv="content-type" content="text/html; charset=utf-8">

	<title>Unknown Server Error</title>

	<link rel="stylesheet" type="text/css" href="<?=$base?>error/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="<?=$base?>error/error.css" />
</head>

<body>
	<div class="container">
		<div class="jumbotron center">
			<h1>Unknown Server Error</h1>
			<br />
			<p>An unknown, but unrecoverable error occured</p>

			<p>The page requested: <a href="<?=$data["uri"]?>"><?=$data["uri"]?></a></p>

			<p><b>Or you could just press this neat little button:</b></p>

			<p><a href="<?=$data["root"]?>" class="btn btn-large btn-info">
				<i class="icon-home icon-white"></i> Take Me Home
			</a></p>

			<p>Error: <?=$data["msg"]?> on page "<?=$data["uri"]?>" in file "<?=$data["file"]?>", line: <?=$data["line"]?></p>
		</div>
	</div>
</body>
</html>