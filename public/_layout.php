<?php

// TODO: Autentikasjon
//if (!isset($_SESSION["student_id"])) {
//    header("Location: /login.php");
//    exit();
//}
    
?>


<!DOCTYPE html>
<html lang="nb">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $page_title ?? "Studenthub"; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Serif:wght@500;600;700&family=Inter:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="/assets/css/colors.css">
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/layout.css">
    <?php foreach ($page_styles ?? [] as $stylesheet): ?>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($stylesheet); ?>">
    <?php endforeach; ?>
</head>

<body>
    <?php include __DIR__."/../components/header.tpl.php"; ?>
    <?php include __DIR__."/../components/breadcrumb.tpl.php"; ?>

    <main class="main">
        <?php include __DIR__."/../components/sidebar.tpl.php"; ?>
        
        <article class="page-content">
            <?php include $page_content; ?>
        </article>
    </main>
    
    <footer>
        <p>&copy; 2026 Studenthub - Gruppe 3</p>
    </footer>

    <script src="/assets/js/sidebar.js" type="module"></script>
</body>

</html>
