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
