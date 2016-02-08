<?php
	require_once '../mynydba/dbaccess.php';
	try {
		$pdo = new PDO(DSN, USER, PASSWORD);
	} catch (PDOException $e) {
		exit('Database connection failed');
	}

//fonction pour écrire dans le fichier log
    function writeLog($message){
        $log = fopen('log.txt', 'a');
        if ($log) {
            fwrite($log, $message.PHP_EOL);
            fclose($log);
        }
    }