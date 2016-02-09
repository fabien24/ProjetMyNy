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
        $time = date('d/m/Y, H:i:s').' =>';
        $log = fopen('log.txt', 'a');
        if ($log) {
            fwrite($log, $time.PHP_EOL);
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

//fonction pour envoyer le mail
    function autoMail($to, $messsageHTML, $messageText) {
        require_once 'PHPMailer/PHPMailerAutoload.php';

        $mail = new PHPMailer;

        $mail->isSMTP();
        $mail->Host = 'smtp.live.com';  
        $mail->SMTPAuth = true;                              
        $mail->Username = 'myny_projet@hotmail.com';                
        $mail->Password = file_get_contents('../mynydba/password.txt'); 
        $mail->SMTPSecure = 'tls';
        $mail->Port = 25;                                   

        $mail->setFrom('myny_projet@hotmail.com', 'MyNy support');
        $mail->addAddress($to);

        $mail->isHTML(true);                                  

        $mail->Subject = 'Password at MyNy';
        $mail->Body    = $messsageHTML;
        $mail->AltBody = $messageText;

        return $mail->send();
    }