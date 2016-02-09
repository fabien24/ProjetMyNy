<?php
	require_once 'php/config.php';
	require_once 'php/loginphp.php';
	require_once 'php/header.php';
?>
<?php if (!$logged) {
        require_once 'php/loginhtml.php';
} else {?>

			<!-- Google Map api -->
			<section id="map">
				
			</section>

			<!-- Slider .hidden on mobile devices -->
			<section id="slider">
				
			</section>
			<!-- locations list 2 per row (1 on mobile device) -->
			<section id="locations">
				
			</section>
<?php } ?>
<?php
	require_once 'php/footer.php';
?>


