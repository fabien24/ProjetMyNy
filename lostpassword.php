<?php
require_once 'php/config.php';

$send = false;
$failure = false;
$emailFilled = true;
$emailValid = true;
if (!empty($_POST)) {
    $try = true;
    if (isset($_POST['email']) && !empty($_POST['email'])) {
        $email = (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) ? $_POST['email'] : '';
    }
    if (isset($email) && !empty($email)) {
        $emailVerif = '
        SELECT usr_id
        FROM user
        WHERE usr_email = :email
        ';
        $pdoStatement = $pdo -> prepare($emailVerif);
        $pdoStatement -> bindvalue(':email', $email, PDO::PARAM_STR);
        if ($pdoStatement -> execute() && $pdoStatement -> rowCount() > 0) {
            $result = $pdoStatement -> fetch(PDO::FETCH_ASSOC);
            $token = md5($email.'peepnsmyny'.$result['usr_id']);
            $emailHTML = '<html>
			<head><title>Lost password</title></head>
			<body>
			Dear user,<br />
			<br />
			You\'ve asked to change your password.<br />
			<a href="http://localhost/projetMyNy/signup.php?token='.$token.'">
            Click here to change your password</a>.<br />
			<br />
			Best regards,
			MyNy
			</body>
			</html>';
            $emailText = 'Go here : http://localhost/projetMyNy/signup.php?token='.$token;
            if (autoMail($email, $emailHTML, $emailText)) {
                $message = 'Email pour restaurer le mot de passe envoyé à '.$email;
                writeLog($message);
                $send = true;
                $addToken = '
                UPDATE user SET usr_token = :token WHERE usr_email = :email
                ';
                $pdoStatement = $pdo -> prepare($addToken);
                $pdoStatement -> bindvalue(':token', $token, PDO::PARAM_STR);
                $pdoStatement -> bindvalue(':email', $email, PDO::PARAM_STR);
                if ($pdoStatement -> execute() && $pdoStatement -> rowCount() > 0) {
                    $message = 'token ajouté à '.$email;
                    writeLog($message);
                } else {
                    $message = 'token ne pouvez pas être ajouter à '.$email;
                    writeLog($message);
                }
            } else {
                $message = 'Email ne pouvez pas être envoyer à '.$email;
                writeLog($message);
                $failure = true;
            }
        } else {
            $emailValid = false;
        }
    } else {
        $emailFilled = false;
    }
}


?>
<?php if (!$send && !$failure) { ?>
<head>
   <link rel="stylesheet" type="text/css" href="css/style.css"> 
</head>
    <div id="headerMax">
        <header>
            <div id="branding">
                <h1>
                    <a href="index.php">MyNy</a>
                </h1>
            </div>
        </header>
    </div>
    <div id="loginScreen">
        <h2>Enter your email to request a password reset</h2>
        <form action="" method="post">
            <input type="email" name="email" placeholder="Your Email">
            <input class="submit" type="submit" value="Send"><br />
            <label>
                <?php
                if (!$emailValid) {
                ?> Email incorrect <?php
                } elseif (!$emailFilled) {
                ?> Email empty <?php
                }
                ?>
            </label>
        </form>
        <?php } elseif ($send) { ?>
        <h2>Email has been sent</h2>
        <?php } elseif (!$send && !$failure) { ?>
        <h2>There has been an problem. Try again later or contact the administrator</h2><br />
        <a href="mailto:myny_projet@hotmail.com?Subject=Link%20broken" target="_top">Contact admin</a>
        <?php } ?>
    </div>
<?php
    require_once 'php/footer.php';
?>