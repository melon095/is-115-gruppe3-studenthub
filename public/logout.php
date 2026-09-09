<?php

// https://stackoverflow.com/a/3512570

require __DIR__."/_bootstrap.php";
unset($_SESSION);
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}
session_destroy();
header('Location: ' . url('/login.php'));
