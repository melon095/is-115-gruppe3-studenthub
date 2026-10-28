<?php

// https://www.php.net/manual/en/function.mail.php

/**
 * Sender en e-post som inviterer en person til å bli med i en gruppe.
 *
 * @return bool Sann hvis e-posten ble sendt.
 */
function send_invitasjon_epost(string $til, string $gruppe_navn, string $lenke, string $invitert_av): bool
{
    $emne = mb_encode_mimeheader("Du er invitert til " . $gruppe_navn . " på Studenthub", "UTF-8");

    $melding = <<<EOF
    Hei!

    {$invitert_av} har invitert deg til å bli med i gruppen "{$gruppe_navn}" på Studenthub.

    Trykk på lenken under for å bli med:
    {$lenke}

    Hilsen Studenthub
    EOF;

    $melding = wordwrap($melding, 80, "\r\n");

    $headers = [
        "From" => "Studenthub <" . EPOST_AVSENDER . ">",
        "Content-Type" => "text/plain; charset=UTF-8"
    ];

    return mail($til, $emne, $melding, $headers);
}
