<?php
require __DIR__ . '/../src/db.php';
require __DIR__ . '/../src/helpers.php';
require __DIR__ . '/../src/validation.php';
require __DIR__ . '/../src/layout.php';

$positiveInt = ['options' => ['min_range' => 1]];

// Edit:   issue_form.php?id=7          (the issue already knows its project)
// Create: issue_form.php?project_id=2  (a new issue for project 2)
$id = null;
$projectId = null;

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT, $positiveInt);
    if ($id === false) {
        not_found('Issue not found.');
    }
} else {
    $projectId = filter_var($_GET['project_id'] ?? null, FILTER_VALIDATE_INT, $positiveInt);
    if ($projectId === false) {
        not_found('Project not found.');
    }
}

// Empty form by default
$issue  = ['title' => '', 'description' => '', 'status' => 'open', 'priority' => 'medium'];
$errors = [];

// Editing: load the issue
if ($id !== null) {
    $stmt = db()->prepare(
        'SELECT project_id, title, description, status, priority FROM issues WHERE id = :id'
    );
    $stmt->execute(['id' => $id]);
    $found = $stmt->fetch();

    if ($found === false) {
        not_found('Issue not found.');
    }
    $projectId = (int) $found['project_id'];
    $issue = [
        'title'       => $found['title'],
        'description' => $found['description'] ?? '',
        'status'      => $found['status'],
        'priority'    => $found['priority'],
    ];
}

// The project must exist (its name is also shown on the page)
$stmt = db()->prepare('SELECT id, name FROM projects WHERE id = :id');
$stmt->execute(['id' => $projectId]);
$project = $stmt->fetch();

if ($project === false) {
    not_found('Project not found.');
}

// The form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $issue = [
        'title'       => clean_string($_POST, 'title'),
        'description' => clean_string($_POST, 'description'),
        'status'      => clean_string($_POST, 'status'),
        'priority'    => clean_string($_POST, 'priority'),
    ];

    $errors = validate_issue($issue);

    if (count($errors) === 0) {
        $description = $issue['description'] === '' ? null : $issue['description'];

        if ($id === null) {
            $stmt = db()->prepare(
                'INSERT INTO issues (project_id, title, description, status, priority)
                 VALUES (:project_id, :title, :description, :status, :priority)'
            );
            $stmt->execute([
                'project_id'  => $projectId,
                'title'       => $issue['title'],
                'description' => $description,
                'status'      => $issue['status'],
                'priority'    => $issue['priority'],
            ]);
            flash('Issue created.');
        } else {
            $stmt = db()->prepare(
                'UPDATE issues
                 SET title = :title, description = :description,
                     status = :status, priority = :priority
                 WHERE id = :id'
            );
            $stmt->execute([
                'title'       => $issue['title'],
                'description' => $description,
                'status'      => $issue['status'],
                'priority'    => $issue['priority'],
                'id'          => $id,
            ]);
            flash('Issue updated.');
        }

        redirect('/project_view.php?id=' . $projectId);
    }
}

$title = $id === null ? 'New issue' : 'Edit issue';
page_header($title);
?>

<p><a href="/project_view.php?id=<?= (int) $projectId ?>">&larr; <?= e($project['name']) ?></a></p>

<h1><?= e($title) ?></h1>

<form method="post" novalidate>
    <div class="field">
        <label for="title">Title *</label>
        <input type="text" id="title" name="title" value="<?= e($issue['title']) ?>">
        <?php if (isset($errors['title'])): ?>
            <p class="error"><?= e($errors['title']) ?></p>
        <?php endif; ?>
    </div>

    <div class="field">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"><?= e($issue['description']) ?></textarea>
        <?php if (isset($errors['description'])): ?>
            <p class="error"><?= e($errors['description']) ?></p>
        <?php endif; ?>
    </div>

    <div class="field">
        <label for="status">Status</label>
        <select id="status" name="status">
            <?php foreach (ISSUE_STATUSES as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= $issue['status'] === $value ? 'selected' : '' ?>>
                    <?= e($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['status'])): ?>
            <p class="error"><?= e($errors['status']) ?></p>
        <?php endif; ?>
    </div>

    <div class="field">
        <label for="priority">Priority</label>
        <select id="priority" name="priority">
            <?php foreach (ISSUE_PRIORITIES as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= $issue['priority'] === $value ? 'selected' : '' ?>>
                    <?= e($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['priority'])): ?>
            <p class="error"><?= e($errors['priority']) ?></p>
        <?php endif; ?>
    </div>

    <div class="form-actions">
        <button type="submit">Save</button>
        <a href="/project_view.php?id=<?= (int) $projectId ?>">Cancel</a>
    </div>
</form>

<?php page_footer(); ?>