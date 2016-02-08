<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8"/>
		<title>MyNy</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<!-- Define Max and min width for devices -->
		<link rel="stylesheet" media="screen and (max-width: 320px)" href="css/mobile.css" type="text/css" />
		<link rel="stylesheet" media="screen and (min-width: 960px)" href="css/desktop.css" type="text/css" />
	</head>
		<body>
		<div id="headerMax">
			<header>
				<div id="branding">
					<h1>
						<a href="index.php">MyNy</a>
					</h1>
				</div>
				<!-- shows who is logged in -->
				<div id="login">
					<ul>
						<li>dot</li>
						<li>account</li>
					</ul>
				</div>
				<!-- menu shows only for admin -->
				<nav id="menu">
					<ul>
						<li><a href="../editlocation.php">locations</a></li>
						<li><a href="../edituser.php">users</a></li>
					</ul>
				</nav>
			</header>
		</div>
		<main>