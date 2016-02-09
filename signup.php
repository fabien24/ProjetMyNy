<?php
require_once 'php/config.php';

$formulaire = false;
//Vérification si le token est existant et que c'est bien le bon token
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = trim($_GET{'token'});

//  Récupération des données de la DB
    $data = '
        SELECT usr_token , usr_email 
        FROM user
        WHERE usr_token = :token
    ';
    $pdoStatement = $pdo -> prepare($data);
    $pdoStatement -> bindvalue(':token', $token, PDO::PARAM_STR);
    if ($pdoStatement -> execute() && $pdoStatement -> rowCount() > 0) {
        $userInfo = $pdoStatement -> fetch(PDO::FETCH_ASSOC);
        $email = $userInfo['usr_email'];
        $_SESSION['email'] = $email;
        $userEmail = $_SESSION['email'];
        $formulaire = true;
    }
}


//Formulaire envoyé
$pwConfirmation = true;
$pwValid = true;
$pwFilled = true;

if (!empty($_POST)) {

//  vérification des données récupérées
    if (isset($_POST['pwinitial']) && !empty($_POST['pwinitial'])) {
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/';
        $pwCheck = preg_match($pattern, $_POST['pwinitial']);
        if ($pwCheck == 1) {
            $password = $_POST['pwinitial'];
            if (!empty($_POST['pwconfirm'])
            && ($password == $_POST['pwconfirm'])) {
                $securePassword = password_hash($password, PASSWORD_BCRYPT);
                $addPw = '
                UPDATE user SET usr_password = :password , usr_token = "" WHERE usr_email = :email
                ';
                $pdoStatement = $pdo -> prepare($addPw);
                $pdoStatement -> bindvalue(':password', $securePassword, PDO::PARAM_STR);
                $pdoStatement -> bindvalue(':email', $userEmail, PDO::PARAM_STR);
                if ($pdoStatement -> execute() && $pdoStatement -> rowCount() > 0) {
                    //changement du mot de passe enregistré dans le log
                    $time = date('d/m/Y, H:i:s').' =>';
                    $message = 'The user '.$userEmail.' entered a new password and got his token removed';
                    writeLog($message);
                    $_SESSION['password'] = $securePassword;
                    header('Location: index.php');
                }
            } else {
                $pwConfirmation = false;
            }
        } else {
            $pwValid = false;
        }
    } else {
        $pwFilled = false;
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
                    <input type="password" name="pwinitial" 
                        placeholder="New password"><br />
                    <input type="password" name="pwconfirm" 
                        placeholder="Confirm password"><br />
                    <input class="submit" type="submit" value="Set password"><br />
                    <label><?php
                        // Gestion des erreurs du formulaire du mot de passe
                    if (!$pwFilled) {
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
        // S'il y a un problème avec le token on affiche un erreur
        } else {
?>
        <div class="error">
            <img src="img/homer.png" />
            <h1>SOMETHING WENT WRONG</h1>
            <p>Your link is defect please contact the <a href="administratorsmail">administrator</a> to get a new one.</p>
        </div>
<?php
        }
?>

<?php
    require_once 'php/footer.php';
?>
