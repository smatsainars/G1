<?php
require __DIR__ . '/../src/db.php';
require __DIR__ . '/../src/helpers.php';

// Deleting only works through the POST form, never by just opening a URL
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false) {
    not_found('Issue not found.');
}

// Find the issue's project first, so we can go back to that project's page
$stmt = db()->prepare('SELECT project_id FROM issues WHERE id = :id');
$stmt->execute(['id' => $id]);
$projectId = $stmt->fetchColumn();

if ($projectId === false) {
    not_found('Issue not found.');
}

$stmt = db()->prepare('DELETE FROM issues WHERE id = :id');
$stmt->execute(['id' => $id]);

flash('Issue deleted.');
redirect('/project_view.php?id=' . (int) $projectId);