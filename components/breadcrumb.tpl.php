<?php

$brødsmulesti = $brødsmulesti ?? [];

?>

<nav aria-label="brødsmulesti" class="breadcrumb">
    <ul>
        <li><a href="<?php echo url("/index.php"); ?>">Studenthub</a></li>
        <?php foreach ($brødsmulesti as $i => $crumb): ?>
            <?php $er_siste = $i === array_key_last($brødsmulesti); ?>
            <li>
                <?php if (!$er_siste && !empty($crumb["lenke"])): ?>
                    <a href="<?php echo htmlspecialchars($crumb["lenke"]); ?>"><?php echo htmlspecialchars($crumb["tekst"]); ?></a>
                <?php else: ?>
                    <span aria-current="page"><?php echo htmlspecialchars($crumb["tekst"]); ?></span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
