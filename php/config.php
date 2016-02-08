<?php
    session_start();
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

//fonction pour vérifier le login
    function loggedIn($sessEmail, $sessPassword){
        global $pdo;
        $dbData = '
        SELECT usr_password 
        FROM user
        WHERE usr_email = :email
        ';
        $pdoStatement = $pdo -> prepare($dbData);
        $pdoStatement -> bindvalue (':email', $sessEmail, PDO::PARAM_STR);
        if ($pdoStatement -> execute() && $pdoStatement -> rowCount() > 0) {
            $pdoFetch = $pdoStatement -> fetch(PDO::FETCH_ASSOC);
            $password = $pdoFetch['usr_password'];
            if ($sessPassword == $password) {
                return true;
            }
        }
        return false;
    }