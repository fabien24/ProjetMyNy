<?php 
$account = isset($_SESSION['email']) ? $_SESSION['email'] : '' ;
$sessRole = isset($_SESSION['role']) ? $_SESSION['role'] : '' ;
if ($sessRole == 4) {
	$role = 'admin';
} elseif ($sessRole < 4 && !empty($sessRole)) {
	$role = 'user';
}else{
	$role = '';
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8"/>
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
						<li class="<?php echo $role ?>">dot</li>
						<li><?php echo $account ;?></li>
					</ul>
				</div>
				<!-- menu shows only for admin -->
				<nav id="menu">
					<ul>
						<li><a href="./editlocation.php">locations</a></li>
						<li><a href="./edituser.php">users</a></li>
					</ul>
				</nav>
			</header>
		</div>
		<main>