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
    not_found('Project not found.');
}

$stmt = db()->prepare('DELETE FROM projects WHERE id = :id');
$stmt->execute(['id' => $id]);

// rowCount() = how many rows were deleted. 0 means that project did not exist.
if ($stmt->rowCount() === 0) {
    not_found('Project not found.');
}

// Its issues are deleted automatically by the database (ON DELETE CASCADE)
flash('Project deleted.');
redirect('/');