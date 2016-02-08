<?php
	require_once '../mynydba/dbaccess.php';
	try {
		$pdo = new PDO(DSN, USER, PASSWORD);
	} catch (PDOException $e) {
		exit('Database connection failed');
	}
	var_dump($pdo);