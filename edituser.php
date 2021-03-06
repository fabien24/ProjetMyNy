<?php
require_once 'php/config.php';
require_once 'php/header.php';
require_once 'php/loginphp.php';
// récupération de tous les utilisateurs
$userMail = '
    SELECT usr_email FROM user 
';
$pdoStatement = $pdo -> prepare($userMail);
if ($pdoStatement -> execute() && $pdoStatement -> rowCount() > 0) {
    $userMail = $pdoStatement -> fetchAll(PDO::FETCH_ASSOC);
}
// changement du role de l'utilisateur sélectioné
if (!empty($_POST) && isset($_POST['editRole'])) {
    $edit = '
    UPDATE user SET usr_role = :role WHERE usr_email = :email
    ';
    $pdoStatement = $pdo -> prepare($edit);
    $pdoStatement -> bindvalue(':role', $_POST['editRole'], PDO::PARAM_INT);
    $pdoStatement -> bindvalue(':email', $_SESSION['editUser'], PDO::PARAM_STR);
    if ($pdoStatement -> execute() && $pdoStatement -> rowCount() > 0){
        $message = $_SESSION['editUser'].' got his role changed';
        writeLog($message);
        unset($_SESSION['editUser']);
    }
}
// récupération des données de l'utilisateur choisit dans le drop down
if (!empty($_POST) && isset($_POST['userList'])) {
    $_SESSION['editUser'] = $_POST['userList'];
    $userData = '
    SELECT usr_role FROM user WHERE usr_email = :email
    ';
    $pdoStatement = $pdo -> prepare($userData);
    $pdoStatement -> bindvalue(':email', $_POST['userList'], PDO::PARAM_STR);
    if ($pdoStatement -> execute() && $pdoStatement -> rowCount() > 0) {
        $userRole = $pdoStatement -> fetch(PDO::FETCH_ASSOC);
        $role = $userRole['usr_role'];   
    }
}
// suppression de l'utilisateur choisit dans de le drop down
if (!empty($_POST) && isset($_POST['delete'])) {
    $remove = '
    DELETE FROM user WHERE usr_email = :email
    ';
    $pdoStatement = $pdo -> prepare($remove);
    $pdoStatement -> bindvalue (':email', $_SESSION['editUser'], PDO::PARAM_STR);
    if ($pdoStatement -> execute() && $pdoStatement -> rowCount() > 0){
        $message = $_SESSION['editUser'].' got his account deleted';
        writeLog($message);
        unset($_SESSION['editUser']);
        header("Refresh:0");
    }
}
// contrôle de l'adresse mail pour l'ajout d'un utilisateur
if (!empty($_POST['add']) && isset($_POST['add'])) {
    $email = (filter_var($_POST['add'], FILTER_VALIDATE_EMAIL)) ? $_POST['add'] : '';    
}// ajout de l'adresse mail du nouveau utilisateur
if (isset($email) && !empty($email)) {
    $add = '
    INSERT INTO user (usr_email, usr_role) VALUES (:email, :role)
    ';
    $pdoStatement = $pdo -> prepare($add);
    $pdoStatement -> bindvalue (':email', $email, PDO::PARAM_STR);
    $pdoStatement -> bindvalue (':role', '1', PDO::PARAM_INT);
    if ($pdoStatement -> execute() && $pdoStatement -> rowCount() > 0){// mail envoyé à l'adresse mail du nouveau utilisateur
        $message = $email.' has been added to the DB';
        writeLog($message);
        $token = md5($email.'peepnsmyny');
        $emailHTML = '<html>
        <head><title>Invitation</title></head>
        <body>
        Dear friend,<br />
        <br />
        You have been invited to join our webpage.<br />
        <a href="http://localhost/projetMyNy/signup.php?token='.$token.'">
        Click here to sign-up</a>.<br />
        <br />
        Best regards,
        MyNy
        </body>
        </html>';
        $emailText = 'Go here : http://localhost/projetMyNy/signup.php?token='.$token;
        if (autoMail($email, $emailHTML, $emailText)) {// si email envoyé alors ajout du token à la DB
            $message = 'Invitation envoyé par email à '.$_POST['add'];
            writeLog($message);
            $addToken = '
            UPDATE user SET usr_token = :token WHERE usr_email = :email
            ';
            $pdoStatement = $pdo -> prepare($addToken);
            $pdoStatement -> bindvalue(':token', $token, PDO::PARAM_STR);
            $pdoStatement -> bindvalue(':email', $_POST['add'], PDO::PARAM_STR);
            if ($pdoStatement -> execute() && $pdoStatement -> rowCount() > 0) {
                $message = 'token ajouté à '.$email;
                writeLog($message);
                header("Refresh:0");
            } else { // les erreurs
                $message = 'token ne pouvez pas être ajouter à '.$email;
                writeLog($message);
            }
        } else {
            $message = 'Email ne pouvez pas être envoyer à '.$email;
            writeLog($message);
        }
    }
}

if (!$logged) {//si on est pas logged in
        require_once 'php/loginhtml.php';
} elseif (isset($_SESSION['role']) && $_SESSION['role'] == 4) {// si on est logged in et si on a les droits nécessaires
?>  

   <div id="usrScreen">
        <form action="" method="post"> 
            <input type="email" name="add" placeholder="Your friends email"><br/>
            <input type="submit" value="Add a friend">
        </form>
        <form action="" method="post">
            <select name="userList">
                <option value="0">User</option>
                <?php foreach ($userMail as $data) : ?>
                <option value="<?php echo $data['usr_email'] ?>"
                <?php   if (isset($_POST['userList']) && ($data['usr_email'] == $_POST['userList'])) {
                             echo 'selected';
                        } ?> >
                <?php echo $data['usr_email'] ?>
                </option>
                <?php endforeach; ?>
            </select><br />
            <input type="submit" value="Show">
        </form><br />
        <section>
           <?php if (isset($role) && $role < 4 && !empty($_POST['userList'])) {?>
            <form method="post">
                <label><?php echo $_POST['userList'] ?></label><br />
                <select name="editRole">
                    <option value="1" selected>User</option>
                    <option value="4">Admin</option>
                </select><br/>
                <input type="submit" value="Edit">
            </form>
            <form action="" method="post">
                <input type="hidden" name="delete">
                <input type="submit" value="Delete">
            </form>
            <?php } elseif (isset($role) && $role == 4 && !empty($_POST['userList'])) { ?>
            <label>User : <?php echo $_POST['userList']; ?></label><br />
            <label>Role : Admin</label>
        </section>
    </div>
    <?php } ?>
<?php
} else {// si on a pas les droits nécessaires
?>
    <h2>You can't see this page because you don't have administrator rights</h2>
    <h3>If you think that this is an mistake contact the administrator</h3>
    <a href="mailto:myny_projet@hotmail.com?Subject=Link%20broken" target="_top">Contact admin</a>
<?php        
}
?>
<?php
    require_once 'php/footer.php';
?>