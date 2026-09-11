<?php
/**
 * Small helper functions used by every page.
 */

// Start the session once. We use it for one-time messages like "Project saved".
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If something unexpected goes wrong (for example, the database is down),
// log the details for the developer and show a short, safe message to the user.
set_exception_handler(function (Throwable $e): void {
    error_log((string) $e);
    http_response_code(500);
    echo 'Something went wrong. Please try again later.';
});

/**
 * Escape text before printing it into HTML.
 * Stops XSS: if someone types <script> as a project name,
 * it is shown as plain text instead of being run.
 */
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

/**
 * Save a one-time message to show on the next page.
 */
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

/**
 * Stop and show "not found" (HTTP 404), e.g. for ?id=999 that does not exist.
 */
function not_found(string $message = 'Page not found.'): never
{
    http_response_code(404);
    exit(e($message));
}