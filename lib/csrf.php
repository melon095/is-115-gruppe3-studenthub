<?php

/**
 * Henter CSRF-tokenet for den gjeldende sesjonen, og oppretter et nytt hvis det ikke finnes fra før.
 *
 * @return string CSRF-tokenet.
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Lager et skjult skjemafelt som inneholder CSRF-tokenet.
 *
 * @return string HTML-koden for det skjulte feltet.
 */
function csrf_felt(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

/**
 * Sjekker om et innsendt CSRF-token stemmer med tokenet i sesjonen.
 *
 * @return bool Sann hvis tokenet er gyldig.
 */
function csrf_gyldig(?string $token): bool
{
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token ?? '');
}
