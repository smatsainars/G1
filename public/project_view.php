<?php
require __DIR__ . '/../src/db.php';
require __DIR__ . '/../src/helpers.php';
require __DIR__ . '/../src/validation.php';   // for the status / priority labels
require __DIR__ . '/../src/layout.php';

// ?id= is required and must be a whole number
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false) {
    not_found('Project not found.');
}

// The project
$stmt = db()->prepare('SELECT id, name, description FROM projects WHERE id = :id');
$stmt->execute(['id' => $id]);
$project = $stmt->fetch();

if ($project === false) {
    not_found('Project not found.');
}

// Its issues: open ones first, then high priority first, then newest first
$stmt = db()->prepare(
    "SELECT id, title, status, priority, created_at
     FROM issues
     WHERE project_id = :project_id
     ORDER BY FIELD(status, 'open', 'in_progress', 'done'),
              FIELD(priority, 'high', 'medium', 'low'),
              created_at DESC"
);
$stmt->execute(['project_id' => $id]);
$issues = $stmt->fetchAll();

page_header($project['name']);
?>

<p><a href="/">&larr; All projects</a></p>

<div class="page-title">
    <h1><?= e($project['name']) ?></h1>
    <a href="/issue_form.php?project_id=<?= (int) $project['id'] ?>" class="button">+ New issue</a>
</div>

<?php if ($project['description'] !== null): ?>
    <p class="description"><?= nl2br(e($project['description'])) ?></p>
<?php endif; ?>

<h2>Issues (<?= count($issues) ?>)</h2>

<?php if (count($issues) === 0): ?>
    <p class="empty">No issues yet.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($issues as $issue): ?>
            <tr>
                <td><?= e($issue['title']) ?></td>
                <td><span class="badge status-<?= e($issue['status']) ?>"><?= e(ISSUE_STATUSES[$issue['status']]) ?></span></td>
                <td><span class="badge priority-<?= e($issue['priority']) ?>"><?= e(ISSUE_PRIORITIES[$issue['priority']]) ?></span></td>
                <td><?= e(date('d.m.Y', strtotime($issue['created_at']))) ?></td>
                <td class="actions">
                    <a href="/issue_form.php?id=<?= (int) $issue['id'] ?>">Edit</a>
                    <form method="post" action="/issue_delete.php"
                          onsubmit="return confirm('Delete this issue?');">
                        <input type="hidden" name="id" value="<?= (int) $issue['id'] ?>">
                        <?= csrf_field() ?>
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php page_footer(); ?>