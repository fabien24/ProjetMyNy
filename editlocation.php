<?php
	require_once 'php/header.php';
	require_once 'php/config.php';
	// default empty values to fill the form with
	$location = array(
		'sit_name' => null,
		'sit_address' => null,
		'sit_postal_code' => null,
		'sit_city' => null,
		'sit_description' => null,
		'sit_image_path' => null,
		'sit_longitude' => null,
		'sit_latitude' => null,
		'sit_phone' => null,
		'sit_email' => null,
		'typ_id' => null
	);
	// fetch all types from the DB
	$types = $pdo->query('SELECT typ_id, typ_name
		FROM type');
	$nameValid = true;
	$emailValid = true;
	$typeIdValid = true;
	$latitudeValid = true;
	$longitudeValid = true;
	$fileValid = true;
	// check whether there is something to post
	if (!empty($_POST)) {
		// data trimming & validation checks
		if (!empty($_POST['name'])) {
			$location['sit_name'] = trim($_POST['name']);
		} else {
			$nameValid = false;
		}
		$location['sit_address'] = isset($_POST['address']) ? trim($_POST['address']) : null;
		$location['sit_postal_code'] = isset($_POST['postal-code']) ? trim($_POST['postal-code']) : null;
		$location['sit_city'] = isset($_POST['city']) ? trim($_POST['city']) : null;
		$location['sit_description'] = isset($_POST['description']) ? trim($_POST['description']) : null;
		if (isset($_POST['email'])) {
			$location['sit_email'] = trim($_POST['email']);
			$emailValid = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL) !== false;
		} else {
			$emailValid = false;
		}
		$location['sit_phone'] = isset($_POST['phone']) ? trim($_POST['phone']) : null;
		if (!empty($_POST['type'])) {
			$location['typ_id'] = trim($_POST['type']);
		} else {
			$typeIdValid = false;
		}
		if (!empty($_POST['latitude'])) {
			$location['sit_latitude'] = trim($_POST['latitude']);
		} else {
			$latitudeValid = false;
		}
		if (!empty($_POST['longitude'])) {
			$location['sit_longitude'] = trim($_POST['longitude']);
		} else {
			$longitudeValid = false;
		}
		if (($_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) && (!isset($_FILES['image']) || ($_FILES['image']['error'] !== UPLOAD_ERR_OK) || !is_uploaded_file($_FILES['image']['tmp_name']) || !in_array($_FILES['image']['type'], array('image/jpeg', 'image/gif', 'image/png')))) {
			$fileValid = false;
		}
		$latitudeAndLongitudeValidOrEmpty = $latitudeValid === $longitudeValid;
		// check whether the posted data is valid
		if ($nameValid && $typeIdValid && $emailValid && $latitudeAndLongitudeValidOrEmpty && $fileValid) {
			// distinguish between addition and modification
			if (isset($_POST['id'])) {
				// update existing location in DB
				$stmt = $pdo->prepare('UPDATE site
					SET
						sit_name = :name,
						sit_address = :address,
						sit_postal_code = :postal_code,
						sit_city = :city,
						sit_description = :description,
						sit_longitude = :longitude,
						sit_latitude = :latitude,
						sit_phone = :phone,
						sit_email = :email,
						typ_id = :type
					WHERE sit_id = :id
					LIMIT 1');
				$stmt->bindValue(':id', trim($_POST['id']));
			} else {
				// insert new location into DB
				$stmt = $pdo->prepare('INSERT INTO site
					SET
						sit_name = :name,
						sit_address = :address,
						sit_postal_code = :postal_code,
						sit_city = :city,
						sit_description = :description,
						sit_longitude = :longitude,
						sit_latitude = :latitude,
						sit_phone = :phone,
						sit_email = :email,
						typ_id = :type');
			}
			$stmt->bindValue(':name', $location['sit_name']);
			$stmt->bindValue(':address', $location['sit_address']);
			$stmt->bindValue(':postal_code', $location['sit_postal_code']);
			$stmt->bindValue(':city', $location['sit_city']);
			$stmt->bindValue(':description', $location['sit_description']);
			$stmt->bindValue(':longitude', $location['sit_longitude']);
			$stmt->bindValue(':latitude', $location['sit_latitude']);
			$stmt->bindValue(':phone', $location['sit_phone']);
			$stmt->bindValue(':email', $location['sit_email']);
			$stmt->bindValue(':type', $location['typ_id'], PDO::PARAM_INT);
			if ($stmt->execute()) {
				// redirect to location.php
				if (isset($_POST['id'])) {
					// made an update
					$idInsertedAt = $_POST['id'];
				} else {
					// made an insert
					$idInsertedAt = $pdo->lastInsertId();
				}
				if (!empty($_FILES)) {
					// move file and set its location in the DB
					$imagePath = 'img/location'.$idInsertedAt.strrchr($_FILES['image']['name'], '.');
					move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
					$stmt = $pdo->prepare('UPDATE site
						SET sit_image_path=:image_path
						WHERE sit_id=:id
						LIMIT 1;');
					$stmt->bindValue(':image_path', $imagePath);
					$stmt->bindValue(':id', $idInsertedAt, PDO::PARAM_INT);
					$stmt->execute();
				}
				header('Location: http://192.168.210.81/projetMyNy/location.php?id='.$idInsertedAt);
				exit;
			}
		}
	}
	// if there is nothing to post or the post data is inValid
	if (isset($_GET['id']) && empty($_POST)) {
		$_GET['id'] = trim($_GET['id']);
		// fetch existing location data from DB if nothing has been attempted to be post
		$stmt = $pdo->prepare('SELECT sit_name, sit_address, sit_postal_code, sit_city, sit_description, sit_image_path, sit_longitude, sit_latitude, sit_phone, sit_email, typ_id
			FROM site
			WHERE sit_id = :id
			LIMIT 1');
		$stmt->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
		if ($stmt->execute()) {
			$location = $stmt->fetch(PDO::FETCH_ASSOC);
		}
	}
	?>
	<div id="addScreen">
		<h1><?php
			echo !isset($_GET['id']) ? 'Add a location' : 'Modify '.$location['sit_name'];
		?></h1>
		<form action="" method="post" enctype="multipart/form-data"><?php
			if (isset($_GET['id'])) {
				?><input type="hidden" name="id" value="<?= $_GET['id'] ?>"/><?php
			}
			?><label><span>Name:</span><input name="name" value="<?= $location['sit_name']; ?>" required="required"/><?php
				if (!$nameValid) {
					?><strong>invalid</strong><?php
				}
			?></label>
			<label><span>Type:</span>
				<select name="type" required="required"><?php
					foreach ($types as $type) {
						?><option value="<?= $type['typ_id']; ?>"<?php
							echo ($type['typ_id'] === $location['typ_id']) ? ' selected="selected"' : '';
						?>><?= $type['typ_name']; ?></option><?php
					}
				?></select><?php
				if (!$typeIdValid) {
					?><strong>invalid</strong><?php
				}
			?></label>
			<label><span>Address:</span><input name="address" value="<?= $location['sit_address']; ?>"/></label>
			<label><span>Postal code:</span><input name="postal-code" value="<?= $location['sit_postal_code']; ?>"/></label>
			<label><span>City:</span><input name="city" value="<?= $location['sit_city']; ?>"/></label>
			<label><span>Email:</span><input type="email" name="email" value="<?= $location['sit_email'];?>"/><?php
				if (!$emailValid) {
					?><strong>invalid</strong><?php
				}
			?></label>
			<label><span>Phone:</span><input type="tel" name="phone" value="<?= $location['sit_phone']; ?>"/></label>
			<label><span>Description:</span>
				<textarea name="description"><?= $location['sit_description']; ?></textarea>
			</label>
			<label><span>Image:</span><input type="file" name="image"/><?php
				if (!$fileValid) {
					?><strong>invalid</strong><?php
				}
			?></label>
			<div>
				<label><span>Latitude:</span><input name="latitude" value="<?= $location['sit_latitude']; ?>"/><?php
				if (!$latitudeValid) {
					?><strong>invalid</strong><?php
				}
			?></label>
				<label><span>Longitude:</span><input name="longitude" value="<?= $location['sit_longitude']; ?>"/><?php
				if (!$longitudeValid) {
					?><strong>invalid</strong><?php
				}
			?></label>
				<button type="button">Check location on the map</button>
				<!--div class="map"></div>
				<script src="https://maps.google.com/maps/api/js"></script>
				<script>
					"use strict";
					var sitLatitude = ;
					var sitLongitude = ;
					var sitName = "";
				</script>
				<script src="js/googlemaps.js"></script-->
			</div>
			<button><?php
				echo !isset($_GET['id']) ? 'Add' : 'Change';
			?></button>
		</form>
	</div><?php
	require_once 'php/footer.php';