<?php


// Start the session once. We use it for one-time messages like "Project saved".
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Send the browser to another page and stop the script.
 */
function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function flash(string $message): void
{
    $_SESSION['flash'] = $message;
}

/**
 * Get the message (if there is one) and remove it, so it shows only once.
 */
function get_flash(): ?string
{
    $message = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $message;
}