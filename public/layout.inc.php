<?php

// Filen layout.inc.php er ansvarlig for å gjøre det så enkelt som mulig for alle sider i Studenthub å se lik ut. 
// Det innebærer at filen lager en <head>, som lenker til alle CSS filer som blir brukt globalt, samt fonter og tittel. 
// I tilleg er det inkludert ekstra php filer som alle undersider skal inneholde.
//
// Systemet i det hele bruker MVC -  Altså Model View Controller. Det betyr at ser definerer tre ting. 
// En kontroller, altså en bit med kode som håndterer bruker input fra GET eller POST, henter det nødvendige informasjonen fra databasen, og kjører denne filen.
// En Model, altså all data som skal plasseres på nettsiden. 
// En View, det som brukeren skal se til slutt. Dette er en vanlig PHP fil, som er inkludert nede med "include $side_innhold". Variabelen er definert i kontroller filen.
// Vi valgte å bruke dette fordi når man programmerer PHP finnes det lite kontroll, lite struktur, og programmeren har fritt valg å gjøre hva den vill. MVC prøver å fikse dette problemet, 
// det hjelper oss med å definere en struktur som alle sider skal bruke. Som sørger for at vi ender opp med så få problemer som mulig.

?>

<!DOCTYPE html>
<html lang="nb">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $side_tittel ?? "Studenthub"; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Serif:wght@500;600;700&family=Inter:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="<?php echo url("/assets/css/colors.css"); ?>">
    <link rel="stylesheet" href="<?php echo url("/assets/css/base.css"); ?>">
    <link rel="stylesheet" href="<?php echo url("/assets/css/layout.css"); ?>">
    <?php foreach ($side_stiler ?? [] as $stylesheet): ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars($stylesheet); ?>">
    <?php endforeach; ?>
</head>

<body>
    <?php include __DIR__ . "/../components/header.tpl.php"; ?>
    <?php include __DIR__ . "/../components/breadcrumb.tpl.php"; ?>

    <main class="main">
        <?php include __DIR__ . "/../components/sidebar.tpl.php"; ?>

        <article class="page-content">
            <?php include $side_innhold; ?>
        </article>
    </main>

    <footer>
        <p>&copy; 2026 Studenthub - Gruppe 3</p>
    </footer>

    <script src="<?php echo url("/assets/js/sidebar.js"); ?>" type="module"></script>
    <?php foreach ($side_skript ?? [] as $script): ?>
        <script src="<?php echo htmlspecialchars($script); ?>" type="module"></script>
    <?php endforeach; ?>
</body>

</html>
