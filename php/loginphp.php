<?php

$logged = false;
if (!empty($_SESSION) && isset($_SESSION['email']) && isset($_SESSION['password'])
    && loggedIn($_SESSION['email'], $_SESSION['password'])) {
    $logged = true;
} else {
    $logged = false;
}

if ($logged) {
    goto loggedIn;
}

$emailVerif = true;
$pwVerif = true;
$emailFilled = true;
$pwFilled = true;
if (!empty($_POST)) {
    if (isset($_POST['email']) && !empty($_POST['email'])) {
        $email = (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) ? $_POST['email'] : '';
    } else {
        $emailFilled = false;
    }
    if (isset($_POST['password']) && !empty($_POST['password'])) {
        $password = $_POST['password'];
    } else {
        $pwFilled = false;
    }
    if (isset($email) && isset($password)) {
        $data = '
            SELECT usr_password, usr_role, usr_name
            FROM user
            WHERE usr_email = :email
        ';
        $pdoStatement = $pdo -> prepare($data);
        $pdoStatement -> bindvalue(':email', $email, PDO::PARAM_STR);
        if ($pdoStatement -> execute() && $pdoStatement -> rowCount() > 0) {
            $result = $pdoStatement -> fetch(PDO::FETCH_ASSOC);
            $passwordSecured = $result['usr_password'];
            $_SESSION['email'] = $email;
            if (password_verify($password, $passwordSecured)) {
                $_SESSION['password'] = $passwordSecured;
                $_SESSION['role'] = $result['usr_role'];
                $_SESSION['username'] = $result['usr_name'];
                header("Refresh:0");
            } else {
                $pwVerif = false;
            }
        } else {
            $emailVerif = false;
        }
    }
}
loggedIn:
