<?php

$e = fn(?string $tekst): string => htmlspecialchars((string) $tekst, ENT_QUOTES, "UTF-8");

$fornavn = $e($nb_student["fornavn"]);
$epost = $e($nb_student["epost"]);
$kontakt_epost = $e($nb_kontakt["epost"]);
$kontakt_telefon = $e($nb_kontakt["telefon"]);
$kontakt_telefon_lenke = $e(preg_replace('/[^\d+]/', "", $nb_kontakt["telefon"]));
$nettsted_url = rtrim($nb_nettsted_url, "/");
$antall_medlem = count($nb_grupper);
$antall_andre = count($nb_grupper_ikke_medlem);

$forhandsvisning = $antall_medlem > 0
    ? "Du er med i $antall_medlem " . ($antall_medlem === 1 ? "gruppe" : "grupper") . " på Studenthub. Se hva som skjer og oppdag flere grupper."
    : "Oppdag grupper du kan bli med i på Studenthub.";

$kort_beskrivelse = function (?string $beskrivelse) use ($e): string {
    $beskrivelse = trim((string) $beskrivelse);

    if ($beskrivelse === "") {
        return "";
    }

    return $e(mb_strimwidth($beskrivelse, 0, 140, "…"));
};

?>
<!DOCTYPE html>
<html lang="nb" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>Nyhetsbrev fra Studenthub</title>

    <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->

    <!--[if mso]>
        <style>
            * {
                font-family: Arial, sans-serif !important;
            }
        </style>
    <![endif]-->

    <style>
        :root {
            color-scheme: light dark;
            supported-color-schemes: light dark;
        }

        html,
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
        }

        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        div[style*="margin: 16px 0"] {
            margin: 0 !important;
        }

        #MessageViewBody,
        #MessageWebViewDiv {
            width: 100% !important;
        }

        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }

        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }

        a {
            text-decoration: none;
        }

        a[x-apple-data-detectors],
        .unstyle-auto-detected-links a,
        .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        .im {
            color: inherit !important;
        }

        @media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
            u~div .email-container {
                min-width: 320px !important;
            }
        }

        @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
            u~div .email-container {
                min-width: 375px !important;
            }
        }

        @media only screen and (min-device-width: 414px) {
            u~div .email-container {
                min-width: 414px !important;
            }
        }

        .button-td,
        .button-a {
            transition: all 100ms ease-in;
        }

        .button-td-primary:hover,
        .button-a-primary:hover {
            background: #3a4fb8 !important;
            border-color: #3a4fb8 !important;
        }

        @media screen and (max-width: 600px) {
            .email-container p {
                font-size: 16px !important;
                line-height: 24px !important;
            }

            .stack-column,
            .stack-column-center {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            .stack-column-center {
                text-align: center !important;
            }
        }

        @media (prefers-color-scheme: dark) {
            .email-bg {
                background: #111318 !important;
            }

            .darkmode-bg {
                background: #1c1f27 !important;
            }

            .darkmode-card {
                background: #252935 !important;
                border-color: #343949 !important;
            }

            .darkmode-heading {
                color: #ffffff !important;
            }

            .darkmode-text {
                color: #d8dbe4 !important;
            }

            .darkmode-muted {
                color: #9aa0b2 !important;
            }

            td.button-td-primary,
            td.button-td-primary a {
                background: #8fa0ff !important;
                border-color: #8fa0ff !important;
                color: #111318 !important;
            }

            td.button-td-primary:hover,
            td.button-td-primary a:hover {
                background: #b3bfff !important;
                border-color: #b3bfff !important;
            }

            .footer td {
                color: #9aa0b2 !important;
            }

            .footer-link,
            .darkmode-link {
                color: #8fa0ff !important;
            }
        }
    </style>
</head>

<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f2f4f8;" class="email-bg">
    <center role="article" aria-roledescription="email" lang="nb" style="width: 100%; background-color: #f2f4f8;" class="email-bg">
        <!--[if mso | IE]>
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f2f4f8;" class="email-bg">
        <tr>
        <td>
        <![endif]-->

        <div style="max-height: 0; overflow: hidden; mso-hide: all;" aria-hidden="true">
            <?= $e($forhandsvisning) ?>
        </div>

        <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: Arial, sans-serif;">
            &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
        </div>

        <div style="max-width: 600px; margin: 0 auto;" class="email-container">
            <!--[if mso]>
            <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="600">
            <tr>
            <td>
            <![endif]-->

            <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
                <tr>
                    <td style="padding: 28px 20px 20px 20px; text-align: center;">
                        <a href="<?= $e($nettsted_url . "/index.php") ?>">
                            <img src="<?= $e($nettsted_url . "/assets/bilder/StudentHub-logo.png") ?>" alt="Studenthub" width="200" height="72" style="display: block; margin: 0 auto; width: 200px; height: auto; border: 0; font-family: Arial, Helvetica, sans-serif; font-size: 22px; line-height: 26px; font-weight: bold; color: #4a5fd0;">
                        </a>
                    </td>
                </tr>

                <tr>
                    <td style="background-color: #4a5fd0; border-radius: 8px 8px 0 0; padding: 36px 28px; text-align: left; font-family: Arial, Helvetica, sans-serif;">
                        <h1 style="margin: 0 0 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 28px; line-height: 34px; color: #ffffff; font-weight: bold;">
                            Hei, <?= $fornavn ?>!
                        </h1>
                        <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 24px; color: #e3e7ff;">
                            Her er ditt nyhetsbrev fra Studenthub, med en oversikt over gruppene dine og grupper du kan oppdage.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="background-color: #ffffff; padding: 28px 28px 8px 28px;" class="darkmode-bg">
                        <h2 style="margin: 0 0 16px 0; font-family: Arial, Helvetica, sans-serif; font-size: 20px; line-height: 26px; color: #1f2430; font-weight: bold;" class="darkmode-heading">
                            Nytt på Studenthub
                        </h2>
                    </td>
                </tr>

                <?php foreach ($nb_nyheter as $nyhet): ?>
                    <tr>
                        <td style="background-color: #ffffff; padding: 0 28px 16px 28px;" class="darkmode-bg">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border: 1px solid #e3e6ee; border-radius: 8px; background-color: #f8f9fc;" class="darkmode-card">
                                <tr>
                                    <td style="padding: 18px 20px; font-family: Arial, Helvetica, sans-serif;">
                                        <h3 style="margin: 0 0 4px 0; font-family: Arial, Helvetica, sans-serif; font-size: 17px; line-height: 22px; color: #1f2430; font-weight: bold;" class="darkmode-heading">
                                            <?= $e($nyhet["tittel"]) ?>
                                        </h3>
                                        <p style="margin: 0 0 8px 0; font-size: 15px; line-height: 21px; color: #4b5263;" class="darkmode-text">
                                            <?= $e($nyhet["tekst"]) ?>
                                        </p>
                                        <a class="darkmode-link" href="<?= $e($nyhet["lenke"]) ?>" style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 20px; font-weight: bold; color: #4a5fd0; text-decoration: underline;"><?= $e($nyhet["knapp"]) ?> &rarr;</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <tr>
                    <td style="background-color: #ffffff; padding: 16px 28px 8px 28px; border-top: 1px solid #e3e6ee;" class="darkmode-bg">
                        <h2 style="margin: 0 0 4px 0; font-family: Arial, Helvetica, sans-serif; font-size: 20px; line-height: 26px; color: #1f2430; font-weight: bold;" class="darkmode-heading">
                            Dine grupper
                        </h2>
                        <p style="margin: 0 0 16px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 20px; color: #6b7280;" class="darkmode-muted">
                            <?php if ($antall_medlem === 0): ?>
                                Du er ikke medlem av noen grupper ennå.
                            <?php elseif ($antall_medlem === 1): ?>
                                Du er medlem av 1 gruppe.
                            <?php else: ?>
                                Du er medlem av <?= $antall_medlem ?> grupper.
                            <?php endif; ?>
                        </p>
                    </td>
                </tr>

                <?php foreach ($nb_grupper as $gruppe): ?>
                    <tr>
                        <td style="background-color: #ffffff; padding: 0 28px 16px 28px;" class="darkmode-bg">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border: 1px solid #e3e6ee; border-radius: 8px; background-color: #f8f9fc;" class="darkmode-card">
                                <tr>
                                    <td style="padding: 18px 20px; font-family: Arial, Helvetica, sans-serif;">
                                        <h3 style="margin: 0 0 6px 0; font-family: Arial, Helvetica, sans-serif; font-size: 17px; line-height: 22px; color: #1f2430; font-weight: bold;" class="darkmode-heading">
                                            <?= $e($gruppe["navn"]) ?>
                                        </h3>
                                        <?php if ($kort_beskrivelse($gruppe["beskrivelse"]) !== ""): ?>
                                            <p style="margin: 0 0 14px 0; font-size: 15px; line-height: 21px; color: #4b5263;" class="darkmode-text">
                                                <?= $kort_beskrivelse($gruppe["beskrivelse"]) ?>
                                            </p>
                                        <?php endif; ?>
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 0;">
                                            <tr>
                                                <td class="button-td button-td-primary" style="border-radius: 6px; background: #4a5fd0;">
                                                    <a class="button-a button-a-primary" href="<?= $e($nettsted_url . "/gruppe.php?gruppe_id=" . (int) $gruppe["gruppe_id"]) ?>" style="background: #4a5fd0; border: 1px solid #4a5fd0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 14px; text-decoration: none; padding: 11px 18px; color: #ffffff; display: block; border-radius: 6px; font-weight: bold;">Åpne gruppe</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if ($antall_andre > 0): ?>
                    <tr>
                        <td style="background-color: #ffffff; padding: 16px 28px 8px 28px; border-top: 1px solid #e3e6ee;" class="darkmode-bg">
                            <h2 style="margin: 12px 0 4px 0; font-family: Arial, Helvetica, sans-serif; font-size: 20px; line-height: 26px; color: #1f2430; font-weight: bold;" class="darkmode-heading">
                                Oppdag flere grupper
                            </h2>
                            <p style="margin: 0 0 16px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 20px; color: #6b7280;" class="darkmode-muted">
                                Disse gruppene finnes på Studenthub. Be et medlem om en invitasjonslenke for å bli med.
                            </p>
                        </td>
                    </tr>

                    <?php foreach ($nb_grupper_ikke_medlem as $gruppe): ?>
                        <tr>
                            <td style="background-color: #ffffff; padding: 0 28px 12px 28px;" class="darkmode-bg">
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border-left: 4px solid #c7cdf5;">
                                    <tr>
                                        <td style="padding: 4px 0 4px 14px; font-family: Arial, Helvetica, sans-serif;">
                                            <p style="margin: 0; font-size: 16px; line-height: 22px; font-weight: bold; color: #1f2430;" class="darkmode-heading">
                                                <?= $e($gruppe["navn"]) ?>
                                            </p>
                                            <?php if ($kort_beskrivelse($gruppe["beskrivelse"]) !== ""): ?>
                                                <p style="margin: 2px 0 0 0; font-size: 14px; line-height: 20px; color: #6b7280;" class="darkmode-muted">
                                                    <?= $kort_beskrivelse($gruppe["beskrivelse"]) ?>
                                                </p>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

                <tr>
                    <td style="background-color: #ffffff; border-radius: 0 0 8px 8px; padding: 12px 28px 32px 28px;" class="darkmode-bg">
                        <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: auto;">
                            <tr>
                                <td class="button-td button-td-primary" style="border-radius: 6px; background: #4a5fd0;">
                                    <a class="button-a button-a-primary" href="<?= $e($nettsted_url . "/index.php") ?>" style="background: #4a5fd0; border: 1px solid #4a5fd0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 15px; text-decoration: none; padding: 14px 24px; color: #ffffff; display: block; border-radius: 6px; font-weight: bold;">Gå til Studenthub</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;" class="footer">
                <tr>
                    <td style="padding: 24px 20px 36px 20px; font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 18px; text-align: center; color: #6b7280;">
                        Dette nyhetsbrevet ble sendt til <span class="unstyle-auto-detected-links"><?= $epost ?></span>
                        fordi du har en konto på Studenthub.
                        <br><br>
                        Studenthub - Gruppe 3 - IS-115 - Universitetet i Agder
                        <br><br>
                        <strong style="font-weight: bold;">Kontakt oss</strong><br>
                        E-post: <a class="footer-link" href="mailto:<?= $kontakt_epost ?>" style="color: #4a5fd0; text-decoration: underline;"><?= $kontakt_epost ?></a><br>
                        Telefon: <a class="footer-link" href="tel:<?= $kontakt_telefon_lenke ?>" style="color: #4a5fd0; text-decoration: underline;"><?= $kontakt_telefon ?></a>
                    </td>
                </tr>
            </table>

            <!--[if mso]>
            </td>
            </tr>
            </table>
            <![endif]-->
        </div>

        <!--[if mso | IE]>
        </td>
        </tr>
        </table>
        <![endif]-->
    </center>
</body>

</html>
