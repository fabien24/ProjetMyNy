<?php
require_once 'php/config.php';

$formulaire = false;
//Vérification si le token est existant et que c'est bien le bon token
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = trim($_GET{'token'});

//  Récupération des données de la DB
    $data = '
        SELECT usr_token, usr_email, usr_role 
        FROM user
        WHERE usr_token = :token
    ';
    $pdoStatement = $pdo -> prepare($data);
    $pdoStatement -> bindvalue(':token', $token, PDO::PARAM_STR);
    if ($pdoStatement -> execute() && $pdoStatement -> rowCount() > 0) {
        $userInfo = $pdoStatement -> fetch(PDO::FETCH_ASSOC);
        $email = $userInfo['usr_email'];
        $uRole = $userInfo['usr_role'];
        $_SESSION['email'] = $email;
        $_SESSION['role'] =$uRole;
        $userEmail = $_SESSION['email'];
        $formulaire = true;
    }
}


//Formulaire envoyé
$pwConfirmation = true;
$pwValid = true;
$pwFilled = true;
$unFilled = true;
$unValid = true;

if (!empty($_POST)) {

//  vérification des données récupérées
	if (isset($_POST['username']) && !empty($_POST['username'])) {
		$pattern = '/^([a-z_\d]*)$/';
		$unCheck = preg_match($pattern, $_POST['username']);
		if ($unCheck == 1) {
			$username = $_POST['username'];
		    if (isset($_POST['pwinitial']) && !empty($_POST['pwinitial'])) {
		        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[\w\W\d]{8,}$/';
		        $pwCheck = preg_match($pattern, $_POST['pwinitial']);
		        if ($pwCheck == 1) {
		            $password = $_POST['pwinitial'];
		            if (!empty($_POST['pwconfirm'])
		            && ($password == $_POST['pwconfirm'])) {
		                $securePassword = password_hash($password, PASSWORD_BCRYPT);
		                $addPw = '
		                UPDATE user SET usr_password = :password, usr_name = :username, usr_token = "" WHERE usr_email = :email
		                ';
		                $pdoStatement = $pdo -> prepare($addPw);
		                $pdoStatement -> bindvalue(':password', $securePassword, PDO::PARAM_STR);
		                $pdoStatement -> bindvalue(':username', $username, PDO::PARAM_STR);
		                $pdoStatement -> bindvalue(':email', $userEmail, PDO::PARAM_STR);
		                if ($pdoStatement -> execute() && $pdoStatement -> rowCount() > 0) {
		                    //changement du mot de passe enregistré dans le log
		                    $message = 'The user '.$username.' entered a new password and got his token removed';
		                    writeLog($message);
		                    $_SESSION['password'] = $securePassword;
		                    $_SESSION['username'] = $username;
		                    header('Location: index.php');
		                }
		            } else {// les erreurs
		                $pwConfirmation = false;
		            }
		        } else {
		            $pwValid = false;
		        }
		    } else {
		        $pwFilled = false;
		    }
		} else {
			$unValid = false;
		}   
	} else {
		$unFilled = false;
	}
}
?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <meta charset="UTF-8">
        <title>Sign-up</title>
    </head>
    <body>
        <div id="headerMax">
        <header>
            <div id="branding">
                <h1>
                    <a href="index.php">MyNy</a>
                </h1>
            </div>
        </header>
        </div>
        <?php
        // Si le token est vérifié on affiche le formulaire
        if ($formulaire) {
        ?>
            <div id="loginScreen">
                <form action="" method="post">
                    <label><?php echo $userEmail ?></label><br />
                    <input type="text" name="username"
                    	placeholder ="Username"><br />
                    <input type="password" name="pwinitial" 
                        placeholder="New password"><br />
                    <input type="password" name="pwconfirm" 
                        placeholder="Confirm password"><br />
                    <input class="submit" type="submit" value="Set password"><br />
                    <label><?php
                        // Gestion des erreurs du formulaire du mot de passe
                    if (!$unFilled) {
                    	?>Username empty <?php
                    } elseif(!$unValid) { ?>
                            Username invalid: 
                            <ul>
                                <li><u>Can only contain :</u></li>
                                <li>lowercase</li>
                                <li>numbers</li>
                                <li>underscore _</li>
                            </ul>
                        <?php
                    } elseif (!$pwFilled) {
                        ?>Password empty <?php
                    } elseif (!$pwValid) { ?>
                            Password invalid: 
                            <ul>
                                <li>min length 8</li>
                                <li>1 uppercase</li>
                                <li>1 lowercase</li>
                                <li>1 number</li>
                            </ul>
                        <?php
                    } elseif (!$pwConfirmation) {
                        ?>Confirmation must be the same then password <?php
                    } ?>
                    </label>
                </form>
            </div>    
<?php
        // S'il y a un problème avec le token on affiche une erreur
        } else {
?>
        <div class="error">
            <img src="img/homer.png" />
            <h1>SOMETHING WENT WRONG</h1>
            <p>Your link is defect please contact the <a href="mailto:myny_projet@hotmail.com?Subject=Link%20broken" target="_top">administrator</a> to get a new one.</p>
        </div>
<?php
        }
?>

<?php
    require_once 'php/footer.php';
?>
