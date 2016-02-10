<?php
	require_once 'php/config.php';
	require_once 'php/loginphp.php';
	require_once 'php/header.php';
	if (!$logged) {
		require_once 'php/loginhtml.php';//TODO: reenable
	} else {
		$allLocations = $pdo->query('SELECT sit_id, sit_name, sit_latitude, sit_longitude, typ_name
			FROM site INNER JOIN type
			ON site.typ_id = type.typ_id');
		$lastAddedLocations = $pdo->query('SELECT sit_id, sit_name, ROUND((5*sit_rating5+4*sit_rating4+3*sit_rating3+2*sit_rating2+sit_rating1)/(sit_rating5+sit_rating4+sit_rating3+sit_rating2+sit_rating1),1) AS sit_rating, LEFT(sit_description, 150) AS sit_short_description, LENGTH(sit_description) AS sit_description_length, typ_name
			FROM site INNER JOIN type
			ON site.typ_id = type.typ_id
			WHERE sit_rating5+sit_rating4+sit_rating3+sit_rating2+sit_rating1 != 0
				AND ROUND((5*sit_rating5+4*sit_rating4+3*sit_rating3+2*sit_rating2+sit_rating1)/(sit_rating5+sit_rating4+sit_rating3+sit_rating2+sit_rating1),1) >= (
					SELECT AVG(ROUND((5*sit_rating5+4*sit_rating4+3*sit_rating3+2*sit_rating2+sit_rating1)/(sit_rating5+sit_rating4+sit_rating3+sit_rating2+sit_rating1),1))
					FROM site
				)
			ORDER BY site.sit_date_added DESC
			LIMIT 10');
		?><section id="map">
			<div class="map"></div>
			<script src="https://maps.google.com/maps/api/js"></script>
			<script src="js/googlemaps.js"></script>
			<script>
				"use strict";
				var mapCenter = {
					latitude: 49.77,
					longitude: 6.10,
					zoom: 8
				}
				var siteList = [<?php
					foreach ($allLocations as $location) {
						?>{
							latitude: <?= $location['sit_latitude']; ?>,
							longitude: <?= $location['sit_longitude']; ?>,
							name: "<?= $location['sit_name']; ?>"
						},<?php
					}
				?>];
				// call addSitesToMap when the page has loaded
				google.maps.event.addDomListener(window, "load", function(){
					addSitesToMap(siteList);
				});
			</script>
		</section>
		<section id="slider"><?php
			// Slider .hidden on mobile devices
		?></section>
		<section id="locations"><?php
			// locations list 2 per row (1 on mobile device)
			foreach ($lastAddedLocations as $location) {
				?><div>
					<h2><a href="./location.php?id=<?= $location['sit_id'] ?>"><?= $location['sit_name']; ?></a></h2>
					<div class="rating" style="width: <?= 30*intval($location['sit_rating']); ?>px;"></div>
					<div class="type"><?= $location['typ_name'] ?></div>
					<div class="description"><?= $location['sit_short_description'] ?><?php echo ($location['sit_description_length'] > 150) ? '…' : ''; ?></div>
					<a href="./location.php?id=<?= $location['sit_id'] ?>">Read more…</a>
				</div><?php
			}
		?></section><?php
	}
	require_once 'php/footer.php';
?>