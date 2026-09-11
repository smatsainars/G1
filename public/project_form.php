<?php
require __DIR__ . '/../src/db.php';
require __DIR__ . '/../src/helpers.php';
require __DIR__ . '/../src/validation.php';
require __DIR__ . '/../src/layout.php';

// ?id=5 means "edit project 5". No id means "create a new project".
$id = null;
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id === false) {
        not_found('Project not found.');
    }
}

// Empty form by default
$project = ['name' => '', 'description' => ''];
$errors  = [];

// Editing: load the existing project
if ($id !== null) {
    $stmt = db()->prepare('SELECT name, description FROM projects WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $found = $stmt->fetch();

    if ($found === false) {
        not_found('Project not found.');
    }
    $project = ['name' => $found['name'], 'description' => $found['description'] ?? ''];
}

// The form was submitted (the CSRF token was already checked in helpers.php)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project = [
        'name'        => clean_string($_POST, 'name'),
        'description' => clean_string($_POST, 'description'),
    ];

    $errors = validate_project($project);

    if (count($errors) === 0) {
        // An empty description is saved as NULL, not as an empty string
        $description = $project['description'] === '' ? null : $project['description'];

        if ($id === null) {
            $stmt = db()->prepare(
                'INSERT INTO projects (name, description) VALUES (:name, :description)'
            );
            $stmt->execute(['name' => $project['name'], 'description' => $description]);
            flash('Project created.');
        } else {
            $stmt = db()->prepare(
                'UPDATE projects SET name = :name, description = :description WHERE id = :id'
            );
            $stmt->execute(['name' => $project['name'], 'description' => $description, 'id' => $id]);
            flash('Project updated.');
        }

        redirect('/');
    }
}

$title = $id === null ? 'New project' : 'Edit project';
page_header($title);
?>

<h1><?= e($title) ?></h1>

<form method="post" novalidate>
    <?= csrf_field() ?>

    <div class="field">
        <label for="name">Name *</label>
        <input type="text" id="name" name="name" value="<?= e($project['name']) ?>">
        <?php if (isset($errors['name'])): ?>
            <p class="error"><?= e($errors['name']) ?></p>
        <?php endif; ?>
    </div>

    <div class="field">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"><?= e($project['description']) ?></textarea>
        <?php if (isset($errors['description'])): ?>
            <p class="error"><?= e($errors['description']) ?></p>
        <?php endif; ?>
    </div>

    <div class="form-actions">
        <button type="submit">Save</button>
        <a href="/">Cancel</a>
    </div>
</form>

<?php page_footer(); ?>