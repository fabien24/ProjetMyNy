<?php
	require_once 'php/header.php';
	if (empty($_GET) || !isset($_GET['id'])) {
		// redirect to home page
		header('Location: http://192.168.210.81/projetMyNy/');
	} else {
		require_once 'php/config.php';
		$id = intval($_GET['id']);
		if (!empty($_POST)) {
			$ratingColumn = null;
			if (isset($_POST['rate5_x'])) {
				$ratingColumn = 'sit_rating5';
			} else if (isset($_POST['rate4_x'])) {
				$ratingColumn = 'sit_rating4';
			} else if (isset($_POST['rate3_x'])) {
				$ratingColumn = 'sit_rating3';
			} else if (isset($_POST['rate2_x'])) {
				$ratingColumn = 'sit_rating2';
			} else if (isset($_POST['rate1_x'])) {
				$ratingColumn = 'sit_rating1';
			}
			if ($ratingColumn) {
				$stmt = $pdo->prepare('UPDATE site
					SET '.$ratingColumn.' = '.$ratingColumn.' + 1
					WHERE sit_id = :id');
				$stmt->bindValue(':id', $id, PDO::PARAM_INT);
				$stmt->execute();
			}
		}
		$stmt = $pdo->prepare('SELECT sit_name, sit_address, sit_postal_code, sit_city, sit_description, sit_image_path, sit_longitude, sit_latitude, ROUND((5*sit_rating5+4*sit_rating4+3*sit_rating3+2*sit_rating2+sit_rating1)/(sit_rating5+sit_rating4+sit_rating3+sit_rating2+sit_rating1),1) AS sit_rating, sit_phone, sit_email, sit_date_added, typ_name
			FROM site INNER JOIN type
			ON site.typ_id = type.typ_id
			WHERE sit_id = :id
			LIMIT 1');
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		if ($stmt->execute()) {
			$location = $stmt->fetch();
			?><h1 class="loc"><?= $location['sit_name']; ?></h1>
			<div class="rating" style="width: <?= 30*floatval($location['sit_rating']); ?>px;"></div>
			<form class="rate" action="" method="post">
				<input type="image" src="img/star.png" alt="Rate with 1 star " name="rate1" title="★"/><input type="image" src="img/star.png" alt="Rate with 2 stars " name="rate2" title="★★"/><input type="image" src="img/star.png" alt="Rate with 3 stars " name="rate3" title="★★★"/><input type="image" src="img/star.png" alt="Rate with 4 stars " name="rate4" title="★★★★"/><input type="image" src="img/star.png" alt="Rate with 5 stars" name="rate5" title="★★★★★"/>
			</form>
			<div class="type"><?= $location['typ_name']; ?></div>
			<object data="<?= $location['sit_image_path']; ?>"></object>
			<div class="locContent">
				<div class="description"><?= $location['sit_description']; ?></div>
				<address>
					<?= $location['sit_address']; ?><br/>
					<?= $location['sit_postal_code']; ?> <?= $location['sit_city']; ?>
				</address>
				<a href="mailto:<?= $location['sit_email']; ?>"><?= $location['sit_email']; ?></a>
				<a href="tel:<?= str_replace(' ', '', $location['sit_phone']); ?>"><?= $location['sit_phone']; ?></a>
				<div class="date-added"><?= $location['sit_date_added']; ?></div><?php
				if (isset($_SESSION['role']) && $_SESSION['role'] >= 3) {
					?><a href="./editlocation.php?id=<?= $id; ?>">Edit</a><?php
				}
			?></div>
			<div class="map"></div>
			<script src="https://maps.google.com/maps/api/js"></script>
			<script src="js/googlemaps.js"></script>
			<script>
				"use strict";
				var mapCenter = {
					latitude: <?= $location['sit_latitude']; ?>,
					longitude: <?= $location['sit_longitude']; ?>,
					zoom: 17
				}
				var siteList = [
					{
						latitude: <?= $location['sit_latitude']; ?>,
						longitude: <?= $location['sit_longitude']; ?>,
						name: "<?= $location['sit_name']; ?>"
					}
				];
				// call addSitesToMap when the page has loaded
				google.maps.event.addDomListener(window, "load", function(){
					addSitesToMap(siteList);
				});
			</script><?php
		}
	}
	require_once 'php/footer.php';