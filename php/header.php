<?php 
$account = isset($_SESSION['username']) ? $_SESSION['username'] : '' ;
$sessRole = isset($_SESSION['role']) ? $_SESSION['role'] : '' ;
$nav = false;
if ($sessRole == 4) {
	$headerRole = 'admin';
	$nav = true;
} elseif ($sessRole < 4 && !empty($sessRole)) {
	$headerRole = 'user';
}else{
	$headerRole = '';
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>MyNy</title>
		<link href="css/style.css" rel="stylesheet"/>
		<!-- Define Max and min width for devices -->
		<link href="css/mobile.css" rel="stylesheet" media="screen and (max-width: 620px)"/>
		<link href="css/desktop.css" rel="stylesheet" media="screen and (min-width: 960px)"/>
	</head>
	<body>
		<div id="headerMax">
			<header>
				<div id="branding">
					<h1>
						<a href="./">MyNy</a>
					</h1>
				</div>
				<!-- shows who is logged in -->
				<div id="login">
					<ul>
						<li></li>
						<li class="<?php echo $headerRole ?>"><?php echo $account ;?></li>
					</ul>
				</div>
				<!-- menu shows only for admin -->
				<?php if($nav) { ?>
				<nav id="menu">
					<ul>
						<li><a href="./editlocation.php">locations</a></li>
						<li><a href="./edituser.php">users</a></li>
					</ul>
				</nav>
				<?php } ?>
			</header>
		</div>
		<main>