<?php
/**
 * Shared page layout.
 * Every page calls page_header() at the start and page_footer() at the end.
 */

function page_header(string $title): void
{
    $flash = get_flash();
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> | Issue Tracker</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <header class="site-header">
        <a href="/" class="logo">Issue Tracker</a>
    </header>
    <main class="container">
        <?php if ($flash !== null): ?>
            <p class="flash"><?= e($flash) ?></p>
        <?php endif; ?>
    <?php
}

function page_footer(): void
{
    ?>
    </main>
</body>
</html>
    <?php
}