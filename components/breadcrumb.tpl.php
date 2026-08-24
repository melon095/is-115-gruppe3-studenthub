<?php

$breadcrumbs = $breadcrumbs ?? [];

?>

<nav aria-label="brødsmulesti" class="breadcrumb">
    <ul>
        <li><a href="/index.php">Studenthub</a></li>
        <?php foreach ($breadcrumbs as $i => $crumb): ?>
            <?php $er_siste = $i === array_key_last($breadcrumbs); ?>
            <li>
                <?php if (!$er_siste && !empty($crumb["href"])): ?>
                    <a href="<?php echo htmlspecialchars($crumb["href"]); ?>"><?php echo htmlspecialchars($crumb["label"]); ?></a>
                <?php else: ?>
                    <span aria-current="page"><?php echo htmlspecialchars($crumb["label"]); ?></span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
