<?php
	require_once 'php/header.php';
	if (empty($_GET) || !isset($_GET['id'])) {
		// redirect to home page
		header('Location: http://192.168.210.81/projetMyNy/');
	} else {
		require_once 'php/config.php';
		$id = intval($_GET['id']);
		$stmt = $pdo->prepare('SELECT sit_name, sit_address, sit_postal_code, sit_city, sit_description, sit_image_path, sit_longitude, sit_latitude, ROUND((5*sit_rating5+4*sit_rating4+3*sit_rating3+2*sit_rating2+sit_rating1)/(sit_rating5+sit_rating4+sit_rating3+sit_rating2+sit_rating1),1) AS sit_rating, sit_phone, sit_email, sit_date_added, typ_name
			FROM site INNER JOIN type
			ON site.typ_id = type.typ_id
			WHERE sit_id = :id');
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		if ($stmt->execute()) {
			$location = $stmt->fetch();
			?><h1 class="loc"><?= $location['sit_name']; ?></h1>
			<div class="rating"><?= $location['sit_rating']; ?></div>
			<div class="type"><?= $location['typ_name']; ?></div>
			<object data="<?= $location['sit_image_path']; ?>"></object>
			<div class="locContent">
				<div class="description"><?= $location['sit_description']; ?></div>
				<address>
					<?= $location['sit_address']; ?><br/>
					<?= $location['sit_postal_code']; ?> <?= $location['sit_city']; ?>
				</address>
				<a href="mailto:<?= $location['sit_email'];?>"><?= $location['sit_email'];?></a>
				<a href="tel:<?= str_replace(' ', '', $location['sit_phone']); ?>"><?= $location['sit_phone']; ?></a>
				<div class="date-added"><?= $location['sit_date_added'];?></div>
			</div>
			<div class="map"></div>
			<script src="https://maps.google.com/maps/api/js"></script>
			<script>
				"use strict";
				var sitLatitude = <?= $location['sit_latitude']; ?>;
				var sitLongitude = <?= $location['sit_longitude']; ?>;
				var sitName = "<?= $location['sit_name']; ?>";
			</script>
			<script src="js/googlemaps.js"></script><?php
		}
	}
	require_once 'php/footer.php';