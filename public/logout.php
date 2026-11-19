<?php

require __DIR__ . "/bootstrap.inc.php";

if (isset($_SESSION["student_id"])) {
    hendelser_skriv((int) $_SESSION["student_id"], "Logout", "Bruker har logget ut");
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

omdiriger(url('/login.php?action=loggut'));
